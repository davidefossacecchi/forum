<?php

namespace App\DTO;

use App\Entity\Topic;

readonly class ActivityDto
{
    /**
     * @param Topic[] $activity
     */
    public function __construct(
        public array $activity
    )
    {

    }
}
