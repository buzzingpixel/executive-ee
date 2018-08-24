<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2018 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace buzzingpixel\executive\factories;

use Symfony\Component\Console\Question\Question;

/**
 * Class ConsoleQuestionFactory
 */
class ConsoleQuestionFactory
{
    /**
     * Gets a Question instance
     * @param string $question
     * @return Question
     */
    public function make(string $question): Question
    {
        return new Question($question);
    }
}
