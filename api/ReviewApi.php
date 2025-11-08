<?php

namespace App\Api;

use App\Services\ReviewService;
use App\Services\ProductService;
use App\Services\UserService;
use App\Models\Review;
use App\Utils\ApiHelper;
use Rakit\Validation\Validator;
use Rakit\Validation\ErrorBag;
use App\Utils\PaginationHelper;

class ReviewApi
{
    private ReviewService $reviewService;
    private UserService $userServcice;
    private ProductService $productService;
    private Validator $validator;

    public function __construct()
    {
        $this->reviewService = new ReviewService();
        $this->userServcice = new UserService();
        $this->productService = new ProductService();
        $this->validator = new Validator();
    }


    public function getReviews($request, $response, $args)
    {
        $params = $request->getQueryParams();
        if (isset($params["start"]) && isset($params["length"])) { // If start and length are present as query params pagination is applied 
            $tableName = "reviews";
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

        $reviews = $this->reviewService->getReviews();
        if (!$reviews) {
            return ApiHelper::error($response, ['message' => 'No reviews found'], 404);
        }
        return ApiHelper::success($response, $reviews);
    }

    public function getDetailedReviews($request, $response, $args)
    {
        $params = $request->getQueryParams();
        if (isset($params["start"]) && isset($params["length"])) { // If start and length are present as query params pagination is applied 
            $selectFields = [
                'r.id',
                'r.user_id',
                'r.product_id',
                'r.rating',
                'r.title',
                'r.comment',
                'r.created_at',
                'r.updated_at',
                'r.active',
                'u.username AS username',
                'p.name AS product_name'
            ];

            $columns = [
                'r.id',
                'u.username',
                'p.name',
                'r.rating',
                'r.title',
                'r.comment',
                'r.created_at',
                'r.updated_at',
                'r.active',
                'r.user_id',
                'r.product_id',
            ];

            $fromClause = 'reviews r JOIN users u ON r.user_id = u.id JOIN products p ON r.product_id = p.id';
            $search = $params['search']['value'] ?? '';

            $data = PaginationHelper::makeCustom($params, $fromClause, $columns, $selectFields);
            $totalRecords = PaginationHelper::getTotalRecordsCustom($fromClause);
            $filteredRecords = PaginationHelper::getFilteredCustomCount($search, $fromClause, $columns);


            $payload = [
                'draw' => (int)($params['draw'] ?? 1),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data
            ];

            $response->getBody()->write(json_encode($payload));
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        }

        $products = $this->productService->getProductsDetailed();
        return ApiHelper::success($response, $products);
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

    public function saveReview($request, $response, $args)
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

        $isValid = $this->validateReview($data);
        if (is_object($isValid) && $isValid instanceof ErrorBag) {
            $errors = $isValid->toArray();
            return ApiHelper::error($response, ['message' => 'Invalid input data', 'details' => $errors], 400);
        } else {
            $data = $this->reviewService->saveReview($method, $data, $this->userServcice, $this->productService);
            return ApiHelper::success($response, $data);
        }
    }

    public function deleteReview($request, $response, $args)
    {
        $this->reviewService->deleteReview($args['id']);
        return ApiHelper::success($response, ['message' => 'Review deleted successfully']);
    }

    public function deleteReviewsbyUser($request, $response, $args)
    {
        $reviews = $this->reviewService->getReviewsByUser($args['id']);
        foreach ($reviews as $review) {
            $this->reviewService->deleteReview($review->getId());
        }
        return ApiHelper::success($response, ['message' => 'All reviews deleted successfully']);
    }

    public function deleteReviewbyUser($request, $response, $args)
    {
        $reviewId = (int) $args['review_id'];
        $review = $this->reviewService->getUserReview($args['user_id'], $reviewId);
        return $this->deleteReview($request, $response, ['id' => $review->getId()]);
    }
    public function deleteReviewsbyProduct($request, $response, $args)
    {
        $reviews = $this->reviewService->getReviewsByProduct($args['id']);
        foreach ($reviews as $review) {
            $this->reviewService->deleteReview($review->getId());
        }
        return ApiHelper::success($response, ['message' => 'All reviews deleted successfully']);
    }

    public function deleteReviewbyProduct($request, $response, $args)
    {
        $reviewId = (int) $args['review_id'];
        $review = $this->reviewService->getProductReview($args['product_id'], $reviewId);
        return $this->deleteReview($request, $response, ['id' => $review->getId()]);
    }

    private function validateReview($data)
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
