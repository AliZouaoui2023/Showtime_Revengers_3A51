<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\SalleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\NotBlank;

    #[ORM\Entity(repositoryClass: SalleRepository::class)]
class Salle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id_salle = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom de la salle est obligatoire.")]

        #[Assert\Regex(
            pattern: "/^(?=.*[a-zA-Z])(?=.*[0-9])[a-zA-Z0-9]+$/",
            message: "Le nom doit contenir au moins une lettre et un chiffre, sans espaces."
        )]
        
    
    #[Assert\Length(
        min: 5,
        minMessage: "Le nom de la salle doit comporter au moins 5 caractères."
    )]
    private ?string $nom_salle = null;

  

    #[ORM\Column(length: 255)]
    private ?string $disponibilite = null;

    #[ORM\Column(length: 255)]
    private ?string $type_salle = null;
    #[Assert\NotBlank(message: "Le nombre de places est obligatoire.")]
    #[ORM\Column(type: 'integer')]
    private ?int $nombre_de_place = null;

    #[ORM\Column(length: 255)]
    private ?string $statut = null;

    #[ORM\Column(length: 255)]
    private ?string $emplacement = null;

    // Relation OneToMany avec Equipement
    #[ORM\OneToMany(mappedBy: 'salle', targetEntity: Equipement::class, cascade: ['persist', 'remove'])]
    private Collection $equipements;

    public function __construct()
    {
        $this->equipements = new ArrayCollection();
    }

    // Getters et setters

    public function getIdSalle(): ?int
    {
        return $this->id_salle;
    }

    public function getNomSalle(): ?string
    {
        return $this->nom_salle;
    }

    public function setNomSalle(string $nom_salle): static
    {
        $this->nom_salle = $nom_salle;
        return $this;
    }

    public function getDisponibilite(): ?string
    {
        return $this->disponibilite;
    }

    public function setDisponibilite(string $disponibilite): static
    {
        $this->disponibilite = $disponibilite;
        return $this;
    }

    public function getNombreDePlace(): ?int
    {
        return $this->nombre_de_place;
    }

    public function setNombreDePlace(int $nombre_de_place): self
    {
        $this->nombre_de_place = $nombre_de_place;
        return $this;
    }

    public function getTypeSalle(): ?string
    {
        return $this->type_salle;
    }

    public function setTypeSalle(string $type_salle): static
    {
        $this->type_salle = $type_salle;
        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;
        return $this;
    }

    public function getEmplacement(): ?string
    {
        return $this->emplacement;
    }

    public function setEmplacement(string $emplacement): static
    {
        $this->emplacement = $emplacement;
        return $this;
    }

    public function getEquipements(): Collection
    {
        return $this->equipements;
    }

    public function addEquipement(Equipement $equipement): static
    {
        if (!$this->equipements->contains($equipement)) {
            $this->equipements[] = $equipement;
            $equipement->setSalle($this); // Définir la relation inverse
        }
        return $this;
    }

    public function removeEquipement(Equipement $equipement): static
    {
        if ($this->equipements->removeElement($equipement)) {
            // Si l'équipement est supprimé, on efface la relation inverse
            if ($equipement->getSalle() === $this) {
                $equipement->setSalle(null);
            }
        }
        return $this;
    }
}
