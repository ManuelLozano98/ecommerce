<?php

namespace App\Controllers;

use Slim\Views\PhpRenderer;

class RoleController
{
    private string $pageName = "roles.php";
    private $renderer;

    public function __construct(PhpRenderer $renderer)
    {
        $this->renderer = $renderer;
    }
    public function index($request, $response, $args)
    {
        return $this->renderer->render($response, $this->pageName);
    }
    public function setPageName(string $pageName): void
    {
        $this->pageName = $pageName;
    }
    public function getPageName(): string
    {
        return $this->pageName;
    }
}
