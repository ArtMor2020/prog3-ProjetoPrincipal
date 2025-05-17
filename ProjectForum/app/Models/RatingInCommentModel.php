<?php

namespace App\Models;

use CodeIgniter\Model;

class RatingInCommentModel extends Model
{
    private int $IdComment = 0;
    private int $IdUser = 0;
    private bool $IsUpvote = 0;

    public function getIdComment(): int { return $this->IdComment; }
    public function setIdComment(int $idComment): void { $this->IdComment = $idComment; }

    public function getIdUser(): int { return $this->IdUser; }
    public function setIdUser(int $idUser): void { $this->IdUser = $idUser; }

    public function getIsUpvote(): bool { return $this->IsUpvote; }
    public function setIsUpvote(bool $isUpvote): void { $this->IsUpvote = $isUpvote; }
}
