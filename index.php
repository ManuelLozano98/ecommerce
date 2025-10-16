<?php
require_once __DIR__ . '/vendor/autoload.php';


use Slim\Factory\AppFactory;
use App\Middleware\ApiMiddleware;
use App\Middleware\ServiceMiddleware;
use Middlewares\TrailingSlash;

$app = AppFactory::create();

$app->setBasePath('/Ecommerce'); // Set the base path to your project folder

$app->add(new ApiMiddleware($app->getResponseFactory()));
$app->add(new ServiceMiddleware($app->getResponseFactory()));

$app->add(new TrailingSlash(true)); // Prevent issues if user forgets the trailing slash by adding it

(require __DIR__ . '/routes/api.php')($app);
(require __DIR__ . '/routes/web.php')($app);

$app->run();
