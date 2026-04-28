<?php

namespace App\Controllers;

use App\Models\Cart;
use Slim\Views\PhpRenderer;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Services\CartService;
use App\Services\CategoryService;

class CartController
{
    private string $pageName = "cart.php";
    private $renderer;
    private CartService $cartService;
    private CategoryService $categoryService;

    public function __construct(PhpRenderer $renderer, CartService $cartService, CategoryService $categoryService)
    {
        $this->renderer = $renderer;
        $this->cartService = $cartService;
        $this->categoryService = $categoryService;
    }
    public function index($request, $response, $args)
    {
        return $this->renderer->render($response, $this->pageName);
    }
    public function add(Request $request, Response $response, array $args): Response
    {
        // unset($_SESSION['cart']);
        try {
            $body = $request->getBody()->getContents();
            $data = json_decode($body, true);

            if (!$data) {
                throw new \Exception('Invalid data');
            }
            if (!isset($_SESSION["cart"])) {
                $_SESSION['cart'] = [];
            }
            $cartItem = new Cart($data);
            $product = $cartItem->getProduct();
            $productId = $product->getId();

            if (!$product || !$product->getId()) {
                throw new \Exception('Invalid product');
            }

            $product->setCategory($this->categoryService->getCategory($product->getId()));
            if (!isset($_SESSION['cart'][$productId])) {
                $_SESSION['cart'][$productId] = $cartItem;
            } else {
                $existingItem = $_SESSION['cart'][$productId];
                $existingItem->setQuantity($existingItem->getQuantity() + $cartItem->getQuantity());
                $_SESSION['cart'][$productId] = $existingItem;
            }

            if (isset($_SESSION["user"])) {
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
                        $cartItem = new Cart($data);
                        $cartItem->setUserId($id);
                        $this->cartService->save($cartItem);
                    }
                }
                unset($_SESSION['cart']);
            }
            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Item added succesfully'
            ]));

            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }
    }


    public function setPageName(string $pageName): void
    {
        $this->pageName = $pageName;
    }
    public function getPageName(): string
    {
        return $this->pageName;
    }
}
