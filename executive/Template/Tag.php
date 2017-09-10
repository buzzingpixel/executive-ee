<?php

namespace User\Tag;

use BuzzingPixel\Executive\Abstracts\BaseTag;

/**
 * Class Tag
 */
class Tag extends BaseTag
{
    /**
     * // TODO: Name and implement tag method
     * @return string
     */
    public function tagMethod()
    {
        /**
         * TODO: remove these notes
         * Note: User tags must extend the BaseTag as this template does.
         * Your tag methods must return a string either directly, or by using
         * the template methods
         * return $this->templateService->parse_variables(
         *     $this->templateService->tagdata,
         *     array(
         *         array(
         *             'my_var' => 'my_val',
         *             'another_var' => 'another_val',
         *         ),
         *         array(
         *             'my_var' => 'my_val_2',
         *             'another_var' => 'another_val_2',
         *         ),
         *     )
         * );
         *
         * Note after creating this class you must also add the tag in your
         * EE config.php file. It should look something like this:
         * $config['tags'] = array(
         *     'my_tag' => array(
         *         'class' => '\User\Tag\MyTag',
         *         'method' => 'testTag',
         *     )
         * );
         *
         * Tag template usage
         *
         * # Single tag
         * {exp:executive:user:my_tag param="val"}
         *
         * # Tag Pair
         * {exp:executive:user:my_tag param="val"}
         *     {my_var}
         * {/exp:executive:user:my_tag}
         */

        // TODO: return appropriate data
        return 'tagMethodValue';
    }
}
