/**
 * Image Utilities for handling image conversion to/from base64
 * Supports large files up to 4GB through chunking
 */
class ImageUtils {
    /**
     * Maximum file size for standard conversion (in bytes)
     * 100MB is a reasonable limit for standard conversion
     */
    static MAX_STANDARD_SIZE = 104857600; // 100MB

    /**
     * Chunk size for processing large files (in bytes)
     * 5MB is a reasonable chunk size
     */
    static CHUNK_SIZE = 5242880; // 5MB

    /**
     * Convert an image file to base64
     * 
     * @param {File} file - The image file to convert
     * @param {Function} progressCallback - Optional callback for progress updates
     * @returns {Promise<Object>} - Promise resolving to the conversion result
     */
    static async toBase64(file, progressCallback = null) {
        try {
            // Check if file is too large for standard conversion
            if (file.size > this.MAX_STANDARD_SIZE) {
                return await this.chunkedToBase64(file, progressCallback);
            }
            
            // Standard conversion for smaller files
            return await this.standardToBase64(file);
        } catch (error) {
            console.error('Image to base64 conversion failed:', error);
            throw error;
        }
    }

    /**
     * Convert a base64 string back to an image file
     * 
     * @param {string} base64 - The base64 string to convert
     * @param {string} filename - The filename to use
     * @param {string} method - The conversion method ('standard' or 'chunked')
     * @param {string} tempPath - The temporary path (required for chunked method)
     * @returns {Promise<Object>} - Promise resolving to the conversion result
     */
    static async fromBase64(base64, filename, method = 'standard', tempPath = null) {
        try {
            const formData = new FormData();
            formData.append('base64', base64);
            formData.append('filename', filename);
            formData.append('method', method);
            
            if (method === 'chunked' && tempPath) {
                formData.append('temp_path', tempPath);
            }
            
            const response = await fetch('/api/images/from-base64', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            });
            
            const result = await response.json();
            
            if (!result.success) {
                throw new Error(result.message || 'Failed to convert base64 to image');
            }
            
            return result;
        } catch (error) {
            console.error('Base64 to image conversion failed:', error);
            throw error;
        }
    }

    /**
     * Standard conversion for files under the size limit
     * 
     * @param {File} file - The image file to convert
     * @returns {Promise<Object>} - Promise resolving to the conversion result
     */
    static standardToBase64(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            
            reader.onload = () => {
                resolve({
                    success: true,
                    base64: reader.result,
                    size: file.size,
                    method: 'standard'
                });
            };
            
            reader.onerror = () => {
                reject(new Error('Failed to read file'));
            };
            
            reader.readAsDataURL(file);
        });
    }

    /**
     * Chunked conversion for large files
     * 
     * @param {File} file - The image file to convert
     * @param {Function} progressCallback - Optional callback for progress updates
     * @returns {Promise<Object>} - Promise resolving to the conversion result
     */
    static async chunkedToBase64(file, progressCallback = null) {
        try {
            // First, upload the file to a temporary location
            const formData = new FormData();
            formData.append('image', file);
            
            const response = await fetch('/api/images/to-base64', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            });
            
            const result = await response.json();
            
            if (!result.success) {
                throw new Error(result.message || 'Failed to upload file');
            }
            
            // If using the chunked method, we'll return the temp path
            if (result.method === 'chunked') {
                if (progressCallback) {
                    progressCallback(100); // Upload complete
                }
                
                return {
                    success: true,
                    method: 'chunked',
                    temp_path: result.temp_path,
                    mime_type: result.mime_type,
                    size: result.size,
                    original_name: result.original_name
                };
            }
            
            // Otherwise, return the standard result
            return result;
        } catch (error) {
            console.error('Chunked conversion failed:', error);
            throw error;
        }
    }

    /**
     * Get a chunk of a file as base64
     * 
     * @param {string} filePath - The path to the file
     * @param {number} chunkIndex - The index of the chunk to get
     * @param {number} totalChunks - The total number of chunks
     * @returns {Promise<Object>} - Promise resolving to the chunk data
     */
    static async getChunk(filePath, chunkIndex, totalChunks) {
        try {
            const formData = new FormData();
            formData.append('file_path', filePath);
            formData.append('chunk_index', chunkIndex);
            formData.append('total_chunks', totalChunks);
            
            const response = await fetch('/api/images/chunk/get', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            });
            
            const result = await response.json();
            
            if (!result.success) {
                throw new Error(result.message || 'Failed to get chunk');
            }
            
            return result;
        } catch (error) {
            console.error('Get chunk failed:', error);
            throw error;
        }
    }

    /**
     * Calculate the total number of chunks for a file
     * 
     * @param {number} fileSize - The size of the file in bytes
     * @returns {number} - The total number of chunks
     */
    static calculateTotalChunks(fileSize) {
        return Math.ceil(fileSize / this.CHUNK_SIZE);
    }
}

// Make the utility available globally
window.ImageUtils = ImageUtils;
