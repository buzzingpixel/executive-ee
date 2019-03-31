<?php

declare(strict_types=1);

namespace buzzingpixel\executive\services;

use buzzingpixel\executive\factories\SplFileInfoFactory;
use Symfony\Component\Filesystem\Filesystem;
use Throwable;
use function str_replace;

class TemplateMakerService
{
    public const DESTINATION_EXISTS_ERROR                  = 'fileExistsAtDestination';
    public const SOURCE_TEMPLATE_MISSING_ERROR             = 'sourceTemplateMissing';
    public const CANNOT_CREATE_DESTINATION_DIRECTORY_ERROR = 'cannotCreateTemplateDirectory';
    public const TEMPLATE_CREATED_SUCCESSFULLY             = 'success';

    /** @var SplFileInfoFactory $splFileInfoFactory */
    private $splFileInfoFactory;
    /** @var Filesystem $filesystem */
    private $filesystem;

    /**
     * CliInstallService constructor
     */
    public function __construct(
        SplFileInfoFactory $splFileInfoFactory,
        Filesystem $filesystem
    ) {
        $this->splFileInfoFactory = $splFileInfoFactory;
        $this->filesystem         = $filesystem;
    }

    /**
     * Runs the installation
     *
     * @return string Returns on of the success or failure constants
     */
    public function makeTemplate(
        string $oldClassName,
        string $className,
        string $nameSpace,
        string $location,
        string $destination
    ) : string {
        if ($this->filesystem->exists($destination)) {
            return self::DESTINATION_EXISTS_ERROR;
        }

        $templateFile = $this->splFileInfoFactory->make($location);

        try {
            $this->filesystem->mkdir($templateFile->getPath());
        } catch (Throwable $e) {
            return self::CANNOT_CREATE_DESTINATION_DIRECTORY_ERROR;
        }

        try {
            $template = $templateFile->getContents();
        } catch (Throwable $e) {
            return self::SOURCE_TEMPLATE_MISSING_ERROR;
        }

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
