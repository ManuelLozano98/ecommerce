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


    public function __construct(
        PhpRenderer $renderer,
        CategoryService $categoryService,
        ProductService $productService,
        ReviewService $reviewService,
        UserService $userService,
        ProductImageService $productImageService,
        ProductInformationService $productInformationService
    ) {
        $this->renderer = $renderer;
        $this->categoryService = $categoryService;
        $this->productService = $productService;
        $this->reviewService = $reviewService;
        $this->userService = $userService;
        $this->productImageService = $productImageService;
        $this->productInformationService = $productInformationService;
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
            $productsWithDiscount = $this->productInformationService->getWithDiscount();
            $products = $this->productService->getProductsRawFiltered($filters, $itemsPerPage, $offset);
            $total = $this->productService->countFiltered($filters);

            $productData = [];

            foreach ($products as $row) {
                foreach ($productsWithDiscount as $productDiscount) {
                    if ($productDiscount->getProductId() === $row['id']) {
                        $row['price'] = round($row['price'] * (1 - $productDiscount->getDiscount() / 100), 2);
                    }
                }
                $category = $this->categoryService->getCategory($row['category_id']);

                $ratingStats = $this->reviewService->getProductRatingStats($row['id']);
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

        $productsWithDiscount = $this->productInformationService->getWithDiscount();
        $products = $this->productService->getProductsFiltered($filters, $itemsPerPage, $offset);
        $total = $this->productService->countFiltered($filters);
        $totalPages = ceil($total / $itemsPerPage);

        foreach ($products as $product) {
            foreach ($productsWithDiscount as $productDiscount) {
                if ($productDiscount->getProductId() === $product->getId()) {
                    $product->setPrice(round($product->getPrice() * (1 - $productDiscount->getDiscount() / 100), 2));
                }
            }
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
            $productsWithDiscount = $this->productInformationService->getWithDiscount();
            $products =  $this->productService->getProductsRawFiltered($filters, $itemsPerPage, $offset);
            $total =  $this->productService->countFiltered($filters);

            $productData = [];

            foreach ($products as $row) {
                foreach ($productsWithDiscount as $productDiscount) {
                    if ($productDiscount->getProductId() === $row['id']) {
                        $row['price'] = round($row['price'] * (1 - $productDiscount->getDiscount() / 100), 2);
                    }
                }

                $productCategory =  $this->categoryService->getCategory($row['category_id']);

                $ratingStats =  $this->reviewService->getProductRatingStats($row['id']);
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
        $productsWithDiscount = $this->productInformationService->getWithDiscount();
        $products =  $this->productService->getProductsFiltered($filters, $itemsPerPage, $offset);
        $total =  $this->productService->countFiltered($filters);
        $totalPages = ceil($total / $itemsPerPage);

        foreach ($products as $product) {
            foreach ($productsWithDiscount as $productDiscount) {
                if ($productDiscount->getProductId() === $product->getId()) {
                    $product->setPrice(round($product->getPrice() * (1 - $productDiscount->getDiscount() / 100), 2));
                }
            }
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
        $reviews = $this->reviewService->getReviewsByProduct($product->getId());
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
}
