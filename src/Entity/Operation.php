<?php

namespace App\Entity;

use App\Repository\OperationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=OperationRepository::class)
 */
class Operation
{

    public function __construct ()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean", options = {"default" : true})
     * @Groups ({"show_operation"})
     */
    private $type;

    /**
     * @ORM\Column(type="integer", options = {"unsigned" : true})
     * @Groups ({"show_operation"})
     */
    private $amount;

    /**
     * @ORM\Column(type="datetime")
     * @Groups ({"show_operation"})
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=Pot::class, inversedBy="operations")
     * @ORM\JoinColumn(nullable=false)
     * @Groups ({"show_operation"})
     */
    private $pot;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="operations")
     * @ORM\JoinColumn(nullable=false)
     * @Groups ({"show_operation"})
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?bool
    {
        return $this->type;
    }

    public function setType(bool $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

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

    public function getPot(): ?Pot
    {
        return $this->pot;
    }

    public function setPot(?Pot $pot): self
    {
        $this->pot = $pot;

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
