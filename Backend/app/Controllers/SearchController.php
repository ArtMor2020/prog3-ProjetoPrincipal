<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Services\SearchService;

class SearchController extends ResourceController
{
    protected SearchService $service;

    public function __construct()
    {
        $this->service = new SearchService();
    }

    public function query()
    {
        $queryString = $this->request->getGet('q');
        if ($queryString === null) {
            return $this->failValidationError('O parâmetro de busca (q) é obrigatório.');
        }
        $data = $this->service->search($queryString);
        return ($data === false)
            ? $this->failServerError('Ocorreu um erro interno durante a busca.')
            : $this->respond($data);
    }

    public function users()
    {
        $term = $this->request->getGet('term');
        if ($term === null) {
            return $this->failValidationError('O parâmetro de busca (term) é obrigatório.');
        }
        $users = $this->service->searchUsers($term);

        return ($users === false)
            ? $this->failServerError('Ocorreu um erro interno durante a busca de usuários.')
            : $this->respond($users ?? []);
    }
}