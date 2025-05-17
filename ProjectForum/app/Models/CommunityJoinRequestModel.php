<?php

namespace App\Models;

use CodeIgniter\Model;
use DateTime;

class CommunityJoinRequestModel extends Model
{
    private int $IdCommunity = 0;
    private int $IdUser = 0;
    private ?DateTime $RequestedAt = null;
    private string $Status = '';

    public function getIdCommunity(): int { return $this->IdCommunity; }
    public function setIdCommunity(int $idCommunity): void { $this->IdCommunity = $idCommunity; }

    public function getIdUser(): int { return $this->IdUser; }
    public function setIdUser(int $idUser): void { $this->IdUser = $idUser; }

    public function getRequestedAt(): ?DateTime { return $this->RequestedAt; }
    public function setRequestedAt(?DateTime $requestedAt): void { $this->RequestedAt = $requestedAt; }

    public function getStatus(): string { return $this->Status; }
    public function setStatus(string $status): void { $this->Status = $status; }
}
