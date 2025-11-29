<?php

namespace App\Api;

use App\Enums\PaymentMethods;
use App\Enums\SaleStatus;
use App\Services\SaleService;
use App\Utils\ApiHelper;
use App\Utils\PaginationHelper;
use Rakit\Validation\Validator;
use Rakit\Validation\ErrorBag;

class SaleApi
{
    private SaleService $saleService;
    private Validator $validator;
    public function __construct()
    {
        $this->saleService = new SaleService();
        $this->validator = new Validator();
    }

    public function getSales($request, $response, $args)
    {
        $sale = $this->saleService->getSales();
        return ApiHelper::success($response, $sale);
    }
    public function getDetailedSales($request, $response, $args)
    {
        $sales = $this->saleService->getSalesDetailedJSON();
        $params = $request->getQueryParams();
        if (isset($params["start"]) && isset($params["length"])) { // If start and length are present as query params pagination is applied
            $data = PaginationHelper::paginateJSON($sales, $params);
            $response->getBody()->write(json_encode($data));
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        }
        $sales = $this->saleService->getSales();
        return ApiHelper::success($response, $sales);
    }

    public function getSale($request, $response, $args)
    {
        $sale = $this->saleService->getSale($args["id"]);
        return ApiHelper::success($response, $sale);
    }


    public function saveSale($request, $response, $args)
    {
        $body = $request->getBody()->getContents();
        $data = json_decode($body, true);
        $method = $request->getMethod();
        $isValid = $this->validateSale($data);
        if (is_object($isValid) && $isValid instanceof ErrorBag) {
            $errors = $isValid->toArray();
            return ApiHelper::error($response, ['message' => 'Invalid input data', 'details' => $errors], 400);
        } else {
            $data = $this->saleService->saveSale($method, $data);
            return ApiHelper::success($response, $data);
        }
    }
    public function saveSaleItem($request, $response, $args)
    {
        $body = $request->getBody()->getContents();
        $data = json_decode($body, true);
        $method = $request->getMethod();
        $data['sale_id'] = $args['id'];
        if ($method === "PUT") {
            $data['id'] = $args['item_id'];
        }
        $isValid = $this->validateSaleItem($data, $method);

        if (is_object($isValid) && $isValid instanceof ErrorBag) {
            $errors = $isValid->toArray();
            return ApiHelper::error($response, ['message' => 'Invalid input data', 'details' => $errors], 400);
        } else {
            $data = $this->saleService->saveSaleItem($method, $data);
            return ApiHelper::success($response, $data);
        }
    }
    public function deleteSale($request, $response, $args)
    {
        $this->saleService->deleteSale($args['id']);
        return ApiHelper::success($response, ['message' => 'Sale deleted successfully']);
    }

    public function deleteSaleItem($request, $response, $args)
    {
        $this->saleService->deleteItemById($args['item_id'], $args['id']);
        return ApiHelper::success($response, ['message' => 'Sale deleted successfully']);
    }

    private function validateSale($data)
    {
        $validator = $this->validator->make($data, [
            'user_id' => 'required|integer|min:1',
            'total_amount' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'payment_method' => [function ($value) {
                return in_array(strtolower($value), PaymentMethods::all());
            }],
            'status' => [function ($value) {
                return in_array(strtolower($value), SaleStatus::all());
            }],
        ]);
        $validator->validate();
        if ($validator->fails()) {
            return $validator->errors();
        }
        return true;
    }
    private function validateSaleItem($data, $method)
    {
        $rules =  [
            'product_id' => 'required|integer|min:1',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'subtotal' => 'required|regex:/^\d+(\.\d{1,2})?$/',
        ];

        if ($method === "PUT") {
            $rules['id'] = 'required|integer|min:1';
            $rules['sale_id'] = 'required|integer|min:1';
        }
        $validator = $this->validator->make($data, $rules);
        $validator->validate();
        if ($validator->fails()) {
            return $validator->errors();
        }
        return true;
    }
}
