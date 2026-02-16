<?php

use Slim\App;
use App\Api\CategoryApi;
use App\Api\ProductApi;
use App\Api\RoleApi;
use App\Api\UserApi;
use App\Api\UserRoleApi;
use App\Api\ReviewApi;
use App\Api\SaleApi;
use App\Api\SaleItemApi;
use App\Repositories\CategoryRepository;
use App\Repositories\DocumentTypeRepository;
use App\Repositories\ProductRepository;
use App\Repositories\ReviewRepository;
use App\Repositories\RoleRepository;
use App\Repositories\SaleItemRepository;
use App\Repositories\SaleRepository;
use App\Repositories\UserRepository;
use App\Repositories\UserRoleRepository;
use App\Services\CategoryService;
use App\Services\DocumentTypeService;
use App\Services\ProductService;
use App\Services\ReviewService;
use App\Services\RoleService;
use App\Services\SaleItemService;
use App\Services\SaleService;
use App\Services\UserRoleService;
use App\Services\UserService;
use Rakit\Validation\Validator;

return function (App $app) {
    $validator = new Validator();
    $categoryRepository = new CategoryRepository();
    $productRepository = new ProductRepository();
    $roleRepository = new RoleRepository();
    $userRepository = new UserRepository();
    $userRoleRepository = new UserRoleRepository();
    $reviewRepository = new ReviewRepository();
    $saleItemRepository = new SaleItemRepository();
    $saleRepository = new SaleRepository($saleItemRepository);
    $saleItemRepository = new SaleItemRepository();
    $documentTypeRepository = new DocumentTypeRepository();

    $categoryService = new CategoryService($categoryRepository, $productRepository);
    $productService = new ProductService($productRepository, $categoryRepository);
    $roleService = new RoleService($roleRepository);
    $userService = new UserService($userRepository);
    $reviewService = new ReviewService($reviewRepository, $userRepository, $productRepository);
    $userRoleService = new UserRoleService($userRoleRepository, $userRepository, $roleRepository);
    $saleService = new SaleService($saleRepository, $userRepository, $productRepository, $saleItemRepository);
    $saleItemService = new SaleItemService($saleItemRepository, $productRepository, $saleRepository);
    $documentService = new DocumentTypeService($documentTypeRepository);

    $categoryApi = new CategoryApi($categoryService, $validator);
    $productApi = new ProductApi($productService, $validator);
    $roleApi = new RoleApi($roleService, $validator);
    $userApi = new UserApi($userService, $documentService, $validator);
    $userRoleApi = new UserRoleApi($userRoleService, $roleService);
    $reviewApi = new ReviewApi($reviewService, $validator);
    $saleApi = new SaleApi($saleService, $validator);
    $saleItemApi = new SaleItemApi($saleItemService, $validator);


    $app->get('/api/categories/name/', [$categoryApi, 'getCategoriesName']);
    $app->get('/api/categories/', [$categoryApi, 'getAll']);
    $app->get('/api/categories/{id:[0-9]+}/', [$categoryApi, 'getCategoryById']);
    $app->post('/api/categories/', [$categoryApi, 'save']);
    $app->put('/api/categories/{id:[0-9]+}/', [$categoryApi, 'save']);
    $app->delete('/api/categories/{id:[0-9]+}/', [$categoryApi, 'delete']);


    $app->get('/api/products/name/', [$productApi, 'getProductsName']);
    $app->get('/api/products/code/{code}/', [$productApi, 'getProductByCode']);
    $app->get('/api/products/', [$productApi, 'getAll']);
    $app->get('/api/products/detailed/', [$productApi, 'getProductsDetailed']);
    $app->get('/api/products/{id:[0-9]+}/', [$productApi, 'getProductById']);
    $app->post('/api/products/', [$productApi, 'save']);
    $app->post('/api/products/{id:[0-9]+}/image/', [$productApi, 'saveImage']);
    $app->put('/api/products/{id:[0-9]+}/', [$productApi, 'save']);
    $app->delete('/api/products/{id:[0-9]+}/', [$productApi, 'delete']);

    $app->get('/api/products/{id:[0-9]+}/reviews/', [$reviewApi, 'getProductReviews']);
    $app->delete('/api/products/{id:[0-9]+}/reviews/', [$reviewApi, 'deleteReviewsbyProduct']);
    $app->delete('/api/products/{product_id:[0-9]+}/reviews/{review_id:[0-9]+}/', [$reviewApi, 'deleteReviewbyProduct']);

    $app->get('/api/users/document-type/', [$userApi, 'getDocumentType']);
    $app->get('/api/users/', [$userApi, 'getAll']);
    $app->get('/api/users/detailed/', [$userApi, 'getUsersDetailed']);
    $app->get('/api/users/{id:[0-9]+}/', [$userApi, 'getUserById']);
    $app->get('/api/users/username/', [$userApi, 'getUsernames']);
    $app->post('/api/users/', [$userApi, 'save']);
    $app->post('/api/users/{id:[0-9]+}/image/', [$userApi, 'saveImage']);
    $app->put('/api/users/{id:[0-9]+}/', [$userApi, 'save']);
    $app->delete('/api/users/{id:[0-9]+}/', [$userApi, 'delete']);

    $app->get('/api/users/roles/', [$userRoleApi, 'getAll']);
    $app->get('/api/users/roles/detailed/', [$userRoleApi, 'getUserRolesDetailed']);
    $app->post('/api/users/{id:[0-9]+}/roles/', [$userRoleApi, 'save']);
    $app->delete('/api/users/{user_id:[0-9]+}/roles/', [$userRoleApi, 'deleteRolesByUserId']);
    $app->delete('/api/users/{id:[0-9]+}/roles/{role_id:[0-9]+}/', [$userRoleApi, 'deletebyUserIdAndRoleId']);

    $app->get('/api/users/{id:[0-9]+}/reviews/', [$reviewApi, 'getUserReviews']);
    $app->delete('/api/users/{id:[0-9]+}/reviews/', [$reviewApi, 'deleteReviewsbyUser']);
    $app->delete('/api/users/{user_id:[0-9]+}/reviews/{review_id:[0-9]+}/', [$reviewApi, 'deleteReviewbyUser']);


    $app->get('/api/roles/name/', [$roleApi, 'getRolesName']);
    $app->get('/api/roles/', [$roleApi, 'getAll']);
    $app->get('/api/roles/{id:[0-9]+}/', [$roleApi, 'getRoleById']);
    $app->post('/api/roles/', [$roleApi, 'save']);
    $app->put('/api/roles/{id:[0-9]+}/', [$roleApi, 'save']);
    $app->delete('/api/roles/{id:[0-9]+}/', [$userRoleApi, 'delete']);


    $app->get('/api/reviews/', [$reviewApi, 'getAll']);
    $app->get('/api/reviews/detailed/', [$reviewApi, 'getDetailedReviews']);
    $app->get('/api/reviews/{id:[0-9]+}/', [$reviewApi, 'getReviewById']);
    $app->post('/api/reviews/', [$reviewApi, 'save']);
    $app->put('/api/reviews/{id:[0-9]+}/', [$reviewApi, 'save']);
    $app->delete('/api/reviews/{id:[0-9]+}/', [$reviewApi, 'delete']);

    $app->get('/api/sales/', [$saleApi, 'getAll']);
    $app->get('/api/sales/detailed/', [$saleApi, 'getAllDetailed']);
    $app->get('/api/sales/detailed/username/', [$saleApi, 'getSalesWithUserAndItems']);
    $app->get('/api/sales/{user_id:[0-9]+}/purchases/', [$saleApi, 'getPurchasesByUser']);
    $app->get('/api/sales/{id:[0-9]+}/', [$saleApi, 'getSale']);
    $app->post('/api/sales/', [$saleApi, 'save']);
    $app->put('/api/sales/{id:[0-9]+}/', [$saleApi, 'save']);
    $app->delete('/api/sales/{id:[0-9]+}/', [$saleApi, 'delete']);


    $app->get('/api/sale-items/', [$saleItemApi, 'getAll']);
    $app->post('/api/sales/{id:[0-9]+}/items/', [$saleItemApi, 'save']);
    $app->put('/api/sales/{id:[0-9]+}/items/{item_id:[0-9]+}/', [$saleItemApi, 'save']);
    $app->delete('/api/sales/{id:[0-9]+}/items/{item_id:[0-9]+}/', [$saleItemApi, 'delete']);
};
