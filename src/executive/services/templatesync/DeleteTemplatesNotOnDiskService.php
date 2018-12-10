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
use EllisLab\ExpressionEngine\Model\Template\Template as TemplateModel;
use EllisLab\ExpressionEngine\Service\Model\Collection as ModelCollection;
use EllisLab\ExpressionEngine\Service\Model\Query\Builder as ModelQueryBuilder;

class DeleteTemplatesNotOnDiskService
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
        $templateQuery = $this->modelFacade->get('Template');

        $templateQuery->with('TemplateGroup');

        /** @var ModelCollection $templates */
        $templates = $templateQuery->all();

        $templates->each(function (TemplateModel $model) {
            $sep = DIRECTORY_SEPARATOR;

            $siteDir = $this->siteShortNames[$model->getProperty('site_id')];

            $path = $this->templatesPath . $sep . $siteDir . $sep .
                $model->TemplateGroup->getProperty('group_name') . '.group' .
                $sep . $model->getProperty('template_name') .
                $model->getFileExtension();

            if ($this->filesystem->exists($path)) {
                return;
            }

            $model->delete();
        });
    }
}
