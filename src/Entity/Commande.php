<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $datecommande = null;

    #[ORM\Column]
    private ?float $montantpaye = null;

    #[ORM\Column(length: 255)]
    private ?string $etat = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'commandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;    

    // Relation ManyToMany avec les produits
    #[ORM\ManyToMany(targetEntity: Produit::class, mappedBy: 'commandes')]
    private Collection $produits;

    public function __construct()
    {
        $this->datecommande = new \DateTime(); // Définit automatiquement la date actuelle
        $this->produits = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function setDatecommandeValue(): void
    {
        if (!$this->datecommande) {
            $this->datecommande = new \DateTime(); // Assure que la date est définie avant l'enregistrement
        }
    }

    // Getter et setter pour l'ID
    public function getId(): ?int
    {
        return $this->id;
    }

    // Getter et setter pour la date de commande
    public function getDatecommande(): ?\DateTimeInterface
    {
        return $this->datecommande;
    }

    // Getter et setter pour le montant payé
    public function getMontantpaye(): ?float
    {
        return $this->montantpaye;
    }

    public function setMontantpaye(float $montantpaye): static
    {
        $this->montantpaye = $montantpaye;
        return $this;
    }

    // Getter et setter pour l'état
    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): static
    {
        $this->etat = $etat;
        return $this;
    }

    // Getter et setter pour l'utilisateur
    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    // Getter et setter pour les produits
    /**
     * @return Collection<int, Produit>
     */
    public function getProduits(): Collection
    {
        return $this->produits;
    }

    public function addProduit(Produit $produit): static
    {
        if (!$this->produits->contains($produit)) {
            $this->produits->add($produit);
            $produit->addCommande($this);  // Ajoute la commande dans le produit
        }

        return $this;
    }

    public function removeProduit(Produit $produit): static
    {
        if ($this->produits->removeElement($produit)) {
            $produit->removeCommande($this);  // Supprime la commande du produit
        }

        return $this;
    }
}