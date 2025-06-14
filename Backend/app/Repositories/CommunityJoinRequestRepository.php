<?php

namespace App\Repositories;

use App\Models\CommunityJoinRequestModel;
use App\Entities\CommunityJoinRequestEntity;

class CommunityJoinRequestRepository
{
    protected CommunityJoinRequestModel $model;

    public function __construct()
    {
        $this->model = new CommunityJoinRequestModel();
    }

    /**
     * Cria uma nova solicitação de ingresso.
     * Permite novo pedido apenas se não houver um nos últimos 7 dias.
     */
    public function create(int $communityId, int $userId): bool
    {
        if (
            $this->model->where('id_community', $communityId)
                        ->where('id_user', $userId)
                        ->where('requested_at >=', date('Y-m-d H:i:s', strtotime('-7 days')))
                        ->first()
        ) {
            return false;
        }

        $data = [
            'id_community'  => $communityId,
            'id_user'       => $userId,
            'requested_at'  => date('Y-m-d H:i:s'),
            'status'        => 'pending',
        ];

        return (bool) $this->model->builder()->insert($data);
    }

    /**
     * Atualiza o status de uma solicitação pelo ID da solicitação.
     */
    protected function updateStatus(int $requestId, string $status): bool
    {
        return (bool) $this->model
            ->builder()
            ->where('id', $requestId)
            ->update(['status' => $status]);
    }

    /**
     * Aprova uma solicitação pelo ID.
     */
    public function approve(int $requestId): bool
    {
        return $this->updateStatus($requestId, 'approved');
    }

    /**
     * Rejeita uma solicitação pelo ID.
     */
    public function reject(int $requestId): bool
    {
        return $this->updateStatus($requestId, 'rejected');
    }

    /**
     * Lista todas as solicitações.
     */
    public function listAll(): array
    {
        return $this->model->orderBy('requested_at', 'DESC')->findAll();
    }

    /**
     * Retorna uma única solicitação pelo ID.
     */
    public function getRequest(int $requestId): ?array
    {
        return $this->model->where('id', $requestId)->first();
    }

    /**
     * Lista solicitações de uma comunidade.
     */
    public function listByCommunity(int $communityId): array
    {
        return $this->model->where('id_community', $communityId)
                           ->orderBy('requested_at', 'DESC')
                           ->findAll();
    }

    /**
     * Lista solicitações de um usuário.
     */
    public function listByUser(int $userId): array
    {
        return $this->model->where('id_user', $userId)
                           ->orderBy('requested_at', 'DESC')
                           ->findAll();
    }
}

