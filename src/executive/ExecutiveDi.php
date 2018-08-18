<?php
declare(strict_types=1);

namespace buzzingpixel\executive;

use EE_Config;
use Exception;
use DI\Container;
use function DI\get;
use DI\ContainerBuilder;
use buzzingpixel\executive\exceptions\DependencyInjectionBuilderException;

/**
 * Class DuBoseDi
 */
class ExecutiveDi
{
    /** @var Container $diContainer */
    private static $diContainer;

    /**
     * Gets the DI Container
     * @return Container
     * @throws DependencyInjectionBuilderException
     */
    public static function diContainer(): Container
    {
        if (! self::$diContainer) {
            try {
                $configDefinitions = [];

                /** @var EE_Config $eeConfig */
                $eeConfig = ee()->config ?? null;

                if ($eeConfig) {
                    $configDefinitions = $eeConfig->item('diDefinitions');

                    if (! \is_array($configDefinitions)) {
                        throw new \LogicException(
                            'diDefinitions must be an array'
                        );
                    }
                }

                $diConfig = array_merge(
                    $configDefinitions,
                    require __DIR__ . '/ExecutiveDiDefinitions.php'
                );

                self::$diContainer = (new ContainerBuilder())
                    ->useAutowiring(false)
                    ->useAnnotations(false)
                    ->addDefinitions($diConfig)
                    ->build();
            } catch (Exception $e) {
                $msg = 'Unable to build Dependency Injection Container';

                if ($e->getMessage() === 'diDefinitions must be an array') {
                    $msg = $msg . ': ' . $e->getMessage();
                }

                throw new DependencyInjectionBuilderException($msg, 500, $e);
            }
        }

        return self::$diContainer;
    }

    /**
     * Gets the DI Container
     * @throws Exception
     * @return Container
     */
    public function getDiContainer(): Container
    {
        return self::diContainer();
    }

    /**
     * Gets a definition
     * @param string $def
     * @return mixed
     * @throws DependencyInjectionBuilderException
     */
    public static function definition(string $def)
    {
        return get($def)->resolve(self::diContainer());
    }

    /**
     * Gets a definition
     * @param string $def
     * @return mixed
     * @throws DependencyInjectionBuilderException
     */
    public function getDefinition(string $def)
    {
        return self::definition($def);
    }
}
