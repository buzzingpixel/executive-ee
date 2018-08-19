<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace BuzzingPixel\Executive\Command;

use buzzingpixel\executive\abstracts\BaseCommand;
use EllisLab\ExpressionEngine\Service\Addon\Factory as EEAddonFactory;
use EllisLab\ExpressionEngine\Service\Addon\Addon as EEAddon;

/**
 * Class Service
 */
class AddonUpdatesCommand extends BaseCommand
{
    /** @var EEAddonFactory $eeAddonFactory */
    private $eeAddonFactory;

    /**
     * Initialize
     */
    public function initCommand()
    {
        $this->eeAddonFactory = ee('Addon');
    }

    /**
     * Run a single add-on's update method (regardless if version is behind)
     * @param string $addon
     */
    public function runAddonUpdateMethod($addon)
    {
        if ($addon === null) {
            $this->consoleService->writeLn(
                lang('addonMustBeSpecified:') . ' ' . '--addon=addon_name',
                'red'
            );
            return;
        }

        if ($this->eeAddonFactory->get($addon) === null) {
            $this->consoleService->writeLn(lang('addonNotFound'), 'red');
            return;
        }

        /** @var EEAddon $addon */
        $addonInfo = $this->eeAddonFactory->get($addon);

        if (! $addonInfo->isInstalled()) {
            $this->consoleService->writeLn(lang('addonNotInstalled'), 'red');
            return;
        }

        $class = $addonInfo->getInstallerClass();
        $installed = ee()->addons->get_installed('modules', true);

        $UPD = new $class;
        $UPD->_ee_path = APPPATH;
        $UPD->update($installed[$addon]['module_version']);

        $this->consoleService->writeLn(
            lang('addonUpdateRunSuccessfully'),
            'green'
        );
    }

    /**
     * Run all updates
     */
    public function run()
    {
        foreach ($this->eeAddonFactory->all() as $addon_info) {
            /** @var EEAddon $addon_info */

            if (! $addon_info->isInstalled()) {
                continue;
            }

            $addon = $addon_info->getProvider()->getPrefix();

            $module = $this->getModule($addon);
            if (! empty($module)
                && $module['installed'] === true
                && array_key_exists('update', $module)
            ) {
                $installed = ee()->addons->get_installed('modules', true);

                $class = $addon_info->getInstallerClass();
                $version = $installed[$addon]['module_version'];

                ee()->load->add_package_path($installed[$addon]['path']);

                $UPD = new $class;
                $UPD->_ee_path = APPPATH;

                if ($UPD->update($version) !== false)
                {
                    $new_version = $addon_info->getVersion();
                    if (version_compare($version, $new_version, '<')) {
                        $module = ee('Model')->get('Module', $installed[$addon]['module_id'])
                            ->first();
                        $module->module_version = $new_version;
                        $module->save();
                    }
                }

                $fieldtype = $this->getFieldtype($addon);
                if (! empty($fieldtype)
                    && $fieldtype['installed'] === true
                    && array_key_exists('update', $fieldtype)
                ) {
                    ee()->api_channel_fields->include_handler($addon);
                    $FT = ee()->api_channel_fields->setup_handler($addon, true);
                    if (method_exists($FT, 'update') &&
                        $FT->update($fieldtype['version']) !== false
                    ) {
                        if (ee()->api_channel_fields->apply('update', array($fieldtype['version'])) !== false) {
                            $model = ee('Model')->get('Fieldtype')
                                ->filter('name', $addon)
                                ->first();

                            $model->version = $addon_info->getVersion();
                            $model->save();
                        }
                    }
                }

                $extension = $this->getExtension($addon);
                if (! empty($extension)
                    && $extension['installed'] === true
                    && array_key_exists('update', $extension)
                ) {
                    $class = $addon_info->getExtensionClass();

                    $class_name = $extension['class'];
                    $Extension = new $class();
                    $Extension->update_extension($extension['version']);
                    ee()->extensions->version_numbers[$class_name] = $addon_info->getVersion();

                    $model = ee('Model')->get('Extension')
                        ->filter('class', $class_name)
                        ->all();

                    $model->version = $addon_info->getVersion();
                    $model->save();
                }
            }

            $plugin = $this->getPlugin($addon);
            if (! empty($plugin)
                && $plugin['installed'] === true
                && array_key_exists('update', $plugin)
            ) {
                $typography = 'n';

                if ($addon_info->get('plugin.typography')) {
                    $typography = 'y';
                }

                $model = ee('Model')->get('Plugin')
                    ->filter('plugin_package', $plugin['package'])
                    ->first();

                $model->plugin_name = $plugin['name'];
                $model->plugin_package = $plugin['package'];
                $model->plugin_version = $addon_info->getVersion();
                $model->is_typography_related = $typography;
                $model->save();
            }
        }

        $this->consoleService->writeLn(
            lang('addonsUpdatedSuccessfully'),
            'green'
        );
    }

    /**
     * Get data on a module
     * @param string $name The add-on name
     * @return array
     */
    private function getModule($name)
    {
        /** @var EEAddon $info */
        $info = ee('Addon')->get($name);

        if (! $info->hasModule()) {
            return array();
        }

        // Use lang file if present, otherwise fallback to addon.setup
        ee()->lang->loadfile($name, '', false);

        $display_name = (lang(strtolower($name).'_module_name') != strtolower($name).'_module_name')
            ? lang(strtolower($name).'_module_name') :
            $info->getName();

        $data = array(
            'developer' => $info->getAuthor(),
            'version' => '--',
            'installed' => false,
            'name' => $display_name,
            'package' => $name,
            'type' => 'module',
        );

        $module = ee('Model')->get('Module')
            ->filter('module_name', $name)
            ->first();

        if ($module) {
            $data['module_id'] = $module->module_id;
            $data['installed'] = true;
            $data['version'] = $module->module_version;

            if ($info->get('settings_exist')) {
                $data['settings_url'] = ee('CP/URL')->make('addons/settings/' . $name);
            }

            if ($info->hasInstaller()) {
                $class = $info->getInstallerClass();

                ee()->load->add_package_path($info->getPath());

                $UPD = new $class;

                if (version_compare($info->getVersion(), $module->module_version, '>')
                    && method_exists($UPD, 'update')
                ) {
                    $data['update'] = $info->getVersion();
                }
            }
        }

        return $data;
    }

    /**
     * Get data on a fieldtype
     * @param string $name The add-on name
     * @return array
     */
    private function getFieldtype($name)
    {
        /** @var EEAddon $info */
        $info = ee('Addon')->get($name);

        if (! $info->hasFieldtype()) {
            return array();
        }

        $data = array(
            'developer' => $info->getAuthor(),
            'version' => '--',
            'installed' => false,
            'name' => $info->getName(),
            'package' => $name,
            'type' => 'fieldtype',
        );

        $model = ee('Model')->get('Fieldtype')
            ->filter('name', $name)
            ->first();

        if ($model) {
            $data['installed'] = true;
            $data['version'] = $model->version;

            if (version_compare($info->getVersion(), $model->version, '>')) {
                $data['update'] = $info->getVersion();
            }

            if ($info->get('settings_exist')) {
                if ($model->settings) {
                    $data['settings'] = $model->settings;
                }
                $data['settings_url'] = ee('CP/URL')->make('addons/settings/' . $name);
            }
        }

        return $data;
    }

    /**
     * Get data on an extension
     * @param string $name The add-on name
     * @return array
     */
    private function getExtension($name)
    {
        if (ee()->config->item('allow_extensions') !== 'y') {
            return array();
        }

        /** @var EEAddon $info */
        $info = ee('Addon')->get($name);

        if (! $info->hasExtension()) {
            return array();
        }

        $class_name = ucfirst($name) . '_ext';

        $data = array(
            'developer' => $info->getAuthor(),
            'version' => '--',
            'installed' => false,
            'enabled' => null,
            'name' => $info->getName(),
            'package' => $name,
            'class' => $class_name,
        );

        $extension = ee('Model')->get('Extension')
            ->filter('class', $class_name)
            ->first();

        if ($extension) {
            $data['version'] = $extension->version;
            $data['installed'] = true;
            $data['enabled'] = $extension->enabled;

            ee()->load->add_package_path($info->getPath());

            if (! class_exists($class_name)) {
                $file = $info->getPath() . '/ext.' . $name . '.php';
                if (ee()->config->item('debug') == 2
                    or (
                        ee()->config->item('debug') == 1
                        and ee()->session->userdata('group_id') == 1
                    )
                ) {
                    include($file);
                } else {
                    @include($file);
                }

                if (! class_exists($class_name)) {
                    trigger_error(str_replace(array('%c', '%f'), array(htmlentities($class_name), htmlentities($file)), lang('extension_class_does_not_exist')));
                    return array();
                }
            }

            // Get some details on the extension
            $ext_obj = new $class_name($extension->settings);
            if (version_compare($info->getVersion(), $extension->version, '>')
                && method_exists($ext_obj, 'update_extension') === true
            ) {
                $data['update'] = $info->getVersion();
            }

            if ($info->get('settings_exist')) {
                $data['settings_url'] = ee('CP/URL')->make('addons/settings/' . $name);
            }
        }

        return $data;
    }

    /**
     * Get data on a plugin
     * @param string $name The add-on name
     * @return array
     */
    private function getPlugin($name)
    {
        /** @var EEAddon $info */
        $info = ee('Addon')->get($name);

        if (! $info->hasPlugin()) {
            return array();
        }

        $data = array(
            'developer' => $info->getAuthor(),
            'version' => '--',
            'installed' => false,
            'name' => $info->getName(),
            'package' => $name,
            'type' => 'plugin',
        );

        $model = ee('Model')->get('Plugin')
            ->filter('plugin_package', $name)
            ->first();

        if (! is_null($model)) {
            $data['installed'] = true;
            $data['version'] = $model->plugin_version;
            if (version_compare($info->getVersion(), $model->plugin_version, '>')) {
                $data['update'] = $info->getVersion();
            }
        }

        return $data;
    }
}
