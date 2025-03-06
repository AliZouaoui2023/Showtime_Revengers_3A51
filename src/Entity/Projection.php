<?php

namespace App\Entity;

use App\Repository\ProjectionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProjectionRepository::class)]
class Projection
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: "La date de projection est obligatoire.")]
    #[Assert\GreaterThan("today", message: "La date de projection doit être dans le futur.")]
    private ?\DateTimeInterface $Date_Projection = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Le prix est obligatoire.")]
    #[Assert\Type(type: "numeric", message: "Le prix doit être un nombre.")]
    #[Assert\Positive(message: "Le prix doit être un nombre positif.")]
    private ?float $Prix = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "La capacité est obligatoire.")]
    #[Assert\Type(type: "integer", message: "La capacité doit être un nombre entier.")]
    #[Assert\Range(
        min: 10,
        max: 50,
        notInRangeMessage: "La capacité doit être comprise entre {{ min }} et {{ max }}."
    )]
    private ?int $Capaciter = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateProjection(): ?\DateTimeInterface
    {
        return $this->Date_Projection;
    }

    public function setDateProjection(\DateTimeInterface $Date_Projection): static
    {
        $this->Date_Projection = $Date_Projection;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->Prix;
    }

    public function setPrix(float $Prix): static
    {
        $this->Prix = $Prix;

        return $this;
    }

    public function getCapaciter(): ?int
    {
        return $this->Capaciter;
    }

    public function setCapaciter(int $Capaciter): static
    {
        $this->Capaciter = $Capaciter;

        return $this;
    }
}
