<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\CreateOrderDto;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Clock\ClockInterface;

class OrderService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ClockInterface $clock,
        private readonly OrderRepository $orderRepository
    ) {
    }

    public function createOrder(CreateOrderDto $dto): Order
    {
        $order = new Order(
            $dto->customer_email,
            $this->clock->now()
        );

        foreach ($dto->items as $itemDto) {
            $orderItem = new OrderItem(
                $itemDto->product_name,
                $itemDto->quantity,
                $itemDto->unit_price
            );

            $order->addOrderItem($orderItem);
        }

        $this->entityManager->persist($order);
        $this->entityManager->flush();

        return $order;
    }

    public function findOrderById(int $id): ?Order
    {
        return $this->orderRepository->find($id);
    }
}
