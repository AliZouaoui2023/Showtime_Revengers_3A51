<?php

namespace App\Entity;

use App\Repository\ProjectionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectionRepository::class)]
class Projection
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $Date_Projection = null;

    #[ORM\Column]
    private ?float $Prix = null;

    #[ORM\Column]
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
