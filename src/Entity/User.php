<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $username;

    /**
     * @ORM\OneToMany(targetEntity=ChatPost::class, mappedBy="user", orphanRemoval=true)
     */
    private $chatPosts;

    /**
     * @ORM\OneToMany(targetEntity=Trick::class, mappedBy="author")
     */
    private $tricks;

    /**
     * @ORM\OneToMany(targetEntity=Trick::class, mappedBy="updatedBy")
     */
    private $tricksUpdate;

    public function __construct()
    {
        $this->chatPosts = new ArrayCollection();
        $this->tricks = new ArrayCollection();
        $this->tricksUpdate = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
//        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return Collection|ChatPost[]
     */
    public function getChatPosts(): Collection
    {
        return $this->chatPosts;
    }

    public function addChatPost(ChatPost $chatPost): self
    {
        if (!$this->chatPosts->contains($chatPost)) {
            $this->chatPosts[] = $chatPost;
            $chatPost->setUser($this);
        }

        return $this;
    }

    public function removeChatPost(ChatPost $chatPost): self
    {
        if ($this->chatPosts->contains($chatPost)) {
            $this->chatPosts->removeElement($chatPost);
            // set the owning side to null (unless already changed)
            if ($chatPost->getUser() === $this) {
                $chatPost->setUser(null);
            }
        }

        return $this;
    }

    public function getSlug(): string
    {
        return (new Slugify())->slugify($this->username);
    }

    /**
     * @return Collection|Trick[]
     */
    public function getTricks(): Collection
    {
        return $this->tricks;
    }

    public function addTrick(Trick $trick): self
    {
        if (!$this->tricks->contains($trick)) {
            $this->tricks[] = $trick;
            $trick->setAuthor($this);
        }

        return $this;
    }

    public function removeTrick(Trick $trick): self
    {
        if ($this->tricks->contains($trick)) {
            $this->tricks->removeElement($trick);
            // set the owning side to null (unless already changed)
            if ($trick->getAuthor() === $this) {
                $trick->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Trick[]
     */
    public function getTricksUpdate(): Collection
    {
        return $this->tricksUpdate;
    }

    public function addTricksUpdate(Trick $tricksUpdate): self
    {
        if (!$this->tricksUpdate->contains($tricksUpdate)) {
            $this->tricksUpdate[] = $tricksUpdate;
            $tricksUpdate->setUpdatedBy($this);
        }

        return $this;
    }

    public function removeTricksUpdate(Trick $tricksUpdate): self
    {
        if ($this->tricksUpdate->contains($tricksUpdate)) {
            $this->tricksUpdate->removeElement($tricksUpdate);
            // set the owning side to null (unless already changed)
            if ($tricksUpdate->getUpdatedBy() === $this) {
                $tricksUpdate->setUpdatedBy(null);
            }
        }

        return $this;
    }
}
