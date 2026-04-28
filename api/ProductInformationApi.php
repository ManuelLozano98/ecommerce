<?php

namespace App\Api;

use App\Services\ProductInformationService;
use App\Utils\ApiHelper;
use Rakit\Validation\Validator;
use Rakit\Validation\ErrorBag;

class ProductInformationApi
{
    private ProductInformationService $service;
    private Validator $validator;

    public function __construct(ProductInformationService $service, Validator $validator)
    {
        $this->service = $service;
        $this->validator = $validator;
    }

    public function getAll($request, $response, $args)
    {
        $params = $request->getQueryParams();
        if (isset($params["start"]) && isset($params["length"])) { // If start and length are present as query params pagination is applied 
            $paginatedData = $this->service->paginate($params);
            $payload = [ // DataTables expects a response object with the following structure
                'draw' => (int)($params['draw'] ?? 1),
                'recordsTotal' => $paginatedData['recordsTotal'],
                'recordsFiltered' => $paginatedData['recordsFiltered'],
                'data' => $paginatedData['data']
            ];

            $response->getBody()->write(json_encode($payload));
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        }

        return ApiHelper::success($response, $this->service->getAll());
    }

    public function getProductInformationDetailed($request, $response, $args)
    {
        $params = $request->getQueryParams();
        if (isset($params["start"]) && isset($params["length"])) { // If start and length are present as query params pagination is applied 
            $paginatedData = $this->service->paginateDetailed($params);
            $payload = [ // DataTables expects a response object with the following structure
                'draw' => (int)($params['draw'] ?? 1),
                'recordsTotal' => $paginatedData['recordsTotal'],
                'recordsFiltered' => $paginatedData['recordsFiltered'],
                'data' => $paginatedData['data']
            ];

            $response->getBody()->write(json_encode($payload));
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        }

        return ApiHelper::success($response, $this->service->getAll());
    }

    public function getOne($request, $response, $args)
    {
        return ApiHelper::success(
            $response,
            $this->service->getById($args["id"])
        );
    }

    public function getByProduct($request, $response, $args)
    {
        return ApiHelper::success(
            $response,
            $this->service->getByProductId($args["product_id"])
        );
    }

    public function getFeatured($request, $response, $args)
    {
        return ApiHelper::success(
            $response,
            $this->service->getFeatured()
        );
    }

    public function getWithDiscount($request, $response, $args)
    {
        return ApiHelper::success(
            $response,
            $this->service->getWithDiscount()
        );
    }

    public function save($request, $response, $args)
    {
        $data = json_decode($request->getBody()->getContents(), true);

        $isValid = $this->validate($data);
        if ($isValid instanceof ErrorBag) {
            return ApiHelper::error(
                $response,
                ['message' => 'Invalid input data', 'details' => $isValid->toArray()],
                400
            );
        }

        if ($request->getMethod() === "POST") {
            return ApiHelper::success(
                $response,
                $this->service->save($data)
            );
        }

        if ($request->getMethod() === "PUT") {
            $data = json_decode(file_get_contents("php://input"), true);
            $data["id"] = $args["id"];
            return ApiHelper::success(
                $response,
                $this->service->update($data)
            );
        }
    }

    public function delete($request, $response, $args)
    {
        $this->service->delete($args["id"]);
        return ApiHelper::success($response, ['message' => 'Product information deleted successfully']);
    }

    private function validate($data)
    {
        $validator = $this->validator->make($data, [
            'product_id'        => 'required|integer|min:1',

            'brand'             => 'nullable|max:255',
            'manufacturer'      => 'nullable|max:255',
            'model'             => 'nullable|max:255',
            'dimensions'        => 'nullable|max:255',
            'color'             => 'nullable|max:100',
            'weight'            => 'nullable|max:100',
            'material'          => 'nullable|max:100',
            'warranty'          => 'nullable|max:255',
            'release_date'      => 'nullable|date',
            'expiration_date'   => 'nullable|date',

            'package_contents'  => 'nullable|array',
            'features'          => 'nullable|array',
            'tags'              => 'nullable|array',
            'color_options'     => 'nullable|array',
            'size_options'      => 'nullable|array',
            'technical_details' => 'nullable|array',

            'rating_average'    => 'numeric|min:0|max:5',
            'discount'          => 'numeric|min:0|max:100',
            'is_featured'       => 'boolean',
        ]);

        $validator->validate();

        return $validator->fails() ? $validator->errors() : true;
    }
}
