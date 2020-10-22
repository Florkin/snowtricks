<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 */
class Category
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=70)
     */
    private $title;

    /**
     * @ORM\ManyToMany(targetEntity=Trick::class, mappedBy="categories")
     */
    private $relatedTricks;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="childCategories")
     */
    private $parentCategory;

    /**
     * @ORM\OneToMany(targetEntity=Category::class, mappedBy="parentCategory")
     */
    private $childCategories;

    /**
     * @ORM\Column(type="text", nullable=true)
     * * @Assert\Length(
     *    max = 1000,
     *    maxMessage = "La description doit contenir au maximum {{ limit }} caractères",
     *    allowEmptyString = true
     * )
     */
    private $description;


    public function __construct()
    {
        $this->relatedTricks = new ArrayCollection();
        $this->childCategories = new ArrayCollection();
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

    /**
     * @return Collection|Trick[]
     */
    public function getRelatedTricks(): Collection
    {
        return $this->relatedTricks;
    }

    public function addRelatedTrick(Trick $relatedTrick): self
    {
        if (!$this->relatedTricks->contains($relatedTrick)) {
            $this->relatedTricks[] = $relatedTrick;
            $relatedTrick->addCategory($this);
        }

        return $this;
    }

    public function removeRelatedTrick(Trick $relatedTrick): self
    {
        if ($this->relatedTricks->contains($relatedTrick)) {
            $this->relatedTricks->removeElement($relatedTrick);
            $relatedTrick->removeCategory($this);
        }

        return $this;
    }

    public function getParentCategory(): ?self
    {
        return $this->parentCategory;
    }

    public function setParentCategory(?self $parentCategory): self
    {
        $this->parentCategory = $parentCategory;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getChildCategories(): Collection
    {
        return $this->childCategories;
    }

    public function addChildCategory(self $childCategory): self
    {
        if (!$this->childCategories->contains($childCategory)) {
            $this->childCategories[] = $childCategory;
            $childCategory->setParentCategory($this);
        }

        return $this;
    }

    public function removeChildCategory(self $childCategory): self
    {
        if ($this->childCategories->contains($childCategory)) {
            $this->childCategories->removeElement($childCategory);
            // set the owning side to null (unless already changed)
            if ($childCategory->getParentCategory() === $this) {
                $childCategory->setParentCategory(null);
            }
        }

        return $this;
    }

    public function getSlug(): string
    {
        return (new Slugify())->slugify($this->title);
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
