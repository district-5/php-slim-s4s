# Slim Framework 4 Stub
This library provides Slim Framework 4 utilities for initialising and maintaining both Slim4 JSON based API's and lightweight UI based apps.

# Usage
These instructions assume a standard app directory structure:
```text
- app
| - api
| | - routes
| - www
| | - routes
- lib
| - ...
initialiser.php
```

## Set Environment
Setting an environment, which can be accessed by routing apps:
```php
\S4S\Slim4\Factory\AppFactory::setEnvironment(\S4S\Slim4\Enum\Environment::\S4S\Slim4\Enum\Development);
```

## JSON API base App
```php
$base = __DIR__ . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR;

$app = AppFactory::createJsonApi($base . 'api');
$app->run();
```

## UI base App
```php
$base = __DIR__ . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR;

$app = AppFactory::createUI($base . 'www');
$app->run();
```