<?php

namespace App\Models;

use CodeIgniter\Model;
use DateTime;

class PostViewModel extends Model
{
    private int $IdPost = 0;
    private int $IdUser = 0;
    private ?DateTime $ViewedAt = null;

    public function getIdPost(): int { return $this->IdPost; }
    public function setIdPost(int $idPost): void { $this->IdPost = $idPost; }

    public function getIdUser(): int { return $this->IdUser; }
    public function setIdUser(int $idUser): void { $this->IdUser = $idUser; }

    public function getViewedAt(): ?DateTime { return $this->ViewedAt; }
    public function setViewedAt(?DateTime $viewedAt): void { $this->ViewedAt = $viewedAt; }

}
