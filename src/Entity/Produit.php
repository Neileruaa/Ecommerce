<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProduitRepository")
 */
class Produit
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $prix;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $photo;

    /**
     * @ORM\Column(type="boolean")
     */
    private $disponible;

    /**
     * @ORM\Column(type="integer")
     */
    private $stock;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TypeProduit")
     * @ORM\JoinColumn(nullable=false)
     */
    private $typeProduit_id;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PanierProduits", mappedBy="produit")
     */
    private $panierProduits;

    public function __construct()
    {
        $this->panierProduits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrix()
    {
        return $this->prix;
    }

    public function setPrix($prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getDisponible(): ?bool
    {
        return $this->disponible;
    }

    public function setDisponible(bool $disponible): self
    {
        $this->disponible = $disponible;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    public function getTypeProduitId(): ?TypeProduit
    {
        return $this->typeProduit_id;
    }

    public function setTypeProduitId(?TypeProduit $typeProduit_id): self
    {
        $this->typeProduit_id = $typeProduit_id;

        return $this;
    }

    /**
     * @return Collection|PanierProduits[]
     */
    public function getPanierProduits(): Collection
    {
        return $this->panierProduits;
    }

    public function addPanierProduit(PanierProduits $panierProduit): self
    {
        if (!$this->panierProduits->contains($panierProduit)) {
            $this->panierProduits[] = $panierProduit;
            $panierProduit->setProduit($this);
        }

        return $this;
    }

    public function removePanierProduit(PanierProduits $panierProduit): self
    {
        if ($this->panierProduits->contains($panierProduit)) {
            $this->panierProduits->removeElement($panierProduit);
            // set the owning side to null (unless already changed)
            if ($panierProduit->getProduit() === $this) {
                $panierProduit->setProduit(null);
            }
        }

        return $this;
    }
}
