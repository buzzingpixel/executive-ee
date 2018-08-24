<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2018 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace buzzingpixel\executive\services;

use Throwable;
use ReflectionException;
use buzzingpixel\executive\ExecutiveDi;
use buzzingpixel\executive\models\CommandModel;
use buzzingpixel\executive\factories\EeDiFactory;
use buzzingpixel\executive\models\CliArgumentsModel;
use buzzingpixel\executive\factories\ReflectionFunctionFactory;
use buzzingpixel\executive\factories\ClosureFromCallableFactory;

/**
 * Class RunCommandService
 */
class RunCommandService
{
    /** @var CliArgumentsModel $cliArgumentsModel */
    private $cliArgumentsModel;

    /** @var ExecutiveDi $executiveDi */
    private $executiveDi;

    /** @var EeDiFactory $eeDiFactory */
    private $eeDiFactory;

    private $closureFromCallableFactory;

    /** @var ReflectionFunctionFactory $reflectionMethodFactory */
    private $reflectionMethodFactory;

    /**
     * RunCommandService constructor
     * @param CliArgumentsModel $cliArgumentsModel
     * @param ExecutiveDi $executiveDi
     * @param EeDiFactory $eeDiFactory
     * @param ClosureFromCallableFactory $closureFromCallableFactory
     * @param ReflectionFunctionFactory $reflectionFunctionFactory
     */
    public function __construct(
        CliArgumentsModel $cliArgumentsModel,
        ExecutiveDi $executiveDi,
        EeDiFactory $eeDiFactory,
        ClosureFromCallableFactory $closureFromCallableFactory,
        ReflectionFunctionFactory $reflectionFunctionFactory
    ) {
        $this->cliArgumentsModel = $cliArgumentsModel;
        $this->executiveDi = $executiveDi;
        $this->eeDiFactory = $eeDiFactory;
        $this->closureFromCallableFactory = $closureFromCallableFactory;
        $this->reflectionMethodFactory = $reflectionFunctionFactory;
    }

    /**
     * Gets the available command groups
     * @param CommandModel $commandModel
     * @throws ReflectionException
     */
    public function runCommand(CommandModel $commandModel): void
    {
        $callable = $commandModel->getCallable();
        $class = $commandModel->getClass();
        $method = $commandModel->getMethod();

        if ($class && $method) {
            $instantiatedClass = null;

            try {
                $instantiatedClass = $this->executiveDi->getDefinition($class);
            } catch (Throwable $e) {
            }

            if (! $instantiatedClass) {
                try {
                    $instantiatedClass = $this->eeDiFactory->make($class);
                } catch (Throwable $e) {
                }
            }

            if (! $instantiatedClass) {
                $instantiatedClass = new $class();
            }

            if (method_exists($instantiatedClass, $method)) {
                $callable = [$instantiatedClass, $method];
            }
        }

        $reflector = $this->reflectionMethodFactory->make(
            $this->closureFromCallableFactory->make($callable)
        );

        $params = [];

        $cliArgumentsModel = $this->cliArgumentsModel;

        if ($commandModel->hasCustomCliArgumentsModel()) {
            $cliArgumentsModel = $commandModel->getCustomCliArgumentsModel();
        }

        foreach ($reflector->getParameters() as $parameter) {
            $params[] = $cliArgumentsModel->getArgument($parameter->name);
        }

        \call_user_func_array($callable, $params);
    }
}
