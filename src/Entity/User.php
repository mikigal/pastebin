<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface {
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min="3", max="20", minMessage="Username should have 3 characters or more", maxMessage="Username should have 20 characters or less")
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min="6", max="32", minMessage="Password should have 6 characters or more", maxMessage="Username should have 32 characters or less")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $mail;

    /**
     * @ORM\Column(type="datetime")
     */
    private $register_date;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $salt;

    public function getId(): ?int {
        return $this->id;
    }

    public function getUsername(): ?string {
        return $this->username;
    }

    public function setUsername(string $username): self {
        $this->username = $username;
        return $this;
    }

    public function getPassword(): ?string {
        return $this->password;
    }

    public function setPassword(string $password): self {
        $this->password = $password;
        return $this;
    }

    public function getMail(): ?string {
        return $this->mail;
    }

    public function setMail(string $mail): self {
        $this->mail = $mail;
        return $this;
    }

    public function getRegisterDate(): ?\DateTimeInterface {
        return $this->register_date;
    }

    public function setRegisterDate(\DateTimeInterface $register_date): self {
        $this->register_date = $register_date;
        return $this;
    }

    public function getSalt(): ?string {
        return $this->salt;
    }

    public function setSalt(string $salt): self {
        $this->salt = $salt;
        return $this;
    }

    public function getRoles() {
        return ["ROLE_USER"];
    }

    public function eraseCredentials() {

    }
}
