<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommandeRepository")
 */
class Commande
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="commandes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PanierProduits", mappedBy="commande")
     */
    private $PanierCommande;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateCommande;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $montant;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $etat;

    public function __construct()
    {
        $this->PanierCommande = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection|PanierProduits[]
     */
    public function getPanierCommande(): Collection
    {
        return $this->PanierCommande;
    }

    public function addPanierCommande(PanierProduits $panierCommande): self
    {
        if (!$this->PanierCommande->contains($panierCommande)) {
            $this->PanierCommande[] = $panierCommande;
            $panierCommande->setCommande($this);
        }

        return $this;
    }

    public function removePanierCommande(PanierProduits $panierCommande): self
    {
        if ($this->PanierCommande->contains($panierCommande)) {
            $this->PanierCommande->removeElement($panierCommande);
            // set the owning side to null (unless already changed)
            if ($panierCommande->getCommande() === $this) {
                $panierCommande->setCommande(null);
            }
        }

        return $this;
    }

    public function getDateCommande(): ?\DateTimeInterface
    {
        return $this->dateCommande;
    }

    public function setDateCommande(\DateTimeInterface $dateCommande): self
    {
        $this->dateCommande = $dateCommande;

        return $this;
    }

    public function getMontant()
    {
        return $this->montant;
    }

    public function setMontant($montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): self
    {
        $this->etat = $etat;

        return $this;
    }
}
