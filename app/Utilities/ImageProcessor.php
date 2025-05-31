<?php

namespace App\Utilities;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ImageProcessor
{
    /**
     * Chunk size for processing large files (in bytes)
     * 5MB is a reasonable chunk size
     */
    const CHUNK_SIZE = 5242880; // 5MB
    
    /**
     * Convert a large file to base64 in chunks
     * 
     * @param string $filePath Path to the file in storage
     * @param int $chunkIndex Chunk index to process
     * @param int $totalChunks Total number of chunks
     * @return array
     */
    public static function getBase64Chunk($filePath, $chunkIndex, $totalChunks)
    {
        try {
            if (!Storage::exists($filePath)) {
                throw new \Exception('File not found: ' . $filePath);
            }
            
            $fileSize = Storage::size($filePath);
            $chunkSize = self::CHUNK_SIZE;
            
            // Calculate the start and end positions for this chunk
            $start = $chunkIndex * $chunkSize;
            $end = min($start + $chunkSize - 1, $fileSize - 1);
            
            // Get the file handle
            $path = Storage::path($filePath);
            $handle = fopen($path, 'rb');
            
            if (!$handle) {
                throw new \Exception('Could not open file: ' . $filePath);
            }
            
            // Seek to the start position
            fseek($handle, $start);
            
            // Read the chunk
            $chunkData = fread($handle, $end - $start + 1);
            fclose($handle);
            
            // Encode the chunk
            $base64Chunk = base64_encode($chunkData);
            
            // Add MIME type only to the first chunk
            $mimeType = '';
            if ($chunkIndex === 0) {
                $mimeType = Storage::mimeType($filePath);
            }
            
            return [
                'success' => true,
                'chunk_index' => $chunkIndex,
                'total_chunks' => $totalChunks,
                'chunk_data' => $base64Chunk,
                'mime_type' => $mimeType,
                'is_last_chunk' => ($chunkIndex === $totalChunks - 1)
            ];
        } catch (\Exception $e) {
            Log::error('Error processing image chunk: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to process chunk: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Save a base64 chunk to a temporary file
     * 
     * @param string $base64Chunk Base64 encoded chunk
     * @param string $tempDir Temporary directory
     * @param int $chunkIndex Chunk index
     * @return array
     */
    public static function saveBase64Chunk($base64Chunk, $tempDir, $chunkIndex)
    {
        try {
            // Create the temp directory if it doesn't exist
            if (!Storage::exists($tempDir)) {
                Storage::makeDirectory($tempDir);
            }
            
            // Decode the base64 chunk
            $data = base64_decode($base64Chunk);
            
            // Save the chunk to a temporary file
            $chunkPath = $tempDir . '/chunk_' . $chunkIndex;
            Storage::put($chunkPath, $data);
            
            return [
                'success' => true,
                'chunk_path' => $chunkPath
            ];
        } catch (\Exception $e) {
            Log::error('Error saving base64 chunk: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to save chunk: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Merge chunks into a single file
     * 
     * @param string $tempDir Directory containing chunks
     * @param string $outputPath Path where the merged file will be saved
     * @param int $totalChunks Total number of chunks
     * @return array
     */
    public static function mergeChunks($tempDir, $outputPath, $totalChunks)
    {
        try {
            // Create a new file for writing
            $outputHandle = fopen(Storage::path($outputPath), 'wb');
            
            if (!$outputHandle) {
                throw new \Exception('Could not create output file: ' . $outputPath);
            }
            
            // Process each chunk in order
            for ($i = 0; $i < $totalChunks; $i++) {
                $chunkPath = $tempDir . '/chunk_' . $i;
                
                if (!Storage::exists($chunkPath)) {
                    throw new \Exception('Chunk file not found: ' . $chunkPath);
                }
                
                // Read the chunk and write it to the output file
                $chunkData = Storage::get($chunkPath);
                fwrite($outputHandle, $chunkData);
                
                // Delete the chunk file
                Storage::delete($chunkPath);
            }
            
            fclose($outputHandle);
            
            // Delete the temporary directory
            Storage::deleteDirectory($tempDir);
            
            return [
                'success' => true,
                'output_path' => $outputPath
            ];
        } catch (\Exception $e) {
            Log::error('Error merging chunks: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to merge chunks: ' . $e->getMessage()
            ];
        }
    }
}
