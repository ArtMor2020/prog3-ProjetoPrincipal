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

    /**
     * POST /direct-messages
     * Envia uma nova mensagem direta
     */
    public function create()
    {
        $data = $this->request->getJSON(true);

        if (empty($data['id_sender']) || empty($data['id_reciever']) || empty($data['content'])) {
            return $this->failValidationError('É necessário id_sender, id_reciever e content.');
        }

        $insert = [
            'id_sender'   => (int) $data['id_sender'],
            'id_reciever' => (int) $data['id_reciever'],
            'content'     => $data['content'],
            'sent_at'     => date('Y-m-d H:i:s'),
        ];

        $id = $this->directMessageService->sendMessage($insert);

        if (! $id) {
            return $this->fail('Falha ao enviar mensagem.', 500);
        }

        return $this->respondCreated([
            'id'     => $id,
            'status' => 'sent',
        ]);
    }

    /**
     * GET /direct-messages/conversation/{userA}/{userB}
     * Recupera toda a conversa entre dois usuários
     */
    public function conversation($userA = null, $userB = null)
    {
        if (! is_numeric($userA) || ! is_numeric($userB)) {
            return $this->failValidationError('IDs de usuário inválidos.');
        }

        $messages = $this->repository->getConversation((int) $userA, (int) $userB);
        return $this->respond($messages);
    }

    /**
     * PUT /direct-messages/{messageId}/seen
     * Marca uma mensagem como lida
     */
    public function markSeen($id = null)
    {
        if (! is_numeric($id)) {
            return $this->failValidationError('ID de mensagem inválido.');
        }

        $success = $this->repository->markAsSeen((int) $id);

        return $success
            ? $this->respond(['status' => 'seen'])
            : $this->failNotFound('Mensagem não encontrada ou erro ao marcar como lida.');
    }

    /**
     * GET /direct-messages/messages/unseen/{userId}
     * Recupera todas as mensagens não lidas de um usuário
     */
    public function getUnseen($userId = null)
    {
        if (! is_numeric($userId)) {
            return $this->failValidationError('ID de usuário inválido.');
        }

        $messages = $this->repository->getUnseenMessagesForUser((int) $userId);
        return $this->respond($messages);
    }
}
