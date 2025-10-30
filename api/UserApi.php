<?php

namespace App\Api;

use App\Services\UserService;
use App\Models\User;
use App\Utils\ApiHelper;
use Rakit\Validation\Validator;
use Rakit\Validation\ErrorBag;
use App\Utils\PaginationHelper;
use App\Services\DocumentTypeService;

class UserApi
{
    private UserService $userService;
    private Validator $validator;

    public function __construct()
    {
        $this->userService = new UserService();
        $this->validator = new Validator();
    }


    public function getUsers($request, $response, $args)
    {
        $params = $request->getQueryParams();
        if (isset($params["start"]) && isset($params["length"])) { // If start and length are present as query params pagination is applied 
            $tableName = "users";
            $search = $params['search']['value'] ?? '';
            $columns = ['id', 'name', 'email', 'username', 'phone', 'image', 'address', 'document', 'document_type_id', 'verification_token', 'token_expires_at', 'registration_date', 'active']; //The columns must be in the same order as front end user table
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

        $users = $this->userService->getUsers();
        return ApiHelper::success($response, $users);
    }

    public function getUsersDetailed($request, $response, $args)
    {
        $params = $request->getQueryParams();
        if (isset($params["start"]) && isset($params["length"])) { // If start and length are present as query params pagination is applied 
            $selectFields = [
                'u.id',
                'u.name',
                'u.email',
                'u.username',
                'u.phone',
                'u.image',
                'u.address',
                'u.document',
                'u.document_type_id',
                'u.verification_token',
                'u.token_expires_at',
                'd.name AS document_name',
                'u.registration_date',
                'u.active'
            ];

            $columns = [
                'u.id',
                'u.name',
                'u.email',
                'u.username',
                'u.phone',
                'u.image',
                'u.address',
                'u.document',
                'd.name',
                'u.verification_token',
                'u.token_expires_at',
                'u.registration_date',
                'u.active'
            ];

            $fromClause = 'users u JOIN document_types d ON u.document_type_id = d.id';
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

        $users = $this->userService->getUsersWithDocumentTypeName();
        return ApiHelper::success($response, $users);
    }


    public function getUserById($request, $response, $args)
    {
        $user = $this->userService->getUser($args['id']);
        return ApiHelper::success($response, $user);
    }

    public function getUserWithDocumentType($request, $response, $args)
    {
        $user = $this->userService->getUsersWithDocumentTypeName();
        return ApiHelper::success($response, $user);
    }

    public function getDocumentType($request, $response, $args)
    {
        $documentTypes = new DocumentTypeService();
        $user = $this->userService->getDocumentTypes($documentTypes);
        return ApiHelper::success($response, $user);
    }


    public function saveUser($request, $response, $args)
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

        $isValid = $this->validateUser($data, $method);
        if (is_object($isValid) && $isValid instanceof ErrorBag) {
            $errors = $isValid->toArray();
            return ApiHelper::error($response, ['message' => 'Invalid input data', 'details' => $errors], 400);
        }

        $data = $this->userService->saveUser($method, $data);
        return ApiHelper::success($response, $data);
    }

    public function deleteUser($request, $response, $args)
    {
        $this->userService->deleteUser($args['id'], $this->userService);
        return ApiHelper::success($response, ['message' => 'User deleted successfully']);
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
        $userId = $args['id'];
        $data = $this->userService->saveImage($img, $userId);
        $dataObj = ["image_url" => $data];
        return ApiHelper::success($response, $dataObj);
    }


    private function validateUser($data, $method)
    {
        if ($method === "POST") {
            $validator = $this->validator->make($data, [
                'name'             => 'required|max:100',
                'active'           => 'nullable|boolean',
                'email'            => 'required|email|max:100',
                'password'         => 'required|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/',
                'username'         => 'required|regex:/^[a-zA-Z0-9](?!.*[_.]{2})[a-zA-Z0-9._]{2,18}[a-zA-Z0-9]$/',
                'document_type_id' => 'nullable|numeric',
                'phone'            => 'nullable|regex:/^[6-9]\d{8}$/',
                'address'          => 'nullable|max:255',
                'document'         => 'nullable|max:255',
            ]);
        } else {
            $validator = $this->validator->make($data, [
                'name'             => 'required|max:100',
                'active'           => 'nullable|boolean',
                'email'            => 'email|max:100',
                'password'         => 'nullable|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/',
                'username'         => 'required|regex:/^[a-zA-Z0-9](?!.*[_.]{2})[a-zA-Z0-9._]{2,18}[a-zA-Z0-9]$/',
                'document_type_id' => 'nullable|numeric',
                'phone'            => 'nullable|regex:/^[6-9]\d{8}$/',
                'address'          => 'nullable|max:255',
                'document'         => 'nullable|max:255',
            ]);
        }

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
