<?php

use Slim\App;
use App\Api\CategoryApi;


return function (App $app) {
    $api = new CategoryApi();
    $app->get('/api/categories/name/', [$api, 'getCategoriesName']);
    $app->get('/api/categories/', [$api, 'getCategories']);
    $app->get('/api/categories/{id:[0-9]+}/', [$api, 'getCategoryById']);
    $app->post('/api/categories/', [$api, 'saveCategory']);
    $app->put('/api/categories/{id:[0-9]+}/', [$api, 'saveCategory']);
    $app->delete('/api/categories/{id:[0-9]+}/', [$api, 'deleteCategory']);


};
