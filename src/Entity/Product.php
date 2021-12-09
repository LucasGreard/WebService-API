<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $fullname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $model;

    /**
     * @ORM\ManyToOne(targetEntity=Brand::class, inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     */
    private $brand;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $weight;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $screen_size;

    /**
     * @ORM\Column(type="integer")
     */
    private $storage;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $battery;

    /**
     * @ORM\Column(type="string", length=45)
     */
    private $RAM;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $image = [];

    /**
     * @ORM\ManyToOne(targetEntity=Resolution::class, inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     */
    private $resolution_id;

    /**
     * @ORM\ManyToOne(targetEntity=OperatingSystem::class, inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     */
    private $operating_system_id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFullname(): ?string
    {
        return $this->fullname;
    }

    public function setFullname(string $fullname): self
    {
        $this->fullname = $fullname;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    public function setBrand(?Brand $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getWeight(): ?float
    {
        return $this->weight;
    }

    public function setWeight(?float $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function getScreenSize(): ?float
    {
        return $this->screen_size;
    }

    public function setScreenSize(?float $screen_size): self
    {
        $this->screen_size = $screen_size;

        return $this;
    }

    public function getStorage(): ?int
    {
        return $this->storage;
    }

    public function setStorage(int $storage): self
    {
        $this->storage = $storage;

        return $this;
    }

    public function getBattery(): ?int
    {
        return $this->battery;
    }

    public function setBattery(?int $battery): self
    {
        $this->battery = $battery;

        return $this;
    }

    public function getRAM(): ?string
    {
        return $this->RAM;
    }

    public function setRAM(string $RAM): self
    {
        $this->RAM = $RAM;

        return $this;
    }

    public function getImage(): ?array
    {
        return $this->image;
    }

    public function setImage(?array $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getResolutionId(): ?Resolution
    {
        return $this->resolution_id;
    }

    public function setResolutionId(?Resolution $resolution_id): self
    {
        $this->resolution_id = $resolution_id;

        return $this;
    }

    public function getOperatingSystemId(): ?OperatingSystem
    {
        return $this->operating_system_id;
    }

    public function setOperatingSystemId(?OperatingSystem $operating_system_id): self
    {
        $this->operating_system_id = $operating_system_id;

        return $this;
    }
}
