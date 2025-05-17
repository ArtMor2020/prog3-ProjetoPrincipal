<?php

namespace App\Models;

use CodeIgniter\Model;
use DateTime;

class CommunityViewModel extends Model
{
    private int $IdCommunity = 0;
    private int $IdUser = 0;
    private ?DateTime $ViewedAt = null;

    public function getIdCommunity(): int { return $this->IdCommunity; }
    public function setIdCommunity(int $idCommunity): void { $this->IdCommunity = $idCommunity; }

    public function getIdUser(): int { return $this->IdUser; }
    public function setIdUser(int $idUser): void { $this->IdUser = $idUser; }

    public function getViewedAt(): ?DateTime { return $this->ViewedAt; }
    public function setViewedAt(?DateTime $viewedAt): void { $this->ViewedAt = $viewedAt; }
}
