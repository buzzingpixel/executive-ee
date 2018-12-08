<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2018 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace buzzingpixel\executive\services\templatesync;

use Symfony\Component\Filesystem\Filesystem;
use buzzingpixel\executive\factories\FinderFactory;

class EnsureIndexTemplatesExistService
{
    private const TEMPLATE_EXTENSIONS = [
        'html',
        'feed',
        'css',
        'js',
        'xml',
    ];

    private $templatesPath;
    private $finderFactory;
    private $filesystem;

    public function __construct(
        string $templatesPath,
        FinderFactory $finderFactory,
        Filesystem $filesystem
    ) {
        $this->templatesPath = $templatesPath;
        $this->finderFactory = $finderFactory;
        $this->filesystem = $filesystem;
    }

    public function run(): void
    {
        if (! $this->filesystem->exists($this->templatesPath)) {
            return;
        }

        $finder = $this->finderFactory->make()
            ->directories()
            ->in($this->templatesPath)
            ->depth('< 1');

        foreach ($finder->getIterator() as $dir) {
            $this->processSitePath($dir->getPathname());
        }
    }

    private function processSitePath(string $sitePath): void
    {
        $finder = $this->finderFactory->make()
            ->directories()
            ->in($sitePath)
            ->depth('< 1')
            ->filter(function (\SplFileInfo $dir) {
                return $dir->getExtension() === 'group';
            });

        foreach ($finder->getIterator() as $dir) {
            $this->processGroupPath($dir->getPathname());
        }
    }

    private function processGroupPath(string $groupPath): void
    {
        $s = DIRECTORY_SEPARATOR;

        $hasIndex = false;

        foreach (self::TEMPLATE_EXTENSIONS as $ext) {
            $hasIndex = $this->filesystem->exists(
                $groupPath . $s . 'index.' . $ext
            );

            if ($hasIndex) {
                break;
            }
        }

        if ($hasIndex) {
            return;
        }

        $this->filesystem->dumpFile(
            $groupPath . $s . 'index.html',
            "{redirect=\"404\"}\n"
        );
    }
}
