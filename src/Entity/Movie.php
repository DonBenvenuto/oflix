<?php

namespace App\Entity;

use App\Entity\Review;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\MovieRepository;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=MovieRepository::class)
 * 
 * Ecoute les événements du cycle de vie de l'entité
 * @link https://symfony.com/doc/current/doctrine/events.html#doctrine-lifecycle-callbacks
 * @ORM\HasLifecycleCallbacks()
 */
class Movie
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"get_collection", "get_item"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Groups({"get_collection", "get_item"})
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotBlank
     * @Groups({"get_collection", "get_item"})
     */
    private $type;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Assert\NotBlank
     * @Groups({"get_collection", "get_item"})
     */
    private $releaseDate;

    /**
     * @ORM\OneToMany(targetEntity=Season::class, mappedBy="movie", cascade={"remove"})
     * @Groups({"get_collection", "get_item"})
     */
    private $seasons;

    /**
     * @ORM\ManyToMany(targetEntity=Genre::class, inversedBy="movies")
     * 
     * Si on souhaite qu'un film ait au moins 1 genre
     * @Assert\Count(min=1)
     * @Groups({"get_collection", "get_item"})
     */
    private $genres;

    /**
     * @ORM\OneToMany(targetEntity=Casting::class, mappedBy="movie", orphanRemoval=true)
     * @ORM\OrderBy({"creditOrder"="ASC"})
     * @Groups({"get_item"})
     * 
     * @link https://www.doctrine-project.org/projects/doctrine-orm/en/2.10/reference/annotations-reference.html#orderby
     */
    private $castings;

    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotBlank
     * @Groups({"get_collection", "get_item"})
     */
    private $duration;

    /**
     * @ORM\Column(type="string", length=600)
     * @Assert\NotBlank
     * @Groups({"get_collection", "get_item"})
     */
    private $summary;
    
    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank
     * @Groups({"get_item"})
     */
    private $synopsis;

    /**
     * @ORM\Column(type="string", length=2048, nullable=true)
     * @Groups({"get_collection", "get_item"})
     */
    private $poster;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups({"get_collection", "get_item"})
     */
    private $rating;
    
    /**
     * @ORM\OneToMany(targetEntity=Review::class, mappedBy="movie", cascade={"remove"})
     * @Groups({"get_item"})
     */
    private $reviews;

    /**
     * @ORM\Column(type="string", length=500)
     * @Groups({"get_collection", "get_item"})
     */
    private $slug;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    public function __construct()
    {
        $this->releaseDate = new DateTime();
        // ArrayCollection est un "super tableau", càd un tableau PHP normal mais à la sauce objet
        $this->seasons = new ArrayCollection();
        $this->genres = new ArrayCollection();
        $this->castings = new ArrayCollection();
        $this->reviews = new ArrayCollection();
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getReleaseDate(): ?\DateTimeInterface
    {
        return $this->releaseDate;
    }

    public function setReleaseDate(?\DateTimeInterface $releaseDate): self
    {
        $this->releaseDate = $releaseDate;

        return $this;
    }

    /**
     * @return Collection|Season[]
     */
    public function getSeasons(): Collection
    {
        return $this->seasons;
    }

    public function addSeason(Season $season): self
    {
        if (!$this->seasons->contains($season)) {
            $this->seasons[] = $season;
            $season->setMovie($this);
        }

        return $this;
    }

    public function removeSeason(Season $season): self
    {
        if ($this->seasons->removeElement($season)) {
            // set the owning side to null (unless already changed)
            if ($season->getMovie() === $this) {
                $season->setMovie(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Genre[]
     */
    public function getGenres(): Collection
    {
        return $this->genres;
    }

    public function addGenre(Genre $genre): self
    {
        if (!$this->genres->contains($genre)) {
            $this->genres[] = $genre;
        }

        return $this;
    }

    public function removeGenre(Genre $genre): self
    {
        $this->genres->removeElement($genre);

        return $this;
    }

    /**
     * @return Collection|Casting[]
     */
    public function getCastings(): Collection
    {
        return $this->castings;
    }

    public function addCasting(Casting $casting): self
    {
        if (!$this->castings->contains($casting)) {
            $this->castings[] = $casting;
            $casting->setMovie($this);
        }

        return $this;
    }

    public function removeCasting(Casting $casting): self
    {
        if ($this->castings->removeElement($casting)) {
            // set the owning side to null (unless already changed)
            if ($casting->getMovie() === $this) {
                $casting->setMovie(null);
            }
        }

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(string $summary): self
    {
        $this->summary = $summary;

        return $this;
    }

    public function getSynopsis(): ?string
    {
        return $this->synopsis;
    }

    public function setSynopsis(string $synopsis): self
    {
        $this->synopsis = $synopsis;

        return $this;
    }

    public function getPoster(): ?string
    {
        return $this->poster;
    }

    public function setPoster(?string $poster): self
    {
        $this->poster = $poster;

        return $this;
    }

    public function getRating(): ?float
    {
        return $this->rating;
    }

    public function setRating(?float $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * @return Collection|Review[]
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): self
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews[] = $review;
            $review->setMovie($this);
        }

        return $this;
    }

    public function removeReview(Review $review): self
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getMovie() === $this) {
                $review->setMovie(null);
            }
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

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

    /**
     * Mise à jour automatique de la date
     * @ORM\PreUpdate
     */
    public function setUpdatedAtValue()
    {
        // Date courante
        $this->updatedAt = new DateTime();
    }
}
