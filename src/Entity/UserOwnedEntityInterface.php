<?php

namespace App\Entity;

interface UserOwnedEntityInterface
{
    public function getAuthor(): User;
}
