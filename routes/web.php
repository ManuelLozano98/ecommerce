<?php

namespace App\Routes;

use Slim\App;
use Slim\Views\PhpRenderer;
use App\Controllers\CategoryController;
use App\Controllers\ProductController;

return function (App $app) {

    $renderer = new PhpRenderer(__DIR__ . '/../views/');
    $categoryController = new CategoryController($renderer);
    $productController = new ProductController($renderer);

    $app->get('/', function ($request, $response) {
        $response->getBody()->write('Welcome to the E-commerce');
        return $response;
    });
    $app->get('/home/', function ($request, $response) {
        $response->getBody()->write('Welcome to the E-commerce');
        return $response;
    });
    $app->get('/categories/', function ($request, $response, $args) use ($categoryController) {
        return $categoryController->index($request, $response, $args);
    });

    $app->get('/products/', function ($request, $response, $args) use ($productController) {
        return $productController->index($request, $response, $args);
    });
};
