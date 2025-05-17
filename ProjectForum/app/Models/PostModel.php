<?php

namespace App\Models;

use CodeIgniter\Model;
use DateTime;

class PostModel extends Model
{
    private int $Id = 0;
    private int $IdUser = 0;
    private int $IdCommunity = 0;
    private string $Title = '';
    private string $Description = '';
    private ?DateTime $PostedAt = null;
    private ?DateTime $UpdatedAt = null;
    private bool $IsApproved = 0;
    private bool $IsDeleted = 0;

    public function getId(): int { return $this->Id; }
    public function setId(int $id): void { $this->Id = $id; }

    public function getIdUser(): int { return $this->IdUser; }
    public function setIdUser(int $idUser): void { $this->IdUser = $idUser; }

    public function getIdCommunity(): int { return $this->IdCommunity; }
    public function setIdCommunity(int $idCommunity): void { $this->IdCommunity = $idCommunity; }

    public function getTitle(): string { return $this->Title; }
    public function setTitle(string $title): void { $this->Title = $title; }

    public function getDescription(): string { return $this->Description; }
    public function setDescription(string $description): void { $this->Description = $description; }

    public function getPostedAt(): ?DateTime { return $this->PostedAt; }
    public function setPostedAt(?DateTime $postedAt): void { $this->PostedAt = $postedAt; }

    public function getUpdatedAt(): ?DateTime { return $this->UpdatedAt; }
    public function setUpdatedAt(?DateTime $updatedAt): void { $this->UpdatedAt = $updatedAt; }

    public function getIsApproved(): bool { return $this->IsApproved; }
    public function setIsApproved(bool $isApproved): void { $this->IsApproved = $isApproved; }

    public function getIsDeleted(): bool { return $this->IsDeleted; }
    public function setIsDeleted(bool $isDeleted): void { $this->IsDeleted = $isDeleted; }
}
