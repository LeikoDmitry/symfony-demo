<?php

declare(strict_types=1);

namespace SymfonyDemo\Entity;

use Doctrine\ORM\Mapping as ORM;
use Laminas\Form\Annotation;

/**
 * @ORM\Entity(repositoryClass="SymfonyDemo\Repository\UserRepository")
 * @ORM\Table(name="user")
 * @Annotation\Hydrator("Laminas\Hydrator\ReflectionHydrator")
 * @Annotation\Name("user")
 */
class User
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Annotation\Exclude()
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $fullName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", unique=true)
     * @Annotation\Validator({"name":"StringLength", "options":{"min":3, "max":25}})
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(type="string", unique=true)
     * @Annotation\Validator({"name":"EmailAddress"})
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @Annotation\Validator({"name":"StringLength", "options":{"min":6, "max":25}})
     */
    private $password;

    /**
     * @var array
     *
     * @ORM\Column(type="json")
     */
    private $roles = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setFullName(string $fullName): void
    {
        $this->fullName = $fullName;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        if (empty($roles)) {
            $roles[] = 'ROLE_USER';
        }

        return array_unique($roles);
    }

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    /**
     * @param  User  $user
     * @param $inputPassword
     *
     * @return bool
     */
    public static function verifyCredential(User $user, $inputPassword)
    {
        return password_verify($inputPassword, $user->getPassword());
    }
}
