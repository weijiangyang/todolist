<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Task::class)]
    private Collection $taches;

    public function __construct()
    {
       
        $this->taches = new ArrayCollection();
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

   

   
    

    /**
     * @return Collection<int, Task>
     */
    public function getTaches(): Collection
    {
        return $this->taches;
    }

    public function addTach(Task $tach): static
    {
        if (!$this->taches->contains($tach)) {
            $this->taches->add($tach);
            $tach->setCategory($this);
        }

        return $this;
    }

    public function removeTach(Task $tach): static
    {
        if ($this->taches->removeElement($tach)) {
            // set the owning side to null (unless already changed)
            if ($tach->getCategory() === $this) {
                $tach->setCategory(null);
            }
        }

        return $this;
    }
}
