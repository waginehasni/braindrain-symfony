<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Reservation
 *
 * @ORM\Table(name="reservation")
 * @ORM\Entity(repositoryClass="App\Repository\ReservationRepository")
 */
class Reservation
{
    /**
     * @var int
     *
     * @ORM\Column(name="numReservation", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public $numreservation;

    /**
     * @var int
     * @Assert\NotNull
     * @ORM\Column(name="numSalles", type="integer", nullable=false)
     */
    public $numsalles;

    /**
     * @var string
     * @Assert\NotNull
     * @ORM\Column(name="specialite", type="string", length=30, nullable=false)
     */
    public $specialite;

    /**
     * @var \DateTime
     * @Assert\NotNull
     * @ORM\Column(name="date", type="date", nullable=false)
     */
    public $date;

    /**
     * @var string
     * @Assert\NotNull
     * @ORM\Column(name="horraire", type="string", length=30, nullable=false)
     */
    public $horraire;

    /**
     * @var string
     * @Assert\NotNull
     * @ORM\Column(name="duree", type="string", length=30, nullable=false)
     */
    public $duree;

    public function getNumreservation(): ?int
    {
        return $this->numreservation;
    }

    public function getNumsalles(): ?int
    {
        return $this->numsalles;
    }

    public function setNumsalles(int $numsalles): self
    {
        $this->numsalles = $numsalles;

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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getHorraire(): ?string
    {
        return $this->horraire;
    }

    public function setHorraire(string $horraire): self
    {
        $this->horraire = $horraire;

        return $this;
    }

    public function getDuree(): ?string
    {
        return $this->duree;
    }

    public function setDuree(string $duree): self
    {
        $this->duree = $duree;

        return $this;
    }


}
