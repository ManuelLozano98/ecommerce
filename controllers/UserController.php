<?php

namespace App\Controllers;

use App\Services\UserService;
use Slim\Views\PhpRenderer;
use App\Exceptions\InvalidCredentialsException;
use App\Services\CartService;
use App\Services\RoleService;
use App\Services\UserRoleService;
use Exception;
use App\Models\Cart;
use App\Services\EmailChangeService;
use App\Services\MailService;
use App\Services\PasswordResetService;
use App\Services\ProductService;
use App\Services\ReviewService;
use App\Services\SaleService;
use DateTime;

class UserController
{
    private string $pageName = "users.php";
    private $renderer;
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

    public function __construct(PhpRenderer $renderer, UserService $userService, UserRoleService $userRoleService, RoleService $roleService, CartService $cartService, SaleService $saleService, ReviewService $reviewService, ProductService $productService, MailService $mailService, PasswordResetService $passwordResetService, EmailChangeService $emailChangeService)
    {
        $this->renderer = $renderer;
        $this->userService = $userService;
        $this->userRoleService = $userRoleService;
        $this->roleService = $roleService;
        $this->cartService = $cartService;
        $this->saleService = $saleService;
        $this->reviewService = $reviewService;
        $this->productService = $productService;
        $this->mailService = $mailService;
        $this->passwordResetService = $passwordResetService;
        $this->emailChangeService = $emailChangeService;
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

    public function indexLogin($request, $response, $args)
    {
        return $this->renderer->render($response, "login.php");
    }

    public function indexProfile($request, $response, $args)
    {
        $cart = $request->getAttribute("cart");
        $userId = $_SESSION["user"]["data"]->getId();
        $user = $this->userService->getUser($userId);
        $orders = $this->saleService->getPurchasesByUser($user->getId());
        $reviews = $this->reviewService->getActiveReviewsByUser($user->getId());
        $buys = [];
        $reviewData = [];
        foreach ($orders as $order) {
            if (strtolower($order->sales['status']->value) === "completed") {
                foreach ($order->items as &$item) {
                    $product = $this->productService->getProduct($item->getProductId());
                    $itemArray = $item->toArray();
                    $itemArray['name'] = $product->getName();
                    $itemArray['image'] = $product->getImage();
                    $item = $itemArray;
                }
                unset($item);
                $buys[] = $order;
            }
        }
        foreach ($reviews as $review) {
            $data = $review->toArray();
            $product = $this->productService->getProduct($review->getProductId());
            $data['image'] = $product->getImage();
            $data['name'] = $product->getName();
            $data['slug'] = $product->getSlug();
            $reviewData[] = $data;
        }
        return $this->renderer->render($response, "my-profile.php", ["userData" => $user, "cart" => $cart, 'orders' => $buys, "reviews" => $reviewData]);
    }

    public function indexAdmin($request, $response, $args)
    {
        $cart = $request->getAttribute("cart");
        $data = [
            "cart" => $cart
        ];

        return $this->renderer->render($response, "admin.php", $data);
    }

    public function login($request, $response, $args)
    {
        $body = $request->getBody()->getContents();
        $json = json_decode($body, true);

        $login = $this->sanitizeLogin($json["login"] ?? "");
        $password = $this->sanitizeLogin($json["password"] ?? "");

        $data = [
            "login" => $login,
            "password" => $password
        ];
        try {
            // Login
            $result = $this->userService->logIn($data);
            if (isset($_SESSION['user'])) {
                $redirect = $_SESSION["redirect_after_login"] ?? ROOT;
                unset($_SESSION["redirect_after_login"]);
                $userRoles = $this->userRoleService->getUserRolesbyUserId($_SESSION['user']['data']->getId());
                foreach ($userRoles as $userRole) {
                    $roles = $this->roleService->getRole($userRole->getRoleId());
                    $_SESSION['user']['roles'] = [$roles->getName()];
                }

                // Merge anonymous user cart with logged-in user
                $id = $_SESSION["user"]["data"]->getId() ?? 0;
                $sessionCart = $_SESSION['cart'] ?? [];
                $userCartItems = $this->cartService->getCartByUser($id);
                $userCart = [];

                foreach ($userCartItems as $item) {
                    $userCart[$item->getProduct()->getId()] = $item;
                }

                foreach ($sessionCart as $productId => $sessionItem) {
                    if (isset($userCart[$productId])) {
                        $existingItem = $userCart[$productId];
                        $newQty = $existingItem->getQuantity() + $sessionItem->getQuantity();
                        $existingItem->setQuantity($newQty);
                        $existingItem->setUserId($id);
                        $this->cartService->update($existingItem);
                    } else {
                        $cartData = [
                            "quantity" => $sessionItem->getQuantity(),
                            "product" => $sessionItem->getProduct()->toArray()
                        ];
                        $cartItem = new Cart($cartData);
                        $cartItem->setUserId($id);
                        $this->cartService->save($cartItem);
                    }
                }
                unset($_SESSION['cart']);
            }
            $response->getBody()->write(json_encode(['success' => $result, 'redirect' => $redirect]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (InvalidCredentialsException $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        } catch (Exception $e1) {
            $response->getBody()->write(json_encode(['error' => $e1->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }
    }

    public function logout($request, $response, $args)
    {
        session_destroy();
        return $response->withHeader('Location',  ROOT)->withStatus(302);
    }


    public function indexSignUp($request, $response, $args)
    {
        return $this->renderer->render($response, "sign-up.php");
    }

    public function indexForgotPassword($request, $response, $args)
    {
        return $this->renderer->render($response, "forgot-password.php");
    }

    public function indexRecoverPassword($request, $response, $args)
    {
        return $this->renderer->render($response, "recover-password.php");
    }

    public function signUp($request, $response, $args)
    {
        $data = $request->getParsedBody();
        $validKeys = ['name', 'username', 'email', 'password', 'repassword', 'terms'];

        $invalidKeys = array_diff(array_keys($data), $validKeys);

        if (!empty($invalidKeys)) {
            $result = [
                'success' => false,
                'message' => 'Invalid data'
            ];
            $response->getBody()->write(json_encode($result));
            return $response;
        }

        $errors = $this->validateNewUser($data);
        if (count($errors) > 0) {
            $result = [
                'success' => false,
                'message' => 'An error occurred while trying to validate',
                'details' => ['field' => $errors]
            ];
            $response->getBody()->write(json_encode($result));
            return $response;
        }

        try {
            $validKeys = ['name', 'username', 'email', 'password'];
            $validData = array_intersect_key(
                $data,
                array_flip($validKeys)
            );

            $user = $this->userService->save($validData);
            $this->mailService->createWelcomeEmail($user->getEmail(), $user->getUsername(), $user->getToken());
            $result = [
                'success' => true,
                'message' => 'The registration has been a success. We have sent an email to your email address',
            ];
            $response->getBody()->write(json_encode($result));
            return $response;
        } catch (Exception $e) {
            $result = [
                'success' => false,
                'message' => $e->getMessage(),
            ];
            $response->getBody()->write(json_encode($result));
            return $response;
        }
    }

    public function updateUserSettings($request, $response, $args)
    {
        $user = $_SESSION['user'];
        $result = [
            'success' => false,
            'message' => 'An error occurred while trying to update'
        ];

        $userId = $user['data']->getId();
        $userDb = $this->userService->getUser($userId);

        if (!$userDb) {
            $response->getBody()->write(json_encode($result));
            return $response;
        }

        $body = $request->getBody()->getContents();
        $json = json_decode($body, true);
        $errors = $this->validateSettings($json);

        if (count($errors) > 0) {
            $result = [
                'success' => false,
                'message' => 'An error occurred while trying to validate',
                'details' => ['field' => $errors]
            ];
        } else {
            $validKeys = ['name', 'phone', 'address'];
            $validData = array_intersect_key(
                $json,
                array_flip($validKeys)
            );

            if ($this->userService->updateProfile($userDb, $validData)) {
                $result = [
                    'success' => true,
                    'message' => 'The information has been updated correctly.'
                ];
            }
        }

        $response->getBody()->write(json_encode($result));
        return $response;
    }

    public function updateUserAvatar($request, $response, $args)
    {
        $body = $request->getUploadedFiles();

        if (empty($body)) {
            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'No avatar uploaded'
            ]));
            return $response->withHeader('Content-Type', 'application/json');
        }

        $result = [
            'success' => false,
            'message' => 'Invalid image'
        ];

        $img = $body["image"];
        $isValid = $this->validateImageExt($img);

        if (!$isValid) {
            $response->getBody()->write(json_encode($result));
            return $response->withHeader('Content-Type', 'application/json');
        }
        $userId = $_SESSION['user']['data']->getId();

        if ($this->userService->saveImage($img, $userId)) {
            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'The avatar has been updated correctly'
            ]));
            return $response->withHeader('Content-Type', 'application/json');
        }
        $response->getBody()->write(json_encode([
            'success' => false,
            'message' => 'An error occurred while trying to update the image'
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function updateUserSecurity($request, $response, $args)
    {
        $result = [
            'success' => false,
            'message' => 'An error occurred while trying to update'
        ];

        $user = $_SESSION['user'];
        $userId = $user['data']->getId();
        $userDb = $this->userService->getUser($userId);
        if (!$userDb) {
            $response->getBody()->write(json_encode($result));
            return $response;
        }
        $body = $request->getBody()->getContents();
        $json = json_decode($body, true);
        if (empty($json['password']) || !password_verify(trim($json['password']), $userDb->getPassword())) {
            $response->getBody()->write(json_encode($result));
            return $response;
        }

        $errors = $this->validateSecurity($json);
        if (count($errors) > 0) {
            $result = [
                'success' => false,
                'message' => 'An error occurred while trying to validate',
                'details' => ['field' => $errors]
            ];
        } else {
            if ($this->userService->updatePassword($userDb->getId(), $json['new-password'])) {
                $result = [
                    'success' => true,
                    'message' => 'The information has been updated correctly.'
                ];
                if ((!empty($json['email'])) && ($json['email'] !== $userDb->getEmail())) {
                    if ($this->userService->getUserByEmail($json['email'])) {
                        $result['message'] .= ' That email address is not available.';
                        $response->getBody()->write(json_encode($result));
                        return $response;
                    }
                    //CREATE TOKEN
                    $token = bin2hex(random_bytes(32));
                    $expired = (new DateTime('+1 day'))->format('Y-m-d H:i:s');

                    //CREATE EMAIL CHANGE REQUEST
                    $rawEmail = [
                        'user_id' => $userDb->getId(),
                        'token' => $token,
                        'old_email' => $userDb->getEmail(),
                        'new_email' => $json['email'],
                        'expires_at' => $expired
                    ];
                    if ($this->emailChangeService->save($rawEmail)) {
                        $this->mailService->changeEmail($json['email'], $userDb->getUsername(), $token);
                        $result = [
                            'success' => true,
                            'message' => 'We have sent an email to your new email address'
                        ];
                    }
                }
            }
        }

        $response->getBody()->write(json_encode($result));
        return $response;
    }

    public function forgotPassword($request, $response)
    {
        $body = $request->getBody()->getContents();
        $json = json_decode($body, true);

        $email = $json['email'];

        $result = [
            'success' => false,
            'message' => 'Invalid email.'
        ];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response->getBody()->write(json_encode($result));
            return $response->withHeader('Content-Type', 'application/json');
        }

        $result = [
            'success' => true,
            'message' => 'If the account exists, a recovery email has been sent.'
        ];

        $user = $this->userService->getUserByEmail($email);

        if (!$user) {
            $response->getBody()->write(json_encode($result));
            return $response->withHeader('Content-Type', 'application/json');
        }

        // CREATE PASSWORD RESET
        $token = bin2hex(random_bytes(32));

        $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $passwordToken = ['user_id' => $user->getId(), 'token' => $token, 'expires_at' => $expiresAt];

        $this->passwordResetService->save($passwordToken);

        $this->mailService->passwordResetEmail(
            $user->getEmail(),
            $user->getUsername(),
            $token
        );

        $response->getBody()->write(json_encode($result));

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function resetPassword($request, $response, $args)
    {
        $token = $args['token'];

        $result = [
            'success' => false,
            'message' => 'Invalid or expired token'
        ];

        if (!$token) {
            $response->getBody()->write(json_encode($result));
            return $response->withHeader('Content-Type', 'application/json');
        }

        $body = json_decode($request->getBody()->getContents(), true);

        $password = $body['password'];
        $confirmPassword = $body['confirmPassword'];

        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
            $result['message'] = 'Password must be at least 8 characters long 
            and include at least one uppercase letter, 
            one lowercase letter, 
            one number, 
            and one special character';

            $response->getBody()->write(json_encode($result));
            return $response->withHeader('Content-Type', 'application/json');
        }

        if ($password !== $confirmPassword) {
            $result['message'] = 'Passwords do not match';

            $response->getBody()->write(json_encode($result));
            return $response->withHeader('Content-Type', 'application/json');
        }

        $resetToken = $this->passwordResetService->getByToken($token);

        if (
            !$resetToken ||
            $resetToken->isUsed() ||
            new DateTime() > new DateTime($resetToken->getExpiredAt())
        ) {
            $response->getBody()->write(json_encode($result));
            return $response->withHeader('Content-Type', 'application/json');
        }

        $user = $this->userService->getUser($resetToken->getUserId());

        if (!$user) {
            $response->getBody()->write(json_encode($result));
            return $response->withHeader('Content-Type', 'application/json');
        }


        $this->userService->updatePassword($user->getId(), $password);

        $resetToken->setUsed(true);
        $rawToken = $resetToken->toArray();
        $this->passwordResetService->update($rawToken);

        $response->getBody()->write(json_encode([
            'success' => true,
            'message' => 'Password updated successfully'
        ]));

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function confirmEmail($request, $response, $args)
    {
        $token = $args['token'];

        if ($token) {
            $result = $this->emailChangeService->verifyToken($token);
            if ($result) {
                $email_change = $this->emailChangeService->getByToken($token);
                $email_change->setUsed(1);

                $this->emailChangeService->update($email_change->toArray());
                $user = $this->userService->getUser($email_change->getUserId());

                if ($this->userService->updateEmail($user, $email_change->getNewEmail())) {
                    return $this->renderer->render($response, "email-confirmed.php");
                }
            }
        }
        return $this->renderer->render($response, "email-confirmed.php", ['error' => 'Invalid or expired link']);
    }

    public function confirmUserEmail($request, $response, $args)
    {
        $token = $args['token'];

        if ($token) {
            $user = $this->userService->getUserByToken($token);
            if ($user) {
                $this->userService->activateAccount($token);
                return $this->renderer->render($response, "email-newuser-confirmed.php");
            }
        }
        return $this->renderer->render($response, "email-newuser-confirmed.php", ['error' => 'Invalid or expired link']);
    }

    public function resendEmailVerification($request, $response, $args)
    {
        $body = json_decode($request->getBody()->getContents(), true);
        $email = $body['email'];
        $result = [
            'success' => false,
            'message' => 'Invalid email address or no account associated with this email'
        ];

        $user = $this->userService->getUserByEmail($email);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !$user) {
            $response->getBody()->write(json_encode($result));
            return $response->withHeader('Content-Type', 'application/json');
        }

        if ($user->getActive()) {
            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'The account is now activated'
            ]));
            return $response->withHeader('Content-Type', 'application/json');
        }

        try {
            $user = $this->userService->updateToken($user);
            $this->mailService->createWelcomeEmail($user->getEmail(), $user->getUsername(), $user->getToken());
            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'We have sent an email to your email address'
            ]));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]));
            return $response->withHeader('Content-Type', 'application/json');
        }
    }

    private function validateImageExt($file)
    {

        $fileExt = strtolower(pathinfo($file->getClientFileName(), PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        return in_array($fileExt, $allowed);
    }

    private function validateSettings($data)
    {
        $errors = [];
        if (strlen($data['name']) > 100) {
            $errors['name'] = 'The name is too long.';
        }

        if (isset($data['phone']) && $data['phone'] !== '') {
            if (!preg_match('/^[6-9]\d{8}$/', $data['phone'])) {
                $errors['phone'] = 'Invalid phone.';
            }
        }

        if (strlen($data['address']) > 255) {
            $errors['address'] = 'The address is too long.';
        }


        return $errors;
    }

    private function validateSecurity($data)
    {
        $errors = [];
        if (!isset($data['new-password']) || (!isset($data['re-password']))) {
            $errors['password'] = 'Password is required.';
            return $errors;
        }

        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $data['new-password'])) {
            $errors['validator'] = 'Password must be at least 8 characters long 
            and include at least one uppercase letter, 
            one lowercase letter, 
            one number, 
            and one special character';
        }

        if ($data['new-password'] !== $data['re-password']) {
            $errors['validator-password'] = 'The passwords do not match.';
        }
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email.';
        }

        return $errors;
    }

    private function validateNewUser($data)
    {
        $errors = [];
        if (!preg_match("/^[a-zA-Z]{3,}$/", $data['name'])) {
            $errors['name'] = 'Name must contain only letters and be at least 3 characters long';
        }
        if (!preg_match("/^[a-zA-Z0-9](?!.*[_.]{2})[a-zA-Z0-9._]{2,18}[a-zA-Z0-9]$/", $data['username'])) {
            $errors['username'] = 'Username must be 4-20 characters long and can only contain letters, numbers, dots and underscores';
        }
        if (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $data['email'])) {
            $errors['email'] = 'Invalid email';
        }
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $data['password'])) {
            $errors['password'] = 'Password must be at least 8 characters long and include uppercase, lowercase, number and special character';
        }
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $data['repassword'])) {
            $errors['repassword'] = 'Password must be at least 8 characters long and include uppercase, lowercase, number and special character';
        }
        if ($data['password'] !== $data['repassword']) {
            $errors['passwords'] = 'Passwords do not match';
        }


        return $errors;
    }



    private function sanitizeLogin($text)
    {
        return strip_tags(trim($text));
    }
}
