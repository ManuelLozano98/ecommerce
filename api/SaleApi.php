<?php

namespace App\Api;

use App\Enums\PaymentMethods;
use App\Enums\SaleStatus;
use App\Services\SaleService;
use App\Utils\ApiHelper;
use Rakit\Validation\Validator;
use Rakit\Validation\ErrorBag;
use App\Utils\PaginationHelper;

class SaleApi
{
    private SaleService $saleService;
    private Validator $validator;

    public function __construct(SaleService $saleService, Validator $validator)
    {
        $this->saleService = $saleService;
        $this->validator = $validator;
    }

    public function getAll($request, $response, $args)
    {
        $sale = $this->saleService->getSales();
        return ApiHelper::success($response, $sale);
    }
    public function getAllDetailed($request, $response, $args)
    {
        $sales = $this->saleService->getSalesDetailed();
        return ApiHelper::success($response, $sales);
    }

    public function getSalesWithUserAndItems($request, $response, $args)
    {
        $sales = $this->saleService->getSalesWithUserAndItems();
        $params = $request->getQueryParams();
        if (isset($params["start"]) && isset($params["length"])) { // If start and length are present as query params pagination is applied
            $data = [];
            foreach ($sales as $sale) {
                $data[] = $sale->toArray();
            }
            $json = json_encode(["data" => array_values($data)], true);
            $paginated = PaginationHelper::paginateJSON($json, $params);
            $response->getBody()->write(json_encode($paginated));
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        }
        $data = [];
        foreach ($sales as $sale) {
            $data[] = $sale->toArray();
        }
        return ApiHelper::success($response, $data);
    }

    public function getPurchasesbyUser($request, $response, $args)
    {
        $sales = $this->saleService->getPurchasesByUser($args["user_id"]);
        $data = [];
        foreach ($sales as $sale) {
            $data[] = $sale->toArray();
        }
        return ApiHelper::success($response, $data);
    }

    public function getSale($request, $response, $args)
    {
        $sale = $this->saleService->getSale($args["id"]);
        return ApiHelper::success($response, $sale);
    }

    public function getDetailedSale($request, $response, $args)
    {
        $sale = $this->saleService->getDetailedSale($args["id"]);
        return ApiHelper::success($response, $sale);
    }


    public function save($request, $response, $args)
    {
        $body = $request->getBody()->getContents();
        $data = json_decode($body, true);
        if (!$data) {
            return ApiHelper::error($response, ['message' => 'Invalid JSON input'], 400);
        }
        $method = $request->getMethod();
        $isValid = $this->validate($data);
        if (is_object($isValid) && $isValid instanceof ErrorBag) {
            $errors = $isValid->toArray();
            return ApiHelper::error($response, ['message' => 'Invalid input data', 'details' => $errors], 400);
        }
        if ($request->getMethod() === "POST") {
            return ApiHelper::success(
                $response,
                $this->saleService->save($data)
            );
        }
        if ($request->getMethod() === "PUT") {
            $data['id'] = $args['id'];
            return ApiHelper::success(
                $response,
                $this->saleService->update($data)
            );
        }
    }
    public function delete($request, $response, $args)
    {
        $this->saleService->deleteSale($args['id']);
        return ApiHelper::success($response, ['message' => 'Sale deleted successfully']);
    }

    private function validate($data)
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
}
