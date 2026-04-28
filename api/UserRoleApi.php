<?php

namespace App\Api;

use App\Services\UserRoleService;
use App\Services\RoleService;
use App\Utils\ApiHelper;
use App\Utils\PaginationHelper;

class UserRoleApi
{
    private UserRoleService $userRoleService;
    private RoleService $roleService;

    public function __construct(UserRoleService $userRoleService, RoleService $roleService)
    {
        $this->userRoleService = $userRoleService;
        $this->roleService = $roleService;
    }

    public function getAll($request, $response, $args)
    {
        $userRole = $this->userRoleService->getUserRoles();
        return ApiHelper::success($response, $userRole);
    }
    public function getUserRolesDetailed($request, $response, $args)
    {
        $usersRoles = $this->userRoleService->getUserRolesDetailed();
        $params = $request->getQueryParams();
        if (isset($params["start"]) && isset($params["length"])) { // If start and length are present as query params pagination is applied
            $json = json_encode(["data" => array_values($usersRoles)], true);
            $data = PaginationHelper::paginateJSON($json, $params);
            $response->getBody()->write(json_encode($data));
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        }
        return ApiHelper::success($response, $usersRoles);
    }


    public function save($request, $response, $args)
    {
        $body = $request->getBody()->getContents();
        $data = json_decode($body, true);
        $data["user_id"] = $args["id"];
        $userRole = $this->userRoleService->save($data);
        return ApiHelper::success($response, $userRole);
    }

    public function delete($request, $response, $args)
    {
        $roles = $this->userRoleService->getUserRoleByRole($args["id"]);
        foreach ($roles as $userRole) {
            $this->userRoleService->delete($userRole->getId());
        }
        $this->roleService->delete($args['id']);
        return ApiHelper::success($response, ['message' => 'Role deleted successfully']);
    }
    public function deleteRolesByUserId($request, $response, $args)
    {
        $userRoles = $this->userRoleService->getUserRolesbyUserId($args["user_id"]);
        foreach ($userRoles as $userRole) {
            $this->userRoleService->delete($userRole->getId());
        }
        return ApiHelper::success($response, ['message' => 'All roles deleted successfully']);
    }
    public function deletebyUserIdAndRoleId($request, $response, $args)
    {
        $userRole = $this->userRoleService->getUserRolebyUserIdAndRoleId($args["id"], $args["role_id"]);
        $this->userRoleService->delete($userRole->getId());
        return ApiHelper::success($response, ['message' => 'Role deleted successfully']);
    }
}
