<?php

namespace App\DTO;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class FileUploadDTO
{
    #[Assert\File(['maxSize' => '1024k', 'mimeTypes' => ['image/*'], 'mimeTypesMessage' => 'Il file caricato deve essere un\'immagine'])]
    #[Assert\NotBlank]
    private UploadedFile $uploadedFile;

    public function getUploadedFile(): UploadedFile
    {
        return $this->uploadedFile;
    }

    public function setUploadedFile(UploadedFile $uploadedFile): FileUploadDTO
    {
        $this->uploadedFile = $uploadedFile;
        return $this;
    }
}
