<?php

namespace App\Controllers;

use App\Dtos\ProductViewDTO;
use App\Dtos\UserPublicDTO;
use Slim\Views\PhpRenderer;
use App\Services\CategoryService;
use App\Services\ReviewService;
use App\Services\ProductService;
use App\Services\ProductImageService;
use App\Services\ProductInformationService;
use App\Services\ShippingAddressService;
use App\Services\UserService;

class Controller
{
    private PhpRenderer $renderer;
    private CategoryService $categoryService;
    private ProductService $productService;
    private ReviewService $reviewService;
    private ProductImageService $productImageService;
    private UserService $userService;
    private ProductInformationService $productInformationService;
    private ShippingAddressService $shippingAddressService;


    public function __construct(
        PhpRenderer $renderer,
        CategoryService $categoryService,
        ProductService $productService,
        ReviewService $reviewService,
        UserService $userService,
        ProductImageService $productImageService,
        ProductInformationService $productInformationService,
        ShippingAddressService $shippingAddressService
    ) {
        $this->renderer = $renderer;
        $this->categoryService = $categoryService;
        $this->productService = $productService;
        $this->reviewService = $reviewService;
        $this->userService = $userService;
        $this->productImageService = $productImageService;
        $this->productInformationService = $productInformationService;
        $this->shippingAddressService = $shippingAddressService;
    }
    public function index($request, $response, $args)
    {
        $params = $request->getQueryParams();
        $cart = $request->getAttribute("cart");

        $page = isset($params['page']) ? (int)$params['page'] : 1;
        $itemsPerPage = 24;
        $offset = ($page - 1) * $itemsPerPage;

        $search = isset($params['search']) ? (string)$params['search'] : "";
        $categoriesFilter = isset($params['categories']) ? $params['categories'] : [];
        $scoreFilter = isset($params['scores']) ? $params['scores'] : [];
        $priceFilter = isset($params['range_price']) ? explode(";", $params['range_price'][0]) : [];
        $sort = isset($params['sort']) ? (string)$params['sort'] : "";

        $filters = [
            'search' => $search,
            'categories' => $categoriesFilter,
            'scores' => $scoreFilter,
            'prices' => $priceFilter,
            'sort' => $sort
        ];


        $categories = $this->categoryService->getActive();
        usort($categories, function ($a, $b) {
            return strcmp(strtolower($a->getName()), strtolower($b->getName()));
        });
        $reviewPerProduct = [];

        $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === "xmlhttprequest";

        if ($isAjax) {
            $products = $this->productService->getProductsRawFiltered($filters, $itemsPerPage, $offset);
            $total = $this->productService->countFiltered($filters);

            $productData = [];

            foreach ($products as $row) {
                $category = $this->categoryService->getCategory($row['category_id']);

                $ratingStats = $this->reviewService->getProductRatingStats($row['id']);
                $reviewPerProduct[$row['id']] = $ratingStats;

                $productData[] = [
                    "id" => $row['id'],
                    "name" => $row['name'],
                    "description" => $row['description'],
                    "price" => $row['price'],
                    "final_price" => $row['final_price'],
                    "image" => $row['image'],
                    "slug" => $row['slug'],
                    "category" => $category->getSlug(),
                    "average" => $row['avg_rating'] ?? 0,
                    "total_sales" => $row['total_sales'] ?? 0
                ];
            }
            $totalPages = ceil($total / $itemsPerPage);
            $response->getBody()->write(json_encode([
                "products" => $productData,
                "reviews" => $reviewPerProduct,
                "totalPages" => $totalPages,
                "currentPage" => $page,
                "totalProducts" => $total,
                "records" => count($products)
            ]));

            return $response->withHeader('Content-Type', 'application/json');
        }

        $products = $this->productService->getProductsFiltered($filters, $itemsPerPage, $offset);
        $total = $this->productService->countFiltered($filters);
        $totalPages = ceil($total / $itemsPerPage);

        foreach ($products as $product) {
            $product = $product->product;
            $ratingStats = $this->reviewService->getProductRatingStats($product->getId());
            $reviewPerProduct[$product->getId()] = $ratingStats;
            $product->setCategory($this->categoryService->getCategoryByProduct($product));
        }


        $data = [
            "categories" => $categories,
            "reviews" => $reviewPerProduct,
            "products" => $products,
            "totalPages" => $totalPages,
            "currentPage" => $page,
            "totalProducts" => $total,
            "records" => count($products),
            "cart" => $cart
        ];

        return $this->renderer->render($response, "index.php", $data);
    }

    public function viewCategoryProducts($request, $response, $args)
    {
        $categoryURL = $args['category'];
        $category = $this->categoryService->getCategoryBySlug($categoryURL);
        if (!$category) {
            return $this->renderer->render($response, "404.php");
        }

        $params = $request->getQueryParams();
        $cart = $request->getAttribute("cart");
        $page = isset($params['page']) ? (int)$params['page'] : 1;
        $itemsPerPage = 24;
        $offset = ($page - 1) * $itemsPerPage;

        $search = isset($params['search']) ? (string)$params['search'] : "";
        $scoreFilter = isset($params['scores']) ? $params['scores'] : [];
        $priceFilter = isset($params['range_price']) ? explode(";", $params['range_price'][0]) : [];
        $sort = isset($params['sort']) ? (string)$params['sort'] : "";

        $filters = [
            'categories' => [$category->getId()],
            'search' => $search,
            'scores' => $scoreFilter,
            'prices' => $priceFilter,
            'sort' => $sort
        ];

        $categories =  $this->categoryService->getActive();
        usort($categories, function ($a, $b) {
            return strcmp(strtolower($a->getName()), strtolower($b->getName()));
        });
        $reviewPerProduct = [];

        $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === "xmlhttprequest";

        if ($isAjax) {
            $products =  $this->productService->getProductsRawFiltered($filters, $itemsPerPage, $offset);
            $total =  $this->productService->countFiltered($filters);

            $productData = [];

            foreach ($products as $row) {

                $productCategory =  $this->categoryService->getCategory($row['category_id']);

                $ratingStats =  $this->reviewService->getProductRatingStats($row['id']);
                $reviewPerProduct[$row['id']] = $ratingStats;

                $productData[] = [
                    "id" => $row['id'],
                    "name" => $row['name'],
                    "description" => $row['description'],
                    "price" => $row['price'],
                    "final_price" => $row['final_price'],
                    "image" => $row['image'],
                    "slug" => $row['slug'],
                    "category" => $productCategory->getSlug(),
                    "average" => $row['avg_rating'] ?? 0,
                    "total_sales" => $row['total_sales'] ?? 0
                ];
            }
            $totalPages = ceil($total / $itemsPerPage);
            $response->getBody()->write(json_encode([
                "products" => $productData,
                "reviews" => $reviewPerProduct,
                "totalPages" => $totalPages,
                "currentPage" => $page,
                "totalProducts" => $total,
                "records" => count($products)
            ]));

            return $response->withHeader('Content-Type', 'application/json');
        }
        $products =  $this->productService->getProductsFiltered($filters, $itemsPerPage, $offset);
        $total =  $this->productService->countFiltered($filters);
        $totalPages = ceil($total / $itemsPerPage);

        foreach ($products as $product) {
            $product = $product->product;
            $ratingStats =  $this->reviewService->getProductRatingStats($product->getId());
            $reviewPerProduct[$product->getId()] = $ratingStats;
            $product->setCategory($this->categoryService->getCategoryByProduct($product));
        }


        $data = [
            "categories" => $categories,
            "reviews" => $reviewPerProduct,
            "products" => $products,
            "totalPages" => $totalPages,
            "currentPage" => $page,
            "totalProducts" => $total,
            "records" => count($products),
            "category" => $category,
            "cart" => $cart
        ];

        return $this->renderer->render($response, "category.php", $data);
    }

    public function viewProduct($request, $response, $args)
    {
        $category = $args["category"];
        $productSlug = $args["slug"];
        $category = $this->categoryService->getCategoryBySlug($category);
        if (!$category) {
            return $this->renderer->render($response, "404.php");
        }

        $product = $this->productService->getProductBySlug($productSlug);
        if (!$product) {
            return $this->renderer->render($response, "404.php");
        }

        $cart = $request->getAttribute("cart");
        $data = [];
        $reviewData = [];
        $pageName = "product.php";
        $images = $this->productImageService->getByProductId($product->getId());
        $product->setCategory($category);
        $categories = $this->categoryService->getActive();
        usort($categories, function ($a, $b) {
            return strcmp(strtolower($a->getName()), strtolower($b->getName()));
        });
        $productInfo = $this->productInformationService->getByProductId($product->getId());
        $reviews = $this->reviewService->getRecentReviewsByProduct($product->getId());
        $ratingStats = $this->reviewService->getProductRatingStats($product->getId());
        foreach ($reviews as $review) {
            $user = $this->userService->getUser($review->getUserId());
            $users[] = $user;
            $reviewData[] = [
                'review' => $review,
                'user' => new UserPublicDTO($user),
            ];
        }
        $productData = new ProductViewDTO($product, $productInfo, $images);

        $data = [
            'product' => $productData,
            'reviews' => $reviewData,
            'rating'  => $ratingStats,
            'categories' => $categories,
            "cart" => $cart

        ];

        return $this->renderer->render($response, $pageName,  $data);
    }
    public function viewCart($request, $response, $args)
    {
        $cart = $request->getAttribute('cart');
        $data = [
            "cart" => $cart
        ];

        return $this->renderer->render($response, "cart.php", $data);
    }

    public function indexCheckout($request, $response, $args)
    {
        return $this->renderer->render($response, "checkout-form.php");
    }

    public function indexCheckoutReturn($request, $response, $args)
    {
        return $this->renderer->render($response, "checkout-return.php");
    }

    public function checkoutAddress($request, $response, $args)
    {
        $body = $request->getBody()->getContents();
        $data = json_decode($body, true);
        if (!$data) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'No data',
            ]));
            return $response;
        }
        if (count($errors = $this->validateAddress($data)) > 0) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Invalid form',
                'details' => ['field' => $errors]
            ]));
            return $response;
        }
        $data['user_id'] = $_SESSION['user']['data']->getId();
        if (!$this->shippingAddressService->getByUser($data['user_id'])) {
            $result = $this->shippingAddressService->save($data);
        } else {
            $result = $this->shippingAddressService->update($data);
        }
        if ($result) {
            $response->getBody()->write(json_encode([
                'success' => true,
            ]));
        } else {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => $result
            ]));
        }

        return $response;
    }

    private function validateAddress($data)
    {
        $errors = [];
        if (!isset($data['full_name']) || trim($data['full_name']) === '') {
            $errors['full_name'] = 'Full name is required.';
        }

        if (!isset($data['phone']) || !preg_match('/^[0-9+\s()-]{7,20}$/', $data['phone'])) {
            $errors['phone'] = 'Invalid telephone number.';
        }

        if (!isset($data['address']) || trim($data['address']) === '') {
            $errors['address'] = 'Address is required.';
        }

        if (!isset($data['city']) || trim($data['city']) === '') {
            $errors['city'] = 'City is required.';
        }

        if (!isset($data['province']) || trim($data['province']) === '') {
            $errors['province'] = 'Province is required.';
        }

        if (!isset($data['postal_code']) || !preg_match('/^[0-9]{5}$/', $data['postal_code'])) {
            $errors['postal_code'] = 'Invalid postal code.';
        }
        return $errors;
    }

    public function checkoutStart($request, $response, $args)
    {
        $body = $request->getBody()->getContents();
        $data = json_decode($body, true);
        if (!$data) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'No data',
            ]));
            return $response;
        }
        $_SESSION["checkout"] = $data;
        if (!isset($_SESSION["user"])) {
            $link = ROOT . "/checkout/address";
            $_SESSION["redirect_after_login"] = $link;

            $response->getBody()->write(json_encode([
                'success' => false,
                'redirect' => ROOT . "/login",
            ]));
            return $response;
        }



        $response->getBody()->write(json_encode([
            'success' => true,
            'redirect' => ROOT . "/checkout/address",
        ]));
        return $response;
    }

    public function indexCheckoutAddress($request, $response, $args)
    {
        $products = $_SESSION["checkout"];
        $userId = $_SESSION['user']['data']->getId();
        $data = $this->shippingAddressService->getByUser($userId);
        $userData = $this->userService->getUser($userId);
        return $this->renderer->render($response, "checkout-address.php", ['address' => $data, 'userData' => $userData, 'products' => $products]);
    }
}
