<?php

namespace App\Entity;

use App\Repository\EquipementRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EquipementRepository::class)]
class Equipement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom de l'équipement est obligatoire.")]

    #[Assert\Regex(
        pattern: "/^(?=.*[a-zA-Z])(?=.*[0-9])[a-zA-Z0-9]+$/",
        message: "Le nom doit contenir au moins une lettre et un chiffre, sans espaces."
    )]
   

#[Assert\Length(
    min: 5,
    minMessage: "Le nom de la salle doit comporter au moins 5 caractères."
)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(length: 255)]
    private ?string $etat = null;

    // Relation ManyToOne avec l'entité Salle
    #[ORM\ManyToOne(targetEntity: Salle::class)]
#[ORM\JoinColumn(name: 'salle_id', referencedColumnName: 'id_salle')]
private ?Salle $salle = null;
   
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): static
    {
        $this->etat = $etat;
        return $this;
    }

    public function getSalle(): ?Salle
    {
        return $this->salle;
    }

    public function setSalle(?Salle $salle): static
    {
        $this->salle = $salle;
        return $this;
    }
}
