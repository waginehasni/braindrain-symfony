<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Offres
 *
 * @ORM\Table(name="offres")
 * @ORM\Entity
 */
class Offres
{
    /**
     * @var int
     *
     * @ORM\Column(name="idOffre", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idoffre;

    /**
     * @var string
     *
     * @ORM\Column(name="nomOffre", type="string", length=50, nullable=false)
     */
    private $nomoffre;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=50, nullable=false)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=150, nullable=false)
     */
    private $description;

    public function getIdoffre(): ?int
    {
        return $this->idoffre;
    }

    public function getNomoffre(): ?string
    {
        return $this->nomoffre;
    }

    public function setNomoffre(string $nomoffre): self
    {
        $this->nomoffre = $nomoffre;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }


}
