<?php

namespace App\Entity;

use DateTime;
use Doctrine\DBAL\Types\Types;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\TaskRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
#[UniqueEntity('name')]
#[ORM\HasLifecycleCallbacks()]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    private ?\DateTime $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $updatedAt = null;

    #[ORM\Column]
    private ?\DateTime $finishAt = null;

    

    #[ORM\ManyToOne(inversedBy: 'tasks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Status $status = null;

    #[ORM\ManyToOne(inversedBy: 'tasks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Priority $priority = null;

    #[ORM\ManyToOne(inversedBy: 'taches')]
    private ?Category $category = null;

    #[ORM\ManyToOne(inversedBy: 'tasks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $creater = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'tasksAfaire')]
    private Collection $operateurs;

    #[ORM\Column]
    private ?bool $isLate = false;

    #[ORM\Column]
    private ?bool $isWarned = false;

    

    public function __construct()
    {
      
        $this->createdAt = new \DateTime();
        $this->operateurs = new ArrayCollection();
    }

    /**
     * @ORM\PrePersist
     */
    public function setUpdatedAtValue()
    {
        $this->updatedAt = new \DateTime();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTime $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getFinishAt(): ?\DateTime
    {
        return $this->finishAt;
    }

    public function setFinishAt(\DateTime $finishAt): static
    {
        $this->finishAt = $finishAt;

        return $this;
    }


   

   

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(?Status $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getPriority(): ?Priority
    {
        return $this->priority;
    }

    public function setPriority(?Priority $priority): static
    {
        $this->priority = $priority;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getCreater(): ?User
    {
        return $this->creater;
    }

    public function setCreater(?User $creater): static
    {
        $this->creater = $creater;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getOperateurs(): Collection
    {
        return $this->operateurs;
    }

    public function addOperateur(User $operateur): static
    {
        if (!$this->operateurs->contains($operateur)) {
            $this->operateurs->add($operateur);
        }

        return $this;
    }

    public function removeOperateur(User $operateur): static
    {
        $this->operateurs->removeElement($operateur);

        return $this;
    }

    public function isIsLate(): ?bool
    {
        return $this->isLate;
    }

    public function setIsLate(bool $isLate): static
    {
        $this->isLate = $isLate;

        return $this;
    }

    public function isIsWarned(): ?bool
    {
        return $this->isWarned;
    }

    public function setIsWarned(bool $isWarned): static
    {
        $this->isWarned = $isWarned;

        return $this;
    }

   
}
