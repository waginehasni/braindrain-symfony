<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;



/**
 * Abonnement
 *
 * @ORM\Table(name="Abonnement")
 * @ORM\Entity(repositoryClass="App\Repository\AbonnementRepository")
 */
class Abonnement
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
     * @var string|null
     *
     * @ORM\Column(name="type", type="string", length=20, nullable=true)
     * @Assert\Length(
     *      min = 2,
     *      max = 20,
     *      minMessage = "Type trop court",
     *      maxMessage = "Type ne doit pas dépasser 20 caractères"
     * )
     * @Assert\NotBlank(
     *     message=" Veuillez donner le type d'abonnement"
     * )
     */
    private $type;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="dateCreation", type="date", nullable=true)
     * @Assert\NotNull
     */
    private $datecreation;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="dateExpiration", type="date", nullable=true)
     * @Assert\NotNull
     */
    private $dateexpiration;

    /**
     * @var int|null
     *
     * @ORM\Column(name="validite", type="integer", nullable=true)
     *  @Assert\Positive(message= "entrez un nombre positif")
     * @Assert\NotBlank(
     *     message=" Veuillez donner la validité d'abonnement"
     * )
     */
    private $validite;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getDatecreation(): ?\DateTimeInterface
    {
        return $this->datecreation;
    }

    public function setDatecreation(?\DateTimeInterface $datecreation): self
    {
        $this->datecreation = $datecreation;

        return $this;
    }

    public function getDateexpiration(): ?\DateTimeInterface
    {
        return $this->dateexpiration;
    }

    public function setDateexpiration(?\DateTimeInterface $dateexpiration): self
    {
        $this->dateexpiration = $dateexpiration;

        return $this;
    }

    public function getValidite(): ?int
    {
        return $this->validite;
    }

    public function setValidite(?int $validite): self
    {
        $this->validite = $validite;

        return $this;
    }


}
