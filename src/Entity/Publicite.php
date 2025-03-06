<?php

// src/Entity/Publicite.php

namespace App\Entity;

use App\Repository\PubliciteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PubliciteRepository::class)]
class Publicite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(targetEntity: Demande::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    #[Assert\NotNull(message: "La demande associée est obligatoire.")]
    private ?Demande $demande = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotNull(message: "La date de début est obligatoire.")]
    #[Assert\GreaterThan("today", message: "La date de début doit être dans le futur.")]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotNull(message: "La date de fin est obligatoire.")]
    #[Assert\GreaterThan(propertyPath: "dateDebut", message: "La date de fin doit être postérieure à la date de début.")]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le support est obligatoire.")]
    #[Assert\Length(min: 3, max: 255, minMessage: "Le support doit contenir au moins 3 caractères.")]
    private ?string $support = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "Le montant est obligatoire.")]
    #[Assert\Positive(message: "Le montant doit être un nombre positif.")]
    private ?float $montant = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDemande(): ?Demande
    {
        return $this->demande;
    }

    public function setDemande(Demande $demande): static
    {
        $this->demande = $demande;
        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): static
    {
        $this->dateDebut = $dateDebut;
        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTimeInterface $dateFin): static
    {
        $this->dateFin = $dateFin;
        return $this;
    }

    public function getSupport(): ?string
    {
        return $this->support;
    }

    public function setSupport(string $support): static
    {
        $this->support = $support;
        return $this;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): static
    {
        $this->montant = $montant;
        return $this;
    }
}
