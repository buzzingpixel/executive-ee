<?php

declare(strict_types=1);

namespace buzzingpixel\executive\services\templatesync;

use EllisLab\ExpressionEngine\Model\Template\TemplateGroup as TemplateGroupModel;
use EllisLab\ExpressionEngine\Service\Model\Collection as ModelCollection;
use EllisLab\ExpressionEngine\Service\Model\Facade as ModelFacade;
use EllisLab\ExpressionEngine\Service\Model\Query\Builder as ModelQueryBuilder;

class DeleteTemplateGroupsWithoutTemplatesService
{
    /** @var ModelFacade $modelFacade */
    private $modelFacade;

    public function __construct(ModelFacade $modelFacade)
    {
        $this->modelFacade = $modelFacade;
    }

    public function run() : void
    {
        /** @var ModelQueryBuilder $query */
        $query = $this->modelFacade->get('TemplateGroup');

        $query->with('Templates');

        /** @var ModelCollection $models */
        $models = $query->all();

        $models->each(static function (TemplateGroupModel $model) : void {
            if ($model->Templates->count()) {
                return;
            }

            $model->delete();
        });
    }
}
