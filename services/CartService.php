<?php

namespace App\Services;

use App\Models\Cart;
use App\Exceptions\InsertException;
use App\Exceptions\UpdateException;
use App\Exceptions\DeleteException;
use App\Exceptions\NotFoundException;
use App\Exceptions\DuplicateException;
use App\Repositories\Contracts\CartRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;

class CartService
{
    private CartRepositoryInterface $repository;
    private UserRepositoryInterface $user_repository;
    private ProductRepositoryInterface $product_repository;

    public function __construct(CartRepositoryInterface $repository, UserRepositoryInterface $user_repository, ProductRepositoryInterface $product_repository)
    {
        $this->repository = $repository;
        $this->user_repository = $user_repository;
        $this->product_repository = $product_repository;
    }

    public function getAll()
    {
        return $this->repository->findAll();
    }

    public function getCart($id)
    {
        $cart = $this->repository->findById($id);
        if (!$cart) {
            throw new NotFoundException("The cart was not found or not exists");
        }
        return $cart;
    }

    public function getCartByUser($id)
    {
        return $this->repository->findByUser($id);
    }

    public function getCartForCurrentUser()
    {
        $user = $_SESSION['user'] ?? null;
        if (!empty($user['data'])) {
            return $this->getCartByUser($user['data']->getId());
        }
        return $_SESSION['cart'] ?? [];
    }

    public function paginate($params)
    {
        return $this->repository->paginate($params);
    }

    public function delete($id)
    {
        $this->getCart($id);

        if (!$this->repository->delete($id)) {
            throw new DeleteException("Failed to delete cart with ID $id.");
        }
    }

    public function save($rawCart)
    {
        if ($this->user_repository->findById($rawCart["user_id"])) {
            throw new NotFoundException("The user was not found or not exists");
        }
        if ($this->product_repository->findById($rawCart["product_id"])) {
            throw new NotFoundException("The user was not found or not exists");
        }

        $cart = new Cart($rawCart);

        if (!$this->repository->insert($cart)) {
            throw new InsertException("Failed to insert cart with ID " . $cart->getId());
        }
        return $cart;
    }
    public function update($rawCart)
    {
        $cartDb = $this->getCart($rawCart["id"]);

        $this->set($cartDb, $rawCart);

        if (!$this->repository->update($cartDb)) {
            throw new UpdateException("Failed to update cart with ID " . $cartDb->getId());
        }
        return $cartDb;
    }

    private function set($productDb, $rawProduct)
    {
        $allowedFields = ['user_id', 'product_id', 'quantity'];

        foreach ($allowedFields as $field) {
            if (isset($rawProduct[$field])) {
                $method = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $field)));

                if (method_exists($productDb, $method)) {
                    $productDb->$method($rawProduct[$field]);
                }
            }
        }

        return $productDb;
    }
}
