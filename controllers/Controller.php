<?php

namespace App\Controllers;

use App\Dtos\ProductViewDTO;
use App\Dtos\UserPublicDTO;
use Slim\Views\PhpRenderer;
use App\Services\CategoryService;
use App\Services\ReviewService;
use App\Services\ProductService;
use App\Repositories\CategoryRepository;
use App\Repositories\ProductRepository;
use App\Repositories\ReviewRepository;
use App\Repositories\UserRepository;
use App\Exceptions\NotFoundException;
use App\Repositories\ProductImageRepository;
use App\Repositories\ProductInformationRepository;
use App\Services\ProductImageService;
use App\Services\ProductInformationService;
use App\Services\UserService;

class Controller
{
    private $renderer;

    public function __construct(PhpRenderer $renderer)
    {
        $this->renderer = $renderer;
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


        $categoryRepository = new CategoryRepository();
        $productRepository = new ProductRepository();
        $userRepository = new UserRepository();
        $reviewRepository = new ReviewRepository();

        $categoryService = new CategoryService($categoryRepository, $productRepository);
        $productService = new ProductService($productRepository, $categoryRepository);
        $reviewService = new ReviewService($reviewRepository, $userRepository, $productRepository);

        $categories = $categoryService->getActive();
        usort($categories, function ($a, $b) {
            return strcmp(strtolower($a->getName()), strtolower($b->getName()));
        });
        $reviewPerProduct = [];

        $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === "xmlhttprequest";

        if ($isAjax) {

            $products = $productService->getProductsRawFiltered($filters, $itemsPerPage, $offset);
            $total = $productService->countFiltered($filters);

            $productData = [];

            foreach ($products as $row) {

                $category = $categoryService->getCategory($row['category_id']);

                $ratingStats = $reviewService->getProductRatingStats($row['id']);
                $reviewPerProduct[$row['id']] = $ratingStats;

                $productData[] = [
                    "id" => $row['id'],
                    "name" => $row['name'],
                    "description" => $row['description'],
                    "price" => $row['price'],
                    "image" => $row['image'],
                    "slug" => $row['slug'],
                    "category" => $category->getName(),
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

        $products = $productService->getProductsFiltered($filters, $itemsPerPage, $offset);
        $total = $productService->countFiltered($filters);
        $totalPages = ceil($total / $itemsPerPage);

        foreach ($products as $product) {
            $ratingStats = $reviewService->getProductRatingStats($product->getId());
            $reviewPerProduct[$product->getId()] = $ratingStats;
            $product->setCategory($categoryService->getCategoryByProduct($product));
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
        $categoryRepository = new CategoryRepository();
        $productRepository = new ProductRepository();
        $categoryService = new CategoryService($categoryRepository, $productRepository);
        $category = $categoryService->getCategoryBySlug($categoryURL);
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

        $userRepository = new UserRepository();
        $reviewRepository = new ReviewRepository();
        $productService = new ProductService($productRepository, $categoryRepository);
        $reviewService = new ReviewService($reviewRepository, $userRepository, $productRepository);
        $categories = $categoryService->getActive();
        usort($categories, function ($a, $b) {
            return strcmp(strtolower($a->getName()), strtolower($b->getName()));
        });
        $reviewPerProduct = [];

        $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === "xmlhttprequest";

        if ($isAjax) {

            $products = $productService->getProductsRawFiltered($filters, $itemsPerPage, $offset);
            $total = $productService->countFiltered($filters);

            $productData = [];

            foreach ($products as $row) {

                $productCategory = $categoryService->getCategory($row['category_id']);

                $ratingStats = $reviewService->getProductRatingStats($row['id']);
                $reviewPerProduct[$row['id']] = $ratingStats;

                $productData[] = [
                    "id" => $row['id'],
                    "name" => $row['name'],
                    "description" => $row['description'],
                    "price" => $row['price'],
                    "image" => $row['image'],
                    "slug" => $row['slug'],
                    "category" => $productCategory->getName(),
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

        $products = $productService->getProductsFiltered($filters, $itemsPerPage, $offset);
        $total = $productService->countFiltered($filters);
        $totalPages = ceil($total / $itemsPerPage);

        foreach ($products as $product) {
            $ratingStats = $reviewService->getProductRatingStats($product->getId());
            $reviewPerProduct[$product->getId()] = $ratingStats;
            $product->setCategory($categoryService->getCategoryByProduct($product));
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
        $productRepository = new ProductRepository();
        $categoryRepository = new CategoryRepository();
        $categoryService = new CategoryService($categoryRepository, $productRepository);
        $productService = new ProductService($productRepository, $categoryRepository);
        $category = $categoryService->getCategoryBySlug($category);
        if (!$category) {
            return $this->renderer->render($response, "404.php");
        }

        $product = $productService->getProductBySlug($productSlug);
        if (!$product) {
            return $this->renderer->render($response, "404.php");
        }

        $cart = $request->getAttribute("cart");
        $data = [];
        $reviewData = [];
        $pageName = "product.php";
        $userRepository = new UserRepository();
        $reviewRepository = new ReviewRepository();
        $productImageRepository = new ProductImageRepository();
        $productInformationRepository = new ProductInformationRepository();
        $reviewService = new ReviewService($reviewRepository, $userRepository, $productRepository);
        $userService = new UserService($userRepository);
        $productImageService = new ProductImageService($productImageRepository, $productRepository);
        $productInformationService = new ProductInformationService($productInformationRepository, $productRepository);
        $images = $productImageService->getByProductId($product->getId());
        $product->setCategory($category);
        $categories = $categoryService->getActive();
        usort($categories, function ($a, $b) {
            return strcmp(strtolower($a->getName()), strtolower($b->getName()));
        });
        $productInfo = $productInformationService->getByProductId($product->getId());
        $reviews = $reviewService->getReviewsByProduct($product->getId());
        $ratingStats = $reviewService->getProductRatingStats($product->getId());
        foreach ($reviews as $review) {
            $user = $userService->getUser($review->getUserId());
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
}
