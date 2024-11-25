<?php

namespace District5\S4S\Slim4\Factory;

use DI\Container;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use District5\S4S\Slim4\Enum\Environment;
use District5\S4S\Slim4\App;
use Slim\Factory\AppFactory as SlimAppFactory;
use Slim\Handlers\Strategies\RequestResponseNamedArgs;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Interfaces\MiddlewareDispatcherInterface;
use Slim\Interfaces\RouteCollectorInterface;
use Slim\Interfaces\RouteResolverInterface;
use Slim\Views\PhpRenderer;

class AppFactory extends SlimAppFactory
{
    /**
     * @var Environment
     */
    protected static Environment $environment = Environment::Production;

    protected static function configureRouteCollector(App $app): void
    {
        $serviceBase = $app->getContainer()->get('serviceBase');
        $routeCollector = $app->getRouteCollector();

        // route cache file, if we are in development automatically regenerate this file
        $routeCacheFilename = $serviceBase . DIRECTORY_SEPARATOR . "routes-cache.php";
        if (true === static::isEnvironmentDevelopmentOrBuild()) {
            if (file_exists($routeCacheFilename)) {
                unlink($routeCacheFilename);
            }
        }
        $routeCollector->setCacheFile($routeCacheFilename);

        // change the route invocation strategy
        $routeCollector->setDefaultInvocationStrategy(new RequestResponseNamedArgs());
    }

//    protected static function configureTwig(App $app): void
//    {
//        $serviceBase = $app->getContainer()->get('serviceBase');
//
//        // route cache file, if we are in development automatically regenerate this file
//        $twigTemplatesDirectory = $serviceBase . DIRECTORY_SEPARATOR . "views";
//
//        $twigCache = static::isEnvironmentDevelopmentOrBuild() ? false : $serviceBase . DIRECTORY_SEPARATOR . "views-cache";
//        $twig = Twig::create($twigTemplatesDirectory, ['cache' => $twigCache]);
//
//        $app->add(TwigMiddleware::create($app, $twig));
//    }

    /**
     * @param ResponseFactoryInterface|null         $responseFactory
     * @param ContainerInterface|null               $container
     * @param CallableResolverInterface|null        $callableResolver
     * @param RouteCollectorInterface|null          $routeCollector
     * @param RouteResolverInterface|null           $routeResolver
     * @param MiddlewareDispatcherInterface|null    $middlewareDispatcher
     * @return App
     */
    public static function create(
        ?ResponseFactoryInterface $responseFactory = null,
        ?ContainerInterface $container = null,
        ?CallableResolverInterface $callableResolver = null,
        ?RouteCollectorInterface $routeCollector = null,
        ?RouteResolverInterface $routeResolver = null,
        ?MiddlewareDispatcherInterface $middlewareDispatcher = null,
        ?PhpRenderer $phpRenderer = null
    ): App {
        static::$responseFactory = $responseFactory ?? static::$responseFactory;
        return new App(
            self::determineResponseFactory(),
            $container ?? static::$container,
            $callableResolver ?? static::$callableResolver,
            $routeCollector ?? static::$routeCollector,
            $routeResolver ?? static::$routeResolver,
            $middlewareDispatcher ?? static::$middlewareDispatcher,
            $phpRenderer
        );
    }

    public static function createJsonApi(string $serviceBase): App
    {
        $responseFactory = new \Slim\Http\Factory\DecoratedResponseFactory(
            new \Slim\Psr7\Factory\ResponseFactory(),
            new \Slim\Psr7\Factory\StreamFactory())
        ;

        $container = static::initialiseContainer();
        $container->set('serviceBase', $serviceBase);

        $app = static::create(
            responseFactory: $responseFactory,
            container: $container
        );

        static::configureRouteCollector($app);

        $app->addBodyParsingMiddleware();
        $app->addRoutingMiddleware();

        $app->addErrorMiddleware(true, true, true);

        // add routes
        foreach (glob($serviceBase . DIRECTORY_SEPARATOR . "routes" . DIRECTORY_SEPARATOR . "*.php") as $filename) {
            include_once $filename;
        }

        $container->set('app', $app);

        return $app;
    }

//    public static function createJsonApiFromContainer(): App
//    {
//
//    }

    public static function createUI(string $serviceBase): App
    {
        $container = static::initialiseContainer();
        $container->set('serviceBase', $serviceBase);

        $phpRenderer = new PhpRenderer($serviceBase . DIRECTORY_SEPARATOR . "views");

        $app = static::create(
            container: $container,
            phpRenderer: $phpRenderer
        );

        static::configureRouteCollector($app);

        $app->addBodyParsingMiddleware();
        $app->addRoutingMiddleware();

        $app->addErrorMiddleware(true, true, true);

        // add routes
        foreach (glob($serviceBase . DIRECTORY_SEPARATOR . "routes" . DIRECTORY_SEPARATOR . "*.php") as $filename) {
            include_once $filename;
        }

        $container->set('app', $app);

        return $app;
    }

//    public static function createUIFromContainer(): App
//    {
//
//    }

    protected static function initialiseContainer(): Container
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->useAttributes(false);
        $containerBuilder->useAutowiring(false);
        return $containerBuilder->build();
    }

    protected static function isEnvironmentDevelopmentOrBuild(): bool
    {
        if (Environment::Development === static::$environment || Environment::Build === static::$environment) {
            return true;
        }

        return false;
    }

    /**
     * @param Environment $environment
     */
    public static function setEnvironment(Environment $environment): void
    {
        static::$environment = $environment;
    }
}
