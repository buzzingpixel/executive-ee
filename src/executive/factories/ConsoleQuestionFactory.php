<?php

declare(strict_types=1);

namespace buzzingpixel\executive\factories;

use Symfony\Component\Console\Question\Question;

class ConsoleQuestionFactory
{
    /**
     * Gets a Question instance
     */
    public function make(string $question) : Question
    {
        return new Question($question);
    }
}
