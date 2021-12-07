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
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $brand;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $price;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $weight;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $operating_system;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $screen_size;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $network_type;

    /**
     * @ORM\Column(type="integer")
     */
    private $storage;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $autonomy;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $resolution;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $RAM;

    /**
     * @ORM\Column(type="json")
     */
    private $img = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getWeight(): ?string
    {
        return $this->weight;
    }

    public function setWeight(string $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function getOperatingSystem(): ?string
    {
        return $this->operating_system;
    }

    public function setOperatingSystem(string $operating_system): self
    {
        $this->operating_system = $operating_system;

        return $this;
    }

    public function getScreenSize(): ?string
    {
        return $this->screen_size;
    }

    public function setScreenSize(?string $screen_size): self
    {
        $this->screen_size = $screen_size;

        return $this;
    }

    public function getNetworkType(): ?string
    {
        return $this->network_type;
    }

    public function setNetworkType(?string $network_type): self
    {
        $this->network_type = $network_type;

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

    public function getAutonomy(): ?int
    {
        return $this->autonomy;
    }

    public function setAutonomy(?int $autonomy): self
    {
        $this->autonomy = $autonomy;

        return $this;
    }

    public function getResolution(): ?string
    {
        return $this->resolution;
    }

    public function setResolution(string $resolution): self
    {
        $this->resolution = $resolution;

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

    public function getImg(): ?array
    {
        return $this->img;
    }

    public function setImg(array $img): self
    {
        $this->img = $img;

        return $this;
    }
}
