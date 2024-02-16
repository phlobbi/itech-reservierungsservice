<?php

namespace App\Entity;

use App\Repository\RestaurantTableRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RestaurantTableRepository::class)]
class RestaurantTable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $size = null;

    #[ORM\Column]
    private ?bool $isOutside = null;

    #[ORM\OneToMany(targetEntity: RestaurantAvailableTime::class, mappedBy: 'restaurantTable', orphanRemoval: true)]
    private Collection $restaurantAvailableTimes;

    #[ORM\Column(length: 255)]
    private ?string $tableNumber = null;

    public function __construct()
    {
        $this->restaurantAvailableTimes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(int $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function isIsOutside(): ?bool
    {
        return $this->isOutside;
    }

    public function setIsOutside(bool $isOutside): static
    {
        $this->isOutside = $isOutside;

        return $this;
    }

    /**
     * @return Collection<int, RestaurantAvailableTime>
     */
    public function getRestaurantAvailableTimes(): Collection
    {
        return $this->restaurantAvailableTimes;
    }

    public function addRestaurantAvailableTime(RestaurantAvailableTime $restaurantAvailableTime): static
    {
        if (!$this->restaurantAvailableTimes->contains($restaurantAvailableTime)) {
            $this->restaurantAvailableTimes->add($restaurantAvailableTime);
            $restaurantAvailableTime->setRestaurantTable($this);
        }

        return $this;
    }

    public function removeRestaurantAvailableTime(RestaurantAvailableTime $restaurantAvailableTime): static
    {
        if ($this->restaurantAvailableTimes->removeElement($restaurantAvailableTime)) {
            // set the owning side to null (unless already changed)
            if ($restaurantAvailableTime->getRestaurantTable() === $this) {
                $restaurantAvailableTime->setRestaurantTable(null);
            }
        }

        return $this;
    }

    public function getTableNumber(): ?string
    {
        return $this->tableNumber;
    }

    public function setTableNumber(string $tableNumber): static
    {
        $this->tableNumber = $tableNumber;

        return $this;
    }
}
