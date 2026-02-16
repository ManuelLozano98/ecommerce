<?php

namespace App\Api;

use App\Services\ReviewService;
use App\Utils\ApiHelper;
use Rakit\Validation\Validator;
use Rakit\Validation\ErrorBag;

class ReviewApi
{
    private ReviewService $reviewService;
    private Validator $validator;

    public function __construct(ReviewService $reviewService, Validator $validator)
    {
        $this->reviewService = $reviewService;
        $this->validator = $validator;
    }


    public function getAll($request, $response, $args)
    {
        $params = $request->getQueryParams();
        if (isset($params["start"]) && isset($params["length"])) { // If start and length are present as query params pagination is applied 
            $paginatedData = $this->reviewService->paginate($params);
            $payload = [ // DataTables expects a response object with the following structure
                'draw' => (int)($params['draw'] ?? 1),
                'recordsTotal' => $paginatedData['recordsTotal'],
                'recordsFiltered' => $paginatedData['recordsFiltered'],
                'data' => $paginatedData['data']
            ];

            $response->getBody()->write(json_encode($payload));
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        }

        $reviews = $this->reviewService->getAll();
        return ApiHelper::success($response, $reviews);
    }

    public function getDetailedReviews($request, $response, $args)
    {
        $params = $request->getQueryParams();
        if (isset($params["start"]) && isset($params["length"])) { // If start and length are present as query params pagination is applied 
            $paginatedData = $this->reviewService->paginateDetailed($params);
            $payload = [ // DataTables expects a response object with the following structure
                'draw' => (int)($params['draw'] ?? 1),
                'recordsTotal' => $paginatedData['recordsTotal'],
                'recordsFiltered' => $paginatedData['recordsFiltered'],
                'data' => $paginatedData['data']
            ];

            $response->getBody()->write(json_encode($payload));
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        }

        $reviews = $this->reviewService->getAll();
        return ApiHelper::success($response, $reviews);
    }


    public function getReviewById($request, $response, $args)
    {
        $review = $this->reviewService->getReview($args['id']);
        return ApiHelper::success($response, $review);
    }

    public function getUserReviews($request, $response, $args)
    {
        $reviews = $this->reviewService->getReviewsByUser($args['id']);
        return ApiHelper::success($response, $reviews);
    }
    public function getProductReviews($request, $response, $args)
    {
        $reviews = $this->reviewService->getReviewsByProduct($args['id']);
        return ApiHelper::success($response, $reviews);
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
            return ApiHelper::success($response, $this->reviewService->save($data));
        }
        if ($request->getMethod() === "PUT") {
            $data['id'] = $args['id'];
            return ApiHelper::success(
                $response,
                $this->reviewService->update($data)
            );
        }
    }

    public function delete($request, $response, $args)
    {
        $this->reviewService->delete($args['id']);
        return ApiHelper::success($response, ['message' => 'Review deleted successfully']);
    }

    public function deleteReviewsbyUser($request, $response, $args)
    {
        $reviews = $this->reviewService->getReviewsByUser($args['id']);
        foreach ($reviews as $review) {
            $this->reviewService->delete($review->getId());
        }
        return ApiHelper::success($response, ['message' => 'All reviews deleted successfully']);
    }

    public function deleteReviewbyUser($request, $response, $args)
    {
        $reviewId = (int) $args['review_id'];
        $review = $this->reviewService->getUserReview($args['user_id'], $reviewId);
        return $this->delete($request, $response, ['id' => $review->getId()]);
    }
    public function deleteReviewsbyProduct($request, $response, $args)
    {
        $reviews = $this->reviewService->getReviewsByProduct($args['id']);
        foreach ($reviews as $review) {
            $this->reviewService->delete($review->getId());
        }
        return ApiHelper::success($response, ['message' => 'All reviews deleted successfully']);
    }

    public function deleteReviewbyProduct($request, $response, $args)
    {
        $reviewId = (int) $args['review_id'];
        $review = $this->reviewService->getProductReview($args['product_id'], $reviewId);
        return $this->delete($request, $response, ['id' => $review->getId()]);
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
