<?php

namespace Tests\Controllers;

use App\Controllers\UserController;
use App\Exceptions\InvalidCredentialsException;
use App\Services\CartService;
use App\Services\EmailChangeService;
use App\Services\MailService;
use App\Services\PasswordResetService;
use App\Services\ProductService;
use App\Services\ReviewService;
use App\Services\RoleService;
use App\Services\SaleService;
use App\Services\UserRoleService;
use App\Services\UserService;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Slim\Views\PhpRenderer;

class UserControllerTest extends TestCase
{
    private UserController $controller;

    private PhpRenderer $renderer;
    private UserService $userService;
    private UserRoleService $userRoleService;
    private RoleService $roleService;
    private CartService $cartService;
    private SaleService $saleService;
    private ReviewService $reviewService;
    private ProductService $productService;
    private MailService $mailService;
    private PasswordResetService $passwordResetService;
    private EmailChangeService $emailChangeService;

    protected function setUp(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        $_SESSION = [];

        // if (!defined('ROOT')) {
        //     define('ROOT', '/');
        // }

        $this->renderer = $this->createMock(PhpRenderer::class);
        $this->userService = $this->createMock(UserService::class);
        $this->userRoleService = $this->createMock(UserRoleService::class);
        $this->roleService = $this->createMock(RoleService::class);
        $this->cartService = $this->createMock(CartService::class);
        $this->saleService = $this->createMock(SaleService::class);
        $this->reviewService = $this->createMock(ReviewService::class);
        $this->productService = $this->createMock(ProductService::class);
        $this->mailService = $this->createMock(MailService::class);
        $this->passwordResetService = $this->createMock(PasswordResetService::class);
        $this->emailChangeService = $this->createMock(EmailChangeService::class);

        $this->controller = new UserController(
            $this->renderer,
            $this->userService,
            $this->userRoleService,
            $this->roleService,
            $this->cartService,
            $this->saleService,
            $this->reviewService,
            $this->productService,
            $this->mailService,
            $this->passwordResetService,
            $this->emailChangeService
        );
    }

    protected function tearsDown() {}

    private function createResponseMock(): array
    {
        $stream = $this->createMock(StreamInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $response->method('getBody')
            ->willReturn($stream);

        $response->method('withHeader')
            ->willReturnSelf();

        $response->method('withStatus')
            ->willReturnSelf();

        return [$response, $stream];
    }

    public function testSetAndGetPageName(): void
    {
        $this->controller->setPageName('custom.php');

        $this->assertEquals(
            'custom.php',
            $this->controller->getPageName()
        );
    }

    public function testIndexRendersDefaultPage(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $this->renderer
            ->expects($this->once())
            ->method('render')
            ->with($response, 'users.php')
            ->willReturn($response);

        $this->controller->index($request, $response, []);
    }

    public function testIndexLoginRendersLoginPage(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $this->renderer
            ->expects($this->once())
            ->method('render')
            ->with($response, 'login.php')
            ->willReturn($response);

        $this->controller->indexLogin($request, $response, []);
    }

    public function testIndexSignUpRendersSignUpPage(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $this->renderer
            ->expects($this->once())
            ->method('render')
            ->with($response, 'sign-up.php')
            ->willReturn($response);

        $this->controller->indexSignUp($request, $response, []);
    }

    public function testIndexForgotPasswordRendersPage(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $this->renderer
            ->expects($this->once())
            ->method('render')
            ->with($response, 'forgot-password.php')
            ->willReturn($response);

        $this->controller->indexForgotPassword($request, $response, []);
    }

    public function testIndexRecoverPasswordRendersPage(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $this->renderer
            ->expects($this->once())
            ->method('render')
            ->with($response, 'recover-password.php')
            ->willReturn($response);

        $this->controller->indexRecoverPassword($request, $response, []);
    }

    public function testLogoutRedirectsToRoot(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);

        $response = $this->createMock(ResponseInterface::class);

        $response->expects($this->once())
            ->method('withHeader')
            ->with('Location', ROOT)
            ->willReturnSelf();

        $response->expects($this->once())
            ->method('withStatus')
            ->with(302)
            ->willReturnSelf();

        $this->controller->logout($request, $response, []);
    }

    public function testSignUpReturnsErrorWhenInvalidFieldsAreSent(): void
    {
        [$response, $stream] = $this->createResponseMock();

        $request = $this->createMock(ServerRequestInterface::class);

        $request->method('getParsedBody')
            ->willReturn([
                'invalid' => 'field'
            ]);

        $stream->expects($this->once())
            ->method('write');

        $this->controller->signUp($request, $response, []);
    }

    public function testSignUpReturnsValidationErrors(): void
    {
        [$response, $stream] = $this->createResponseMock();

        $request = $this->createMock(ServerRequestInterface::class);

        $request->method('getParsedBody')
            ->willReturn([
                'name' => 'a',
                'username' => 'b',
                'email' => 'bad-email',
                'password' => '123',
                'repassword' => '123',
                'terms' => true
            ]);

        $stream->expects($this->once())
            ->method('write');

        $this->controller->signUp($request, $response, []);
    }

    public function testForgotPasswordReturnsErrorWhenEmailInvalid(): void
    {
        [$response, $stream] = $this->createResponseMock();

        $request = $this->createMock(ServerRequestInterface::class);
        $body = $this->createMock(StreamInterface::class);

        $request->method('getBody')->willReturn($body);

        $body->method('getContents')
            ->willReturn(json_encode([
                'email' => 'invalid-email'
            ]));

        $stream->expects($this->once())
            ->method('write');

        $this->controller->forgotPassword($request, $response);
    }

    public function testForgotPasswordReturnsSuccessWhenUserDoesNotExist(): void
    {
        [$response, $stream] = $this->createResponseMock();

        $request = $this->createMock(ServerRequestInterface::class);
        $body = $this->createMock(StreamInterface::class);

        $request->method('getBody')->willReturn($body);

        $body->method('getContents')
            ->willReturn(json_encode([
                'email' => 'test@test.com'
            ]));

        $this->userService
            ->expects($this->once())
            ->method('getUserByEmail')
            ->willReturn(null);

        $stream->expects($this->once())
            ->method('write');

        $this->controller->forgotPassword($request, $response);
    }

    public function testLoginReturns401WhenCredentialsAreInvalid(): void
    {
        [$response, $stream] = $this->createResponseMock();

        $request = $this->createMock(ServerRequestInterface::class);
        $body = $this->createMock(StreamInterface::class);

        $request->method('getBody')->willReturn($body);

        $body->method('getContents')
            ->willReturn(json_encode([
                'login' => 'admin',
                'password' => 'bad-password'
            ]));

        $this->userService
            ->expects($this->once())
            ->method('logIn')
            ->willThrowException(
                new InvalidCredentialsException('Invalid credentials')
            );

        $stream->expects($this->once())
            ->method('write');

        $this->controller->login(
            $request,
            $response,
            []
        );
    }
}
