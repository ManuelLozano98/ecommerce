<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/constants.php';

use Slim\Factory\AppFactory;
use App\Middleware\ApiMiddleware;
use Middlewares\TrailingSlash;
use App\Middleware\SessionMiddleware;
use App\Middleware\CartMiddleware;
use App\Handlers\ErrorHandler;

$container = require __DIR__ . '/container.php';
AppFactory::setContainer($container);
$app = AppFactory::create();

$app->setBasePath('/Ecommerce'); // Set the base path to your project folder

$app->add(SessionMiddleware::class);
$app->addRoutingMiddleware();
$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorMiddleware->setDefaultErrorHandler([ErrorHandler::class, 'handle']);


$app->group('/api', function ($group) {
    (require __DIR__ . '/routes/api.php')($group);
})->add(new ApiMiddleware($app->getResponseFactory()));

$app->add(new TrailingSlash(true)); // Prevent issues if user forgets the trailing slash by adding it

(require __DIR__ . '/routes/web.php')($app);
// $app->add(CartMiddleware::class);

$app->run();
