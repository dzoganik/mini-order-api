<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Enum\OrderStatus;
use DateTimeImmutable;
use Generator;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class OrderTest extends TestCase
{
    public function testCreate(): void
    {
        $now = new DateTimeImmutable();

        $order = new Order(
            'user@example.com',
            $now
        );

        $orderItem = new OrderItem(
            'Sample Product',
            1,
            19.99
        );

        $order->addOrderItem($orderItem);

        $this->assertCount(1, $order->getOrderItems());
        $this->assertSame('user@example.com', $order->getCustomerEmail());
        $this->assertSame(OrderStatus::NEW, $order->getStatus());
        $this->assertSame(19.99, $order->getTotalPrice());
        $this->assertSame($now, $order->getCreatedAt());
    }

    /**
     * @return \Generator<string, array{
     *     itemsToAdd: list<array{
     *         productName: string,
     *         quantity: int,
     *         unitPrice: string
     *     }>,
     *     expectedItemCount: int
     * }>
     */
    public static function addOrderItemDataProvider(): Generator
    {
        yield '1 item' => (function (): array {
            return [
               'itemsToAdd' => [
                    [
                        'productName' => 'Solo Product',
                        'quantity' => 1,
                        'unitPrice' => 9.99,
                    ],
                ],
                'expectedItemCount' => 1,
                'expectedTotalPrice' => 9.99,
            ];
        })();

        yield 'float price edge case' => (function (): array {
            return [
                'itemsToAdd' => [
                    [
                        'productName' => 'Product A',
                        'quantity' => 1,
                        'unitPrice' => 0.1,
                    ],
                    [
                        'productName' => 'Product B',
                        'quantity' => 1,
                        'unitPrice' => 0.2,
                    ],
                ],
                'expectedItemCount' => 2,
                'expectedTotalPrice' => 0.3,
            ];
        })();

        yield '2 products, various quantities' => (function (): array {
            return [
                'itemsToAdd' => [
                    [
                        'productName' => 'Product A',
                        'quantity' => 2,
                        'unitPrice' => 10.00,
                    ],
                    [
                        'productName' => 'Product B',
                        'quantity' => 3,
                        'unitPrice' => 12.50,
                    ],
                ],
                'expectedItemCount' => 2,
                'expectedTotalPrice' => 57.50,
            ];
        })();
    }

    /**
     * @param array<array{
     *     productName: string,
     *     quantity: int,
     *     unitPrice: float
     * }> $itemsToAdd
     * @param int $expectedCount
     * @param float $expectedTotalPrice
     */
    #[DataProvider('addOrderItemDataProvider')]
    public function testAddOrderItem(
        array $itemsToAdd,
        int $expectedItemCount,
        float $expectedTotalPrice
    ): void {
        $now = new DateTimeImmutable();
        $customerEmail = 'user@example.com';

        $order = new Order(
            $customerEmail,
            $now
        );

        foreach ($itemsToAdd as $itemData) {
            $orderItem = new OrderItem(
                $itemData['productName'],
                $itemData['quantity'],
                $itemData['unitPrice']
            );

            $order->addOrderItem($orderItem);
        }

        $orderItemsInOrder = $order->getOrderItems()->getValues();
        $this->assertCount($expectedItemCount, $orderItemsInOrder);

        foreach ($itemsToAdd as $index => $expectedData) {
            $this->assertArrayHasKey($index, $orderItemsInOrder);
            $actualItem = $orderItemsInOrder[$index];
            $this->assertSame($expectedData['productName'], $actualItem->getProductName());
            $this->assertSame($expectedData['quantity'], $actualItem->getQuantity());
            $this->assertSame($expectedData['unitPrice'], $actualItem->getUnitPrice());
        }

        $this->assertSame($customerEmail, $order->getCustomerEmail());
        $this->assertSame(OrderStatus::NEW, $order->getStatus());
        $this->assertSame($expectedTotalPrice, $order->getTotalPrice());
        $this->assertSame($now, $order->getCreatedAt());
    }
}
