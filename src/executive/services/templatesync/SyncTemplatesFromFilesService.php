<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2018 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace buzzingpixel\executive\services\templatesync;

use EllisLab\ExpressionEngine\Controller\Design\Template;

/**
 * Copied from EE's Design controller. Why the sam holy hill this is happening
 * in the design controller and not somewhere abstracted for logic and method
 * re-use is quite beyond my capability to understand
 */
class SyncTemplatesFromFilesService
{
    /**
     * @see \EllisLab\ExpressionEngine\Controller\Design\Design::_sync_from_files
     */
    public function run()
    {
        if (ee()->config->item('save_tmpl_files') != 'y')
        {
            return FALSE;
        }

        ee()->load->library('api');
        ee()->legacy_api->instantiate('template_structure');

        // Lazy load templates instead, this was looping the group query with it included

        $groups = ee('Model')->get('TemplateGroup')
            ->with('Templates')
            ->filter('site_id', ee()->config->item('site_id'))
            ->all();
        $group_ids_by_name = $groups->getDictionary('group_name', 'group_id');

        $existing = array();

        foreach ($groups as $group)
        {
            $existing[$group->group_name.'.group'] = array_combine(
                $group->Templates->pluck('template_name'),
                $group->Templates->pluck('template_name')
            );
        }

        $basepath = PATH_TMPL . ee()->config->item('site_short_name');
        ee()->load->helper('directory');
        $files = directory_map($basepath, 0, 1);

        if ($files !== FALSE)
        {
            foreach ($files as $group => $templates)
            {
                if (substr($group, -6) != '.group')
                {
                    continue;
                }

                $group_name = substr($group, 0, -6); // remove .group

                // DB column limits template and group name to 50 characters
                if (strlen($group_name) > 50)
                {
                    continue;
                }

                $group_id = '';

                if ( ! preg_match("#^[a-zA-Z0-9_\-]+$#i", $group_name))
                {
                    continue;
                }

                // if the template group doesn't exist, make it!
                if ( ! isset($existing[$group]))
                {
                    if ( ! ee()->legacy_api->is_url_safe($group_name))
                    {
                        continue;
                    }

                    if (in_array($group_name, array('act', 'css')))
                    {
                        continue;
                    }

                    $data = array(
                        'group_name'		=> $group_name,
                        'is_site_default'	=> 'n',
                        'site_id'			=> ee()->config->item('site_id')
                    );

                    $new_group = ee('Model')->make('TemplateGroup', $data)->save();
                    $group_id = $new_group->group_id;

                    $existing[$group] = array();
                }

                // Grab group_id if we still don't have it.
                if ($group_id == '')
                {
                    $group_id = $group_ids_by_name[$group_name];
                }

                // if the templates don't exist, make 'em!
                foreach ($templates as $template)
                {
                    // Skip subdirectories (such as those created by svn)
                    if (is_array($template))
                    {
                        continue;
                    }
                    // Skip hidden ._ files
                    if (substr($template, 0, 2) == '._')
                    {
                        continue;
                    }
                    // If the last occurance is the first position?  We skip that too.
                    if (strrpos($template, '.') == FALSE)
                    {
                        continue;
                    }

                    $ext = strtolower(ltrim(strrchr($template, '.'), '.'));
                    if ( ! in_array('.'.$ext, ee()->api_template_structure->file_extensions))
                    {
                        continue;
                    }

                    $ext_length = strlen($ext) + 1;
                    $template_name = substr($template, 0, -$ext_length);
                    $template_type = array_search('.'.$ext, ee()->api_template_structure->file_extensions);

                    if (in_array($template_name, $existing[$group]))
                    {
                        continue;
                    }

                    if ( ! ee()->legacy_api->is_url_safe($template_name))
                    {
                        continue;
                    }

                    if (strlen($template_name) > 50)
                    {
                        continue;
                    }

                    $data = array(
                        'group_id'				=> $group_id,
                        'template_name'			=> $template_name,
                        'template_type'			=> $template_type,
                        'template_data'			=> file_get_contents($basepath.'/'.$group.'/'.$template),
                        'edit_date'				=> ee()->localize->now,
                        'last_author_id'		=> ee()->session->userdata['member_id'],
                        'site_id'				=> ee()->config->item('site_id')
                    );

                    // do it!
                    $template_model = ee('Model')->make('Template', $data)->save();
                    $this->saveNewTemplateRevision($template_model);

                    // add to existing array so we don't try to create this template again
                    $existing[$group][] = $template_name;
                }

                // An index template is required- so we create it if necessary
                if ( ! in_array('index', $existing[$group]))
                {
                    $data = array(
                        'group_id'				=> $group_id,
                        'template_name'			=> 'index',
                        'template_data'			=> '',
                        'edit_date'				=> ee()->localize->now,
                        'save_template_file'	=> 'y',
                        'last_author_id'		=> ee()->session->userdata['member_id'],
                        'site_id'				=> ee()->config->item('site_id')
                    );

                    $template_model = ee('Model')->make('Template', $data)->save();
                    $this->saveNewTemplateRevision($template_model);
                }

                unset($existing[$group]);
            }
        }
    }

    /**
     * @see \EllisLab\ExpressionEngine\Controller\Design\AbstractDesign::saveNewTemplateRevision
     *
     * @param	Template	$template	Saved template model object
     */
    protected function saveNewTemplateRevision($template)
    {
        if ( ! bool_config_item('save_tmpl_revisions'))
        {
            return;
        }

        // Create the new version
        $version = ee('Model')->make('RevisionTracker');
        $version->Template = $template;
        $version->item_table = 'exp_templates';
        $version->item_field = 'template_data';
        $version->item_data = $template->template_data;
        $version->item_date = ee()->localize->now;
        $version->Author = $template->LastAuthor;
        $version->save();

        // Now, rotate template revisions based on 'max_tmpl_revisions' config item
        $versions = ee('Model')->get('RevisionTracker')
            ->filter('item_id', $template->getId())
            ->filter('item_field', 'template_data')
            ->order('item_date', 'desc')
            ->limit(ee()->config->item('max_tmpl_revisions'))
            ->all();

        // Reassign versions and delete the leftovers
        $template->Versions = $versions;
        $template->save();
    }
}
