<?php

namespace App\Service;

use App\Entity\Team;
use App\Repository\TeamRepository;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    private $targetDirectory;
    private $teamRepository;

    public function __construct($targetDirectory, TeamRepository $teamRepository)
    {
        $this->targetDirectory = $targetDirectory;
        $this->teamRepository = $teamRepository;
    }

    public function upload(UploadedFile $file, $team)
    {
        $teamResult = $this->teamRepository->find($team);

        $fileName = $teamResult->getName() . '_' . $teamResult->getUser()->getDansSchool() . '.' . $file->guessExtension();
        try {
            $file->move($this->getTargetDirectory(), $fileName);
        } catch (FileException $e) {

        }

        return $fileName;
    }

    public function removeFile($fileName)
    {
        if (file_exists($this->getTargetDirectory().'/'.$fileName)) {
            try {
                unlink($this->getTargetDirectory() . '/' . $fileName);
            } catch (FileException $e) {
                return false;
            }
        }
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}