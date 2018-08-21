<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace buzzingpixel\executive\services;

use Symfony\Component\Filesystem\Filesystem;
use buzzingpixel\executive\factories\SplFileInfoFactory;

/**
 * Class TemplateMakerService
 */
class TemplateMakerService
{
    public const DESTINATION_EXISTS_ERROR = 'destinationExists';
    public const CANNOT_CREATE_DESTINATION_DIRECTORY_ERROR = 'cannotCreateDirectory';
    public const TEMPLATE_CREATED_SUCCESSFULLY = 'success';

    /** @var SplFileInfoFactory $splFileInfoFactory */
    private $splFileInfoFactory;

    /** @var Filesystem $filesystem */
    private $filesystem;

    /**
     * CliInstallService constructor
     * @param SplFileInfoFactory $splFileInfoFactory
     * @param Filesystem $filesystem
     */
    public function __construct(
        SplFileInfoFactory $splFileInfoFactory,
        Filesystem $filesystem
    ) {
        $this->splFileInfoFactory = $splFileInfoFactory;
        $this->filesystem = $filesystem;
    }

    /**
     * Runs the installation
     * @param string $oldClassName
     * @param string $className
     * @param string $nameSpace
     * @param string $location
     * @param string $destination
     * @return string Returns on of the success or failure constants
     */
    public function makeTemplate(
        string $oldClassName,
        string $className,
        string $nameSpace,
        string $location,
        string $destination
    ): string {
        if ($this->filesystem->exists($destination)) {
            return self::DESTINATION_EXISTS_ERROR;
        }

        $templateFile = $this->splFileInfoFactory->make($location);

        try {
            $this->filesystem->mkdir($templateFile->getPath());
        } catch (\Throwable $e) {
            return self::CANNOT_CREATE_DESTINATION_DIRECTORY_ERROR;
        }

        $template = $templateFile->getContents();

        $template = str_replace(
            [
                'sample\name\space',
                'Class ' . $oldClassName,
                'class ' . $oldClassName,
                $oldClassName . ' constructor',
            ],
            [
                $nameSpace,
                'Class ' . $className,
                'class ' . $className,
                $className . ' constructor',
            ],
            $template
        );

        $this->filesystem->appendToFile($destination, $template);

        return self::TEMPLATE_CREATED_SUCCESSFULLY;
    }
}
