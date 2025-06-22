<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\RatingInCommentEntity;

class RatingInCommentModel extends Model
{
    protected $table = 'rating_in_comment';

    protected $primaryKey = ['id_comment', 'id_user'];

    protected $useAutoIncrement = false;
    protected $returnType = RatingInCommentEntity::class;

    protected $allowedFields = [
        'id_comment',
        'id_user',
        'is_upvote',
    ];
}