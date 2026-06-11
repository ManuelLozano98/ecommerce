<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Exceptions\NotFoundException;
use App\Exceptions\UpdateException;
use App\Exceptions\InsertException;
use App\Exceptions\DeleteException;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\SaleItemRepositoryInterface;
use App\Repositories\Contracts\SaleRepositoryInterface;

class SaleItemService
{
    private SaleItemRepositoryInterface $repository;
    private ProductRepositoryInterface $productRepository;
    private SaleRepositoryInterface $saleRepository;

    public function __construct(SaleItemRepositoryInterface $repository, ProductRepositoryInterface $productRepository, SaleRepositoryInterface $saleRepository)
    {
        $this->repository = $repository;
        $this->productRepository = $productRepository;
        $this->saleRepository = $saleRepository;
    }
    public function getAll()
    {
        return $this->repository->findAll();
    }

    public function getSale($id)
    {
        $sale = $this->saleRepository->findById($id);
        if (!$sale) {
            throw new NotFoundException("The sale was not found or not exists");
        }
        return $sale;
    }

    public function getSaleItemsBySale(Sale $sale)
    {
        return $this->repository->findBySale($sale);
    }


    public function save($rawSaleItem)
    {

        if (!$this->saleRepository->findById($rawSaleItem["sale_id"])) {
            throw new NotFoundException("The sale item was not found");
        }
        if (!$this->productRepository->findById($rawSaleItem["product_id"])) {
            throw new NotFoundException("The sale item was not found");
        }

        $saleItem = new SaleItem($rawSaleItem);
        try {
            return $this->repository->insert($saleItem);
        } catch (\Throwable $e) {
            throw new InsertException("Failed to insert sale item");
        }
    }

    public function update($rawSaleItem)
    {
        $existing = $this->repository->findById($rawSaleItem['id']);

        if (!$existing) {
            throw new NotFoundException("The sale item was not found");
        }

        if (!$this->saleRepository->findById($rawSaleItem["sale_id"])) {
            throw new NotFoundException("The sale was not found");
        }

        if (!$this->productRepository->findById($rawSaleItem["product_id"])) {
            throw new NotFoundException("The product was not found");
        }

        $editSaleItem = new SaleItem($rawSaleItem);

        try {
            return $this->repository->update($editSaleItem);
        } catch (\Throwable $e) {
            throw new UpdateException("Failed to update sale item with ID " . $rawSaleItem['id']);
        }
    }

    public function deleteItemById($id, $saleId)
    {

        if (!$this->repository->findById($id)) {
            throw new NotFoundException("The sale item was not found");
        }

        if (!$this->repository->deleteByIdAndSaleId($id, $saleId)) {
            throw new DeleteException("Failed to delete sale item");
        }
    }
}
