<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Repositories\DirectMessageRepository;

class DirectMessageController extends ResourceController
{
    protected $format = 'json';
    protected DirectMessageRepository $repository;

    public function __construct()
    {
        $this->repository = new DirectMessageRepository();
    }

    public function create()
    {
        $data = $this->request->getJSON(true);

        if (empty($data['id_sender']) || empty($data['id_reciever']) || empty($data['content'])) {
            return $this->failValidationError('É necessário id_sender, id_reciever e content.');
        }

        $insert = [
            'id_sender' => (int) $data['id_sender'],
            'id_reciever' => (int) $data['id_reciever'],
            'content' => $data['content'],
            'sent_at' => date('Y-m-d H:i:s'),
        ];

        $id = $this->repository->sendMessage($insert);

        if (!$id) {
            return $this->fail('Falha ao enviar mensagem.', 500);
        }

        return $this->respondCreated(['id' => $id, 'status' => 'sent']);
    }

    public function conversation($userA = null, $userB = null)
    {
        if (!is_numeric($userA) || !is_numeric($userB)) {
            return $this->failValidationError('IDs de usuário inválidos.');
        }

        $msgs = $this->repository->getConversation((int) $userA, (int) $userB);

        return $this->respond($msgs);
    }
    public function markSeen($id = null)
    {
        if (!is_numeric($id)) {
            return $this->failValidationError('ID de mensagem inválido.');
        }

        $success = $this->repository->markAsSeen((int) $id);

        return $success
            ? $this->respond(['status' => 'seen'])
            : $this->failNotFound('Mensagem não encontrada ou erro ao marcar como lida.');
    }

    public function getUnseen($id)
    {
            if (!is_numeric($id)) {
            return $this->failValidationError('ID de mensagem inválido.');
            }

            return $this->repository->getUnseenMessagesForUser($id);
    }
}
