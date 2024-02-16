<?php

namespace App\Entity;

use App\Repository\RestaurantReserverationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RestaurantReserverationRepository::class)]
class RestaurantReserveration
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'restaurantReserveration', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?RestaurantAvailableTime $restaurantAvailableTime = null;

    #[ORM\Column]
    private ?int $guests = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $specialWishes = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRestaurantAvailableTime(): ?RestaurantAvailableTime
    {
        return $this->restaurantAvailableTime;
    }

    public function setRestaurantAvailableTime(RestaurantAvailableTime $restaurantAvailableTime): static
    {
        $this->restaurantAvailableTime = $restaurantAvailableTime;

        return $this;
    }

    public function getGuests(): ?int
    {
        return $this->guests;
    }

    public function setGuests(int $guests): static
    {
        $this->guests = $guests;

        return $this;
    }

    public function getSpecialWishes(): ?string
    {
        return $this->specialWishes;
    }

    public function setSpecialWishes(?string $specialWishes): static
    {
        $this->specialWishes = $specialWishes;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }
}
