<?php

namespace App\Models\Contracts;

interface BeforeInsertInterface
{
    public function beforeInsert(array &$data): array;
}