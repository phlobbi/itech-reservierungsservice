<?php

namespace App\Entity;

use App\Repository\RestaurantAvailableTimeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RestaurantAvailableTimeRepository::class)]
class RestaurantAvailableTime
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'restaurantAvailableTimes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?RestaurantTable $restaurantTable = null;


    #[ORM\OneToOne(mappedBy: 'restaurantAvailableTime', cascade: ['persist', 'remove'])]
    private ?RestaurantReserveration $restaurantReserveration = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $time = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRestaurantTable(): ?RestaurantTable
    {
        return $this->restaurantTable;
    }

    public function setRestaurantTable(?RestaurantTable $restaurantTable): static
    {
        $this->restaurantTable = $restaurantTable;

        return $this;
    }

    public function getRestaurantReserveration(): ?RestaurantReserveration
    {
        return $this->restaurantReserveration;
    }

    public function setRestaurantReserveration(RestaurantReserveration $restaurantReserveration): static
    {
        // set the owning side of the relation if necessary
        if ($restaurantReserveration->getRestaurantAvailableTime() !== $this) {
            $restaurantReserveration->setRestaurantAvailableTime($this);
        }

        $this->restaurantReserveration = $restaurantReserveration;

        return $this;
    }

    public function getTime(): ?\DateTimeInterface
    {
        return $this->time;
    }

    public function setTime(\DateTimeInterface $time): static
    {
        $this->time = $time;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }
}
