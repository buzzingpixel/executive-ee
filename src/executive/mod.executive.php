<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2018 BuzzingPixel, LLC
 * @license Apache-2.0
 */

use buzzingpixel\executive\ExecutiveDi;
use Twig\Environment as TwigEnvironment;
use buzzingpixel\executive\models\RouteModel;
use buzzingpixel\executive\exceptions\InvalidTagException;

/**
 * Class Executive
 */
class Executive
{
    /** @var EE_Template $template */
    private $template;

    /** @var EE_Lang $lang */
    private $lang;

    /** @var EE_Config */
    private $config;

    public function __construct()
    {
        $this->template = ee()->TMPL;
        $this->lang = ee()->lang;
        $this->config = ee()->config;
    }

    /**
     * User tag
     * @return mixed
     * @throws InvalidTagException
     */
    public function user()
    {
        if (! isset($this->template->tagparts[2])) {
            $this->lang->loadfile('executive');
            throw new InvalidTagException($this->lang->line('tagNameNotSet'));
        }

        /** @var array $tagConf */
        $tagConf = $this->config->item($this->template->tagparts[2], 'tags');

        if (! \is_array($tagConf)) {
            $this->lang->loadfile('executive');
            throw new InvalidTagException(
                str_replace(
                    '{{tag}}',
                    $this->template->tagparts[2],
                    $this->lang->line('tagConfigNotFound')
                )
            );
        }

        if (! isset($tagConf['class'])) {
            $this->lang->loadfile('executive');
            throw new InvalidTagException(
                str_replace(
                    '{{tag}}',
                    $this->template->tagparts[2],
                    lang('tagClassNotSet')
                )
            );
        }

        if (! isset($tagConf['method'])) {
            $this->lang->loadfile('executive');
            throw new InvalidTagException(
                str_replace(
                    '{{tag}}',
                    $this->template->tagparts[2],
                    lang('tagMethodNotSet')
                )
            );
        }

        if (! class_exists($tagConf['class'])) {
            $this->lang->loadfile('executive');
            throw new InvalidTagException(
                str_replace(
                    '{{tag}}',
                    $this->template->tagparts[2],
                    lang('tagClassNotFound')
                )
            );
        }

        try {
            $class = ExecutiveDi::make($tagConf['class']);
        } catch (\Throwable $e) {
            $class = new $tagConf['class']();
        }

        if (! method_exists($class, $tagConf['method'])) {
            $this->lang->loadfile('executive');
            throw new InvalidTagException(
                str_replace(
                    '{{tag}}',
                    $this->template->tagparts[2],
                    $this->lang->line('tagMethodNotFound')
                )
            );
        }

        return $class->{$tagConf['method']}();
    }

    /**
     * Route pair tag
     * @return string
     */
    // @codingStandardsIgnoreStart
    public function route_pair(): string // @codingStandardsIgnoreEnd
    {
        if (! $name = $this->template->fetch_param('name')) {
            return '';
        }

        try {
            /** @var RouteModel $routeModel */
            $routeModel = ExecutiveDi::get(RouteModel::SINGLETON_DI_NAME);
        } catch (Throwable $e) {
            return '';
        }

        $pair = $routeModel->getPair($name);

        if (! is_array($pair)) {
            return '';
        }

        if ($namespace = $this->template->fetch_param('namespace')) {
            $newPair = [];

            foreach ($pair as $key => $varSet) {
                foreach ($varSet as $var => $val) {
                    $newPair[$key][$namespace . ':' . $var] = $val;
                }
            }

            $pair = $newPair;
        }

        return $this->template->parse_variables(
            $this->template->tagdata,
            $pair
        );
    }

    public function render_twig_template()
    {
        if (! $template = $this->template->fetch_param('template')) {
            return '';
        }

        $varString = $this->template->fetch_param(
            'vars',
            $this->template->tagdata
        );
        $varString = \is_string($varString) ? $varString : '';

        /** @var TwigEnvironment $twig */
        $twig = ExecutiveDi::get(TwigEnvironment::class);

        return $twig->render($template, json_decode($varString, true));
    }
}
