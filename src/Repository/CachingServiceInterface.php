<?php

namespace App\Repository;

interface CachingServiceInterface
{
    public function clearCache(): void;
}
