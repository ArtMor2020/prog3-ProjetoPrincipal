<?php

namespace App\Models;

use CodeIgniter\Model;
use DateTime;

class CommentModel extends Model
{
    private int $Id = 0;
    private int $IdUser = 0;
    private int $IdParentPost = 0;
    private ?int $IdParentComment = null;
    private string $Content = '';
    private bool $IsDeleted = 0;
    private ?DateTime $CreatedAt = null;
    private ?DateTime $UpdatedAt = null;

    public function getId(): int { return $this->Id; }
    public function setId(int $id): void { $this->Id = $id; }

    public function getIdUser(): int { return $this->IdUser; }
    public function setIdUser(int $idUser): void { $this->IdUser = $idUser; }

    public function getIdParentPost(): int { return $this->IdParentPost; }
    public function setIdParentPost(int $idParentPost): void { $this->IdParentPost = $idParentPost; }

    public function getIdParentComment(): ?int { return $this->IdParentComment; }
    public function setIdParentComment(?int $idParentComment): void { $this->IdParentComment = $idParentComment; }

    public function getContent(): string { return $this->Content; }
    public function setContent(string $content): void { $this->Content = $content; }

    public function getIsDeleted(): bool { return $this->IsDeleted; }
    public function setIsDeleted(bool $isDeleted): void { $this->IsDeleted = $isDeleted; }

    public function getCreatedAt(): ?DateTime { return $this->CreatedAt; }
    public function setCreatedAt(?DateTime $createdAt): void { $this->CreatedAt = $createdAt; }

    public function getUpdatedAt(): ?DateTime { return $this->UpdatedAt; }
    public function setUpdatedAt(?DateTime $updatedAt): void { $this->UpdatedAt = $updatedAt; }
}
