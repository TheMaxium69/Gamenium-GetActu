<?php

namespace App\Entity;

use App\Repository\VideoRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: VideoRepository::class)]
class Video
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['video:read'])]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[Groups(['video:read'])]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Groups(['video:read'])]
    private ?string $ytId = null;

    #[ORM\Column(length: 255)]
    #[Groups(['video:read'])]
    private ?string $thumbnailUrl = null;

    #[ORM\Column(length: 255)]
    #[Groups(['video:read'])]
    private ?string $publishTime = null;

    #[ORM\ManyToOne(inversedBy: 'videos')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['video:read'])]
    private ?Channel $channelId = null;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getYtId(): ?string
    {
        return $this->ytId;
    }

    public function setYtId(string $ytId): static
    {
        $this->ytId = $ytId;

        return $this;
    }

    public function getThumbnailUrl(): ?string
    {
        return $this->thumbnailUrl;
    }

    public function setThumbnailUrl(string $thumbnailUrl): static
    {
        $this->thumbnailUrl = $thumbnailUrl;

        return $this;
    }

    public function getPublishTime(): ?string
    {
        return $this->publishTime;
    }

    public function setPublishTime(string $publishTime): static
    {
        $this->publishTime = $publishTime;

        return $this;
    }

    public function getChannelId(): ?Channel
    {
        return $this->channelId;
    }

    public function setChannelId(?Channel $channelId): static
    {
        $this->channelId = $channelId;

        return $this;
    }
}
