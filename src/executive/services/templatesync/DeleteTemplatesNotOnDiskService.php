<?php

declare(strict_types=1);

namespace buzzingpixel\executive\services\templatesync;

use EllisLab\ExpressionEngine\Model\Template\Template as TemplateModel;
use EllisLab\ExpressionEngine\Service\Model\Collection as ModelCollection;
use EllisLab\ExpressionEngine\Service\Model\Facade as ModelFacade;
use EllisLab\ExpressionEngine\Service\Model\Query\Builder as ModelQueryBuilder;
use Symfony\Component\Filesystem\Filesystem;
use const DIRECTORY_SEPARATOR;

class DeleteTemplatesNotOnDiskService
{
    /** @var string $templatesPath */
    private $templatesPath;
    /** @var array $siteShortNames */
    private $siteShortNames;
    /** @var ModelFacade $modelFacade */
    private $modelFacade;
    /** @var Filesystem $filesystem */
    private $filesystem;

    public function __construct(
        string $templatesPath,
        array $siteShortNames,
        ModelFacade $modelFacade,
        Filesystem $filesystem
    ) {
        $this->templatesPath  = $templatesPath;
        $this->siteShortNames = $siteShortNames;
        $this->modelFacade    = $modelFacade;
        $this->filesystem     = $filesystem;
    }

    public function run() : void
    {
        /** @var ModelQueryBuilder $templateQuery */
        $templateQuery = $this->modelFacade->get('Template');

        $templateQuery->with('TemplateGroup');

        /** @var ModelCollection $templates */
        $templates = $templateQuery->all();

        $templates->each(function (TemplateModel $model) : void {
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
