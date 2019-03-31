<?php

declare(strict_types=1);

namespace buzzingpixel\executive;

use buzzingpixel\executive\exceptions\DependencyInjectionBuilderException;
use corbomite\configcollector\Factory;
use DI\ContainerBuilder;
use EE_Config;
use LogicException;
use Psr\Container\ContainerInterface;
use Throwable;
use const DIRECTORY_SEPARATOR;
use function array_merge;
use function function_exists;
use function in_array;
use function is_array;

class ExecutiveDi
{
    /** @var ContainerInterface $diContainer */
    private static $diContainer;

    /**
     * @throws DependencyInjectionBuilderException
     */
    public static function diContainer() : ContainerInterface
    {
        if (! self::$diContainer) {
            return self::$diContainer;
        }

        self::build();

        return self::$diContainer;
    }

    /**
     * @throws DependencyInjectionBuilderException
     */
    public function getDiContainer() : ContainerInterface
    {
        return self::diContainer();
    }

    /**
     * @throws DependencyInjectionBuilderException
     */
    public static function build(array $definitions = []) : void
    {
        try {
            $collector = Factory::collector();

            $configDefinitions = [];

            /** @var EE_Config $eeConfig */
            $eeConfig = function_exists('ee') ? ee()->config : null;

            if ($eeConfig && isset($eeConfig->config['diDefinitions'])) {
                $configDefinitions = $eeConfig->item('diDefinitions');

                if (! is_array($configDefinitions)) {
                    throw new LogicException(
                        'diDefinitions must be an array'
                    );
                }
            }

            $diConfig = array_merge(
                $collector->collect('diConfigFilePath'),
                $configDefinitions,
                $definitions
            );

            $builder = new ContainerBuilder();

            $builder->useAutowiring(true);

            $builder->useAnnotations(true);

            $builder->ignorePhpDocErrors(in_array(
                $eeConfig->item('diIgnorePhpDocErrors'),
                [
                    true,
                    'true',
                    1,
                    'yes',
                    'y',
                ],
                true
            ));

            if (in_array(
                $eeConfig->item('diEnableCompilation'),
                [
                    true,
                    'true',
                    1,
                    'yes',
                    'y',
                ],
                true
            )) {
                $sep = DIRECTORY_SEPARATOR;

                $compileCacheDir = SYSPATH . 'user' . $sep . 'cache' . $sep . 'diCompileCache';

                $proxyCacheDir = SYSPATH . 'user' . $sep . 'cache' . $sep . 'diProxyCache';

                $builder->enableCompilation($compileCacheDir);

                $builder->writeProxiesToFile(true, $proxyCacheDir);
            }

            $builder->addDefinitions($diConfig);

            self::$diContainer = $builder->build();
        } catch (Throwable $e) {
            $msg = 'Unable to build Dependency Injection Container';

            if ($e->getMessage() === 'diDefinitions must be an array') {
                $msg .= ': ' . $e->getMessage();
            }

            throw new DependencyInjectionBuilderException($msg, 500, $e);
        }
    }

    /**
     * @throws DependencyInjectionBuilderException
     */
    public function buildContainer(array $definitions = []) : void
    {
        self::build($definitions);
    }
}
