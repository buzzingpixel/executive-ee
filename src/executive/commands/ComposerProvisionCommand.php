<?php

declare(strict_types=1);

namespace buzzingpixel\executive\commands;

use buzzingpixel\executive\factories\FinderFactory;
use Composer\Package\CompletePackage;
use Composer\Repository\InstalledFilesystemRepository;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;
use ZipArchive;
use const DIRECTORY_SEPARATOR;
use const PHP_OS;
use function array_map;
use function dirname;
use function exec;
use function explode;
use function fopen;
use function implode;
use function ltrim;
use function mb_strpos;
use function realpath;
use function rtrim;
use function sleep;

class ComposerProvisionCommand
{
    /** @var OutputInterface $consoleOutput */
    private $consoleOutput;
    /** @var InstalledFilesystemRepository $installedFilesystemRepository */
    private $installedFilesystemRepository;
    /** @var string $vendorPath */
    private $vendorPath;
    /** @var Filesystem $fileSystem */
    private $fileSystem;
    /** @var FinderFactory $finderFactory */
    private $finderFactory;
    /** @var array $extraEEAddons */
    private $extraEEAddons;
    /** @var array $installFromDownload */
    private $installFromDownload;
    /** @var string $appBasePath */
    private $appBasePath;
    /** @var string $systemUserAddons */
    private $systemUserAddons;
    /** @var string $themesUser */
    private $themesUser;
    /** @var string $systemPath */
    private $systemPath;
    /** @var string $themesPath */
    private $themesPath;
    /** @var array $systemUserAddonsGitIgnore */
    private $systemUserAddonsGitIgnore = [];
    /** @var array $systemUserAddonsGitIgnore */
    private $themesUserGitIgnore = [];
    /** @var bool $eeProvisioned */
    private $eeProvisioned = false;
    /** @var string $provisionEEVersion */
    private $provisionEEVersionTag;

    /**
     * ComposerProvisionCommand constructor
     *
     * @param array $extraEEAddons
     * @param array $installFromDownload
     */
    public function __construct(
        OutputInterface $consoleOutput,
        InstalledFilesystemRepository $installedFilesystemRepository,
        string $vendorPath,
        Filesystem $fileSystem,
        FinderFactory $finderFactory,
        string $publicDir,
        array $extraEEAddons,
        array $installFromDownload,
        string $provisionEEVersionTag
    ) {
        $this->consoleOutput                 = $consoleOutput;
        $this->installedFilesystemRepository = $installedFilesystemRepository;
        $this->vendorPath                    = ltrim(ltrim($vendorPath, '/'), DIRECTORY_SEPARATOR);
        $this->fileSystem                    = $fileSystem;
        $this->finderFactory                 = $finderFactory;
        $this->extraEEAddons                 = $extraEEAddons;
        $this->installFromDownload           = $installFromDownload;
        $this->provisionEEVersionTag         = $provisionEEVersionTag;

        $publicDir = rtrim(rtrim($publicDir, '/'), DIRECTORY_SEPARATOR);

        $this->appBasePath      = dirname($this->vendorPath);
        $this->systemUserAddons = $this->appBasePath . '/system/user/addons';

        $this->themesUser = $this->appBasePath;

        if ($publicDir) {
            $this->themesUser .= '/' . $publicDir;
        }

        $this->themesUser .= '/themes/user';

        $this->systemPath = $this->appBasePath . '/system';

        $this->themesPath = $this->appBasePath;

        if ($publicDir) {
            $this->themesPath .= '/' . $publicDir;
        }

        $this->themesPath .= '/themes';
    }

    /**
     * Runs composer provisioning
     */
    public function run() : void
    {
        $this->cleanUpPreviousProvisioning();

        array_map(
            [$this, 'processPackage'],
            $this->installedFilesystemRepository->getCanonicalPackages()
        );

        foreach ($this->extraEEAddons as $extraEEAddon) {
            $this->provisionExtraEEAddon($extraEEAddon);
        }

        foreach ($this->installFromDownload as $downloadAddon) {
            $this->provisionAddonFromDownload($downloadAddon);

            // Work around a stupid throttling issue with GitHub
            // and probably others. Apparently waiting one second between
            // requests is a common throttling threshold
            sleep(1);
        }

        $this->consoleOutput->writeln(
            '<fg=green>Composer Provisioning Complete</>'
        );

        if ($this->provisionEEVersionTag) {
            $this->processEEOfficial();
        }

        $this->processGitIgnores();
    }

    private function cleanUpPreviousProvisioning() : void
    {
        $toRemove         = [];
        $systemUserAddons = DIRECTORY_SEPARATOR . $this->systemUserAddons;
        $themesUser       = DIRECTORY_SEPARATOR . $this->themesUser;

        if ($this->fileSystem->exists($systemUserAddons)) {
            $finder = $this->finderFactory->make()
                ->directories()
                ->in($systemUserAddons)
                ->filter(static function ($file) {
                    /** @var SplFileInfo $file */
                    return $file->isLink();
                });

            foreach ($finder as $file) {
                /** @var SplFileInfo $file */
                $toRemove[] = $file->getPathname();
            }
        }

        if ($this->fileSystem->exists($themesUser)) {
            $finder = $this->finderFactory->make()
                ->directories()
                ->in(DIRECTORY_SEPARATOR . $this->themesUser)
                ->filter(static function ($file) {
                    /** @var SplFileInfo $file */
                    return $file->isLink();
                });

            foreach ($finder as $file) {
                /** @var SplFileInfo $file */
                $toRemove[] = $file->getPathname();
            }
        }

        if (! $toRemove) {
            return;
        }

        $this->fileSystem->remove($toRemove);
    }

    private function processPackage(CompletePackage $package) : void
    {
        switch ($package->getType()) {
            case 'ee-add-on':
                $this->provisionEEAddOn($package);
                break;
            case 'ee':
                $this->processEE($package);
                break;
        }
    }

    private function processEEOfficial() : void
    {
        $isWin = mb_strpos(PHP_OS, 'WIN') === 0;

        $this->consoleOutput->writeln(
            '<comment>Provisioning ExpressionEngine from official repo...</comment>'
        );

        $path        = $this->vendorPath . '/expressionengine-provision';
        $installPath = $path . '/ee';
        $zipFile     = $path . '/ee-' . $this->provisionEEVersionTag . '.zip';

        $path        = $this->processPathForPlatform($path);
        $installPath = $this->processPathForPlatform($installPath);
        $zipFile     = $this->processPathForPlatform($zipFile);

        $this->fileSystem->mkdir($path);

        $installPathExists = $this->fileSystem->exists($installPath);

        if ($isWin && $installPathExists) {
            $this->fileSystem->remove($installPath);
        }

        if (! $isWin && $installPathExists) {
            exec('rm -rf ' . $installPath);
        }

        if (! $this->fileSystem->exists($zipFile)) {
            $url  = 'https://github.com/ExpressionEngine/ExpressionEngine/archive/';
            $url .= $this->provisionEEVersionTag . '.zip';

            $this->fileSystem->appendToFile($zipFile, fopen($url, 'rb'));
        }

        $zipHandler = new ZipArchive();
        $zipHandler->open($zipFile);
        $zipHandler->extractTo($installPath);
        $zipHandler->close();

        $unzippedPath = $this->processPathForPlatform(
            $path . '/ee/ExpressionEngine-' . $this->provisionEEVersionTag
        );

        $remoteBootCommonUrl = 'https://raw.githubusercontent.com/buzzingpixel/executive-ee-provision-support/master/system/ee/EllisLab/ExpressionEngine/Boot/boot.common.php';

        $bootCommonPath = $unzippedPath . '/system/ee/EllisLab/ExpressionEngine/Boot/boot.common.php';
        $bootCommonPath = $this->processPathForPlatform($bootCommonPath);

        if ($isWin) {
            $this->fileSystem->remove($bootCommonPath);
        }

        if (! $isWin) {
            exec('rm ' . $bootCommonPath);
        }

        $bootCommonStorage = $path . '/boot.common.php';
        $bootCommonStorage = $this->processPathForPlatform($bootCommonStorage);

        if (! $this->fileSystem->exists($bootCommonStorage)) {
            $this->fileSystem->appendToFile($bootCommonStorage, fopen($remoteBootCommonUrl, 'rb'));
        }

        $this->fileSystem->copy($bootCommonStorage, $bootCommonPath);

        $fromSysPath = $this->processPathForPlatform(
            $unzippedPath . '/system/ee'
        );

        $toSysPath = $this->processPathForPlatform(
            $this->systemPath . '/ee'
        );

        $this->consoleOutput->writeln(
            '<comment>Symlinking ' .
            $fromSysPath .
            ' to ' .
            $toSysPath .
            '</comment>'
        );

        if ($this->fileSystem->exists($toSysPath)) {
            $this->fileSystem->remove($toSysPath);
        }

        $this->fileSystem->symlink(realpath($fromSysPath), $toSysPath, true);

        $fromThemePath = $this->processPathForPlatform(
            $unzippedPath . '/themes/ee'
        );

        $toThemePath = $this->processPathForPlatform(
            $this->themesPath . '/ee'
        );

        $this->consoleOutput->writeln(
            '<comment>Symlinking ' .
            $fromThemePath .
            ' to ' .
            $toThemePath .
            '</comment>'
        );

        if ($this->fileSystem->exists($toThemePath)) {
            $this->fileSystem->remove($toThemePath);
        }

        $this->fileSystem->symlink(realpath($fromThemePath), $toThemePath, true);

        $this->eeProvisioned = true;
    }

    /**
     * Process ExpressionEngine dependency
     */
    private function processEE(CompletePackage $package) : void
    {
        $this->consoleOutput->writeln(
            '<comment>Found ExpressionEngine package ' .
            $package->getName() .
            '</comment>'
        );

        $hasBlockingErrors = false;

        $extra = $package->getExtra();

        if (! isset($extra['systemEEDir'])) {
            $hasBlockingErrors = true;
            $this->consoleOutput->writeln(
                '<fg=red>' .
                $package->getName() .
                ' does not have "systemEEDir" set in its composer.json extra object' .
                '</>'
            );
        }

        if (! isset($extra['themesEEDir'])) {
            $hasBlockingErrors = true;
            $this->consoleOutput->writeln(
                '<fg=red>' .
                $package->getName() .
                ' does not have "themesEEDir" set in its composer.json extra object' .
                '</>'
            );
        }

        if ($hasBlockingErrors) {
            return;
        }

        $sysPath  = $this->vendorPath;
        $sysPath .= '/' . $package->getName() . '/' . $extra['systemEEDir'];
        $sysPath  = $this->processPathForPlatform($sysPath);

        $themePath  = $this->vendorPath;
        $themePath .= '/' . $package->getName() . '/' . $extra['themesEEDir'];
        $themePath  = $this->processPathForPlatform($themePath);

        if (! $this->fileSystem->exists($sysPath)) {
            $hasBlockingErrors = true;
            $this->consoleOutput->writeln(
                '<fg=red>' .
                $package->getName() .
                ' specified system directory does not exist' .
                '</>'
            );
        }

        if (! $this->fileSystem->exists($themePath)) {
            $hasBlockingErrors = true;
            $this->consoleOutput->writeln(
                '<fg=red>' .
                $package->getName() .
                ' specified themes directory does not exist' .
                '</>'
            );
        }

        if ($hasBlockingErrors) {
            return;
        }

        $linkToPath = $this->processPathForPlatform($this->systemPath . '/ee');

        $this->consoleOutput->writeln(
            '<comment>Symlinking ' .
            $sysPath .
            ' to ' .
            $linkToPath .
            '</comment>'
        );

        if ($this->fileSystem->exists($linkToPath)) {
            $this->fileSystem->remove($linkToPath);
        }

        $this->fileSystem->symlink(realpath($sysPath), $linkToPath, true);

        $linkToPath = $this->processPathForPlatform($this->themesPath . '/ee');

        $this->consoleOutput->writeln(
            '<comment>Symlinking ' .
            $themePath .
            ' to ' .
            $linkToPath .
            '</comment>'
        );

        if ($this->fileSystem->exists($linkToPath)) {
            $this->fileSystem->remove($linkToPath);
        }

        $this->fileSystem->symlink(realpath($themePath), $linkToPath, true);

        $this->eeProvisioned = true;
    }

    /**
     * Processes an EE Addon Package
     */
    private function provisionEEAddOn(CompletePackage $package) : void
    {
        $this->consoleOutput->writeln(
            '<comment>Found add-on package ' .
            $package->getName() .
            '</comment>'
        );

        $hasBlockingErrors = false;

        $extra = $package->getExtra();

        if (! isset($extra['handle'])) {
            $hasBlockingErrors = true;
            $this->consoleOutput->writeln(
                '<fg=red>' .
                $package->getName() .
                ' does not have a "handle" set in its composer.json extra object' .
                '</>'
            );
        }

        if (! isset($extra['systemPath'])) {
            $hasBlockingErrors = true;
            $this->consoleOutput->writeln(
                '<fg=red>' .
                $package->getName() .
                ' does not have a "systemPath" set in its composer.json extra object' .
                '</>'
            );
        }

        if ($hasBlockingErrors) {
            return;
        }

        $sysPath  = $this->vendorPath;
        $sysPath .= '/' . $package->getName() . '/' . $extra['systemPath'];
        $sysPath  = $this->processPathForPlatform($sysPath);

        if (! $this->fileSystem->exists($sysPath)) {
            $hasBlockingErrors = true;
            $this->consoleOutput->writeln(
                '<fg=red>' .
                $package->getName() .
                ' specified system directory does not exist' .
                '</>'
            );
        }

        if ($hasBlockingErrors) {
            return;
        }

        $linkToPath = $this->processPathForPlatform(
            $this->systemUserAddons . '/' . $extra['handle']
        );

        $this->consoleOutput->writeln(
            '<comment>Symlinking ' .
            $sysPath .
            ' to ' .
            $linkToPath .
            '</comment>'
        );

        if ($this->fileSystem->exists($linkToPath)) {
            $this->fileSystem->remove($linkToPath);
        }

        $this->fileSystem->symlink(realpath($sysPath), $linkToPath);

        $this->systemUserAddonsGitIgnore[] = $extra['handle'];

        if (! isset($extra['themePath'])) {
            return;
        }

        $themePath  = $this->vendorPath;
        $themePath .= '/' . $package->getName() . '/' . $extra['themePath'];
        $themePath  = $this->processPathForPlatform($themePath);

        if (! $this->fileSystem->exists($themePath)) {
            $hasBlockingErrors = true;
            $this->consoleOutput->writeln(
                '<fg=red>' .
                $package->getName() .
                ' specified theme directory does not exist' .
                '</>'
            );
        }

        if ($hasBlockingErrors) {
            return;
        }

        $linkToPath .= $this->processPathForPlatform(
            $this->themesUser . '/' . $extra['handle']
        );

        $this->consoleOutput->writeln(
            '<comment>Symlinking ' .
            $themePath .
            ' to ' .
            $linkToPath .
            '</comment>'
        );

        if ($this->fileSystem->exists($linkToPath)) {
            $this->fileSystem->remove($linkToPath);
        }

        $this->fileSystem->symlink(realpath($themePath), $linkToPath, true);

        $this->themesUserGitIgnore[] = $extra['handle'];
    }

    /**
     * Provisions an EE add-on from the composer Extra block
     *
     * @param array $addOn
     */
    private function provisionExtraEEAddon(array $addOn) : void
    {
        $hasBlockingErrors = false;

        if (! isset($addOn['handle'])) {
            $hasBlockingErrors = true;
            $this->consoleOutput->writeln(
                '<fg=red>EE Add-On in composer.json extra block does not have' .
                ' not have a "handle" set</>'
            );
        }

        if (! isset($addOn['systemPath'])) {
            $hasBlockingErrors = true;
            $this->consoleOutput->writeln(
                '<fg=red>EE Add-On in composer.json extra block does not have' .
                ' not have a "systemPath" set</>'
            );
        }

        if ($hasBlockingErrors) {
            return;
        }

        $this->consoleOutput->writeln(
            '<comment>Provisioning ' .
            $addOn['handle'] .
            ' from composer.json extra</comment>'
        );

        $sysPath = $this->processPathForPlatform(
            $this->appBasePath . '/' . $addOn['systemPath']
        );

        if (! $this->fileSystem->exists($sysPath)) {
            $hasBlockingErrors = true;
            $this->consoleOutput->writeln(
                '<fg=red>' .
                $addOn['handle'] .
                ' specified system directory does not exist' .
                '</>'
            );
        }

        if ($hasBlockingErrors) {
            return;
        }

        $linkToPath = $this->processPathForPlatform(
            $this->systemUserAddons . '/' . $addOn['handle']
        );

        $this->consoleOutput->writeln(
            '<comment>Symlinking ' .
            $sysPath .
            ' to ' .
            $linkToPath .
            '</comment>'
        );

        if ($this->fileSystem->exists($linkToPath)) {
            $this->fileSystem->remove($linkToPath);
        }

        $this->fileSystem->symlink(realpath($sysPath), $linkToPath, true);

        $this->systemUserAddonsGitIgnore[] = $addOn['handle'];

        if (! isset($addOn['themePath'])) {
            return;
        }

        $themePath = $this->processPathForPlatform(
            $this->appBasePath . '/' . $addOn['themePath']
        );

        if (! $this->fileSystem->exists($themePath)) {
            $hasBlockingErrors = true;
            $this->consoleOutput->writeln(
                '<fg=red>' .
                $addOn['handle'] .
                ' specified theme directory does not exist' .
                '</>'
            );
        }

        if ($hasBlockingErrors) {
            return;
        }

        $linkToPath = $this->processPathForPlatform(
            $this->themesUser . '/' . $addOn['handle']
        );

        $this->consoleOutput->writeln(
            '<comment>Symlinking ' .
            $themePath .
            ' to ' .
            $linkToPath .
            '</comment>'
        );

        if ($this->fileSystem->exists($linkToPath)) {
            $this->fileSystem->remove($linkToPath);
        }

        $this->fileSystem->symlink(realpath($themePath), $linkToPath, true);

        $this->themesUserGitIgnore[] = $addOn['handle'];
    }

    /**
     * Provisions an EE add-on from download from the composer Extra block
     *
     * @param array $addOn
     */
    private function provisionAddonFromDownload(array $addOn) : void
    {
        $hasBlockingErrors = false;

        if (! isset($addOn['url'])) {
            $hasBlockingErrors = true;
            $this->consoleOutput->writeln(
                '<fg=red>EE Add-On in composer.json extra block does not have' .
                ' not have a "url" set</>'
            );
        }

        if (! isset($addOn['handle'])) {
            $hasBlockingErrors = true;
            $this->consoleOutput->writeln(
                '<fg=red>EE Add-On in composer.json extra block does not have' .
                ' not have a "handle" set</>'
            );
        }

        if (! isset($addOn['systemPath'])) {
            $hasBlockingErrors = true;
            $this->consoleOutput->writeln(
                '<fg=red>EE Add-On in composer.json extra block does not have' .
                ' a "systemPath" set</>'
            );
        }

        if ($hasBlockingErrors) {
            return;
        }

        $this->consoleOutput->writeln(
            '<comment>Downloading ' .
            $addOn['handle'] .
            ' from ' .
            $addOn['url'] .
            '</comment>'
        );

        $installPath     = $this->vendorPath . '/install-from-download';
        $fullInstallPath = $installPath . '/' . $addOn['handle'];
        $zipFile         = $fullInstallPath . '.zip';

        $installPath     = $this->processPathForPlatform($installPath);
        $fullInstallPath = $this->processPathForPlatform($fullInstallPath);
        $zipFile         = $this->processPathForPlatform($zipFile);

        $this->fileSystem->mkdir($installPath);

        if ($this->fileSystem->exists($zipFile)) {
            $this->fileSystem->remove($zipFile);
        }

        if ($this->fileSystem->exists($fullInstallPath)) {
            $this->fileSystem->remove($fullInstallPath);
        }

        $this->fileSystem->appendToFile(
            $zipFile,
            fopen($addOn['url'], 'rb')
        );

        $zipHandler = new ZipArchive();
        $zipHandler->open($zipFile);
        $zipHandler->extractTo($fullInstallPath);
        $zipHandler->close();

        $this->consoleOutput->writeln(
            '<comment>Provisioning ' .
            $addOn['handle'] .
            ' from composer.json extra</comment>'
        );

        $sysPath = $this->processPathForPlatform(
            $fullInstallPath . '/' . $addOn['systemPath']
        );

        if (! $this->fileSystem->exists($sysPath)) {
            $hasBlockingErrors = true;
            $this->consoleOutput->writeln(
                '<fg=red>' .
                $addOn['handle'] .
                ' specified system directory does not exist' .
                '</>'
            );
        }

        if ($hasBlockingErrors) {
            return;
        }

        $linkToPath = $this->processPathForPlatform(
            $this->systemUserAddons . '/' . $addOn['handle']
        );

        $this->consoleOutput->writeln(
            '<comment>Symlinking ' .
            $sysPath .
            ' to ' .
            $linkToPath .
            '</comment>'
        );

        if ($this->fileSystem->exists($linkToPath)) {
            $this->fileSystem->remove($linkToPath);
        }

        $this->fileSystem->symlink(realpath($sysPath), $linkToPath, true);

        $this->systemUserAddonsGitIgnore[] = $addOn['handle'];

        if (! isset($addOn['themePath'])) {
            return;
        }

        $themePath = $this->processPathForPlatform(
            $fullInstallPath . '/' . $addOn['themePath']
        );

        if (! $this->fileSystem->exists($themePath)) {
            $hasBlockingErrors = true;
            $this->consoleOutput->writeln(
                '<fg=red>' .
                $addOn['handle'] .
                ' specified theme directory does not exist' .
                '</>'
            );
        }

        if ($hasBlockingErrors) {
            return;
        }

        $linkToPath = $this->processPathForPlatform(
            $this->themesUser . '/' . $addOn['handle']
        );

        $this->consoleOutput->writeln(
            '<comment>Symlinking ' .
            $themePath .
            ' to ' .
            $linkToPath .
            '</comment>'
        );

        if ($this->fileSystem->exists($linkToPath)) {
            $this->fileSystem->remove($linkToPath);
        }

        $this->fileSystem->symlink(realpath($themePath), $linkToPath, true);

        $this->themesUserGitIgnore[] = $addOn['handle'];
    }

    /**
     * Processes setting of GitIgnore
     */
    private function processGitIgnores() : void
    {
        $ignorePath = $this->processPathForPlatform(
            $this->systemUserAddons . '/' . '.gitignore'
        );

        if ($this->fileSystem->exists($ignorePath)) {
            $this->fileSystem->remove($ignorePath);
        }

        $this->fileSystem->appendToFile(
            $ignorePath,
            implode("\n", $this->systemUserAddonsGitIgnore) . "\n"
        );

        ////////////////////////////////////////////////////////////////////////

        $ignorePath = $this->processPathForPlatform(
            $this->themesUser . '/' . '.gitignore'
        );

        if ($this->fileSystem->exists($ignorePath)) {
            $this->fileSystem->remove($ignorePath);
        }

        $this->fileSystem->appendToFile(
            $ignorePath,
            implode("\n", $this->themesUserGitIgnore) . "\n"
        );

        ////////////////////////////////////////////////////////////////////////

        $ignorePath = $this->processPathForPlatform(
            $this->systemPath . '/' . '.gitignore'
        );

        if ($this->fileSystem->exists($ignorePath)) {
            $this->fileSystem->remove($ignorePath);
        }

        if ($this->eeProvisioned) {
            $this->fileSystem->appendToFile(
                $ignorePath,
                "ee\n"
            );
        }

        ////////////////////////////////////////////////////////////////////////

        $ignorePath = $this->processPathForPlatform(
            $this->themesPath . '/' . '.gitignore'
        );

        if ($this->fileSystem->exists($ignorePath)) {
            $this->fileSystem->remove($ignorePath);
        }

        if (! $this->eeProvisioned) {
            return;
        }

        $this->fileSystem->appendToFile(
            $ignorePath,
            "ee\n"
        );
    }

    /**
     * Processes a path for the platform PHP is running on
     */
    private function processPathForPlatform(string $path) : string
    {
        $newPath = '';

        foreach (explode('/', $path) as $namePart) {
            if (! $namePart) {
                continue;
            }

            $newPath .= DIRECTORY_SEPARATOR . $namePart;
        }

        return $newPath;
    }
}
