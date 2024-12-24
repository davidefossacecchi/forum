<?php

namespace App\EventListener;

use App\Exception\BadRequestException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

#[AsEventListener]
class InvalidRequestListener
{
    public function __invoke(ExceptionEvent $event): void
    {
        $throwable = $event->getThrowable();

        if ($throwable instanceof BadRequestException) {
            $errors = iterator_to_array($throwable->form->getErrors(true));

            $e = [];

            /** @var FormError $error */
            foreach ($errors as $error) {
                $e[] = [
                    'property' => $error->getCause()->getPropertyPath(),
                    'message' => $error->getMessage()
                ];
            }

            $response = new JsonResponse($e, Response::HTTP_BAD_REQUEST);
            $event->setResponse($response);
        }
    }
}
