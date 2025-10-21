<?php

namespace App\Api;

use App\Services\CategoryService;
use App\Services\ProductService;
use App\Models\Product;
use App\Utils\ApiHelper;
use Rakit\Validation\Validator;
use Rakit\Validation\ErrorBag;
use App\Utils\PaginationHelper;

class ProductApi
{
    private ProductService $productService;
    private CategoryService $categoryService;
    private Validator $validator;

    public function __construct()
    {
        $this->categoryService = new CategoryService();
        $this->productService = new ProductService($this->categoryService);
        $this->validator = new Validator();
    }


    public function getProducts($request, $response, $args)
    {
        $params = $request->getQueryParams();
        if (isset($params["start"]) && isset($params["length"])) { // If start and length are present as query params pagination is applied 
            $tableName = "products";
            $search = $params['search']['value'] ?? '';
            $columns = ['id', 'name', 'description', 'active', 'code', 'image', 'stock', 'price', 'category_id', 'created_at'];
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


    private function validateProduct($data)
    {
        $validator = $this->validator->make($data, [
            'category_id' => 'required|integer|min:1',
            'code'        => 'required|max:50',
            'name'        => 'required|max:255',

            'description' => 'nullable|max:255',
            'price'       => 'nullable|numeric|min:0',
            'image'       => 'nullable|max:200',
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
}
