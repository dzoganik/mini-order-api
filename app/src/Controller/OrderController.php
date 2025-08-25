<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\CreateOrderDto;
use App\Service\OrderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class OrderController extends AbstractController
{
    public function __construct(
        private OrderService $orderService
    ) {
    }

    #[Route('/orders', name: 'api_order_create', methods: ['POST'])]
    public function create(
        #[MapRequestPayload] CreateOrderDto $orderDto
    ): JsonResponse {
        $order = $this->orderService->createOrder($orderDto);

        return new JsonResponse(
            [
                'order ID' => $order->getId(),
                'status' => $order->getStatus(),
            ],
            Response::HTTP_CREATED
        );
    }
}
