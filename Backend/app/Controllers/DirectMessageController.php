<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Repositories\DirectMessageRepository;
use App\Services\DirectMessageService;

class DirectMessageController extends ResourceController
{
    protected $format = 'json';
    protected DirectMessageRepository $repository;
    protected DirectMessageService $directMessageService;

    public function __construct()
    {
        $this->repository = new DirectMessageRepository();
        $this->directMessageService = new DirectMessageService();
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

        $id = $this->directMessageService->sendMessage($insert);

        if (!$id) {
            return $this->fail('Falha ao enviar mensagem.', 500);
        }

        return $this->respondCreated([
            'id' => $id,
            'status' => 'sent',
        ]);
    }

    public function conversation($userA = null, $userB = null)
    {
        if (!is_numeric($userA) || !is_numeric($userB)) {
            return $this->failValidationError('IDs de usuário inválidos.');
        }

        $messages = $this->repository->getConversation((int) $userA, (int) $userB);
        return $this->respond($messages);
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

    public function getUnseen($userId = null)
    {
        if (!is_numeric($userId)) {
            return $this->failValidationError('ID de usuário inválido.');
        }

        $messages = $this->repository->getUnseenMessagesForUser((int) $userId);
        return $this->respond($messages);
    }

    public function unreadSummary($userId = null)
    {
        if (!is_numeric($userId)) {
            return $this->failValidationError('ID de usuário inválido.');
        }

        $summary = $this->repository->getUnreadSummary((int) $userId);
        return $this->respond($summary);
    }

    public function markConversationSeen($readerId = null, $senderId = null)
    {
        if (!is_numeric($readerId) || !is_numeric($senderId)) {
            return $this->failValidationError('IDs de usuário inválidos.');
        }

        $success = $this->repository->markConversationAsSeen((int) $readerId, (int) $senderId);

        return $this->respond(['status' => 'ok']);
    }
}
