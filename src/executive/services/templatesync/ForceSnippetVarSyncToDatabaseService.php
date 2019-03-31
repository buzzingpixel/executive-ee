<?php

declare(strict_types=1);

namespace buzzingpixel\executive\services\templatesync;

use EllisLab\ExpressionEngine\Model\Template\GlobalVariable as VariableModel;
use EllisLab\ExpressionEngine\Model\Template\Snippet as SnippetModel;
use EllisLab\ExpressionEngine\Service\Model\Facade as ModelFacade;

class ForceSnippetVarSyncToDatabaseService
{
    /** @var ModelFacade $modelFacade */
    private $modelFacade;

    public function __construct(ModelFacade $modelFacade)
    {
        $this->modelFacade = $modelFacade;
    }

    public function run() : void
    {
        /** @var SnippetModel $model */
        $model = $this->modelFacade->make('Snippet');
        $model->loadAll();

        /** @var VariableModel $model */
        $model = $this->modelFacade->make('GlobalVariable');
        $model->loadAll();
    }
}
