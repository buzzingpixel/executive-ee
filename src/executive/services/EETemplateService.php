<?php
declare(strict_types=1);

namespace buzzingpixel\executive\services;

use EE_Config;
use Exception;
use EE_Template;
use EE_Extensions;
use EllisLab\ExpressionEngine\Legacy\Facade as LegacyApp;

class EETemplateService extends EE_Template
{
    /** @var EE_Config $config */
    private $config;

    /** @var LegacyApp $legacyApp */
    private $legacyApp;

    /** @var EE_Extensions $extensions */
    private $extensions;

    public function __construct()
    {
        parent::__construct();

        $this->config = ee()->config;
        $this->legacyApp = ee();
        $this->extensions = ee()->extensions;
    }

    public function renderTemplate(string $group, string $template, array $variables = []): string
    {
        $this->start_microtime = microtime(true);

        $primaryInstance = $this->legacyApp->TMPL;
        $this->legacyApp->remove('TMPL');
        $this->legacyApp->set('TMPL', $this);

        $this->log_item(' - Begin Template Processing - ');

        $oldIndicator = $this->config->item('hidden_template_indicator');

        $this->config->set_item('hidden_template_indicator', '');

        $oldGlobals = $this->config->_global_vars;

        $this->config->_global_vars = array_merge($oldGlobals, $variables);

        // Run garbage collection about 10% of the time
        try {
            if (random_int(1, 10) === 1) {
                $this->_garbage_collect_cache();
                ee('ChannelSet')->garbageCollect();
            }
        } catch (Exception $e) {
        }

        $this->log_item("Template: {$group}/{$template}");

        $this->fetch_and_parse($group, $template, false);

        $this->log_item(' - End Template Processing - ');
        $this->log_item('Parse Global Variables');

        $this->config->set_item('hidden_template_indicator', $oldIndicator);

        if ($this->template_type === 'static') {
            $this->final_template = $this->restore_xml_declaration($this->final_template);
        }

        if ($this->template_type !== 'static') {
            $this->final_template = $this->parse_globals($this->final_template);
        }

        $this->legacyApp->remove('TMPL');
        $this->legacyApp->set('TMPL', $primaryInstance);

        $this->config->_global_vars = $oldGlobals;

        $this->log_item('Template Parsing Finished');

        return $this->final_template;
    }

    public function renderPath(string $path, array $variables = []): string
    {
        if (! file_exists($path)) {
            $path = APP_DIR . '/' . ltrim($path, '/');
        }

        $templateContents = file_get_contents($path) ?: '';

        if (! $templateContents) {
            return '';
        }

        return $this->renderString($templateContents, $variables);
    }

    public function renderString(string $str, array $variables = []): string
    {
        $this->start_microtime = microtime(true);

        $primaryInstance = $this->legacyApp->TMPL;
        $this->legacyApp->remove('TMPL');
        $this->legacyApp->set('TMPL', $this);

        $this->log_item(' - Begin Template Processing - ');

        $oldGlobals = $this->config->_global_vars;

        $this->config->_global_vars = array_merge($oldGlobals, $variables);

        $this->template_type = 'webpage';

        $this->cache_status = 'NO_CACHE';

        $this->template = $str;

        $siteId = $this->config->item('site_id');

        $this->parse($this->template, false, $siteId, false);

        if ($this->extensions->active_hook('template_post_parse') === true) {
            $this->final_template = $this->extensions->call(
                'template_post_parse',
                $this->final_template,
                false,
                $siteId
            );
        }

        $this->log_item(' - End Template Processing - ');
        $this->log_item('Parse Global Variables');

        $this->final_template = $this->parse_globals($this->final_template);

        $this->legacyApp->remove('TMPL');
        $this->legacyApp->set('TMPL', $primaryInstance);

        $this->config->_global_vars = $oldGlobals;

        $this->log_item('Template Parsing Finished');

        return $this->final_template;
    }
}
