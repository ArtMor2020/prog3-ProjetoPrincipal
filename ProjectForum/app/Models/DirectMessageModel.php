<?php

namespace App\Models;

use CodeIgniter\Model;
use DateTime;

class DirectMessageModel extends Model
{
    private int $Id = 0;
    private int $IdSender = 0;
    private int $IdReciever = 0;
    private string $Content = '';
    private ?DateTime $SentAt = null;
    private bool $IsSeen = 0;
    private bool $IsDeleted = 0;

    public function getId(): int { return $this->Id; }
    public function setId(int $id): void { $this->Id = $id; }

    public function getIdSender(): int { return $this->IdSender; }
    public function setIdSender(int $idSender): void { $this->IdSender = $idSender; }

    public function getIdReciever(): int { return $this->IdReciever; }
    public function setIdReciever(int $idReciever): void { $this->IdReciever = $idReciever; }

    public function getContent(): string { return $this->Content; }
    public function setContent(string $content): void { $this->Content = $content; }

    public function getSentAt(): ?DateTime { return $this->SentAt; }
    public function setSentAt(?DateTime $sentAt): void { $this->SentAt = $sentAt; }

    public function getIsSeen(): bool { return $this->IsSeen; }
    public function setIsSeen(bool $isSeen): void { $this->IsSeen = $isSeen; }

    public function getIsDeleted(): bool { return $this->IsDeleted; }
    public function setIsDeleted(bool $isDeleted): void { $this->IsDeleted = $isDeleted; }
}
