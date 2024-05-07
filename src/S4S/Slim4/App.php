<?php

namespace District5\S4S\Slim4;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\App as SlimApp;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Interfaces\MiddlewareDispatcherInterface;
use Slim\Interfaces\RouteCollectorInterface;
use Slim\Interfaces\RouteResolverInterface;
use Slim\Views\PhpRenderer;

class App extends SlimApp
{
    /**
     * Current version
     *
     * @var string
     */
    public const VERSION = parent::VERSION . '-D5';

    /**
     * @var ?PhpRenderer
     */
    protected ?PhpRenderer $phpRenderer = null;

    /**
     * @param ResponseFactoryInterface              $responseFactory
     * @param ContainerInterface|null               $container
     * @param CallableResolverInterface|null        $callableResolver
     * @param RouteCollectorInterface|null          $routeCollector
     * @param RouteResolverInterface|null           $routeResolver
     * @param MiddlewareDispatcherInterface|null    $middlewareDispatcher
     */
    public function __construct(
        ResponseFactoryInterface $responseFactory,
        ?ContainerInterface $container = null,
        ?CallableResolverInterface $callableResolver = null,
        ?RouteCollectorInterface $routeCollector = null,
        ?RouteResolverInterface $routeResolver = null,
        ?MiddlewareDispatcherInterface $middlewareDispatcher = null,
        ?PhpRenderer $phpRenderer = null
    ) {
        parent::__construct(
            $responseFactory,
            $container,
            $callableResolver,
            $routeCollector,
            $routeResolver,
            $middlewareDispatcher
        );

        if (null !== $phpRenderer) {
            $this->phpRenderer = $phpRenderer;
        }
    }

    public function getView(): ?PhpRenderer
    {
        return $this->phpRenderer;
    }
}
