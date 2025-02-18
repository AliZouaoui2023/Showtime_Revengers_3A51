<?php

namespace App\Entity;

use App\Repository\FilmsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FilmsRepository::class)]
class Films
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Nom_Film = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $Date_Production = null;

    #[ORM\Column(length: 255)]
    private ?string $Realisateur = null;

    #[ORM\Column(length: 255)]
    private ?string $Genre = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomFilm(): ?string
    {
        return $this->Nom_Film;
    }

    public function setNomFilm(string $Nom_Film): static
    {
        $this->Nom_Film = $Nom_Film;

        return $this;
    }

    public function getDateProduction(): ?\DateTimeInterface
    {
        return $this->Date_Production;
    }

    public function setDateProduction(\DateTimeInterface $Date_Production): static
    {
        $this->Date_Production = $Date_Production;

        return $this;
    }

    public function getRealisateur(): ?string
    {
        return $this->Realisateur;
    }

    public function setRealisateur(string $Realisateur): static
    {
        $this->Realisateur = $Realisateur;

        return $this;
    }

    public function getGenre(): ?string
    {
        return $this->Genre;
    }

    public function setGenre(string $Genre): static
    {
        $this->Genre = $Genre;

        return $this;
    }
}
