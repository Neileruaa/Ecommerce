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
     * @ORM\ManyToMany(targetEntity="App\Entity\Produit")
     */
    private $listeProduits;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateAchat;


    public function __construct()
    {
        $this->listeProduits = new ArrayCollection();
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

    /**
     * @return Collection|Produit[]
     */
    public function getListeProduits(): Collection
    {
        return $this->listeProduits;
    }

    public function addListeProduit(Produit $listeProduit): self
    {
        if (!$this->listeProduits->contains($listeProduit)) {
            $this->listeProduits[] = $listeProduit;
        }

        return $this;
    }

    public function removeListeProduit(Produit $listeProduit): self
    {
        if ($this->listeProduits->contains($listeProduit)) {
            $this->listeProduits->removeElement($listeProduit);
        }

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
}
