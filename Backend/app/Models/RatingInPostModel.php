<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\RatingInPostEntity;

class RatingInPostModel extends Model
{
    protected $table = 'rating_in_post';
    protected $primaryKey = ['id_post', 'id_user'];
    protected $useAutoIncrement = false;
    protected $returnType = RatingInPostEntity::class;

    protected $allowedFields = [
        'id_post',
        'id_user',
        'is_upvote',
    ];
}
