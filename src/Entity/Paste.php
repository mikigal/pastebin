<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PasteRepository")
 */
class Paste
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $syntax;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $visibility;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $owner;

    /**
     * @ORM\Column(type="string", length=15000)
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     */
    private $upload_date;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSyntax(): ?string
    {
        return $this->syntax;
    }

    public function setSyntax(?string $syntax): self
    {
        $this->syntax = $syntax;

        return $this;
    }

    public function getVisibility(): ?int
    {
        return $this->visibility;
    }

    public function setVisibility(?int $visibility): self
    {
        $this->visibility = $visibility;

        return $this;
    }

    public function getOwner(): ?int
    {
        return $this->owner;
    }

    public function setOwner(?int $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getUploadDate(): ?\DateTimeInterface
    {
        return $this->upload_date;
    }

    public function setUploadDate(\DateTimeInterface $upload_date): self
    {
        $this->upload_date = $upload_date;

        return $this;
    }
}
