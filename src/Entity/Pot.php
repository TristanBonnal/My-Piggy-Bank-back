<?php

namespace App\Entity;

use App\Repository\OperationRepository;
use App\Repository\PotRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
     * @Groups ({"show_pot", "show_user"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups ({"show_pot"})
     * @Groups ({"show_user"})
     * @Groups ({"show_operation"})
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups ({"show_pot"})
     * @Groups ({"show_operation"})
     */
    private $dateGoal;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups ({"show_pot"})
     * @Groups ({"show_operation"})
     */
    private $amountGoal;

    /**
     * @ORM\Column(type="datetime")
     * @Groups ({"show_pot"})
     * @Groups ({"show_operation"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="pots")
     * @ORM\JoinColumn(nullable=false)
     * @Groups ({"show_pot"})
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=Operation::class, mappedBy="pot")
     */
    private $operations;

    /**
     * Undocumented variable
     *@Groups ({"show_pot"})
     * @var float
     */
    private $amount;

    // La date du jour sera automatique associée à la propriété createdAt
    public function __construct ()
    {
        $this->createdAt = new \DateTime();
        $this->operations = new ArrayCollection();
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

    public function getAmountGoal():?int
    {
        return $this->amountGoal;
    }

    public function setAmountGoal(?int $amountGoal): self
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

    /**
     * @return Collection<int, Operation>
     */
    public function getOperations(): Collection
    {
        return $this->operations;
    }

    public function addOperation(Operation $operation): self
    {
        if (!$this->operations->contains($operation)) {
            $this->operations[] = $operation;
            $operation->setPot($this);
        }

        return $this;
    }

    public function removeOperation(Operation $operation): self
    {
        if ($this->operations->removeElement($operation)) {
            // set the owning side to null (unless already changed)
            if ($operation->getPot() === $this) {
                $operation->setPot(null);
            }
        }

        return $this;
    }

    /**
     * Get *@Groups ({"show_pot"})
     *
     * @return  float
     */ 
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set *@Groups ({"show_pot"})
     *
     * @param  float  $amount  *@Groups ({"show_pot"})
     *
     * @return  self
     */ 
    public function setAmount(float $amount)
    {
        $this->amount = $amount;

        return $this;
    }

     // Retourne le nom de la cagnotte en chaîne de caractères
    public function __toString()
    {
        return $this->getName();
    }
}
