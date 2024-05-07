<?php

namespace District5\S4S\Slim4\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class CORS implements MiddlewareInterface
{
    /**
     * @var array
     */
    private array $allowedOrigins;

    /**
     * @var array
     */
    private array $allowedMethods;

    /**
     * @var bool
     */
    private bool $allowAjaxAuthHeaders;

    /**
     * Creates a new instance of APIKeyEnforcer Middleware
     */
    public function __construct(array $allowedOrigins, array $allowedMethods = ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'OPTIONS'], bool $allowAjaxAuthHeaders = false)
    {
        // TODO: temporary until origin lookup added:
        if (count($allowedOrigins) !== 1) {
            throw new \Exception('Must have exactly 1 CORS origin at present');
        }

        $this->allowedOrigins = $allowedOrigins;
        $this->allowedMethods = $allowedMethods;
        $this->allowAjaxAuthHeaders = $allowAjaxAuthHeaders;
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        $requestHeaders = $request->getHeaderLine('Access-Control-Request-Headers');

        $response = $handler->handle($request);

        $response = $response->withHeader('Access-Control-Allow-Origin', $this->allowedOrigins[0]);
        $response = $response->withHeader('Access-Control-Allow-Methods', implode(',', $this->allowedMethods));
        $response = $response->withHeader('Access-Control-Allow-Headers', $requestHeaders);

        // Allow Ajax CORS requests with Authorization header
        if (true === $this->allowAjaxAuthHeaders) {
            $response = $response->withHeader('Access-Control-Allow-Credentials', 'true');
        }

        return $response;
    }
}
