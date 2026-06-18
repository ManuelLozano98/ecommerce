<?php

namespace App\Api;

use App\Services\ProductImageService;
use App\Utils\ApiHelper;
use Rakit\Validation\Validator;
use Rakit\Validation\ErrorBag;

class ProductImageApi
{
    private ProductImageService $service;
    private Validator $validator;

    public function __construct(ProductImageService $service, Validator $validator)
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

    public function getGalleryDetailed($request, $response, $args)
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

    public function getByProduct($request, $response, $args)
    {
        return ApiHelper::success(
            $response,
            $this->service->getByProductId($args["product_id"])
        );
    }

    public function getOne($request, $response, $args)
    {
        return ApiHelper::success(
            $response,
            $this->service->getImage($args["id"])
        );
    }

    public function save($request, $response, $args)
    {
        $data = $request->getParsedBody();
        $uploadedFile = $request->getUploadedFiles();
        $image = $uploadedFile['image'] ?? null;
        if (!$image) {
            return ApiHelper::error($response, ['message' => 'Image is required'], 400);
        }
        $img = $uploadedFile["image"];
        $isImageValid = $this->validateImageExt($img);
        if (!$isImageValid) {
            return ApiHelper::error($response, ['message' => 'Invalid image'], 400);
        }
        $data["image"] = $img;
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
            $data["id"] = $args["id"];
            return ApiHelper::success(
                $response,
                $this->service->update($data)
            );
        }
    }
    public function update($request, $response, $args)
    {
        $data = $request->getParsedBody();
        $uploadedFile = $request->getUploadedFiles();
        $image = $uploadedFile['image'] ?? null;
        if ($image && $image->getError() === UPLOAD_ERR_OK) {
            $isImageValid = $this->validateImageExt($image);

            if (!$isImageValid) {
                return ApiHelper::error($response, ['message' => 'Invalid image'], 400);
            }
            $data["image"] = $image;
        }
        $isValid = $this->validate($data);
        if ($isValid instanceof ErrorBag) {
            return ApiHelper::error(
                $response,
                ['message' => 'Invalid input data', 'details' => $isValid->toArray()],
                400
            );
        }

        if ($request->getMethod() === "POST") {
            $data["id"] = $args["id"];
            return ApiHelper::success(
                $response,
                $this->service->update($data)
            );
        }
    }

    private function validateImageExt($file)
    {

        $fileExt = strtolower(pathinfo($file->getClientFilename(), PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        return in_array($fileExt, $allowed);
    }

    public function delete($request, $response, $args)
    {
        $this->service->delete($args["id"]);
        return ApiHelper::success($response, ['message' => 'Product image deleted successfully']);
    }

    private function validate($data)
    {
        $validator = $this->validator->make($data, [
            'product_id' => 'required|integer|min:1',
            'type' => 'required',
            'active' => 'boolean'
        ]);

        $validator->validate();

        return $validator->fails() ? $validator->errors() : true;
    }
}
