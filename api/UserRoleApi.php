<?php

namespace App\Api;

use App\Services\UserRoleService;
use App\Utils\ApiHelper;
use App\Utils\PaginationHelper;

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
    public function getUserRolesDetailed($request, $response, $args)
    {
        $usersRoles = $this->userRoleService->getUserRolesDetailedJSON();
        $params = $request->getQueryParams();
        if (isset($params["start"]) && isset($params["length"])) { // If start and length are present as query params pagination is applied
            $data = PaginationHelper::paginateJSON($usersRoles, $params);
            $response->getBody()->write(json_encode($data));
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        }
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
    public function deleteRolesByUserId($request, $response, $args)
    {
        $userRoles = $this->userRoleService->getUserRolesbyUserId($args["user_id"]);
        foreach ($userRoles as $userRole) {
            $this->userRoleService->deleteUserRole($userRole->getId());
        }
        return ApiHelper::success($response, ['message' => 'All roles deleted successfully']);
    }
    public function deletebyUserIdAndRoleId($request, $response, $args)
    {
        $userRole = $this->userRoleService->getUserRolebyUserIdAndRoleId($args["id"], $args["role_id"]);
        $this->userRoleService->deleteUserRole($userRole->getId());
        return ApiHelper::success($response, ['message' => 'Role deleted successfully']);
    }
}
