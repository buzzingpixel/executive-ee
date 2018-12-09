<?php
declare(strict_types=1);

namespace buzzingpixel\executive\services;

use EE_Config;
use Exception;
use EE_Template;
use EllisLab\ExpressionEngine\Legacy\Facade as LegacyApp;

class EETemplateService extends EE_Template
{
    /** @var EE_Config $config  */
    private $config;

    /** @var LegacyApp $legacyApp  */
    private $legacyApp;

    public function __construct()
    {
        parent::__construct();

        $this->config = ee()->config;
        $this->legacyApp = ee();
    }

    public function renderTemplate(
        string $group,
        string $template,
        array $variables = []
    ): string {
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
            $this->final_template = $this->restore_xml_declaration(
                $this->final_template
            );
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
}
