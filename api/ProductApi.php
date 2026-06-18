<?php

namespace App\Api;

use App\Services\ProductService;
use App\Utils\ApiHelper;
use Rakit\Validation\Validator;
use Rakit\Validation\ErrorBag;
use App\Dtos\ProductNameDTO;

class ProductApi
{
    private ProductService $productService;
    private Validator $validator;

    public function __construct(ProductService $productService, Validator $validator)
    {
        $this->productService = $productService;
        $this->validator = $validator;
    }


    public function getAll($request, $response, $args)
    {
        $params = $request->getQueryParams();
        if (isset($params["start"]) && isset($params["length"])) { // If start and length are present as query params pagination is applied 
            $paginatedData = $this->productService->paginate($params);
            $payload = [ // DataTables expects a response object with the following structure
                'draw' => (int)($params['draw'] ?? 1),
                'recordsTotal' => $paginatedData['recordsTotal'],
                'recordsFiltered' => $paginatedData['recordsFiltered'],
                'data' => $paginatedData['data']
            ];

            $response->getBody()->write(json_encode($payload));
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } else if (isset($params)) {
            $search = $params['search'] ?? "";
            $categoriesFilter = $params['categories'] ?? [];
            $scoreFilter = $params['scores'] ?? [];
            $priceFilter = $params['range_price'] ?? [];
            $sort = $params['sort'] ?? "";
            $limit = $params["limit"] ?? 0;
            $offset = $params["offset"] ?? 0;

            $filters = [
                'search' => $search,
                'categories' => $categoriesFilter,
                'scores' => $scoreFilter,
                'prices' => $priceFilter,
                'sort' => $sort
            ];
            $products = $this->productService->getProductsFiltered($filters, $limit, $offset);
            return ApiHelper::success($response, $products);
        }

        $products = $this->productService->getAll();
        return ApiHelper::success($response, $products);
    }

    public function getProductsDetailed($request, $response, $args)
    {
        $params = $request->getQueryParams();
        if (isset($params["start"]) && isset($params["length"])) { // If start and length are present as query params pagination is applied 
            $paginatedData = $this->productService->paginateDetailed($params);
            $payload = [ // DataTables expects a response object with the following structure
                'draw' => (int)($params['draw'] ?? 1),
                'recordsTotal' => $paginatedData['recordsTotal'],
                'recordsFiltered' => $paginatedData['recordsFiltered'],
                'data' => $paginatedData['data']
            ];
            $response->getBody()->write(json_encode($payload));
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        }

        $products = $this->productService->getAll();
        return ApiHelper::success($response, $products);
    }


    public function getProductById($request, $response, $args)
    {
        $product = $this->productService->getProduct($args['id']);
        return ApiHelper::success($response, $product);
    }

    public function getProductByCode($request, $response, $args)
    {
        $product = $this->productService->getProductByCode($args['code']);
        return ApiHelper::success($response, $product);
    }

    public function getProductsName($request, $response, $args)
    {
        $products = $this->productService->getAll();
        return ApiHelper::success($response, ProductNameDTO::from($products));
    }


    public function save($request, $response, $args)
    {
        $body = $request->getBody()->getContents();
        $data = json_decode($body, true);
        if (!$data) {
            return ApiHelper::error($response, ['message' => 'Invalid JSON input'], 400);
        }
        $isValid = $this->validate($data);
        if (is_object($isValid) && $isValid instanceof ErrorBag) {
            $errors = $isValid->toArray();
            return ApiHelper::error($response, ['message' => 'Invalid input data', 'details' => $errors], 400);
        }
        if ($request->getMethod() === "POST") {
            return ApiHelper::success(
                $response,
                $this->productService->save($data)
            );
        }
        if ($request->getMethod() === "PUT") {
            $data['id'] = $args['id'];
            return ApiHelper::success(
                $response,
                $this->productService->update($data)
            );
        }
    }

    public function delete($request, $response, $args)
    {
        $this->productService->delete($args['id']);
        return ApiHelper::success($response, ['message' => 'Product deleted successfully']);
    }

    public function saveImage($request, $response, $args)
    {
        $body = $request->getUploadedFiles();
        if (empty($body)) {
            return ApiHelper::error($response, ['message' => 'Invalid JSON input'], 400);
        }

        $img = $body["image"];
        $isValid = $this->validateImageExt($img);
        if (!$isValid) {
            return ApiHelper::error($response, ['message' => 'Invalid image'], 400);
        }
        $productId = $args['id'];
        $data = $this->productService->saveImage($img, $productId);
        $dataObj = ["image_url" => $data];
        return ApiHelper::success($response, $dataObj);
    }


    private function validate($data)
    {
        $validator = $this->validator->make($data, [
            'category_id' => 'required|integer|min:1',
            'code'        => 'required|max:50',
            'name'        => 'required|max:255',

            'description' => 'nullable|max:255',
            'price'       => 'nullable|numeric|min:0',
            'stock'       => 'nullable|integer|min:0',
            'active'      => 'nullable|boolean',
            'created_at'  => 'nullable|date:Y-m-d H:i:s|date:Y-m-d|date:Y-m-d H:i',

        ]);
        $validator->validate();
        if ($validator->fails()) {
            return $validator->errors();
        }
        return true;
    }

    private function validateImageExt($file)
    {

        $fileExt = strtolower(pathinfo($file->getClientFilename(), PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        return in_array($fileExt, $allowed);
    }
}
