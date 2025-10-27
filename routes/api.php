<?php

use Slim\App;
use App\Api\CategoryApi;
use App\Api\ProductApi;

return function (App $app) {
    $categoryApi = new CategoryApi();
    $productApi = new ProductApi();
    $app->get('/api/categories/name/', [$categoryApi, 'getCategoriesName']);
    $app->get('/api/categories/', [$categoryApi, 'getCategories']);
    $app->get('/api/categories/{id:[0-9]+}/', [$categoryApi, 'getCategoryById']);
    $app->post('/api/categories/', [$categoryApi, 'saveCategory']);
    $app->put('/api/categories/{id:[0-9]+}/', [$categoryApi, 'saveCategory']);
    $app->delete('/api/categories/{id:[0-9]+}/', [$categoryApi, 'deleteCategory']);


    $app->get('/api/products/name/', [$productApi, 'getProductsName']);
    $app->get('/api/products/code/{code}/', [$productApi, 'getProductByCode']);
    $app->get('/api/products/', [$productApi, 'getProductsDetailed']);
    $app->get('/api/products/detailed/', [$productApi, 'getProductsDetailed']);
    $app->get('/api/products/{id:[0-9]+}/', [$productApi, 'getProductById']);
    $app->post('/api/products/', [$productApi, 'saveProduct']);
    $app->post('/api/products/{id:[0-9]+}/image/', [$productApi, 'saveImage']);
    $app->put('/api/products/{id:[0-9]+}/', [$productApi, 'saveProduct']);
    $app->delete('/api/products/{id:[0-9]+}/', [$productApi, 'deleteProduct']);
};
