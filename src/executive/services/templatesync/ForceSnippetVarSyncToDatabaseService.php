<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2018 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace buzzingpixel\executive\services\templatesync;

use EllisLab\ExpressionEngine\Service\Model\Facade as ModelFacade;
use EllisLab\ExpressionEngine\Model\Template\Snippet as SnippetModel;
use EllisLab\ExpressionEngine\Model\Template\GlobalVariable as VariableModel;

class ForceSnippetVarSyncToDatabaseService
{
    private $modelFacade;

    public function __construct(ModelFacade $modelFacade)
    {
        $this->modelFacade = $modelFacade;
    }

    public function run(): void
    {
        /** @var SnippetModel $model */
        $model = $this->modelFacade->make('Snippet');
        $model->loadAll();

        /** @var VariableModel $model */
        $model = $this->modelFacade->make('GlobalVariable');
        $model->loadAll();
    }
}
