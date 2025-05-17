<?php

namespace App\Models;

use CodeIgniter\Model;

class BlockedUserModel extends Model
{
    private int $IdUser = 0;
    private int $IdBlockedUser = 0;

    public function getIdUser(): int { return $this->IdUser; }
    public function setIdUser(int $idUser): void { $this->IdUser = $idUser; }

    public function getIdBlockedUser(): int { return $this->IdBlockedUser; }
    public function setIdBlockedUser(int $idBlockedUser): void { $this->IdBlockedUser = $idBlockedUser; }

}
