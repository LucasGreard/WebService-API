<?php

namespace App\Entity;

use App\Repository\ResolutionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass=ResolutionRepository::class)
 * @Serializer\ExclusionPolicy("ALL")
 */
class Resolution
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=45)
     * @Serializer\Expose()
     */
    private $height;

    /**
     * @ORM\Column(type="string", length=45)
     * @Serializer\Expose()
     */
    private $width;

    /**
     * @ORM\OneToMany(targetEntity=Product::class, mappedBy="resolution_id")
     */
    private $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHeight(): ?string
    {
        return $this->height;
    }

    public function setHeight(string $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function getWidth(): ?string
    {
        return $this->width;
    }

    public function setWidth(string $width): self
    {
        $this->width = $width;

        return $this;
    }

    // /**
    //  * @return Collection|Product[]
    //  */
    // public function getProducts(): Collection
    // {
    //     return $this->products;
    // }

    // public function addProduct(Product $product): self
    // {
    //     if (!$this->products->contains($product)) {
    //         $this->products[] = $product;
    //         $product->setResolutionId($this);
    //     }

    //     return $this;
    // }

    // public function removeProduct(Product $product): self
    // {
    //     if ($this->products->removeElement($product)) {
    //         // set the owning side to null (unless already changed)
    //         if ($product->getResolutionId() === $this) {
    //             $product->setResolutionId(null);
    //         }
    //     }

    //     return $this;
    // }
}
