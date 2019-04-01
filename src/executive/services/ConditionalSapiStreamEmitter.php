<?php

declare(strict_types=1);

namespace buzzingpixel\executive\services;

use Psr\Http\Message\ResponseInterface;
use Zend\HttpHandlerRunner\Emitter\EmitterInterface;
use Zend\HttpHandlerRunner\Emitter\SapiStreamEmitter;

class ConditionalSapiStreamEmitter implements EmitterInterface
{
    /** @var SapiStreamEmitter */
    private $streamEmitter;
    /** @var int */
    private $contentSizeThresholdInBytes;

    public function __construct(SapiStreamEmitter $streamEmitter, int $contentSizeThresholdInBytes)
    {
        $this->streamEmitter               = $streamEmitter;
        $this->contentSizeThresholdInBytes = $contentSizeThresholdInBytes;
    }

    public function emit(ResponseInterface $response) : bool
    {
        $contentSize = $response->getBody()->getSize();

        if ($contentSize < $this->contentSizeThresholdInBytes
            && $response->getHeaderLine('content-range') === ''
        ) {
            return false;
        }

        return $this->streamEmitter->emit($response);
    }
}
