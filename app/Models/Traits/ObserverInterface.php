<?php

namespace App\Models\Traits;

interface ObserverInterface
{
    public function handle(array &$data): array;

}