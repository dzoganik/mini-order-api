<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Order;
use App\Entity\OrderItem;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class OrderItemTest extends TestCase
{
    public function testCreate(): void
    {
        $productName = 'Sample Product';
        $quantity = 2;
        $unitPrice = 19.99;

        $orderItem = new OrderItem(
            $productName,
            $quantity,
            $unitPrice
        );

        $order = new Order(
            'user@example.com',
            new DateTimeImmutable()
        );

        $order->addOrderItem($orderItem);

        $this->assertSame($productName, $orderItem->getProductName());
        $this->assertSame($quantity, $orderItem->getQuantity());
        $this->assertSame($unitPrice, $orderItem->getUnitPrice());
        $this->assertSame(39.98, $orderItem->getTotalPrice());
        $this->assertSame($order, $orderItem->getOrder());
    }
}
