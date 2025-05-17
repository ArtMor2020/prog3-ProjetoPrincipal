<?php

namespace App\Models;

use CodeIgniter\Model;

class CommunityModel extends Model
{
    private int $Id = 0;
    private string $Name = '';
    private string $Description = '';
    private int $IdOwner = 0;
    private bool $IsPrivate = 0;
    private bool $IsDeleted = 0;
    private bool $IsBanned = 0;

    public function getId(): int { return $this->Id; }
    public function setId(int $id): void { $this->Id = $id; }

    public function getName(): string { return $this->Name; }
    public function setName(string $name): void { $this->Name = $name; }

    public function getDescription(): string { return $this->Description; }
    public function setDescription(string $description): void { $this->Description = $description; }

    public function getIdOwner(): int { return $this->IdOwner; }
    public function setIdOwner(int $idOwner): void { $this->IdOwner = $idOwner; }

    public function getIsPrivate(): bool { return $this->IsPrivate; }
    public function setIsPrivate(bool $isPrivate): void { $this->IsPrivate = $isPrivate; }

    public function getIsDeleted(): bool { return $this->IsDeleted; }
    public function setIsDeleted(bool $isDeleted): void { $this->IsDeleted = $isDeleted; }

    public function getIsBanned(): bool { return $this->IsBanned; }
    public function setIsBanned(bool $isBanned): void { $this->IsBanned = $isBanned; }
}
