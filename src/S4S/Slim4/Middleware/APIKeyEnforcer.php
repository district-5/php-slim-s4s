<?php

namespace District5\S4S\Slim4\Middleware;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use District5\S4S\Slim4\Factory\AppFactory;

class APIKeyEnforcer implements MiddlewareInterface
{
    /**
     * @var array
     */
    protected array $apiKeys;

    /**
     * @var array
     */
    protected array $exemptPaths;

    /**
     * @var string
     */
    protected string $headerKey;

    /**
     * @var ResponseFactoryInterface
     */
    protected ResponseFactoryInterface $responseFactory;

    /**
     * Creates a new instance of APIKeyEnforcer Middleware
     */
    public function __construct(array $apiKeys, array $exemptPaths = [], string $headerKey = 'X-Api-Key', ResponseFactoryInterface $responseFactory = null)
    {
        $this->apiKeys = $apiKeys;
        $this->exemptPaths = $exemptPaths;
        $this->headerKey = $headerKey;
        $this->responseFactory = $responseFactory ?: AppFactory::determineResponseFactory();
    }

    /**
     * @param Request $request
     * @param RequestHandler $handler
     * @return Response
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        if (!$this->isValid($request)) {
            return $this->responseFactory->createResponse(403);
        }

        return $handler->handle($request);
    }

    /**
     * @param Request $request
     * @return bool
     */
    protected function isValid(Request $request): bool
    {
        if (in_array($request->getUri()->getPath(), $this->exemptPaths, true)) {
            return true;
        }

        $headerValues = $request->getHeader($this->headerKey);
        if (count($headerValues) !== 1) {
            return false;
        }

        $passedAPIKey = $headerValues[0];
        return $this->isProvidedAPIKeyValid($passedAPIKey);
    }

    /**
     * @param string $apiKey
     * @return bool
     */
    protected function isProvidedAPIKeyValid(string $apiKey): bool
    {
        return in_array($apiKey, $this->apiKeys, true);
    }
}
