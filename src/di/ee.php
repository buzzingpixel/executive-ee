<?php

declare(strict_types=1);

use EllisLab\ExpressionEngine\Service\Addon\Factory as EEAddOnFactory;
use EllisLab\ExpressionEngine\Service\Model\Facade as ModelFacade;
use Psr\Container\ContainerInterface;

return [
    EE_Config::class => static function () {
        return ee()->config;
    },
    EE_Functions::class => static function () {
        return ee()->functions;
    },
    EE_Lang::class => static function () {
        return ee()->lang;
    },
    EE_Loader::class => static function () {
        return ee()->load;
    },
    EE_Template::class => static function (ContainerInterface $di) {
        $loader = $di->get(EE_Loader::class);

        $loader->library('template', null, 'TMPL');

        return ee()->TMPL;
    },
    EE_Router::class => static function () {
        return ee()->router;
    },
    EE_Session::class => static function () {
        return ee()->session;
    },
    EEAddOnFactory::class => static function () {
        return ee('Addon');
    },
    EllisLab\ExpressionEngine\Library\Filesystem\Filesystem::class => static function () {
        return ee('Filesystem');
    },
    ModelFacade::class => static function () {
        return ee('Model');
    },
];
