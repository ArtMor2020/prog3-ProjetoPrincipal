<?php

namespace App\Models;

use CodeIgniter\Model;

class AttachmentModel extends Model
{
    private int $Id = 0;
    private string $Type = '';
    private string $Path = '';
    private bool $IsDeleted = '';

public function getId(): int { return $this->Id; }
public function setId(int $id): void { $this->Id = $id; }

public function getType(): string { return $this->Type; }
public function setType(string $type): void { $this->Type = $type; }

public function getPath(): string { return $this->Path; }
public function setPath(string $path): void { $this->Path = $path; }

public function getIsDeleted(): bool { return $this->IsDeleted; }
public function setIsDeleted(bool $isDeleted): void { $this->IsDeleted = $isDeleted; }

}
