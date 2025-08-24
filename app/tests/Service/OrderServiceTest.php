<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Dto\CreateOrderDto;
use App\Dto\CreateOrderItemDto;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Enum\OrderStatus;
use App\Service\OrderService;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\MockClock;

class OrderServiceTest extends TestCase
{
    public static function createOrderDataProvider(): Generator
    {
        $now = new DateTimeImmutable('2025-08-25 12:00:00');

        yield 'single item order' => (function () use ($now): array {
            $orderItemDto = new CreateOrderItemDto();
            $orderItemDto->product_name = 'Solo Product';
            $orderItemDto->quantity = 1;
            $orderItemDto->unit_price = 9.99;

            $orderDto = new CreateOrderDto();
            $orderDto->customer_email = 'customer@example.com';
            $orderDto->items[] = $orderItemDto;

            return [
                'orderDto' => $orderDto,
                'now' => $now,
                'expectedOrderItemCount' => 1,
                'expectedTotalPrice' => 9.99,
            ];
        })();

        yield 'multiple items order' => (function () use ($now): array {
            $orderItemDto1 = new CreateOrderItemDto();
            $orderItemDto1->product_name = 'Product A';
            $orderItemDto1->quantity = 2;
            $orderItemDto1->unit_price = 10.00;

            $orderItemDto2 = new CreateOrderItemDto();
            $orderItemDto2->product_name = 'Product B';
            $orderItemDto2->quantity = 1;
            $orderItemDto2->unit_price = 5.50;

            $orderDto = new CreateOrderDto();
            $orderDto->customer_email = 'customer@example.com';
            $orderDto->items[] = $orderItemDto1;
            $orderDto->items[] = $orderItemDto2;

            return [
                'orderDto' => $orderDto,
                'now' => $now,
                'expectedOrderItemCount' => 2,
                'expectedTotalPrice' => 25.50,
            ];
        })();

        yield 'order with zero price item' => (function () use ($now): array {
            $orderItemDto = new CreateOrderItemDto();
            $orderItemDto->product_name = 'Free Product';
            $orderItemDto->quantity = 1;
            $orderItemDto->unit_price = 0.0;

            $orderDto = new CreateOrderDto();
            $orderDto->customer_email = 'customer@example.com';
            $orderDto->items[] = $orderItemDto;

            return [
                'orderDto' => $orderDto,
                'now' => $now,
                'expectedOrderItemCount' => 1,
                'expectedTotalPrice' => 0.0,
            ];
        })();
    }

    #[dataProvider('createOrderDataProvider')]
    public function testCreateOrder(
        CreateOrderDto $orderDto,
        DateTimeInterface $now,
        int $expectedOrderItemCount,
        float $expectedTotalPrice
    ): void {
        $entityManagerMock = $this->createMock(EntityManagerInterface::class);

        $entityManagerMock
            ->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(Order::class));

        $entityManagerMock
            ->expects($this->once())
            ->method('flush');

        $service = new OrderService(
            $entityManagerMock,
            new MockClock($now)
        );

        $order = $service->createOrder($orderDto);

        $this->assertInstanceOf(Order::class, $order);
        $this->assertSame($orderDto->customer_email, $order->getCustomerEmail());
        $this->assertSame(OrderStatus::NEW, $order->getStatus());
        $this->assertSame($expectedTotalPrice, $order->getTotalPrice());
        $this->assertEquals($now, $order->getCreatedAt());

        $orderItemsFromEntity = $order->getOrderItems()->getValues();
        $this->assertCount($expectedOrderItemCount, $orderItemsFromEntity);

        foreach ($orderDto->items as $index => $expectedOrderItemDto) {
            $this->assertArrayHasKey($index, $orderItemsFromEntity, "OrderItem at index $index missing.");
            $orderItemFromEntity = $orderItemsFromEntity[$index];

            $this->assertInstanceOf(OrderItem::class, $orderItemFromEntity);
            $this->assertSame($expectedOrderItemDto->product_name, $orderItemFromEntity->getProductName());
            $this->assertSame($expectedOrderItemDto->quantity, $orderItemFromEntity->getQuantity());
            $this->assertSame($expectedOrderItemDto->unit_price, $orderItemFromEntity->getUnitPrice());
            $this->assertSame($order, $orderItemFromEntity->getOrder());
        }
    }
}
