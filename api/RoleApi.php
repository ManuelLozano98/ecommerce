<?php

namespace App\Api;

use App\Dtos\RoleNameDTO;
use App\Services\RoleService;
use App\Utils\ApiHelper;
use Rakit\Validation\Validator;
use Rakit\Validation\ErrorBag;

class RoleApi
{
    private RoleService $roleService;
    private Validator $validator;

    public function __construct(RoleService $roleService, Validator $validator)
    {
        $this->roleService = $roleService;
        $this->validator = $validator;
    }


    public function getAll($request, $response, $args)
    {
        $params = $request->getQueryParams();
        if (isset($params["start"]) && isset($params["length"])) { // If start and length are present as query params pagination is applied 
            $paginatedData = $this->roleService->paginate($params);
            $payload = [ // DataTables expects a response object with the following structure
                'draw' => (int)($params['draw'] ?? 1),
                'recordsTotal' => $paginatedData['recordsTotal'],
                'recordsFiltered' => $paginatedData['recordsFiltered'],
                'data' => $paginatedData['data']
            ];

            $response->getBody()->write(json_encode($payload));
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        }

        $roles = $this->roleService->getAll();
        return ApiHelper::success($response, $roles);
    }


    public function getRoleById($request, $response, $args)
    {
        $role = $this->roleService->getRole($args['id']);
        return ApiHelper::success($response, $role);
    }


    public function getRolesName($request, $response, $args)
    {
        $roles = $this->roleService->getAll();
        return ApiHelper::success($response, RoleNameDTO::from($roles));
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
                $this->roleService->save($data)
            );
        }
        if ($request->getMethod() === "PUT") {
            $data['id'] = $args['id'];
            return ApiHelper::success(
                $response,
                $this->roleService->update($data)
            );
        }
    }

    public function delete($request, $response, $args)
    {
        $this->roleService->delete($args['id']);
        return ApiHelper::success($response, ['message' => 'Role deleted successfully']);
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
