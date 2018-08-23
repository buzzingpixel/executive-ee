<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

use buzzingpixel\executive\ExecutiveDi;
use buzzingpixel\executive\exceptions\InvalidTagException;

/**
 * Class Executive
 */
class Executive
{
    /**
     * User tag
     * @return string
     * @throws InvalidTagException
     */
    public function user(): string
    {
        /** @var \EE_Template $templateService */
        $templateService = ee()->TMPL;

        if (! isset($templateService->tagparts[2])) {
            ee()->lang->loadfile('executive');
            throw new InvalidTagException(lang('tagNameNotSet'));
        }

        /** @var \EE_Config $configService */
        $configService = ee()->config;

        /** @var array $tagConf */
        $tagConf = $configService->item($templateService->tagparts[2], 'tags');

        if (! \is_array($tagConf)) {
            ee()->lang->loadfile('executive');
            throw new InvalidTagException(
                str_replace(
                    '{{tag}}',
                    $templateService->tagparts[2],
                    lang('tagConfigNotFound')
                )
            );
        }

        if (! isset($tagConf['class'])) {
            ee()->lang->loadfile('executive');
            throw new InvalidTagException(
                str_replace(
                    '{{tag}}',
                    $templateService->tagparts[2],
                    lang('tagClassNotSet')
                )
            );
        }

        if (! isset($tagConf['method'])) {
            ee()->lang->loadfile('executive');
            throw new InvalidTagException(
                str_replace(
                    '{{tag}}',
                    $templateService->tagparts[2],
                    lang('tagMethodNotSet')
                )
            );
        }

        if (! class_exists($tagConf['class'])) {
            ee()->lang->loadfile('executive');
            throw new InvalidTagException(
                str_replace(
                    '{{tag}}',
                    $templateService->tagparts[2],
                    lang('tagClassNotFound')
                )
            );
        }

        try {
            $class = ExecutiveDi::get($tagConf['class']);
        } catch (\Throwable $e) {
            $class = new $tagConf['class']();
        }

        if (! method_exists($class, $tagConf['method'])) {
            ee()->lang->loadfile('executive');
            throw new InvalidTagException(
                str_replace(
                    '{{tag}}',
                    $templateService->tagparts[2],
                    lang('tagMethodNotFound')
                )
            );
        }

        return $class->{$tagConf['method']}();
    }
}
