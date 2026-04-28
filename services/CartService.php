<?php

namespace App\Services;

use App\Models\Cart;
use App\Exceptions\InsertException;
use App\Exceptions\UpdateException;
use App\Exceptions\DeleteException;
use App\Exceptions\NotFoundException;
use App\Exceptions\DuplicateException;
use App\Repositories\Contracts\CartRepositoryInterface;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\ProductInformationRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;

class CartService
{
    private CartRepositoryInterface $repository;
    private UserRepositoryInterface $user_repository;
    private ProductRepositoryInterface $product_repository;
    private CategoryRepositoryInterface $category_repository;
    private ProductInformationRepositoryInterface $product_information_repository;

    public function __construct(CartRepositoryInterface $repository, UserRepositoryInterface $user_repository, ProductRepositoryInterface $product_repository, CategoryRepositoryInterface $category_repository, ProductInformationRepositoryInterface $product_information_repository)
    {
        $this->repository = $repository;
        $this->user_repository = $user_repository;
        $this->product_repository = $product_repository;
        $this->category_repository = $category_repository;
        $this->product_information_repository = $product_information_repository;
    }

    public function getAll()
    {
        $cart = $this->repository->findAll();
        foreach ($cart as $items) {
            $items->setProduct($this->product_repository->findById($items->getProduct()->getId()));
            $items->getProduct()->setCategory($this->category_repository->findById($items->getProduct()->getCategoryId()));
            $discount = $this->product_information_repository->findByProductId($items->getProduct()->getId())->getDiscount() ?? 0;
            $items->getProduct()->setPrice(round($items->getProduct()->getPrice() * (1 - $discount / 100), 2));
        }
        return $cart;
    }

    public function getCart($id)
    {
        $cart = $this->repository->findById($id);
        if (!$cart) {
            throw new NotFoundException("The cart was not found or not exists");
        }
        $cart->setProduct($this->product_repository->findById($cart->getProduct()->getId()));
        $cart->getProduct()->setCategory($this->category_repository->findById($cart->getProduct()->getCategoryId()));
        $discount = $this->product_information_repository->findByProductId($cart->getProduct()->getId())->getDiscount() ?? 0;
        $cart->getProduct()->setPrice(round($cart->getProduct()->getPrice() * (1 - $discount / 100), 2));
        return $cart;
    }

    public function getCartByUser($id)
    {
        $cart = $this->repository->findByUser($id);
        foreach ($cart as $items) {
            $items->setProduct($this->product_repository->findById($items->getProduct()->getId()));
            $items->getProduct()->setCategory($this->category_repository->findById($items->getProduct()->getCategoryId()));
            $discount = $this->product_information_repository->findByProductId($items->getProduct()->getId())->getDiscount() ?? 0;
            $items->getProduct()->setPrice(round($items->getProduct()->getPrice() * (1 - $discount / 100), 2));
        }
        return $cart;
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

    public function deleteUserCart($userId)
    {
        if (!$this->repository->deleteUserCart($userId)) {
            throw new DeleteException("Failed to delete cart with ID $userId.");
        }
    }

    public function save($cart)
    {
        if (!$this->user_repository->findById($cart->getUserId())) {
            throw new NotFoundException("The user was not found or not exists");
        }
        if (!$this->product_repository->findById($cart->getProduct()->getId())) {
            throw new NotFoundException("The user was not found or not exists");
        }

        if (!$this->repository->insert($cart)) {
            throw new InsertException("Failed to insert cart with ID " . $cart->getId());
        }
        return $cart;
    }
    public function update($cart)
    {
        $cartDb = $this->getCart($cart->getId());

        $this->set($cartDb, $cart);

        if (!$this->repository->update($cartDb)) {
            throw new UpdateException("Failed to update cart with ID " . $cartDb->getId());
        }
        return $cartDb;
    }

    private function set($cartDb, $cart)
    {
        $allowedFields = ['user_id', 'product_id', 'quantity'];

        foreach ($allowedFields as $field) {
            if (isset($cart->$field)) {
                $method = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $field)));

                if (method_exists($cartDb, $method)) {
                    $cartDb->$method($cart->$field);
                }
            }
        }

        return $cartDb;
    }
}
