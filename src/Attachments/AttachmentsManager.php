<?php

namespace App\Attachments;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

readonly class AttachmentsManager
{
    public function __construct(
        private SluggerInterface $slugger,
        #[Autowire('%attachments.upload_folder%')] private string $attachmentUploadFolder,
        #[Autowire('%kernel.project_dir%')] private string $projectDir
    )
    {

    }

    /**
     * Moves the uploaded file in the storing position and returns the file path
     * @param UploadedFile $file
     * @return string the path relative to the public folder where the file has been stored
     */
    public function storeFile(UploadedFile $file): string
    {
        $originalFilename = pathinfo(trim($file->getClientOriginalName()), PATHINFO_FILENAME);
        $sluggedFilename = $this->slugger->slug($originalFilename);
        $uniqueFilename = $sluggedFilename.'-'.uniqid().'.'.$file->guessExtension();
        $storagePath = $this->attachmentUploadFolder.'/'.strtolower($uniqueFilename[0]).'/'.$uniqueFilename;
        $storageFullPath = $this->projectDir.'/public/'.$storagePath;
        $file->move($storageFullPath, $uniqueFilename);

        return $storagePath;
    }
}
