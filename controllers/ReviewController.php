<?php

namespace App\Controllers;

use App\Exceptions\NotFoundException;
use App\Services\ReviewService;
use Slim\Views\PhpRenderer;
use Rakit\Validation\Validator;
use Rakit\Validation\ErrorBag;

class ReviewController
{
    private string $pageName = "reviews.php";
    private $renderer;
    private ReviewService $reviewService;
    private Validator $validator;

    public function __construct(PhpRenderer $renderer, ReviewService $reviewService, Validator $validator)
    {
        $this->renderer = $renderer;
        $this->reviewService = $reviewService;
        $this->validator = $validator;
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

    public function preReviewProduct($request, $response, $args)
    {

        $body = $request->getBody()->getContents();
        $data = json_decode($body, true);
        if (!isset($_SESSION["user"])) {
            $actual_link = $data["url"];
            $_SESSION["redirect_after_login"] = $actual_link;

            $response->getBody()->write(json_encode([
                'success' => false,
                'redirect' => ROOT . "/login",
            ]));
            return $response;
        }
        try {
            $review = $this->reviewService->getReviewByProductIdAndUserId($data["product_id"], $_SESSION["user"]["data"]->getId());
            $response->getBody()->write(json_encode([
                'success' => true,
                "review" => $review
            ]));
            return $response;
        } catch (NotFoundException $exception) {
            $response->getBody()->write(json_encode([
                'success' => true,
                "review" => null
            ]));
            return $response;
        }
    }

    public function reviewProduct($request, $response, $args)
    {
        $body = $request->getBody()->getContents();
        $data = json_decode($body, true);
        if (!$data) {
            $response->getBody()->write(json_encode([
                'data' => null
            ]));

            return $response
                ->withHeader('Content-Type', 'application/json');
        }

        $isValid = $this->validate($data);
        if (is_object($isValid) && $isValid instanceof ErrorBag) {
            $errors = $isValid->toArray();
            $response->getBody()->write(json_encode(['message' => 'Invalid input data', 'details' => $errors]));
            return $response;
        }
        try {
            $reviewed = $this->reviewService->getReviewByProductIdAndUserId($data["product_id"], $_SESSION["user"]["data"]->getId());
            if ($reviewed) {
                $data["id"] = $reviewed->getId();
                $res = $this->reviewService->update($data);
                $response->getBody()->write(json_encode([
                    'status' => 'success',
                    'data' => $res
                ]));
                return $response;
            }
            $res = $this->reviewService->save($data);
            $response->getBody()->write(json_encode([
                'status' => 'success',
                'data' => $res
            ]));
            return $response;
        } catch (NotFoundException $exception) {
            $res = $this->reviewService->save($data);
            $response->getBody()->write(json_encode([
                'status' => 'success',
                'data' => $res
            ]));
            return $response;
        }
    }

    private function validate($data)
    {
        $validator = $this->validator->make($data, [
            'active' => 'nullable|boolean',
            'title' => 'nullable|regex:/^.{3,255}$/u',
            'comment' => 'nullable|regex:/^.{3,255}$/u',
            'rating' => [function ($value) {
                return preg_match('/^(0\.5|[1-4](\.5)?|5(\.0)?)$/', $value);
            }],
            'product_id' => 'required|numeric',
            'user_id' => 'required|numeric'
        ]);
        $validator->validate();
        if ($validator->fails()) {
            return $validator->errors();
        }
        return true;
    }
}
