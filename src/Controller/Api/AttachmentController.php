<?php

namespace App\Controller\Api;

use App\Attachments\AttachmentsManager;
use App\DTO\FileUploadDTO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Asset\Packages;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\UrlHelper;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AttachmentController extends AbstractController
{
    public function __construct(
        private readonly AttachmentsManager $attachmentsManager
    )
    {

    }
    #[Route('/attachments', name: 'topic_file_upload', methods: 'POST')]
    public function fileUpload(
        Request $request,
        ValidatorInterface $validator,
        Packages $urlHelper
    ): Response
    {
        $file = $request->files->get('file');

        if (empty($file)) {
            throw new BadRequestHttpException('Nessun file inviato');
        }

        $uploadedFileDTO = new FileUploadDTO();
        $uploadedFileDTO->setUploadedFile($file);

        $errors = $validator->validate($uploadedFileDTO);

        if (count($errors) > 0) {
            return new JsonResponse(['error' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        $uploadedFile = $uploadedFileDTO->getUploadedFile();
        $filePath = $this->attachmentsManager->storeFile($uploadedFile);

        return new JsonResponse(['url' => $urlHelper->getUrl($filePath)]);
    }
}
