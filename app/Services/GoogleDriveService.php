<?php

namespace App\Services;

use Google_Client;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;
use Illuminate\Support\Facades\Storage;

class GoogleDriveService
{
    protected $client;
    protected $service;

    public function __construct()
    {
        $credentialsPath = storage_path('app/google/isekirifa-96b56da844cf.json');

        putenv("GOOGLE_APPLICATION_CREDENTIALS={$credentialsPath}");

        $this->client = new Google_Client();
        $this->client->useApplicationDefaultCredentials();
        $this->client->addScope(Google_Service_Drive::DRIVE);

        $this->service = new Google_Service_Drive($this->client);
    }

    public function uploadFile($localPath, $driveFolderId, $newFileName = null)
    {
        if (!file_exists($localPath)) {
            throw new \Exception("File not found: $localPath");
        }

        $fileMetadata = new Google_Service_Drive_DriveFile([
            'name' => $newFileName ?? basename($localPath),
            'parents' => [$driveFolderId]
        ]);

        $content = file_get_contents($localPath);

        $file = $this->service->files->create($fileMetadata, [
            'data' => $content,
            'mimeType' => mime_content_type($localPath),
            'uploadType' => 'multipart',
            'fields' => 'id'
        ]);

        return $file->id;
    }
}
