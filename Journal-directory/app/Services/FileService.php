<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;

class FileService
{
    private $maxFileSizeBytes = null;
    private $allowedExtensions  = null;
    private $publicFilesDirectory = null;

    function __construct()
    {
        $this->publicFilesDirectory = env('PUBLIC_FILES_DIRECTORY','uploads');
        $this->setMaxFileSizeBytes(2097152);
        $this->setAllowedExtensions(['png','jpg']);
    }

    /**
     * Set array allowing for uploading extensions
     * @param array $allowedExtensions
     * @return $this
     */
    public function setAllowedExtensions(array $allowedExtensions)
    {
        $this->allowedExtensions = $allowedExtensions;
        return $this;
    }

    /**
     * Set maximum file size for uploading in bytes
     * @param $maxFileSizeBytes
     * @return $this
     */
    public function setMaxFileSizeBytes($maxFileSizeBytes)
    {
        $this->maxFileSizeBytes = $maxFileSizeBytes;
        return $this;
    }

    /**
     * Generate unique name for file
     * @return string
     */
    protected function generateFileName(){
        return hash('sha256', Str::uuid()->toString());
    }

    /**
     * Save file with extension to public folder in storage from raw content
     * @param $file_content
     * @param $fileExtension
     * @return string ($fileFullName)
     */
    public function saveFile($file_content, $fileExtension)
    {
        if (!isset($file_content)) {
            throw new HttpException(422,'file content is empty');
        }

        if (strlen($file_content) > $this->maxFileSizeBytes) {
            throw new HttpException(422,'file size over ' . $this->maxFileSizeBytes);
        }

        if (!in_array($fileExtension,$this->allowedExtensions)) {
            throw new HttpException(422,'unsupported file extension');
        }

        $unique_file_name = $this->generateFileName();
        $fileFullName = $unique_file_name . '.' . $fileExtension;
        Storage::disk('public')->put($this->publicFilesDirectory . "//" . $fileFullName, $file_content);

        if (!(Storage::disk('public')->exists($this->publicFilesDirectory . "//" . $fileFullName))) {
            throw new HttpException(409,'error on saving file to storage');
        }

        return $fileFullName;
    }

    /**
     * @param $fileFullName
     * @return string (http(s)://your_domain.com/storage/..($publicFilesDirectory)../file_name.extension)
     */
    public function getWebRoute($fileFullName)
    {
        return request()->getSchemeAndHttpHost().'/storage/' . $this->publicFilesDirectory . "/" . $fileFullName;
    }

    /**
     * Delete file from full name if exist
     * @param $fileFullName
     * @return $this
     */
    public function deleteFile($fileFullName)
    {
        $relativeFilePath = $this->publicFilesDirectory . '\\' . $fileFullName;
        if (Storage::disk('public')->exists($relativeFilePath)) {
            unlink(Storage::disk('public')->path($relativeFilePath));
        }

        return $this;
    }
}
