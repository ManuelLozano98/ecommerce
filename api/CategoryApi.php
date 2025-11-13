<?php

namespace App\Api;

use App\Services\CategoryService;
use App\Services\ProductService;
use App\Models\Category;
use App\Utils\ApiHelper;
use Rakit\Validation\Validator;
use Rakit\Validation\ErrorBag;
use App\Utils\PaginationHelper;

class CategoryApi
{
    private CategoryService $categoryService;
    private ProductService $productService;
    private Validator $validator;

    public function __construct()
    {
        $this->categoryService = new CategoryService();
        $this->productService = new ProductService();
        $this->validator = new Validator();
    }


    public function getCategories($request, $response, $args)
    {
        $params = $request->getQueryParams();
        if (isset($params["start"]) && isset($params["length"])) { // If start and length are present as query params pagination is applied 
            $tableName = "categories";
            $search = $params['search']['value'] ?? '';
            $columns = ['id', 'name', 'description', 'active'];
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

        $categories = $this->categoryService->getCategories();
        if (!$categories) {
            return ApiHelper::error($response, ['message' => 'No categories found'], 404);
        }
        return ApiHelper::success($response, $categories);
    }


    public function getCategoryById($request, $response, $args)
    {
        $category = $this->categoryService->getCategory($args['id']);
        return ApiHelper::success($response, $category);
    }


    public function getCategoriesName($request, $response, $args)
    {
        $categories = $this->categoryService->getCategoriesName();
        $category = array_map(fn(Category $c) => [
            'id' => (int) $c->getId(),
            'name' => $c->getName()
        ], $categories);
        return ApiHelper::success($response, $category);
    }


    public function saveCategory($request, $response, $args)
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

        $isValid = $this->validateCategory($data);
        if (is_object($isValid) && $isValid instanceof ErrorBag) {
            $errors = $isValid->toArray();
            return ApiHelper::error($response, ['message' => 'Invalid input data', 'details' => $errors], 400);
        } else {
            $data = $this->categoryService->saveCategory($method, $data);
            return ApiHelper::success($response, $data);
        }
    }



    public function deleteCategory($request, $response, $args)
    {
        $this->categoryService->deleteCategory($args['id'], $this->productService);
        return ApiHelper::success($response, ['message' => 'Category deleted successfully']);
    }


    private function validateCategory($data)
    {
        $validator = $this->validator->make($data, [
            'name' => 'required|regex:/^.{3,70}$/u',
            'active' => 'nullable|boolean',
            'description' => 'nullable|regex:/^.{3,255}$/u'
        ]);
        $validator->validate();
        if ($validator->fails()) {
            return $validator->errors();
        }
        return true;
    }
}
