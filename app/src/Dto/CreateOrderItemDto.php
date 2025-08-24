<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class CreateOrderItemDto
{
    #[Assert\NotBlank(message: "product_name cannot be blank.")]
    #[Assert\Length(min: 3, max: 255)]
    public ?string $product_name = null;

    #[Assert\NotBlank(message: "quantity cannot be blank.")]
    #[Assert\Type('int')]
    #[Assert\Positive]
    public ?int $quantity = null;

    #[Assert\NotBlank(message: "unit_price cannot be blank.")]
    #[Assert\Type('float')]
    #[Assert\PositiveOrZero]
    public ?float $unit_price = null;
}
