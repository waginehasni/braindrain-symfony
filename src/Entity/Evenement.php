<?php

namespace App\Entity;

use App\Repository\EvenementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=EvenementRepository::class)
 */
class Evenement
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $idEvenement;

    /**
     * @Assert\NotBlank(message="Veuillez saisir numero de salle")
     * @ORM\Column(type="integer")
     */
    private $numSalle;

    /**
     * @Assert\NotBlank(message="Veuillez saisir le nom de l'offre")
     * @ORM\Column(type="string", length=250)
     */
    private $nomOffre;

    /**
     * @Assert\NotBlank(message="Veuillez saisir la date de debut")
     * @ORM\Column(type="date")
     */
    private $dateDebut;

    /**
     * @Assert\NotBlank(message="Veuillez saisir la date de fin")
     * @ORM\Column(type="date")
     */
    private $dateFin;

    /**
     * @Assert\NotBlank(message="Veuillez saisir la spécialité")
     * @ORM\Column(type="string", length=250)
     */
    private $specialite;

    /**
     * @Assert\NotBlank(message="Veuillez saisir le nom")
     * @Assert\Length(min=10, max=200, minMessage="Taille minimale (10)", maxMessage="Taille maximale (100) depassé")
     * @ORM\Column(type="string", length=200)
     */
    private $nom;

    public function __construct()
    {
        $this->ratings = new ArrayCollection();
    }

    public function getIdEvenement(): ?int
    {
        return $this->idEvenement;
    }

    public function getNumSalle(): ?int
    {
        return $this->numSalle;
    }

    public function setNumSalle(int $numSalle): self
    {
        $this->numSalle = $numSalle;

        return $this;
    }

    public function getNomOffre(): ?string
    {
        return $this->nomOffre;
    }

    public function setNomOffre(string $nomOffre): self
    {
        $this->nomOffre = $nomOffre;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTimeInterface $dateFin): self
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getSpecialite(): ?string
    {
        return $this->specialite;
    }

    public function setSpecialite(string $specialite): self
    {
        $this->specialite = $specialite;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

}
