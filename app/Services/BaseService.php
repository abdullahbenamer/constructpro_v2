<?php

require_once __DIR__ . '/../Core/Model.php';

abstract class BaseService extends Model
{
    public function __construct(?Database $db = null)
    {
        parent::__construct($db);
    }

    protected function transaction(callable $callback)
    {
        return $this->db->transaction($callback);
    }

    protected function now(): string
    {
        return date('Y-m-d H:i:s');
    }

    protected function currentUserId(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }
}