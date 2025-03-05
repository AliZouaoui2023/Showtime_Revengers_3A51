<?php

namespace App\Entity;

use App\Repository\SeanceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: SeanceRepository::class)]
class Seance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotNull(message: "La date de la séance ne peut pas être vide.")]
    #[Assert\GreaterThan("today", message: "La date de la séance doit être future.")]
    private ?\DateTimeInterface $dateSeance = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    #[Assert\NotNull(message: "La durée est obligatoire.")]
    private ?\DateTimeInterface $duree = null;



    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Les objectifs ne peuvent pas être vides.")]
    #[Assert\Length(
        min: 10,
        max: 255,
        minMessage: "Les objectifs doivent contenir au moins {{ limit }} caractères.",
        maxMessage: "Les objectifs ne peuvent pas dépasser {{ limit }} caractères."
    )]
    private ?string $objectifs = null;

    #[ORM\ManyToOne(targetEntity: Cour::class, inversedBy: 'seances')]
    #[ORM\JoinColumn(name: 'cour_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?Cour $cour = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateSeance(): ?\DateTimeInterface
    {
        return $this->dateSeance;
    }

    public function setDateSeance(\DateTimeInterface $dateSeance): static
    {
        $this->dateSeance = $dateSeance;

        return $this;
    }

    public function getDuree(): ?\DateTimeInterface
    {
        return $this->duree;
    }

    public function setDuree(?\DateTimeInterface $duree): self
    {
        $this->duree = $duree;
        return $this;
    }

    public function getObjectifs(): ?string
    {
        return $this->objectifs;
    }

    public function setObjectifs(string $objectifs): static
    {
        $this->objectifs = $objectifs;

        return $this;
    }

    public function getCour(): ?Cour
    {
        return $this->cour;
    }

    public function setCour(?Cour $cour): static
    {
        $this->cour = $cour;

        return $this;
    }
   
}