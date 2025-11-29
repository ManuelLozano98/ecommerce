<?php

namespace App\Services;

use App\Services\UserService;
use App\Services\ProductService;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use App\Exceptions\NotFoundException;
use App\Exceptions\UpdateException;
use App\Exceptions\InsertException;
use App\Exceptions\DeleteException;


class SaleService
{
    private UserService $userService;
    private ProductService $productService;

    public function __construct()
    {
        $this->userService = new UserService();
        $this->productService = new ProductService();
    }
    public function getSales()
    {
        return Sale::getAll();
    }
    public function getSalesDetailedJSON()
    {
        $sales = Sale::getAll();
        $salesDetailed = [];

        foreach ($sales as $sale) {
            $saleData = $sale->toArray();
            $saleData["username"] = $this->userService->getUser($sale->getUserId())->getUsername();
            $items = $sale->getItems();
            $saleData["items"] = array_map(function ($item) {
                $itemData = $item->toArray();
                $itemData["product_name"] = $this->productService->getProduct($item->getProductId())->getName();
                return $itemData;
            }, $items);

            $salesDetailed[] = $saleData;
        }
        if (count($salesDetailed) > 0) {
            $json = json_encode(["data" => array_values($salesDetailed)], true);
            return $json;
        }
        return json_encode(["data" => array_values($sales)], true);
    }
    public function getSale($id)
    {
        $sale = Sale::findById($id);
        if (!$sale) {
            throw new NotFoundException("The sale was not found or not exists");
        }
        return $sale;
    }
    public function getSalesAndItemsJSON()
    {
        $sales = Sale::getAll();
        $salesAndItems = [];

        foreach ($sales as $sale) {
            $saleData = $sale->toArray();
            $items = $sale->getItems();
            $saleData["items"] = array_map(function ($item) {
                return $item->toArray();
            }, $items);

            $salesAndItems[] = $saleData;
        }
        if (count($salesAndItems) > 0) {
            $json = json_encode($salesAndItems, true);
            return $json;
        }
        return json_encode(["data" => array_values($sales)], true);
    }

    public function getSalesByUser(User $user)
    {
        $userId = $user->getId();
        return Sale::findByUserId($userId);
    }
    public function getSalesByUserId(int $userId)
    {
        return Sale::findByUserId($userId);
    }

    public function getSalesByProduct($id)
    {
        return Sale::getSalesByProductId($id);
    }

    public function getPurchasesByUserJSON($userId)
    {
        $sales = Sale::findByUserId($userId);
        $userSales = [];

        foreach ($sales as $sale) {
            $saleArray = $sale->toArray();
            $items = $sale->getItems();

            $saleArray['items'] = array_map(function ($item) {
                return $item->toArray();
            }, $items);

            $userSales[] = $saleArray;
        }
        if (count($userSales) > 0) {
            $json = json_encode(["data" => array_values($userSales)], true);
            return $json;
        }
        return json_encode(["data" => array_values($sales)], true);
    }

    public function saveSale($method, $rawSale)
    {

        if ($method === "POST") {
            if (!$this->userService->getUser($rawSale["user_id"])) {
                throw new NotFoundException("The sale was not found or not exists");
            }

            $sale = new Sale($rawSale);
            if (!Sale::insert($sale)) {
                throw new InsertException("Failed to insert sale");
            }
            return $sale;
        } else {
            $saleDb = Sale::findById($rawSale["id"]);
            if (!$saleDb) {
                throw new NotFoundException("The sale was not found");
            }
            $editSale = new Sale($rawSale);
            if (!Sale::edit($editSale)) {
                throw new UpdateException("Failed to update sale with ID " . $editSale->getId());
            }
            return $editSale;
        }
    }

    public function saveSaleItem($method, $rawSaleItem)
    {

        if ($method === "POST") {
            if (!Sale::findById($rawSaleItem["sale_id"])) {
                throw new NotFoundException("The sale item was not found");
            }
            if (!$this->productService->getProduct($rawSaleItem["product_id"])) {
                throw new NotFoundException("The sale item was not found");
            }

            $saleItem = new SaleItem($rawSaleItem);
            if (!SaleItem::insert($saleItem)) {
                throw new InsertException("Failed to insert sale item");
            }
            return $saleItem;
        } else {
            $saleItem = SaleItem::findById($rawSaleItem["id"]);
            if (!$saleItem) {
                throw new NotFoundException("The sale item was not found");
            }
            if (!Sale::findById($saleItem->getSaleId())) {
                throw new NotFoundException("The sale item was not found");
            }
            if (!$this->productService->getProduct($rawSaleItem["product_id"])) {
                throw new NotFoundException("The sale item was not found");
            }
            $editSale = new SaleItem($rawSaleItem);
            if (!SaleItem::edit($editSale)) {
                throw new UpdateException("Failed to update sale item with ID " . $saleItem->getId());
            }

            return $saleItem;
        }
    }


    public function deleteItemById($id, $saleId)
    {

        if (!SaleItem::findById($id)) {
            throw new NotFoundException("The sale item was not found");
        }

        if (!SaleItem::deleteBySaleId($saleId, $id)) {
            throw new DeleteException("Failed to delete sale item");
        }
    }

    public function deleteSale($id)
    {
        $sale = Sale::findById($id);

        if (!$sale) {
            throw new NotFoundException("The sale was not found");
        }

        $products = $sale->getItems();
        foreach ($products as $product) {
            SaleItem::delete($product->getId());
        }
        if (!Sale::delete($id)) {
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
                if (!SaleItem::delete($item->getId())) {
                    $deletedAll = false;
                }
            }

            if (!Sale::delete($sale->getId())) {
                $deletedAll = false;
            }
        }
        if (!$deletedAll) {
            throw new DeleteException("Failed to delete sales");
        }
    }

    public function getUserService()
    {
        return $this->userService;
    }
    public function getProductService()
    {
        return $this->productService;
    }
}
