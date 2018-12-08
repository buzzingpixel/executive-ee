<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2018 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace buzzingpixel\executive\services\templatesync;

use Symfony\Component\Filesystem\Filesystem;
use EllisLab\ExpressionEngine\Service\Model\Facade as ModelFacade;
use EllisLab\ExpressionEngine\Model\Template\Snippet as SnippetModel;
use EllisLab\ExpressionEngine\Service\Model\Collection as ModelCollection;
use EllisLab\ExpressionEngine\Service\Model\Query\Builder as ModelQueryBuilder;

class DeleteSnippetsNotOnDiskService
{
    private $templatesPath;
    private $siteShortNames;
    private $modelFacade;
    private $filesystem;

    public function __construct(
        string $templatesPath,
        array $siteShortNames,
        ModelFacade $modelFacade,
        Filesystem $filesystem
    ) {
        $this->templatesPath = $templatesPath;
        $this->siteShortNames = $siteShortNames;
        $this->modelFacade = $modelFacade;
        $this->filesystem = $filesystem;
    }

    public function run(): void
    {
        /** @var ModelQueryBuilder $templateQuery */
        $snippetsQuery = $this->modelFacade->get('Snippet');

        /** @var ModelCollection $snippets */
        $snippets = $snippetsQuery->all();

        $snippets->each(function (SnippetModel $model) {
            $sep = DIRECTORY_SEPARATOR;

            $folder = '_global_partials';

            if (isset($this->siteShortNames[$model->getProperty('site_id')])) {
                $folder = $this->siteShortNames[$model->getProperty('site_id')];
                $folder .= $sep . '_partials';
            }

            $fullPath = $this->templatesPath . $sep . $folder . $sep .
                $model->getProperty('snippet_name') . '.html';

            if ($this->filesystem->exists($fullPath)) {
                return;
            }

            $model->delete();
        });
    }
}
