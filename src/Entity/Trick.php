<?php

namespace App\Entity;

use App\Repository\TrickRepository;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=TrickRepository::class)
 * @UniqueEntity("title")
 */
class Trick
{
    const DIFFICULTIES = [
        1 => "Very easy",
        2 => "Easy",
        3 => "Intermediate",
        4 => "Hard",
        5 => "Very hard"
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=40)
     * * @Assert\Length(
     *    min = 5,
     *    max = 40,
     *    minMessage = "Le titre doit contenir au moins {{ limit }} caractères",
     *    maxMessage = "Le titre doit contenir au maximum {{ limit }} caractères",
     *    allowEmptyString = false
     * )
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Assert\Length(
     *    min = 100,
     *    max = 10000,
     *    minMessage = "La description doit contenir au moins {{ limit }} caractères",
     *    maxMessage = "La description doit contenir au maximum {{ limit }} caractères",
     *    allowEmptyString = false
     * )
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_add;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_update;

    /**
     * @ORM\Column(type="boolean")
     */
    private $visible = false;

    /**
     * @ORM\Column(type="smallint")
     */
    private $difficulty;

    /**
     * @ORM\ManyToMany(targetEntity=Category::class, inversedBy="relatedTricks")
     */
    private $categories;

    /**
     * @ORM\OneToMany(targetEntity=ChatPost::class, mappedBy="trick", orphanRemoval=true)
     */
    private $chatPosts;

    /**
     * @ORM\OneToMany(targetEntity=Picture::class, mappedBy="trick", orphanRemoval=true, cascade={"persist"})
     */
    private $pictures;

    /**
     * @ORM\OneToMany(targetEntity=EmbedVideo::class, mappedBy="trick", orphanRemoval=true, cascade={"persist"})
     * @Assert\Valid()
     */
    private $videos;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="tricks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="tricksUpdate")
     */
    private $updatedBy;


    /**
     * Trick constructor.
     */
    public function __construct()
    {
        $this->date_add = new \DateTime();
        $this->categories = new ArrayCollection();
        $this->chatPosts = new ArrayCollection();
        $this->pictures = new ArrayCollection();
        $this->videos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDateAdd(): ?\DateTimeInterface
    {
        return $this->date_add;
    }

    public function setDateAdd(\DateTimeInterface $date_add): self
    {
        $this->date_add = $date_add;

        return $this;
    }

    public function getDateUpdate(): ?\DateTimeInterface
    {
        return $this->date_update;
    }

    public function setDateUpdate(?\DateTimeInterface $date_update): self
    {
        $this->date_update = $date_update;

        return $this;
    }

    public function getVisible(): ?bool
    {
        return $this->visible;
    }

    public function setVisible(bool $visible): self
    {
        $this->visible = $visible;

        return $this;
    }

    public function getSlug(): string
    {
        return (new Slugify())->slugify($this->title);
    }

    /**
     * @return string
     * Return date_add or date_update if exist, string formated
     */
    public function getStringedDatetime(): string
    {
        if (isset($this->date_update)) {
            return $this->date_update->format("yy-m-d h:m:s");
        };

        return $this->date_add->format("yy-m-d h:m:s");
    }

    public function getDifficulty(): ?int
    {
        return $this->difficulty;
    }

    public function setDifficulty(int $difficulty): self
    {
        $this->difficulty = $difficulty;

        return $this;
    }

    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);
        }

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
            $chatPost->setTrick($this);
        }

        return $this;
    }

    public function removeChatPost(ChatPost $chatPost): self
    {
        if ($this->chatPosts->contains($chatPost)) {
            $this->chatPosts->removeElement($chatPost);
            // set the owning side to null (unless already changed)
            if ($chatPost->getTrick() === $this) {
                $chatPost->setTrick(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Picture[]
     */
    public function getPictures(): Collection
    {
        return $this->pictures;
    }

    public function addPicture(Picture $picture): self
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures[] = $picture;
            $picture->setTrick($this);
        }

        return $this;
    }

    public function removePicture(Picture $picture): self
    {
        if ($this->pictures->contains($picture)) {
            $this->pictures->removeElement($picture);
            // set the owning side to null (unless already changed)
            if ($picture->getTrick() === $this) {
                $picture->setTrick(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|EmbedVideo[]
     */
    public function getVideos(): Collection
    {
        return $this->videos;
    }

    public function addVideo(EmbedVideo $video): self
    {
        if (!$this->videos->contains($video)) {
            $this->videos[] = $video;
            $video->setTrick($this);
        }

        return $this;
    }

    public function removeVideo(EmbedVideo $video): self
    {
        if ($this->videos->contains($video)) {
            $this->videos->removeElement($video);
            // set the owning side to null (unless already changed)
            if ($video->getTrick() === $this) {
                $video->setTrick(null);
            }
        }

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getUpdatedBy(): ?User
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?User $updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

}
