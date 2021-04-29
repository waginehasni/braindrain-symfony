<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Sponsors
 *
 * @ORM\Table(name="sponsors")
 * @ORM\Entity(repositoryClass="App\Repository\SponsorsRepository")
 */
class Sponsors
{
    /**
     * @var int
     *
     * @ORM\Column(name="idSponsor", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public $idsponsor;

    /**
     * @var string
     * * @Assert\NotNull
     *  @Assert\Type(
     *     type="string",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     * @ORM\Column(name="societe", type="string", length=50, nullable=false)
     */
    public $societe;

    /**
     * @var float
     * * @Assert\NotNull
     *  @Assert\Type(
     *     type="float",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     * @ORM\Column(name="budget", type="float", precision=10, scale=0, nullable=false)
     */
    public $budget;

    /**
     * @var int
     * * @Assert\NotNull
     *  @Assert\Type(
     *     type="integer",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     *  @Assert\Length(
     *      min = 8,
     *      max = 8,
     *      minMessage = "Your first name must be at least {{ limit }} characters long",
     *      maxMessage = "Your first name cannot be longer than {{ limit }} characters"
     * )
     * @ORM\Column(name="numtelephone", type="integer", nullable=false)
     */
    public $numtelephone;

    public function getIdsponsor(): ?int
    {
        return $this->idsponsor;
    }

    public function getSociete(): ?string
    {
        return $this->societe;
    }

    public function setSociete(string $societe): self
    {
        $this->societe = $societe;

        return $this;
    }

    public function getBudget(): ?float
    {
        return $this->budget;
    }

    public function setBudget(float $budget): self
    {
        $this->budget = $budget;

        return $this;
    }

    public function getNumtelephone(): ?int
    {
        return $this->numtelephone;
    }

    public function setNumtelephone(int $numtelephone): self
    {
        $this->numtelephone = $numtelephone;

        return $this;
    }


}
