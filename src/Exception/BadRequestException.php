<?php

namespace App\Exception;

use Symfony\Component\Form\FormInterface;

class BadRequestException extends \RuntimeException
{
    public function __construct(public readonly FormInterface $form)
    {
        parent::__construct();
    }
}
