<?php

namespace App\Api;

use App\Services\RoleService;
use App\Models\Role;
use App\Utils\ApiHelper;
use Rakit\Validation\Validator;
use Rakit\Validation\ErrorBag;
use App\Utils\PaginationHelper;

class RoleApi
{
    private RoleService $roleService;
    private Validator $validator;

    public function __construct()
    {
        $this->roleService = new RoleService();
        $this->validator = new Validator();
    }


    public function getRoles($request, $response, $args)
    {
        $params = $request->getQueryParams();
        if (isset($params["start"]) && isset($params["length"])) { // If start and length are present as query params pagination is applied 
            $tableName = "roles";
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

        $roles = $this->roleService->getRoles();
        if (!$roles) {
            return ApiHelper::error($response, ['message' => 'No roles found'], 404);
        }
        return ApiHelper::success($response, $roles);
    }


    public function getRoleById($request, $response, $args)
    {
        $role = $this->roleService->getRole($args['id']);
        return ApiHelper::success($response, $role);
    }


    public function getRolesName($request, $response, $args)
    {
        $roles = $this->roleService->getRolesName();
        $role = array_map(fn(Role $r) => [
            'id' => (int) $r->getId(),
            'name' => $r->getName()
        ], $roles);
        return ApiHelper::success($response, $role);
    }


    public function saveRole($request, $response, $args)
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

        $isValid = $this->validateRole($data);
        if (is_object($isValid) && $isValid instanceof ErrorBag) {
            $errors = $isValid->toArray();
            return ApiHelper::error($response, ['message' => 'Invalid input data', 'details' => $errors], 400);
        } else {
            $data = $this->roleService->saveRole($method, $data);
            return ApiHelper::success($response, $data);
        }
    }

    private function validateRole($data)
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
