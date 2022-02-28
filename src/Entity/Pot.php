<?php

namespace App\Entity;

use App\Repository\PotRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=PotRepository::class)
 */
class Pot
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups ({"add_pot"})
     * @Groups ({"show_pot"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups ({"add_pot"})
     * @Groups ({"show_pot"})
     */
    private $name;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups ({"add_pot"})
     * @Groups ({"show_pot"})
     */
    private $dateGoal;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups ({"add_pot"})
     * @Groups ({"show_pot"})
     */
    private $amountGoal;

    /**
     * @ORM\Column(type="datetime")
     * @Groups ({"show_pot"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="pots")
     * @ORM\JoinColumn(nullable=false)
     * @Groups ({"add_pot"})
     * @Groups ({"show_pot"})
     */
    private $user;

    public function __construct ()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDateGoal(): ?\DateTimeInterface
    {
        return $this->dateGoal;
    }

    public function setDateGoal(?\DateTimeInterface $dateGoal): self
    {
        $this->dateGoal = $dateGoal;

        return $this;
    }

    public function getAmountGoal(): ?\DateTimeInterface
    {
        return $this->amountGoal;
    }

    public function setAmountGoal(?\DateTimeInterface $amountGoal): self
    {
        $this->amountGoal = $amountGoal;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
