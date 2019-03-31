<?php

declare(strict_types=1);

namespace buzzingpixel\executive\services\templatesync;

use EllisLab\ExpressionEngine\Model\Template\GlobalVariable as VariableModel;
use EllisLab\ExpressionEngine\Service\Model\Collection as ModelCollection;
use EllisLab\ExpressionEngine\Service\Model\Facade as ModelFacade;
use EllisLab\ExpressionEngine\Service\Model\Query\Builder as ModelQueryBuilder;
use Symfony\Component\Filesystem\Filesystem;
use const DIRECTORY_SEPARATOR;

class DeleteVariablesNotOnDiskService
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
        $variableQuery = $this->modelFacade->get('GlobalVariable');

        /** @var ModelCollection $variables */
        $variables = $variableQuery->all();

        $variables->each(function (VariableModel $model) : void {
            $sep = DIRECTORY_SEPARATOR;

            $folder = '_global_variables';

            if (isset($this->siteShortNames[$model->getProperty('site_id')])) {
                $folder  = $this->siteShortNames[$model->getProperty('site_id')];
                $folder .= $sep . '_variables';
            }

            $fullPath = $this->templatesPath . $sep . $folder . $sep .
                $model->getProperty('variable_name') . '.html';

            if ($this->filesystem->exists($fullPath)) {
                return;
            }

            $model->delete();
        });
    }
}
