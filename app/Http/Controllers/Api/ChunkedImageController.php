<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Utilities\ImageProcessor;

class ChunkedImageController extends Controller
{
    /**
     * Get a chunk of a file as base64
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getChunk(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'file_path' => 'required|string',
                'chunk_index' => 'required|integer|min:0',
                'total_chunks' => 'required|integer|min:1',
            ]);

            $filePath = $request->input('file_path');
            $chunkIndex = $request->input('chunk_index');
            $totalChunks = $request->input('total_chunks');

            // Check if the file exists
            if (!Storage::exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found: ' . $filePath
                ], 404);
            }

            // Get the chunk
            $result = ImageProcessor::getBase64Chunk($filePath, $chunkIndex, $totalChunks);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 500);
            }

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Error getting chunk: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get chunk: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save a base64 chunk to a temporary file
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveChunk(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'chunk_data' => 'required|string',
                'chunk_index' => 'required|integer|min:0',
                'temp_dir' => 'required|string',
            ]);

            $chunkData = $request->input('chunk_data');
            $chunkIndex = $request->input('chunk_index');
            $tempDir = $request->input('temp_dir');

            // Save the chunk
            $result = ImageProcessor::saveBase64Chunk($chunkData, $tempDir, $chunkIndex);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 500);
            }

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Error saving chunk: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to save chunk: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Merge chunks into a single file
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function mergeChunks(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'temp_dir' => 'required|string',
                'filename' => 'required|string',
                'total_chunks' => 'required|integer|min:1',
            ]);

            $tempDir = $request->input('temp_dir');
            $filename = $request->input('filename');
            $totalChunks = $request->input('total_chunks');

            // Generate a unique filename
            $uniqueFilename = Str::uuid() . '_' . $filename;
            $outputPath = 'public/images/' . $uniqueFilename;

            // Merge the chunks
            $result = ImageProcessor::mergeChunks($tempDir, $outputPath, $totalChunks);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 500);
            }

            return response()->json([
                'success' => true,
                'file_path' => Storage::url($outputPath),
                'method' => 'chunked'
            ]);
        } catch (\Exception $e) {
            Log::error('Error merging chunks: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to merge chunks: ' . $e->getMessage()
            ], 500);
        }
    }
}
