<?php

use Slim\App;
use App\Api\CategoryApi;
use App\Api\ProductApi;
use App\Api\RoleApi;
use App\Api\UserApi;
use App\Api\UserRoleApi;

return function (App $app) {
    $categoryApi = new CategoryApi();
    $productApi = new ProductApi();
    $roleApi = new RoleApi();
    $userApi = new UserApi();
    $userRoleApi = new UserRoleApi();
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

    $app->get('/api/users/documentType/', [$userApi, 'getDocumentType']);
    $app->get('/api/users/', [$userApi, 'getUsers']);
    $app->get('/api/users/detailed/', [$userApi, 'getUsersDetailed']);
    $app->get('/api/users/{id:[0-9]+}/', [$userApi, 'getUserById']);
    $app->get('/api/users/username/', [$userApi, 'getUsernames']);
    $app->post('/api/users/', [$userApi, 'saveUser']);
    $app->post('/api/users/{id:[0-9]+}/image/', [$userApi, 'saveImage']);
    $app->put('/api/users/{id:[0-9]+}/', [$userApi, 'saveUser']);
    $app->delete('/api/users/{id:[0-9]+}/', [$userApi, 'deleteUser']);

    $app->get('/api/users/roles/', [$userRoleApi, 'getUserRoles']);
    $app->post('/api/users/{id:[0-9]+}/roles/', [$userRoleApi, 'saveUserRole']);

    $app->get('/api/roles/name/', [$roleApi, 'getRolesName']);
    $app->get('/api/roles/', [$roleApi, 'getRoles']);
    $app->get('/api/roles/{id:[0-9]+}/', [$roleApi, 'getRoleById']);
    $app->post('/api/roles/', [$roleApi, 'saveRole']);
    $app->put('/api/roles/{id:[0-9]+}/', [$roleApi, 'saveRole']);
    $app->delete('/api/roles/{id:[0-9]+}/', [$userRoleApi, 'deleteUserRoles']);
};
