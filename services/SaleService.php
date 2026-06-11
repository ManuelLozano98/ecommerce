<?php

namespace App\Services;

use App\Dtos\SaleDetailedUsernameDTO;
use App\Dtos\SaleDetailedDTO;
use App\Models\Sale;
use App\Models\User;
use App\Exceptions\NotFoundException;
use App\Exceptions\UpdateException;
use App\Exceptions\InsertException;
use App\Exceptions\DeleteException;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\SaleItemRepositoryInterface;
use App\Repositories\Contracts\SaleRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Enums\PaymentMethods;
use App\Enums\SaleStatus;

class SaleService
{
    private SaleRepositoryInterface $repository;
    private UserRepositoryInterface $userRepository;
    private ProductRepositoryInterface $productRepository;
    private SaleItemRepositoryInterface $saleItemRepository;

    public function __construct(SaleRepositoryInterface $repository, UserRepositoryInterface $userRepository, ProductRepositoryInterface $productRepository, SaleItemRepositoryInterface $saleItemRepository)
    {
        $this->repository = $repository;
        $this->userRepository = $userRepository;
        $this->productRepository = $productRepository;
        $this->saleItemRepository = $saleItemRepository;
    }
    public function getSales()
    {
        return $this->repository->findAll();
    }

    public function getSalesDetailed()
    {
        return $this->repository->findAllDetailed();
    }

    public function getSalesWithUserAndItems()
    {

        $sales = $this->repository->findAll();
        $result = [];
        foreach ($sales as $sale) {
            $user = $this->userRepository->findById($sale->getUserId());
            $items = $this->saleItemRepository->findBySale($sale);
            $itemsData = [];
            foreach ($items as $item) {
                $itemArray = $item->toArray();
                $productName = $this->productRepository->findById($item->getProductId())->getName();
                $itemArray['product_name'] = $productName;
                $itemsData[] = $itemArray;
            }

            $result[] = new SaleDetailedUsernameDTO(
                $sale->toArray(),
                $user->getUsername(),
                $itemsData
            );
        }
        return $result;
    }
    public function getSale($id)
    {
        $sale = $this->repository->findById($id);
        if (!$sale) {
            throw new NotFoundException("The sale was not found or not exists");
        }
        return $sale;
    }
    public function getDetailedSale($id)
    {
        $sale = $this->repository->findDetailedById($id);
        if (!$sale) {
            throw new NotFoundException("The sale was not found or not exists");
        }
        return $sale;
    }

    public function getSalesByUser(User $user)
    {
        return $this->repository->findByUserId($user->getId());
    }
    public function getSalesByUserId(int $userId)
    {
        return $this->repository->findByUserId($userId);
    }

    public function getSalesByProduct($id)
    {
        return $this->repository->findByProductId($id);
    }

    public function getPurchasesByUser($userId)
    {
        $sales = $this->repository->findByUserId($userId);
        $result = [];

        foreach ($sales as $sale) {
            $items = $this->saleItemRepository->findBySale($sale);
            $result[] = new SaleDetailedDTO(
                $sale->toArray(),
                $items
            );
        }
        return $result;
    }

    public function save($rawSale)
    {
        $user = $this->userRepository->findById($rawSale["user_id"]);

        if (!$user) {
            throw new NotFoundException("User not found");
        }

        $sale = new Sale($rawSale);

        try {
            return $this->repository->insert($sale);
        } catch (\Throwable $e) {
            throw new InsertException("Failed to insert sale");
        }
    }
    public function update($rawSale)
    {
        $saleDb = $this->getSale($rawSale["id"]);

        $this->set($saleDb, $rawSale);

        try {
            return $this->repository->update($saleDb);
        } catch (\Throwable $e) {
            throw new UpdateException("Failed to update sale");
        }
    }

    public function deleteSale($id)
    {
        $sale = $this->repository->findById($id);

        if (!$sale) {
            throw new NotFoundException("The sale was not found");
        }

        $saleItems = $this->saleItemRepository->findBySale($sale);
        foreach ($saleItems as $product) {
            $this->saleItemRepository->delete($product->getId());
        }
        try {
            $deleted = $this->repository->delete($id);
        } catch (\Throwable $e) {
            throw new DeleteException("Failed to delete sale");
        }

        if (!$deleted) {
            throw new DeleteException("Failed to delete sale");
        }
    }

    public function deletePurchasesByUser(User $user)
    {

        $sales = $this->getSalesByUser($user);
        if (!$sales) {
            throw new NotFoundException("The sale was not found");
        }
        $deletedAll = true;

        foreach ($sales as $sale) {
            foreach ($sale->getItems() as $item) {
                if (!$this->saleItemRepository->delete($item->getId())) {
                    $deletedAll = false;
                }
            }

            if (!$this->repository->delete($sale->getId())) {
                $deletedAll = false;
            }
        }
        if (!$deletedAll) {
            throw new DeleteException("Failed to delete sales");
        }
    }
    private function set(Sale $saleDb, array $rawSale): Sale
    {
        $allowedFields = [
            'user_id',
            'total_amount',
            'payment_method',
            'status',
            'created_at',
            'updated_at'
        ];

        foreach ($allowedFields as $field) {
            if (isset($rawSale[$field])) {

                $method = 'set' . str_replace(
                    ' ',
                    '',
                    ucwords(str_replace('_', ' ', $field))
                );

                if (method_exists($saleDb, $method)) {

                    $value = $rawSale[$field];

                    if ($field === 'payment_method' && is_string($value)) {
                        $value = PaymentMethods::from($value);
                    }

                    if ($field === 'status' && is_string($value)) {
                        $value = SaleStatus::from($value);
                    }

                    $saleDb->$method($value);
                }
            }
        }

        return $saleDb;
    }
}
