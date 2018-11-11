<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PanierRepository")
 */
class Panier
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="panier", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user_id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateAchat;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PanierProduits", mappedBy="panier", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $panierProduits;


    public function __construct()
    {
        $this->listeProduits = new ArrayCollection();
        $this->panierProduits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?User
    {
        return $this->user_id;
    }

    public function setUserId(User $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getDateAchat(): ?\DateTimeInterface
    {
        return $this->dateAchat;
    }

    public function setDateAchat(?\DateTimeInterface $dateAchat): self
    {
        $this->dateAchat = $dateAchat;

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
            $panierProduit->setPanier($this);
        }

        return $this;
    }

    public function removePanierProduit(PanierProduits $panierProduit): self
    {
        if ($this->panierProduits->contains($panierProduit)) {
            $this->panierProduits->removeElement($panierProduit);
            // set the owning side to null (unless already changed)
            if ($panierProduit->getPanier() === $this) {
                $panierProduit->setPanier(null);
            }
        }

        return $this;
    }
}
