<?php

namespace App\Controllers;

use App\Services\UserService;
use Slim\Views\PhpRenderer;
use App\Exceptions\InvalidCredentialsException;
use App\Services\CartService;
use App\Services\RoleService;
use App\Services\UserRoleService;
use Exception;
use App\Models\Cart;
use App\Services\ProductService;
use App\Services\ReviewService;
use App\Services\SaleService;

class UserController
{
    private string $pageName = "users.php";
    private $renderer;
    private UserService $userService;
    private UserRoleService $userRoleService;
    private RoleService $roleService;
    private CartService $cartService;
    private SaleService $saleService;
    private ReviewService $reviewService;
    private ProductService $productService;

    public function __construct(PhpRenderer $renderer, UserService $userService, UserRoleService $userRoleService, RoleService $roleService, CartService $cartService, SaleService $saleService, ReviewService $reviewService, ProductService $productService)
    {
        $this->renderer = $renderer;
        $this->userService = $userService;
        $this->userRoleService = $userRoleService;
        $this->roleService = $roleService;
        $this->cartService = $cartService;
        $this->saleService = $saleService;
        $this->reviewService = $reviewService;
        $this->productService = $productService;
    }
    public function index($request, $response, $args)
    {
        return $this->renderer->render($response, $this->pageName);
    }
    public function setPageName(string $pageName): void
    {
        $this->pageName = $pageName;
    }
    public function getPageName(): string
    {
        return $this->pageName;
    }

    public function indexLogin($request, $response, $args)
    {
        return $this->renderer->render($response, "login.php");
    }

    public function indexProfile($request, $response, $args)
    {
        $cart = $request->getAttribute("cart");
        $userId = $_SESSION["user"]["data"]->getId();
        $user = $this->userService->getUser($userId);
        $orders = $this->saleService->getPurchasesByUser($user->getId());
        $reviews = $this->reviewService->getActiveReviewsByUser($user->getId());
        $buys = [];
        $reviewData = [];
        foreach ($orders as $order) {
            if (strtolower($order->sales['status']) === "completed") {
                foreach ($order->items as &$item) {
                    $product = $this->productService->getProduct($item->getProductId());
                    $itemArray = $item->toArray();
                    $itemArray['name'] = $product->getName();
                    $itemArray['image'] = $product->getImage();
                    $item = $itemArray;
                }
                unset($item);
                $buys[] = $order;
            }
        }
        foreach ($reviews as $review) {
            $data = $review->toArray();
            $product = $this->productService->getProduct($review->getProductId());
            $data['image'] = $product->getImage();
            $data['name'] = $product->getName();
            $data['slug'] = $product->getSlug();
            $reviewData[] = $data;
        }
        return $this->renderer->render($response, "my-profile.php", ["userData" => $user, "cart" => $cart, 'orders' => $buys, "reviews" => $reviewData]);
    }

    public function indexAdmin($request, $response, $args)
    {
        $cart = $request->getAttribute("cart");
        $data = [
            "cart" => $cart
        ];

        return $this->renderer->render($response, "admin.php", $data);
    }

    public function login($request, $response, $args)
    {
        $body = $request->getBody()->getContents();
        $json = json_decode($body, true);

        $login = $this->sanitizeLogin($json["login"] ?? "");
        $password = $this->sanitizeLogin($json["password"] ?? "");

        $data = [
            "login" => $login,
            "password" => $password
        ];
        try {
            // Login
            $result = $this->userService->logIn($data);
            if (isset($_SESSION['user'])) {
                $redirect = $_SESSION["redirect_after_login"] ?? ROOT;
                unset($_SESSION["redirect_after_login"]);
                $userRoles = $this->userRoleService->getUserRolesbyUserId($_SESSION['user']['data']->getId());
                foreach ($userRoles as $userRole) {
                    $roles = $this->roleService->getRole($userRole->getRoleId());
                    $_SESSION['user']['roles'] = [$roles->getName()];
                }

                // Merge anonymous user cart with logged-in user
                $id = $_SESSION["user"]["data"]->getId() ?? 0;
                $sessionCart = $_SESSION['cart'] ?? [];
                $userCartItems = $this->cartService->getCartByUser($id);
                $userCart = [];

                foreach ($userCartItems as $item) {
                    $userCart[$item->getProduct()->getId()] = $item;
                }

                foreach ($sessionCart as $productId => $sessionItem) {
                    if (isset($userCart[$productId])) {
                        $existingItem = $userCart[$productId];
                        $newQty = $existingItem->getQuantity() + $sessionItem->getQuantity();
                        $existingItem->setQuantity($newQty);
                        $existingItem->setUserId($id);
                        $this->cartService->update($existingItem);
                    } else {
                        $cartData = [
                            "quantity" => $sessionItem->getQuantity(),
                            "product" => $sessionItem->getProduct()->toArray()
                        ];
                        $cartItem = new Cart($cartData);
                        $cartItem->setUserId($id);
                        $this->cartService->save($cartItem);
                    }
                }
                unset($_SESSION['cart']);
            }
            $response->getBody()->write(json_encode(['success' => $result, 'redirect' => $redirect]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (InvalidCredentialsException $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        } catch (Exception $e1) {
            $response->getBody()->write(json_encode(['error' => $e1->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }
    }

    public function logout($request, $response, $args)
    {
        session_destroy();
        return $response->withHeader('Location',  ROOT)->withStatus(302);
    }



    private function sanitizeLogin($text)
    {
        return strip_tags(trim($text));
    }
}
