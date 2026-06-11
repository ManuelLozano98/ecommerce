<?php

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use App\Services\CartService;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Category;

use App\Exceptions\NotFoundException;
use App\Exceptions\InsertException;
use App\Exceptions\UpdateException;
use App\Exceptions\DeleteException;

use App\Repositories\Contracts\CartRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\ProductInformationRepositoryInterface;

class CartServiceTest extends TestCase
{
    private CartRepositoryInterface $cartRepository;
    private UserRepositoryInterface $userRepository;
    private ProductRepositoryInterface $productRepository;
    private CategoryRepositoryInterface $categoryRepository;
    private ProductInformationRepositoryInterface $productInformationRepository;

    private CartService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cartRepository =
            $this->createMock(CartRepositoryInterface::class);

        $this->userRepository =
            $this->createMock(UserRepositoryInterface::class);

        $this->productRepository =
            $this->createMock(ProductRepositoryInterface::class);

        $this->categoryRepository =
            $this->createMock(CategoryRepositoryInterface::class);

        $this->productInformationRepository =
            $this->createMock(ProductInformationRepositoryInterface::class);

        $this->service = new CartService(
            $this->cartRepository,
            $this->userRepository,
            $this->productRepository,
            $this->categoryRepository,
            $this->productInformationRepository
        );
    }

    private function createCart(): Cart
    {
        return new Cart([
            'id' => 1,
            'user_id' => 10,
            'quantity' => 2,
            'product' => [
                'id' => 100,
                'price' => 50,
                'category_id' => 5
            ]
        ]);
    }

    public function testGetCartThrowsExceptionWhenCartDoesNotExist(): void
    {
        $this->cartRepository
            ->method('findById')
            ->willReturn(null);

        $this->expectException(NotFoundException::class);

        $this->service->getCart(1);
    }

    public function testSaveThrowsExceptionWhenUserNotExists(): void
    {
        $cart = $this->createCart();

        $this->userRepository
            ->method('findById')
            ->willReturn(null);

        $this->expectException(NotFoundException::class);

        $this->service->save($cart);
    }

    public function testSaveThrowsExceptionWhenProductNotExists(): void
    {
        $cart = $this->createCart();

        $this->userRepository
            ->method('findById')
            ->willReturn(new \App\Models\User([
                'id' => 10
            ]));

        $this->productRepository
            ->method('findById')
            ->willReturn(null);

        $this->expectException(NotFoundException::class);

        $this->service->save($cart);
    }


    public function testSaveReturnsCartWhenInsertSucceeds(): void
    {
        $cart = $this->createCart();

        $this->userRepository
            ->method('findById')
            ->willReturn(new \App\Models\User([
                'id' => 1
            ]));

        $this->productRepository
            ->method('findById')
            ->willReturn(new Product());

        $this->cartRepository
            ->method('insert')
            ->willReturn($cart);

        $result = $this->service->save($cart);

        $this->assertSame($cart, $result);
    }

    public function testDeleteThrowsExceptionWhenRepositoryFails(): void
    {
        $cart = $this->createCart();

        $product = $this->createMock(\App\Models\Product::class);
        $category = $this->createMock(\App\Models\Category::class);

        $product->method('getId')->willReturn(1);
        $product->method('getCategoryId')->willReturn(1);
        $product->method('getPrice')->willReturn(100);

        $this->cartRepository
            ->method('findById')
            ->willReturn($cart);

        $this->productRepository
            ->method('findById')
            ->willReturn($product);

        $this->categoryRepository
            ->method('findById')
            ->willReturn($category);

        $this->productInformationRepository
            ->method('findByProductId')
            ->willReturn(null);

        $this->cartRepository
            ->method('delete')
            ->willReturn(false);

        $this->expectException(DeleteException::class);

        $this->service->delete(1);
    }

    public function testDeleteSucceeds(): void
    {
        $cart = $this->createCart();

        $this->cartRepository
            ->method('findById')
            ->willReturn($cart);

        $this->productRepository
            ->method('findById')
            ->willReturn($cart->getProduct());

        $category = new Category([
            'id' => 5,
            'name' => 'Electronics'
        ]);

        $this->categoryRepository
            ->method('findById')
            ->willReturn($category);

        $this->productInformationRepository
            ->method('findByProductId')
            ->willReturn(null);

        $this->cartRepository
            ->method('delete')
            ->willReturn(true);

        $this->service->delete(1);

        $this->assertTrue(true);
    }

    public function testDeleteUserCartThrowsException(): void
    {
        $this->cartRepository
            ->method('deleteUserCart')
            ->willReturn(false);

        $this->expectException(DeleteException::class);

        $this->service->deleteUserCart(10);
    }

    public function testDeleteUserCartSucceeds(): void
    {
        $this->cartRepository
            ->method('deleteUserCart')
            ->willReturn(true);

        $this->service->deleteUserCart(10);

        $this->assertTrue(true);
    }

    public function testDeleteByUserAndProductThrowsException(): void
    {
        $this->cartRepository
            ->method('deleteByUserAndProduct')
            ->willReturn(false);

        $this->expectException(DeleteException::class);

        $this->service->deleteByUserAndProduct(10, 100);
    }

    public function testDeleteByUserAndProductReturnsTrue(): void
    {
        $this->cartRepository
            ->method('deleteByUserAndProduct')
            ->willReturn(true);

        $result = $this->service->deleteByUserAndProduct(10, 100);

        $this->assertTrue($result);
    }


    public function testUpdateReturnsUpdatedCart(): void
    {
        $cart = $this->createCart();

        $product = $this->createMock(\App\Models\Product::class);
        $category = $this->createMock(\App\Models\Category::class);

        $product->method('getId')->willReturn(1);
        $product->method('getCategoryId')->willReturn(1);
        $product->method('getPrice')->willReturn(100);

        $this->cartRepository
            ->method('findById')
            ->willReturn($cart);

        $this->productRepository
            ->method('findById')
            ->willReturn($product);

        $this->categoryRepository
            ->method('findById')
            ->willReturn($category);

        $this->productInformationRepository
            ->method('findByProductId')
            ->willReturn(null);

        $this->cartRepository
            ->method('update')
            ->willReturn($cart);

        $result = $this->service->update($cart);

        $this->assertSame($cart, $result);
    }

    public function testGetCartForCurrentUserReturnsSessionCart(): void
    {
        $_SESSION['cart'] = ['item'];

        $result = $this->service->getCartForCurrentUser();

        $this->assertEquals(['item'], $result);
    }

    public function testGetCartAppliesDiscount(): void
    {
        $product = new Product([
            'id' => 100,
            'price' => 50,
            'category_id' => 5
        ]);

        $cart = new Cart([
            'id' => 1,
            'user_id' => 10,
            'quantity' => 2,
            'product' => [
                'id' => 100
            ]
        ]);

        $category = new \App\Models\Category([
            'id' => 5,
            'name' => 'Electronics'
        ]);

        $discountInfo = $this->createMock(
            \App\Models\ProductInformation::class
        );

        $discountInfo
            ->method('getDiscount')
            ->willReturn(20.0);

        $this->cartRepository
            ->method('findById')
            ->willReturn($cart);

        $this->productRepository
            ->method('findById')
            ->with(100)
            ->willReturn($product);

        $this->categoryRepository
            ->method('findById')
            ->with(5)
            ->willReturn($category);

        $this->productInformationRepository
            ->method('findByProductId')
            ->with(100)
            ->willReturn($discountInfo);

        $result = $this->service->getCart(1);

        $this->assertEquals(40, $result->getProduct()->getPrice());
    }

    public function testGetCartWithoutDiscountKeepsOriginalPrice(): void
    {
        $product = new Product([
            'id' => 100,
            'price' => 50,
            'category_id' => 5
        ]);

        $cart = new Cart([
            'id' => 1,
            'user_id' => 10,
            'product' => [
                'id' => 100
            ]
        ]);

        $category = new Category([
            'id' => 5,
            'name' => 'Electronics'
        ]);

        $this->cartRepository
            ->method('findById')
            ->willReturn($cart);

        $this->productRepository
            ->method('findById')
            ->willReturn($product);

        $this->categoryRepository
            ->method('findById')
            ->willReturn($category);

        $this->productInformationRepository
            ->method('findByProductId')
            ->willReturn(null);

        $result = $this->service->getCart(1);

        $this->assertEquals(50, $result->getProduct()->getPrice());
    }

    public function testGetAllReturnsEnrichedCarts(): void
    {
        $cart = new Cart([
            'id' => 1,
            'user_id' => 1,
            'product' => [
                'id' => 100
            ]
        ]);

        $product = new Product([
            'id' => 100,
            'price' => 100,
            'category_id' => 5
        ]);

        $category = new Category([
            'id' => 5,
            'name' => 'Electronics'
        ]);

        $discountInfo = $this->createMock(
            \App\Models\ProductInformation::class
        );

        $discountInfo
            ->method('getDiscount')
            ->willReturn(10.0);

        $this->cartRepository
            ->method('findAll')
            ->willReturn([$cart]);

        $this->productRepository
            ->method('findById')
            ->willReturn($product);

        $this->categoryRepository
            ->method('findById')
            ->willReturn($category);

        $this->productInformationRepository
            ->method('findByProductId')
            ->willReturn($discountInfo);

        $result = $this->service->getAll();

        $this->assertCount(1, $result);
        $this->assertEquals(90, $result[0]->getProduct()->getPrice());
    }

    public function testGetCartByUserReturnsUserCart(): void
    {
        $cart = new Cart([
            'id' => 1,
            'user_id' => 15,
            'product' => [
                'id' => 100
            ]
        ]);

        $product = new Product([
            'id' => 100,
            'price' => 200,
            'category_id' => 5
        ]);

        $category = new Category([
            'id' => 5,
            'name' => 'Electronics'
        ]);

        $this->cartRepository
            ->method('findByUser')
            ->with(15)
            ->willReturn([$cart]);

        $this->productRepository
            ->method('findById')
            ->willReturn($product);

        $this->categoryRepository
            ->method('findById')
            ->willReturn($category);

        $this->productInformationRepository
            ->method('findByProductId')
            ->willReturn(null);

        $result = $this->service->getCartByUser(15);

        $this->assertCount(1, $result);
    }

    public function testPaginateReturnsRepositoryResponse(): void
    {
        $payload = [
            'data' => [],
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
        ];

        $params = [
            'draw' => 1
        ];

        $this->cartRepository
            ->expects($this->once())
            ->method('paginate')
            ->with($params)
            ->willReturn($payload);

        $result = $this->service->paginate($params);

        $this->assertSame($payload, $result);
    }
}
