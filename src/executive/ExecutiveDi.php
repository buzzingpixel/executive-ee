<?php
declare(strict_types=1);

namespace buzzingpixel\executive;

use EE_Config;
use Exception;
use DI\Container;
use DI\ContainerBuilder;
use DI\NotFoundException;
use DI\DependencyException;
use buzzingpixel\executive\exceptions\DependencyInjectionBuilderException;

/**
 * Class ExecutiveDi
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
                $eeConfig = \function_exists('ee') ? ee()->config : null;

                if ($eeConfig && isset($eeConfig->config['diDefinitions'])) {
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

    /**
     * @param string $def
     * @return mixed
     * @throws NotFoundException
     * @throws DependencyException
     * @throws DependencyInjectionBuilderException
     */
    public static function get(string $def)
    {
        return self::diContainer()->get($def);
    }

    /**
     * Gets a definition
     * @param string $def
     * @return mixed
     * @throws NotFoundException
     * @throws DependencyException
     * @throws DependencyInjectionBuilderException
     */
    public function getDefinition(string $def)
    {
        return self::get($def);
    }
}
