<?php

use DI\Container;
use App\Repositories\Contracts\CartRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\CartRepository;
use App\Repositories\Contracts\RoleRepositoryInterface;
use App\Repositories\Contracts\UserRoleRepositoryInterface;
use App\Repositories\UserRepository;
use App\Repositories\ProductRepository;
use App\Repositories\RoleRepository;
use App\Repositories\UserRoleRepository;
use App\Services\CartService;
use App\Services\UserRoleService;
use App\Services\UserService;
use Slim\Views\PhpRenderer;


$container = new Container();

$container->set(CartRepositoryInterface::class, DI\autowire(CartRepository::class));
$container->set(UserRepositoryInterface::class, DI\autowire(UserRepository::class));
$container->set(ProductRepositoryInterface::class, DI\autowire(ProductRepository::class));
$container->set(UserService::class, DI\autowire());
$container->set(UserRoleService::class, DI\autowire());
$container->set(PhpRenderer::class, function () {
    return new PhpRenderer(__DIR__ . '/views/');
});
$container->set(RoleRepositoryInterface::class, DI\autowire(RoleRepository::class));
$container->set(UserRoleRepositoryInterface::class, DI\autowire(UserRoleRepository::class));
$container->set(UserRoleService::class, DI\autowire());
$container->set(CartService::class, DI\autowire());
return $container;
