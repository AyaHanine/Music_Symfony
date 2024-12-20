<?php

namespace App\Entity;

use App\Repository\SongRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SongRepository::class)]
class Song
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $artist = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $album = null;

    #[ORM\Column(length: 255)]
    private ?\DateTime $releaseDate = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $genre = null;

    /**
     * @var Collection<int, Playlist>
     */
    #[ORM\ManyToMany(targetEntity: Playlist::class, mappedBy: 'songs')]
    private Collection $playlists;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'song')]
    private Collection $comments;

    public function __construct()
    {
        $this->playlists = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getArtist(): ?string
    {
        return $this->artist;
    }

    public function setArtist(string $artist): static
    {
        $this->artist = $artist;

        return $this;
    }

    public function getAlbum(): ?string
    {
        return $this->album;
    }

    public function setAlbum(?string $album): static
    {
        $this->album = $album;

        return $this;
    }

    public function getReleaseDate(): ?string
    {
        return $this->releaseDate;
    }

    public function setReleaseDate(string $releaseDate): static
    {
        $this->releaseDate = $releaseDate;

        return $this;
    }

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(?string $genre): static
    {
        $this->genre = $genre;

        return $this;
    }

    /**
     * @return Collection<int, Playlist>
     */
    public function getPlaylists(): Collection
    {
        return $this->playlists;
    }

    public function addPlaylist(Playlist $playlist): static
    {
        if (!$this->playlists->contains($playlist)) {
            $this->playlists->add($playlist);
            $playlist->addSong($this);
        }

        return $this;
    }

    public function removePlaylist(Playlist $playlist): static
    {
        if ($this->playlists->removeElement($playlist)) {
            $playlist->removeSong($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setSong($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getSong() === $this) {
                $comment->setSong(null);
            }
        }

        return $this;
    }
}
