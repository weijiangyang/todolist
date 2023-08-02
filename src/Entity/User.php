<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity('email')]
#[ORM\EntityListeners(['App\EntityListener\UserListener'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\Length(min: 2, max: 180)]
    #[Assert\Email()]
    #[Assert\NotNull()]
    private ?string $email = null;

    #[ORM\Column]
    #[Assert\NotNull()]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Assert\Blank()]
    private ?string $password;

    private $plainPassword = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min: 2, max: 255)]
    #[Assert\NotBlank()]
    private ?string $nickname = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'createur', targetEntity: Task::class)]
    private Collection $taches;

    #[ORM\OneToMany(mappedBy: 'creater', targetEntity: Task::class)]
    private Collection $tasks;

    #[ORM\ManyToMany(targetEntity: Task::class, mappedBy: 'operateurs')]
    private Collection $tasksAfaire;

    public function __construct()
    {

        $this->createdAt = new \DateTime();
        $this->taches = new ArrayCollection();
        $this->tasks = new ArrayCollection();
        $this->tasksAfaire = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(string $nickname): static
    {
        $this->nickname = $nickname;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

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
           
        }

        return $this;
    }

    public function removeTach(Task $tach): static
    {
        if ($this->taches->removeElement($tach)) {
            // set the owning side to null (unless already changed)
           
        }

        return $this;
    }

    /**
     * @return Collection<int, Task>
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): static
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks->add($task);
            $task->setCreater($this);
        }

        return $this;
    }

    public function removeTask(Task $task): static
    {
        if ($this->tasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getCreater() === $this) {
                $task->setCreater(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Task>
     */
    public function getTasksAfaire(): Collection
    {
        return $this->tasksAfaire;
    }

    public function addTasksAfaire(Task $tasksAfaire): static
    {
        if (!$this->tasksAfaire->contains($tasksAfaire)) {
            $this->tasksAfaire->add($tasksAfaire);
            $tasksAfaire->addOperateur($this);
        }

        return $this;
    }

    public function removeTasksAfaire(Task $tasksAfaire): static
    {
        if ($this->tasksAfaire->removeElement($tasksAfaire)) {
            $tasksAfaire->removeOperateur($this);
        }

        return $this;
    }

    public function __toString()
    {
        return $this->nickname;
    }
}
