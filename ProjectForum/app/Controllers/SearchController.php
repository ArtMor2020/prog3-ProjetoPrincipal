<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Services\SearchService;

class SearchController extends ResourceController
{
    protected $repo;

    public function __construct(){
        $this->repo = new SearchService();
    }

    public function query(string $string){
        $data = $this->repo->search($string);

        return $data
            ? $this->respond($data)
            : $this->failNotFound('Nada encontrado');
    }
}
