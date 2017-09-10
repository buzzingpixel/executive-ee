<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace BuzzingPixel\Executive;

use BuzzingPixel\Executive\Abstracts\BaseTag;

/**
 * Class Executive
 */
class Executive
{
    /**
     * User tag
     * @return string
     */
    public function user()
    {
        /** @var \EE_Template $templateService */
        $templateService = ee()->TMPL;

        if (! isset($templateService->tagparts[2])) {
            return '';
        }

        /** @var \EE_Config $configService */
        $configService = ee()->config;

        /** @var array $tagConf */
        $tagConf = $configService->item($templateService->tagparts[2], 'tags');

        $tagClass = new $tagConf['class'](array(
            'templateService' => $templateService,
        ));

        if (! $tagClass instanceof BaseTag) {
            return '';
        }

        return $tagClass->{$tagConf['method']}();
    }
}
