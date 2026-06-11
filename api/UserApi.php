<?php

namespace App\Api;

use App\Dtos\UserDocumentTypeDTO;
use App\Services\UserService;
use App\Utils\ApiHelper;
use Rakit\Validation\Validator;
use Rakit\Validation\ErrorBag;
use App\Services\DocumentTypeService;
use App\Dtos\UserUsernamesDTO;

class UserApi
{
    private UserService $userService;
    private DocumentTypeService $documentService;
    private Validator $validator;

    public function __construct(UserService $userService, DocumentTypeService $documentService, Validator $validator)
    {
        $this->userService = $userService;
        $this->documentService = $documentService;
        $this->validator = $validator;
    }


    public function getAll($request, $response, $args)
    {
        $params = $request->getQueryParams();
        if (isset($params["start"]) && isset($params["length"])) { // If start and length are present as query params pagination is applied 
            $paginatedData = $this->userService->paginate($params);
            $payload = [ // DataTables expects a response object with the following structure
                'draw' => (int)($params['draw'] ?? 1),
                'recordsTotal' => $paginatedData['recordsTotal'],
                'recordsFiltered' => $paginatedData['recordsFiltered'],
                'data' => $paginatedData['data']
            ];

            $response->getBody()->write(json_encode($payload));
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        }

        $users = $this->userService->getAll();
        return ApiHelper::success($response, $users);
    }

    public function getUsersDetailed($request, $response, $args)
    {
        $params = $request->getQueryParams();
        if (isset($params["start"]) && isset($params["length"])) { // If start and length are present as query params pagination is applied 
            $paginatedData = $this->userService->paginateDetailed($params);
            $payload = [ // DataTables expects a response object with the following structure
                'draw' => (int)($params['draw'] ?? 1),
                'recordsTotal' => $paginatedData['recordsTotal'],
                'recordsFiltered' => $paginatedData['recordsFiltered'],
                'data' => $paginatedData['data']
            ];

            $response->getBody()->write(json_encode($payload));
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        }
        $users = $this->userService->getAll();
        $documents = $this->documentService->getAll();
        return ApiHelper::success($response, UserDocumentTypeDTO::from($users, $documents));
    }


    public function getUserById($request, $response, $args)
    {
        $user = $this->userService->getUser($args['id']);
        return ApiHelper::success($response, $user);
    }

    public function getUserWithDocumentType($request, $response, $args)
    {
        $users = $this->userService->getAll();
        $documents = $this->documentService->getAll();
        return ApiHelper::success($response, UserDocumentTypeDTO::from($users, $documents));
    }

    public function getDocumentType($request, $response, $args)
    {
        $documents = $this->userService->getDocumentTypes($this->documentService);
        return ApiHelper::success($response, $documents);
    }

    public function getUsernames($request, $response, $args)
    {
        $users = $this->userService->getAll();
        return ApiHelper::success($response, UserUsernamesDTO::from($users));
    }


    public function save($request, $response, $args)
    {
        $body = $request->getBody()->getContents();
        $data = json_decode($body, true);
        if (!$data) {
            return ApiHelper::error($response, ['message' => 'Invalid JSON input'], 400);
        }
        $isValid = $this->validate($data, $request->getMethod());

        if (is_object($isValid) && $isValid instanceof ErrorBag) {
            $errors = $isValid->toArray();
            return ApiHelper::error($response, ['message' => 'Invalid input data', 'details' => $errors], 400);
        }

        if ($request->getMethod() === "POST") {
            return ApiHelper::success(
                $response,
                $this->userService->save($data)
            );
        }
        if ($request->getMethod() === "PUT") {
            $data['id'] = $args['id'];
            return ApiHelper::success(
                $response,
                $this->userService->update($data)
            );
        }
    }

    public function delete($request, $response, $args)
    {
        $this->userService->delete($args['id'], $this->userService);
        return ApiHelper::success($response, ['message' => 'User deleted successfully']);
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
        $userId = $args['id'];
        $data = $this->userService->saveImage($img, $userId);
        $dataObj = ["image_url" => $data];
        return ApiHelper::success($response, $dataObj);
    }


    private function validate($data, $method)
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

    private function validateImageExt($file)
    {

        $fileExt = strtolower(pathinfo($file->getClientFileName(), PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        return in_array($fileExt, $allowed);
    }
}
