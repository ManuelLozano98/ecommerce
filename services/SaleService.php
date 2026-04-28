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
        if (!$this->userRepository->findById($rawSale["user_id"])) {
            throw new NotFoundException("The sale was not found or not exists");
        }

        $sale = new Sale($rawSale);
        if (!$this->repository->insert($sale)) {
            throw new InsertException("Failed to insert sale");
        }
        return $sale;
    }
    public function update($rawSale)
    {
        $saleDb = $this->getSale($rawSale["id"]);
        $editSale = new Sale($rawSale);
        if (!$this->repository->update($editSale)) {
            throw new UpdateException("Failed to update sale with ID " . $editSale->getId());
        }
        return $editSale;
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
        if (!$this->repository->delete($id)) {
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
}
