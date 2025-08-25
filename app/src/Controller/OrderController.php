<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\CreateOrderDto;
use App\Service\OrderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/orders/{id}', name: 'api_order_get_one', methods: ['GET'])]
    public function getOrderById(int $id): JsonResponse
    {
        $order = $this->orderService->findOrderById($id);

        if ($order === null) {
            return $this->json(
                [
                    'error' => [
                        'code' => Response::HTTP_NOT_FOUND,
                        'message' => "Order with ID {$id} not found.",
                    ]
                ],
                Response::HTTP_NOT_FOUND
            );
        }

        return $this->json(
            $order,
            Response::HTTP_OK,
            [],
            ['groups' => ['order:read']]
        );
    }

    #[Route('/orders', name: 'api_order_get_all', methods: ['GET'])]
    public function getAllOrders(
        Request $request,
        OrderService $orderService
    ): JsonResponse {
        $page = $request->query->getInt('page', 1);

        if ($page < 1) {
            $errorPayload = [
                'error' => [
                    'code' => Response::HTTP_BAD_REQUEST,
                    'message' => 'The "page" query parameter must be a positive integer greater than zero.'
                ]
            ];

            return $this->json($errorPayload, Response::HTTP_BAD_REQUEST);
        }

        $paginator = $orderService->getOrdersByPage($page);
        $totalItems = count($paginator);
        $pagesCount = (int) ceil($totalItems / $this->getParameter('app.paginator_per_page'));

        $responseData = [
            'data' => iterator_to_array($paginator->getIterator()),
            'meta' => [
                'total_items' => $totalItems,
                'items_per_page' => $this->getParameter('app.paginator_per_page'),
                'current_page' => $page,
                'total_pages' => $pagesCount,
            ]
        ];

        return $this->json(
            $responseData,
            Response::HTTP_OK,
            [],
            ['groups' => 'order:read']
        );
    }
}
