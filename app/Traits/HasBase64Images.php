<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

trait HasBase64Images
{
    /**
     * Convert an uploaded file to base64 and store it in a file
     * Returns the path to the stored base64 file
     *
     * @param UploadedFile $file
     * @param string $directory Directory to store the base64 file in
     * @return string|null Path to the stored base64 file
     */
    public function convertToBase64(UploadedFile $file, string $directory = 'base64'): ?string
    {
        try {
            $type = $file->getMimeType();
            $data = file_get_contents($file->getRealPath());
            $base64Data = 'data:' . $type . ';base64,' . base64_encode($data);

            // Create a unique filename
            $filename = 'base64_' . time() . '_' . \Illuminate\Support\Str::random(10) . '.txt';

            // Create directory if it doesn't exist
            $path = public_path($directory);
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }

            // Save the base64 data to a file
            $fullPath = $path . '/' . $filename;
            file_put_contents($fullPath, $base64Data);

            // Return the path to the file
            return $directory . '/' . $filename;
        } catch (\Exception $e) {
            Log::error('Failed to convert image to base64: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Revert a base64 string to a file and save it
     *
     * @param string $base64
     * @param string $directory
     * @param string $filename
     * @return string|null Path to the saved file
     */
    public function revertFromBase64(string $base64, string $directory, string $filename): ?string
    {
        try {
            // Remove data URI scheme if present
            if (strpos($base64, 'data:') === 0) {
                $parts = explode(',', $base64, 2);
                $base64 = $parts[1];
            }

            // Decode the base64 string
            $data = base64_decode($base64);

            // Create directory if it doesn't exist
            $path = public_path($directory);
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }

            // Save the file
            $fullPath = $path . '/' . $filename;
            file_put_contents($fullPath, $data);

            return $directory . '/' . $filename;
        } catch (\Exception $e) {
            Log::error('Failed to revert base64 to image: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Check if a string is a base64 encoded image
     *
     * @param string|null $string
     * @return bool
     */
    public function isBase64Image(?string $string): bool
    {
        if (!$string) {
            return false;
        }

        // Check if it starts with the data URI scheme
        return strpos($string, 'data:image/') === 0 && strpos($string, ';base64,') !== false;
    }

    /**
     * Get base64 data from a file
     *
     * @param string $path Path to the file containing base64 data
     * @return string|null The base64 data
     */
    public function getBase64FromFile(string $path): ?string
    {
        try {
            if (!file_exists(public_path($path))) {
                return null;
            }

            return file_get_contents(public_path($path));
        } catch (\Exception $e) {
            Log::error('Failed to get base64 data from file: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Check if a path is a base64 file
     *
     * @param string|null $path
     * @return bool
     */
    public function isBase64File(?string $path): bool
    {
        if (!$path) {
            return false;
        }

        // Check if the path contains 'base64_' and ends with '.txt'
        return strpos($path, 'base64_') !== false && substr($path, -4) === '.txt';
    }

    /**
     * Get the MIME type from a base64 string
     *
     * @param string $base64
     * @return string|null
     */
    public function getMimeTypeFromBase64(string $base64): ?string
    {
        if (!$this->isBase64Image($base64)) {
            return null;
        }

        $parts = explode(';', $base64);
        $mimePart = $parts[0];

        return str_replace('data:', '', $mimePart);
    }

    /**
     * Get the file extension from a base64 string
     *
     * @param string $base64
     * @return string
     */
    public function getExtensionFromBase64(string $base64): string
    {
        $mimeType = $this->getMimeTypeFromBase64($base64);

        if (!$mimeType) {
            return 'jpg'; // Default extension
        }

        $extensions = [
            'image/jpeg' => 'jpg',
            'image/jpg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'image/svg+xml' => 'svg',
        ];

        return $extensions[$mimeType] ?? 'jpg';
    }
}
