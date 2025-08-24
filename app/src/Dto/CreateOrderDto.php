<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class CreateOrderDto
{
    #[Assert\NotBlank(message: "customer_email cannot be blank.")]
    #[Assert\Email]
    public ?string $customer_email = null;

    /**
     * @var CreateOrderItemDto[]
     */
    #[Assert\NotBlank]
    #[Assert\Count(min: 1, minMessage: 'Order must contain at least one item.')]
    #[Assert\Valid]
    public array $items = [];
}
