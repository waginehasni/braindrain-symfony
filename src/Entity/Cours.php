<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Cours
 *
 * @ORM\Table(name="cours")
 * @ORM\Entity(repositoryClass="App\Repository\CoursRepository")
 */
class Cours
{
    /**
     * @var int
     *
     * @ORM\Column(name="numCours", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public $numcours;

    /**
     * @var int
     * @Assert\NotNull
     * @ORM\Column(name="numReservation", type="integer", nullable=false)
     */
    public $numreservation;

    /**
     * @var string
     * @Assert\NotNull
     * @ORM\Column(name="nomCours", type="string", length=30, nullable=false)
     */
    public $nomcours;

    /**
     * @var string
     * @Assert\NotNull
     * @ORM\Column(name="nomCoach", type="string", length=30, nullable=false)
     */
    public $nomcoach;

    /**
     * @var string
     * @Assert\NotNull
     * @ORM\Column(name="type", type="string", length=30, nullable=false)
     */
    public $type;

    /**
     * @var int
     * @Assert\NotNull
     * @ORM\Column(name="prix", type="integer", nullable=false)
     */
    public $prix;

    public function getNumcours(): ?int
    {
        return $this->numcours;
    }

    public function getNumreservation(): ?int
    {
        return $this->numreservation;
    }

    public function setNumreservation(int $numreservation): self
    {
        $this->numreservation = $numreservation;

        return $this;
    }

    public function getNomcours(): ?string
    {
        return $this->nomcours;
    }

    public function setNomcours(string $nomcours): self
    {
        $this->nomcours = $nomcours;

        return $this;
    }

    public function getNomcoach(): ?string
    {
        return $this->nomcoach;
    }

    public function setNomcoach(string $nomcoach): self
    {
        $this->nomcoach = $nomcoach;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getPrix(): ?int
    {
        return $this->prix;
    }

    public function setPrix(int $prix): self
    {
        $this->prix = $prix;

        return $this;
    }


}
