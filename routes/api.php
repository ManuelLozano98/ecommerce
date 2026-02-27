<?php

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
use Slim\Routing\RouteCollectorProxy;

return function (RouteCollectorProxy $group) {
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

    $group->get('/categories/{id:[0-9]+}/', [$categoryApi, 'getCategoryById']);
    $group->get('/categories/name/', [$categoryApi, 'getCategoriesName']);
    $group->get('/categories/', [$categoryApi, 'getAll']);
    $group->post('/categories/', [$categoryApi, 'save']);
    $group->put('/categories/{id:[0-9]+}/', [$categoryApi, 'save']);
    $group->delete('/categories/{id:[0-9]+}/', [$categoryApi, 'delete']);


    $group->get('/products/name/', [$productApi, 'getProductsName']);
    $group->get('/products/code/{code}/', [$productApi, 'getProductByCode']);
    $group->get('/products/', [$productApi, 'getAll']);
    $group->get('/products/detailed/', [$productApi, 'getProductsDetailed']);
    $group->get('/products/{id:[0-9]+}/', [$productApi, 'getProductById']);
    $group->post('/products/', [$productApi, 'save']);
    $group->post('/products/{id:[0-9]+}/image/', [$productApi, 'saveImage']);
    $group->put('/products/{id:[0-9]+}/', [$productApi, 'save']);
    $group->delete('/products/{id:[0-9]+}/', [$productApi, 'delete']);

    $group->get('/products/{id:[0-9]+}/reviews/', [$reviewApi, 'getProductReviews']);
    $group->delete('/products/{id:[0-9]+}/reviews/', [$reviewApi, 'deleteReviewsbyProduct']);
    $group->delete('/products/{product_id:[0-9]+}/reviews/{review_id:[0-9]+}/', [$reviewApi, 'deleteReviewbyProduct']);

    $group->get('/users/document-type/', [$userApi, 'getDocumentType']);
    $group->get('/users/', [$userApi, 'getAll']);
    $group->get('/users/detailed/', [$userApi, 'getUsersDetailed']);
    $group->get('/users/{id:[0-9]+}/', [$userApi, 'getUserById']);
    $group->get('/users/username/', [$userApi, 'getUsernames']);
    $group->post('/users/', [$userApi, 'save']);
    $group->post('/users/{id:[0-9]+}/image/', [$userApi, 'saveImage']);
    $group->put('/users/{id:[0-9]+}/', [$userApi, 'save']);
    $group->delete('/users/{id:[0-9]+}/', [$userApi, 'delete']);

    $group->get('/users/roles/', [$userRoleApi, 'getAll']);
    $group->get('/users/roles/detailed/', [$userRoleApi, 'getUserRolesDetailed']);
    $group->post('/users/{id:[0-9]+}/roles/', [$userRoleApi, 'save']);
    $group->delete('/users/{user_id:[0-9]+}/roles/', [$userRoleApi, 'deleteRolesByUserId']);
    $group->delete('/users/{id:[0-9]+}/roles/{role_id:[0-9]+}/', [$userRoleApi, 'deletebyUserIdAndRoleId']);

    $group->get('/users/{id:[0-9]+}/reviews/', [$reviewApi, 'getUserReviews']);
    $group->delete('/users/{id:[0-9]+}/reviews/', [$reviewApi, 'deleteReviewsbyUser']);
    $group->delete('/users/{user_id:[0-9]+}/reviews/{review_id:[0-9]+}/', [$reviewApi, 'deleteReviewbyUser']);


    $group->get('/roles/name/', [$roleApi, 'getRolesName']);
    $group->get('/roles/', [$roleApi, 'getAll']);
    $group->get('/roles/{id:[0-9]+}/', [$roleApi, 'getRoleById']);
    $group->post('/roles/', [$roleApi, 'save']);
    $group->put('/roles/{id:[0-9]+}/', [$roleApi, 'save']);
    $group->delete('/roles/{id:[0-9]+}/', [$userRoleApi, 'delete']);


    $group->get('/reviews/', [$reviewApi, 'getAll']);
    $group->get('/reviews/detailed/', [$reviewApi, 'getDetailedReviews']);
    $group->get('/reviews/{id:[0-9]+}/', [$reviewApi, 'getReviewById']);
    $group->post('/reviews/', [$reviewApi, 'save']);
    $group->put('/reviews/{id:[0-9]+}/', [$reviewApi, 'save']);
    $group->delete('/reviews/{id:[0-9]+}/', [$reviewApi, 'delete']);

    $group->get('/sales/{id:[0-9]+}/', [$saleApi, 'getSale']);
    $group->get('/sales/{user_id:[0-9]+}/purchases/', [$saleApi, 'getPurchasesByUser']);
    $group->get('/sales/', [$saleApi, 'getAll']);
    $group->get('/sales/detailed/', [$saleApi, 'getAllDetailed']);
    $group->get('/sales/detailed/username/', [$saleApi, 'getSalesWithUserAndItems']);
    $group->post('/sales/', [$saleApi, 'save']);
    $group->put('/sales/{id:[0-9]+}/', [$saleApi, 'save']);
    $group->delete('/sales/{id:[0-9]+}/', [$saleApi, 'delete']);


    $group->get('/sale-items/', [$saleItemApi, 'getAll']);
    $group->post('/sales/{id:[0-9]+}/items/', [$saleItemApi, 'save']);
    $group->put('/sales/{id:[0-9]+}/items/{item_id:[0-9]+}/', [$saleItemApi, 'save']);
    $group->delete('/sales/{id:[0-9]+}/items/{item_id:[0-9]+}/', [$saleItemApi, 'delete']);
};
