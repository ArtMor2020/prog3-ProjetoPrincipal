<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    private int $Id = 0;
    private string $Name = '';
    private string $Email = '';
    private string $Password = '';
    private string $About = '';
    private bool $IsPrivate = 0;
    private bool $IsBanned = 0;
    private bool $IsDeleted = 0;

    public function getId(): int { return $this->Id; }
    public function setId(int $id): void { $this->Id = $id; }

    public function getName(): string { return $this->Name; }
    public function setName(string $name): void { $this->Name = $name; }

    public function getEmail(): string { return $this->Email; }
    public function setEmail(string $email): void { $this->Email = $email; }

    public function getPassword(): string { return $this->Password; }
    public function setPassword(string $password): void { $this->Password = $password; }

    public function getAbout(): string { return $this->About; }
    public function setAbout(string $about): void { $this->About = $about; }

    public function getIsPrivate(): bool { return $this->IsPrivate; }
    public function setIsPrivate(bool $isPrivate): void { $this->IsPrivate = $isPrivate; }

    public function getIsBanned(): bool { return $this->IsBanned; }
    public function setIsBanned(bool $isBanned): void { $this->IsBanned = $isBanned; }

    public function getIsDeleted(): bool { return $this->IsDeleted; }
    public function setIsDeleted(bool $isDeleted): void { $this->IsDeleted = $isDeleted; }
}
