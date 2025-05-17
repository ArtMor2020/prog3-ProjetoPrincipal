<?php

namespace App\Models;

use CodeIgniter\Model;

class RatingInPostModel extends Model
{
    private int $IdPost = 0;
    private int $IdUser = 0;
    private bool $IsUpvote = 0;

    public function getIdPost(): int { return $this->IdPost; }
    public function setIdPost(int $idPost): void { $this->IdPost = $idPost; }

    public function getIdUser(): int { return $this->IdUser; }
    public function setIdUser(int $idUser): void { $this->IdUser = $idUser; }

    public function getIsUpvote(): bool { return $this->IsUpvote; }
    public function setIsUpvote(bool $isUpvote): void { $this->IsUpvote = $isUpvote; }

}
