<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Reclamation
 *
 * @ORM\Table(name="Reclamation")
 * @ORM\Entity(repositoryClass="App\Repository\ReclamationRepository")
 */
class Reclamation
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int|null
     *
     * @ORM\Column(name="idUser", type="integer", nullable=true)
     */
    private $iduser;

    /**
     * @var string|null
     *
     * @ORM\Column(name="titre", type="string", length=255, nullable=true)
     *  @Assert\Length(
     *      min = 2,
     *      max = 50,
     *      minMessage = "Titre trop court",
     *      maxMessage = "titre ne doit pas dépasser 50 caractères"
     * )
     * @Assert\NotBlank(
     *     message=" Veuillez donner un titre pour votre réclamation"
     * )
     *
     */
    private $titre;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="string", length=5000, nullable=true)
     * @Assert\Length(
     *      min = 2,
     *      max = 5000,
     *      minMessage = "Description trop court",
     *      maxMessage = "Description ne doit pas dépasser 5000 caractères"
     * )
     * @Assert\NotBlank(
     *     message=" Veuillez donner la description pour votre réclamation"
     * )
     */
    private $description;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="dateReponse", type="date", nullable=true)
     */
    private $datereponse;

    /**
     * @var string|null
     *
     * @ORM\Column(name="etat", type="string", length=10, nullable=true)
     */
    private $etat;

    /**
     * @var string|null
     *
     * @ORM\Column(name="reponse", type="string", length=500, nullable=true)
     * @Assert\Length(
     *      min = 2,
     *      max = 5000,
     *      minMessage = "Reponse trop court",
     *      maxMessage = "Reponse ne doit pas dépasser 5000 caractères"
     * )
     */
    private $reponse;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="dateReclamation", type="datetime", nullable=true)
     */
    private $datereclamation;

    /**
     * @var string|null
     *
     * @ORM\Column(name="image", type="string", length=1000, nullable=true)
     */
    private $image;

    /**
     * @var string|null
     *
     * @ORM\Column(name="satisfaction", type="string", length=30, nullable=true)
     */
    private $satisfaction;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIduser(): ?int
    {
        return $this->iduser;
    }

    public function setIduser(?int $iduser): self
    {
        $this->iduser = $iduser;

        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(?string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDatereponse(): ?\DateTimeInterface
    {
        return $this->datereponse;
    }

    public function setDatereponse(?\DateTimeInterface $datereponse): self
    {
        $this->datereponse = $datereponse;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(?string $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getReponse(): ?string
    {
        return $this->reponse;
    }

    public function setReponse(?string $reponse): self
    {
        $this->reponse = $reponse;

        return $this;
    }

    public function getDatereclamation(): ?\DateTimeInterface
    {
        return $this->datereclamation;
    }

    public function setDatereclamation(?\DateTimeInterface $datereclamation): self
    {
        $this->datereclamation = $datereclamation;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getSatisfaction(): ?string
    {
        return $this->satisfaction;
    }

    public function setSatisfaction(?string $satisfaction): self
    {
        $this->satisfaction = $satisfaction;

        return $this;
    }


}
