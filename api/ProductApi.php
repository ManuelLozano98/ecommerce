<?php

namespace App\Api;

use App\Services\ProductService;
use App\Models\Product;
use App\Utils\ApiHelper;
use Rakit\Validation\Validator;
use Rakit\Validation\ErrorBag;
use App\Utils\PaginationHelper;

class ProductApi
{
    private ProductService $productService;
    private Validator $validator;

    public function __construct()
    {
        $this->productService = new ProductService();
        $this->validator = new Validator();
    }


    public function getProducts($request, $response, $args)
    {
        $params = $request->getQueryParams();
        if (isset($params["start"]) && isset($params["length"])) { // If start and length are present as query params pagination is applied 
            $tableName = "products";
            $search = $params['search']['value'] ?? '';
            $columns = ['id', 'name', 'description', 'code', 'image', 'stock', 'price', 'category_id', 'created_at', 'active']; //The columns must be in the same order as front end product table
            $data = PaginationHelper::make($params, $tableName, $columns);


            $filteredRecords = PaginationHelper::getFilteredCount($search, $tableName, $columns);
            $totalRecords = PaginationHelper::getTotalRecords($tableName);

            $payload = [ // DataTables expects a response object with the following structure
                'draw' => (int)($params['draw'] ?? 1),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data
            ];

            $response->getBody()->write(json_encode($payload));
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        }

        $products = $this->productService->getProducts();
        return ApiHelper::success($response, $products);
    }

    public function getProductsDetailed($request, $response, $args)
    {
        $params = $request->getQueryParams();
        if (isset($params["start"]) && isset($params["length"])) { // If start and length are present as query params pagination is applied 
            $selectFields = [
                'p.id',
                'p.name AS product_name',
                'p.code',
                'p.description',
                'p.price',
                'p.image',
                'p.stock',
                'p.active AS active',
                'p.created_at',
                'c.name AS category_name',
                'c.id AS category_id'
            ];

            $columns = [
                'p.id',
                'p.name',
                'p.description',
                'p.code',
                'p.image',
                'p.stock',
                'p.price',
                'c.name',
                'p.created_at',
                'p.active'
            ];

            $fromClause = 'products p JOIN categories c ON p.category_id = c.id';
            $search = $params['search']['value'] ?? '';

            $data = PaginationHelper::makeCustom($params, $fromClause, $columns, $selectFields);
            $totalRecords = PaginationHelper::getTotalRecordsCustom($fromClause);
            $filteredRecords = PaginationHelper::getFilteredCustomCount($search, $fromClause, $columns);


            $payload = [
                'draw' => (int)($params['draw'] ?? 1),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data
            ];

            $response->getBody()->write(json_encode($payload));
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        }

        $products = $this->productService->getProductsDetailed();
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
        $products = $this->productService->getProductsName();
        $product = array_map(fn(Product $p) => [
            'id' => $p->getId(),
            'name' => $p->getName()
        ], $products);
        return ApiHelper::success($response, $product);
    }



    public function saveProduct($request, $response, $args)
    {
        $body = $request->getBody()->getContents();
        $data = json_decode($body, true);
        if (!$data) {
            return ApiHelper::error($response, ['message' => 'Invalid JSON input'], 400);
        }
        if (isset($args['id'])) {
            $data['id'] = $args['id'];
            $method = "PUT";
        } else {
            $method = "POST";
        }

        $isValid = $this->validateProduct($data);
        if (is_object($isValid) && $isValid instanceof ErrorBag) {
            $errors = $isValid->toArray();
            return ApiHelper::error($response, ['message' => 'Invalid input data', 'details' => $errors], 400);
        }

        $data = $this->productService->saveProduct($method, $data);
        return ApiHelper::success($response, $data);
    }

    public function deleteProduct($request, $response, $args)
    {
        $this->productService->deleteProduct($args['id'], $this->productService);
        return ApiHelper::success($response, ['message' => 'Product deleted successfully']);
    }

    public function saveImage($request, $response, $args)
    {
        $body = $request->getUploadedFiles();
        if (!$body) {
            return ApiHelper::error($response, ['message' => 'Invalid JSON input'], 400);
        }

        $img = $body["image"];
        $isValid = $this->validateImage($img);
        if (!$isValid) {
            return ApiHelper::error($response, ['message' => 'Invalid image'], 400);
        }
        $productId = $args['id'];
        $data = $this->productService->saveImage($img, $productId);
        $dataObj = ["image_url" => $data];
        return ApiHelper::success($response, $dataObj);
    }


    private function validateProduct($data)
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

    private function validateImage($file)
    {

        $fileExt = strtolower(pathinfo($file->getClientFileName(), PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        return in_array($fileExt, $allowed);
    }
}
