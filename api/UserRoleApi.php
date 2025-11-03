<?php

namespace App\Api;

use App\Services\UserRoleService;
use App\Utils\ApiHelper;

class UserRoleApi
{
    private UserRoleService $userRoleService;
    public function __construct()
    {
        $this->userRoleService = new UserRoleService();
    }

    public function getUserRoles($request, $response, $args)
    {
        $userRole = $this->userRoleService->getUserRoles();
        return ApiHelper::success($response, $userRole);
    }


    public function saveUserRole($request, $response, $args)
    {
        $body = $request->getBody()->getContents();
        $data = json_decode($body, true);
        $data["user_id"] = $args["id"];
        $userRole = $this->userRoleService->saveUserRole($request->getMethod(), $data);
        return ApiHelper::success($response, $userRole);
    }

    public function deleteUserRoles($request, $response, $args)
    {
        $roleService = $this->userRoleService->getRoleService();
        $roles = $this->userRoleService->getUserRoleByRole($args["id"]);
        foreach ($roles as $role) {
            $this->userRoleService->deleteUserRole($role->getId());
        }
        $roleService->deleteRole($args['id']);
        return ApiHelper::success($response, ['message' => 'Role deleted successfully']);
    }
}
