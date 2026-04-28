<?php

namespace App\Api;

use App\Dtos\CategoryNameDTO;
use App\Services\CategoryService;
use App\Utils\ApiHelper;
use Rakit\Validation\Validator;
use Rakit\Validation\ErrorBag;

class CategoryApi
{
    private CategoryService $categoryService;
    private Validator $validator;

    public function __construct(CategoryService $categoryService, Validator $validator)
    {
        $this->categoryService = $categoryService;
        $this->validator = $validator;
    }

    public function getAll($request, $response, $args)
    {
        $params = $request->getQueryParams();
        if (isset($params["start"]) && isset($params["length"])) { // If start and length are present as query params pagination is applied 
            $paginatedData = $this->categoryService->paginate($params);

            $payload = [ // DataTables expects a response object with the following structure
                'draw' => (int)($params['draw'] ?? 1),
                'recordsTotal' => $paginatedData['recordsTotal'],
                'recordsFiltered' => $paginatedData['recordsFiltered'],
                'data' => $paginatedData['data']
            ];

            $response->getBody()->write(json_encode($payload));
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        }

        $categories = $this->categoryService->getAll();
        return ApiHelper::success($response, $categories);
    }


    public function getCategoryById($request, $response, $args)
    {
        $category = $this->categoryService->getCategory($args['id']);
        return ApiHelper::success($response, $category);
    }


    public function getCategoriesName($request, $response, $args)
    {
        $categories = $this->categoryService->getAll();
        return ApiHelper::success($response, CategoryNameDTO::from($categories));
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
                $this->categoryService->save($data)
            );
        }
        if ($request->getMethod() === "PUT") {
            $data['id'] = $args['id'];
            return ApiHelper::success(
                $response,
                $this->categoryService->update($data)
            );
        }
    }



    public function delete($request, $response, $args)
    {
        $this->categoryService->delete($args['id']);
        return ApiHelper::success($response, ['message' => 'Category deleted successfully']);
    }


    private function validate($data)
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
