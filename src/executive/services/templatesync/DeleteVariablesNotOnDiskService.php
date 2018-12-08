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
use EllisLab\ExpressionEngine\Service\Model\Collection as ModelCollection;
use EllisLab\ExpressionEngine\Model\Template\GlobalVariable as VariableModel;
use EllisLab\ExpressionEngine\Service\Model\Query\Builder as ModelQueryBuilder;

class DeleteVariablesNotOnDiskService
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
        $variableQuery = $this->modelFacade->get('GlobalVariable');

        /** @var ModelCollection $variables */
        $variables = $variableQuery->all();

        $variables->each(function (VariableModel $model) {
            $sep = DIRECTORY_SEPARATOR;

            $folder = '_global_variables';

            if (isset($this->siteShortNames[$model->getProperty('site_id')])) {
                $folder = $this->siteShortNames[$model->getProperty('site_id')];
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
