<?php

namespace App\Repository;

interface CachingRepositoryInterface
{
    public function clearCache(): void;
}
