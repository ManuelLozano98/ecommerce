<?php

namespace App\Routes;

use Slim\App;
use App\Controllers\CategoryController;
use App\Controllers\ProductController;
use App\Controllers\RoleController;
use App\Controllers\UserController;
use App\Controllers\ReviewController;
use App\Controllers\SaleController;
use App\Controllers\Controller;
use App\Controllers\ProductImageController;
use App\Controllers\ProductInformationController;
use App\Controllers\CartController;
use App\Controllers\StripeController;
use App\Middleware\AdminMiddleware;
use App\Middleware\AuthMiddleware;
use App\Middleware\CartMiddleware;

return function (App $app) {
    $app->get('/', Controller::class . ':index')->setName('index')->add(CartMiddleware::class);
    $app->get('/profile/', UserController::class . ':indexProfile')->add(AuthMiddleware::class)->add(CartMiddleware::class);
    $app->get('/categories/', CategoryController::class . ':index')->add(AdminMiddleware::class);
    $app->get('/products/', ProductController::class . ':index')->add(AdminMiddleware::class);
    $app->get('/users/', UserController::class . ':index')->add(AdminMiddleware::class);
    $app->get('/roles/', RoleController::class . ':index')->add(AdminMiddleware::class);
    $app->get('/reviews/', ReviewController::class . ':index')->add(AdminMiddleware::class);
    $app->get('/sales/', SaleController::class . ':index')->add(AdminMiddleware::class);
    $app->get('/product-information/', ProductInformationController::class . ':index')->add(AdminMiddleware::class);
    $app->get('/gallery/', ProductImageController::class . ':index')->add(AdminMiddleware::class);
    $app->get('/my-cart/', Controller::class . ':viewCart')->add(CartMiddleware::class);
    $app->get('/login/', UserController::class . ':indexLogin');
    $app->get('/logout/', UserController::class . ':logout')->add(AuthMiddleware::class);
    $app->get('/admin/', UserController::class . ':indexAdmin')->add(AdminMiddleware::class)->add(CartMiddleware::class);
    $app->get('/checkout/', Controller::class . ':indexCheckout');
    $app->get('/checkoutReturn/', Controller::class . ':indexCheckoutReturn');
    $app->get('/checkout/address/', Controller::class . ':indexCheckoutAddress')->add(CartMiddleware::class);

    $app->post('/cart/', CartController::class . ':add');
    $app->post('/login/', UserController::class . ':login');
    $app->post('/checkout/', StripeController::class . ':checkout');
    $app->post('/checkout-status/', StripeController::class . ':checkCheckout');
    $app->post('/webhook/stripe/', StripeController::class . ':processOrderWebhook');
    $app->post('/review/', ReviewController::class . ':preReviewProduct');
    $app->post('/review-product/', ReviewController::class . ':reviewProduct');

    $app->get('/{category:[a-z0-9-]+}/{slug:[a-z0-9-]+}/', Controller::class . ':viewProduct')->add(CartMiddleware::class);
    $app->get('/{category:[a-z0-9-]+}/',  Controller::class . ':viewCategoryProducts')->add(CartMiddleware::class);

    $app->post('/checkout/start/', Controller::class . ':checkoutStart');
    $app->post('/checkout/address/', Controller::class . ':checkoutAddress');
};
