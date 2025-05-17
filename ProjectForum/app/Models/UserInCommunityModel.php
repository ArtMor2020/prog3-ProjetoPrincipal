<?php

namespace App\Models;

use CodeIgniter\Model;

class UserInCommunityModel extends Model
{
    private int $IdUser = 0;
    private int $IdCommunity = 0;
    private string $Role = '';
    private bool $IsBanned = 0;

    public function getIdUser(): int { return $this->IdUser; }
    public function setIdUser(int $idUser): void { $this->IdUser = $idUser; }

    public function getIdCommunity(): int { return $this->IdCommunity; }
    public function setIdCommunity(int $idCommunity): void { $this->IdCommunity = $idCommunity; }

    public function getRole(): string { return $this->Role; }
    public function setRole(string $role): void { $this->Role = $role; }

    public function getIsBanned(): bool { return $this->IsBanned; }
    public function setIsBanned(bool $isBanned): void { $this->IsBanned = $isBanned; }

}
