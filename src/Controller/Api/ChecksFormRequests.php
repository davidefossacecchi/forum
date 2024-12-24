<?php

namespace App\Controller\Api;

use App\Exception\BadRequestException;
use Symfony\Component\Form\FormInterface;

trait ChecksFormRequests
{
    public function throwExceptionIfInvalid(FormInterface $form):void
    {
        if (false === $form->isValid()) {
            throw new BadRequestException($form);
        }
    }
}
