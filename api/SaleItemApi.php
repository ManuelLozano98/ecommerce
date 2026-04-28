<?php

namespace App\Api;

use App\Services\SaleItemService;
use App\Utils\ApiHelper;
use Rakit\Validation\Validator;
use Rakit\Validation\ErrorBag;

class SaleItemApi
{
    private SaleItemService $saleItemService;
    private Validator $validator;

    public function __construct(SaleItemService $saleItemService, Validator $validator)
    {
        $this->saleItemService = $saleItemService;
        $this->validator = $validator;
    }

    public function getAll($request, $response, $args)
    {
        $sale = $this->saleItemService->getAll();
        return ApiHelper::success($response, $sale);
    }

    public function save($request, $response, $args)
    {
        $body = $request->getBody()->getContents();
        $data = json_decode($body, true);
        $method = $request->getMethod();
        $data['sale_id'] = $args['id'];
        if (isset($args['item_id'])) {
            $data['id'] = $args['item_id'];
        }
        $isValid = $this->validate($data, $method);

        if (is_object($isValid) && $isValid instanceof ErrorBag) {
            $errors = $isValid->toArray();
            return ApiHelper::error($response, ['message' => 'Invalid input data', 'details' => $errors], 400);
        }
        if ($request->getMethod() === "POST") {
            return ApiHelper::success(
                $response,
                $this->saleItemService->save($data)
            );
        }
        if ($request->getMethod() === "PUT") {
            return ApiHelper::success(
                $response,
                $this->saleItemService->update($data)
            );
        }
    }

    public function delete($request, $response, $args)
    {
        $this->saleItemService->deleteItemById($args['item_id'], $args['id']);
        return ApiHelper::success($response, ['message' => 'Sale deleted successfully']);
    }

    private function validate($data, $method)
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
