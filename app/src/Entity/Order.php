<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\OrderStatus;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['order:read'])]
    private int $id;

    #[ORM\Column]
    #[Groups(['order:read'])]
    #[Assert\NotBlank]
    #[Assert\Email]
    private string $customerEmail;

    #[ORM\Column(enumType: OrderStatus::class)]
    #[Groups(['order:read'])]
    #[Assert\NotBlank]
    private OrderStatus $status;

    #[ORM\Column(type: "decimal", precision: 10, scale: 2)]
    #[Groups(['order:read'])]
    #[Assert\PositiveOrZero]
    private float $totalPrice;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Groups(['order:read'])]
    private DateTimeImmutable $createdAt;

    /**
     * @var Collection<int, OrderItem>
     */
    #[ORM\OneToMany(
        targetEntity: OrderItem::class,
        mappedBy: 'order',
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    #[Groups(['order:read'])]
    #[Assert\Valid]
    #[Assert\Count(min: 1)]
    private Collection $orderItems;

    public function __construct(
        string $customerEmail,
        DateTimeImmutable $createdAt
    ) {
        $this->customerEmail = $customerEmail;
        $this->status = OrderStatus::NEW;
        $this->createdAt = $createdAt;
        $this->orderItems = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCustomerEmail(): string
    {
        return $this->customerEmail;
    }

    public function getStatus(): OrderStatus
    {
        return $this->status;
    }

    public function setStatus(OrderStatus $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getTotalPrice(): float
    {
        return $this->totalPrice;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return Collection<int, OrderItem>
     */
    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }

    public function addOrderItem(OrderItem $orderItem): static
    {
        if (!$this->orderItems->contains($orderItem)) {
            $this->orderItems->add($orderItem);
            $orderItem->setOrder($this);
            $this->recalculateTotalPrice();
        }
        return $this;
    }

    private function recalculateTotalPrice(): void
    {
        $total = '0.0';

        foreach ($this->orderItems as $item) {
            $total = bcadd($total, (string) $item->getTotalPrice(), 2);
        }

        $this->totalPrice = (float) $total;
    }
}
