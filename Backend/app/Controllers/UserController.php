<?php
namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Repositories\UserRepository;
use App\Entities\UserEntity;

class UserController extends ResourceController
{
    protected $format = 'json';
    protected UserRepository $repository;

    public function __construct()
    {
        $this->repository = new UserRepository();
    }

    public function index()
    {
        $users = $this->repository->findAll();
        $data = array_map(function ($user) {
            return $user->toArray();
        }, $users);

        return $this->respond($data);
    }

    public function show($id = null)
    {
        $user = $this->repository->getUserById((int) $id);
        return $user
            ? $this->respond($user)
            : $this->failNotFound('User not found');
    }

    public function create()
    {
        $data = $this->request->getJSON(true);

        if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
            return $this->failValidationError('Campos name, email e password são obrigatórios.');
        }

        $user = new UserEntity();
        $user->setName($data['name'])
            ->setEmail($data['email'])
            ->setPassword($data['password'])
            ->setAbout($data['about'] ?? null)
            ->setIsPrivate((bool) ($data['is_private'] ?? false));

        $id = $this->repository->createUser($user);

        if ($id === false) {
            return $this->fail('Não foi possível criar o usuário.', 400);
        }

        return $this->respondCreated(['id' => $id]);
    }

    public function update($id = null)
    {
        // Lê JSON como array
        $data = $this->request->getJSON(true);

        if (empty($data)) {
            return $this->failValidationError('Nenhum dado recebido.');
        }

        $success = $this->repository->updateUser((int) $id, $data);

        return $success
            ? $this->respond(['status' => 'updated'])
            : $this->fail('Update failed');
    }

    public function delete($id = null)
    {
        $success = $this->repository->deleteUser((int) $id);

        if (!$success) {
            $exists = !!$this->repository->getUserById((int) $id);
            return $exists
                ? $this->fail('User already deleted or cannot be deleted', 400)
                : $this->failNotFound('User not found');
        }

        return $this->respondDeleted(['status' => 'deleted']);
    }

    public function ban($id = null)
    {
        if (empty($id)) {
            return $this->failValidationError('User ID is required.');
        }

        $success = $this->repository->banUser((int) $id);

        return $success
            ? $this->respond(['status' => 'banned'])
            : $this->fail('Ban failed');
    }

    public function unban($id = null)
    {
        if (empty($id)) {
            return $this->failValidationError('User ID is required.');
        }

        $success = $this->repository->unbanUser((int) $id);

        return $success
            ? $this->respond(['status' => 'unbanned'])
            : $this->fail('Unban failed');
    }

    public function restore($id = null)
    {
        if (empty($id)) {
            return $this->failValidationError('User ID is required.');
        }

        $success = $this->repository->restoreUser((int) $id);

        return $success
            ? $this->respond(['status' => 'restored'])
            : $this->fail('Restore failed');
    }

public function auth()
{
    $payload = $this->request->getJSON(true);
    $user = $this->repository->authenticate($payload['email'], $payload['password']);

    if (! $user) {
        return $this->fail('Credenciais inválidas', 401);
    }

    // Converte a entidade em array (sem o hash)
    return $this->respond([
        'status' => 'success',
        'user'   => $user->toRawArray(),
    ]);
}
}
