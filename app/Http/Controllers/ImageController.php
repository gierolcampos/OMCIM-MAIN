<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ImageController extends Controller
{
    /**
     * Maximum file size for standard base64 conversion (in bytes)
     * 100MB is a reasonable limit for standard conversion
     */
    const MAX_STANDARD_SIZE = 104857600; // 100MB

    /**
     * Convert an image file to base64 encoding
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function convertToBase64(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'image' => 'required|file|mimes:jpeg,png,jpg,gif,svg,webp|max:4096000', // Max 4GB
            ]);

            $file = $request->file('image');
            $fileSize = $file->getSize();
            
            // Check if file is too large for standard conversion
            if ($fileSize > self::MAX_STANDARD_SIZE) {
                return $this->handleLargeFile($file);
            }
            
            // Standard conversion for smaller files
            $base64 = $this->standardConversion($file);
            
            return response()->json([
                'success' => true,
                'base64' => $base64,
                'size' => $fileSize,
                'method' => 'standard'
            ]);
        } catch (\Exception $e) {
            Log::error('Image to base64 conversion failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to convert image: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Convert a base64 encoded string back to an image file
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function revertFromBase64(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'base64' => 'required|string',
                'filename' => 'required|string',
                'method' => 'required|in:standard,chunked',
                'temp_path' => 'required_if:method,chunked|string',
            ]);

            $base64 = $request->input('base64');
            $filename = $request->input('filename');
            $method = $request->input('method');
            
            if ($method === 'chunked') {
                $tempPath = $request->input('temp_path');
                $filePath = $this->revertChunkedFile($tempPath, $filename);
            } else {
                $filePath = $this->revertStandardFile($base64, $filename);
            }
            
            return response()->json([
                'success' => true,
                'file_path' => $filePath,
                'method' => $method
            ]);
        } catch (\Exception $e) {
            Log::error('Base64 to image conversion failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to convert base64 to image: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Standard conversion for files under the size limit
     * 
     * @param \Illuminate\Http\UploadedFile $file
     * @return string
     */
    private function standardConversion($file)
    {
        $type = $file->getMimeType();
        $data = file_get_contents($file->getRealPath());
        return 'data:' . $type . ';base64,' . base64_encode($data);
    }

    /**
     * Handle large files by chunking them
     * 
     * @param \Illuminate\Http\UploadedFile $file
     * @return \Illuminate\Http\JsonResponse
     */
    private function handleLargeFile($file)
    {
        // Create a temporary directory to store chunks
        $tempDir = 'temp/' . Str::uuid();
        Storage::makeDirectory($tempDir);
        
        // Store the original file in the temp directory
        $tempPath = Storage::putFile($tempDir, $file);
        
        // Get file info
        $fileSize = $file->getSize();
        $mimeType = $file->getMimeType();
        $originalName = $file->getClientOriginalName();
        
        return response()->json([
            'success' => true,
            'method' => 'chunked',
            'temp_path' => $tempPath,
            'mime_type' => $mimeType,
            'size' => $fileSize,
            'original_name' => $originalName,
            'message' => 'File is too large for standard base64 conversion. Using chunked method.'
        ]);
    }

    /**
     * Revert a standard base64 string to a file
     * 
     * @param string $base64
     * @param string $filename
     * @return string
     */
    private function revertStandardFile($base64, $filename)
    {
        // Remove data URI scheme if present
        if (Str::startsWith($base64, 'data:')) {
            $parts = explode(',', $base64, 2);
            $base64 = $parts[1];
        }
        
        // Decode the base64 string
        $data = base64_decode($base64);
        
        // Generate a unique filename
        $uniqueFilename = Str::uuid() . '_' . $filename;
        
        // Store the file
        $path = 'public/images/' . $uniqueFilename;
        Storage::put($path, $data);
        
        return Storage::url($path);
    }

    /**
     * Revert a chunked file to its original form
     * 
     * @param string $tempPath
     * @param string $filename
     * @return string
     */
    private function revertChunkedFile($tempPath, $filename)
    {
        // Check if the temp file exists
        if (!Storage::exists($tempPath)) {
            throw new \Exception('Temporary file not found');
        }
        
        // Generate a unique filename
        $uniqueFilename = Str::uuid() . '_' . $filename;
        
        // Copy the file from temp to public storage
        $destinationPath = 'public/images/' . $uniqueFilename;
        Storage::copy($tempPath, $destinationPath);
        
        // Clean up the temp file
        Storage::delete($tempPath);
        
        // Get the directory of the temp file
        $tempDir = dirname($tempPath);
        if (Storage::exists($tempDir) && count(Storage::files($tempDir)) === 0) {
            Storage::deleteDirectory($tempDir);
        }
        
        return Storage::url($destinationPath);
    }
}
