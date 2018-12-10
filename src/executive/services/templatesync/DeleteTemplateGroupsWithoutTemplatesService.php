<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2018 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace buzzingpixel\executive\services\templatesync;

use EllisLab\ExpressionEngine\Service\Model\Facade as ModelFacade;
use EllisLab\ExpressionEngine\Service\Model\Collection as ModelCollection;
use EllisLab\ExpressionEngine\Service\Model\Query\Builder as ModelQueryBuilder;
use EllisLab\ExpressionEngine\Model\Template\TemplateGroup as TemplateGroupModel;

class DeleteTemplateGroupsWithoutTemplatesService
{
    private $modelFacade;

    public function __construct(ModelFacade $modelFacade)
    {
        $this->modelFacade = $modelFacade;
    }

    public function run(): void
    {
        /** @var ModelQueryBuilder $query */
        $query = $this->modelFacade->get('TemplateGroup');

        $query->with('Templates');

        /** @var ModelCollection $models */
        $models = $query->all();

        $models->each(function (TemplateGroupModel $model) {
            if ($model->Templates->count()) {
                return;
            }

            $model->delete();
        });
    }
}
