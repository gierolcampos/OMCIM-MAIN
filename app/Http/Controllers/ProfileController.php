<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        // Handle profile picture upload
        try {
            // Check if we're using base64 data
            if ($request->has('using_base64') && $request->has('profile_picture_base64')) {
                // Get the base64 image data
                $base64Image = $request->input('profile_picture_base64');

                // Validate the base64 data
                if (empty($base64Image)) {
                    return back()->withErrors(['profile_picture' => 'Invalid image data']);
                }

                // Store the base64 data directly in the database
                // If GD library is available, it will be optimized
                $validated['profile_picture'] = $this->optimizeBase64Image($base64Image);
            }
            // Handle traditional file upload
            elseif ($request->hasFile('profile_picture')) {
                // Store the new profile picture
                $profilePicture = $request->file('profile_picture');

                // Check file size and type
                if ($profilePicture->getSize() > 5 * 1024 * 1024) {
                    return back()->withErrors(['profile_picture' => 'Image size should not exceed 5MB']);
                }

                if (!in_array($profilePicture->getMimeType(), ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'])) {
                    return back()->withErrors(['profile_picture' => 'Only JPG, PNG and GIF images are allowed']);
                }

                // Convert the uploaded file to base64
                $imageData = file_get_contents($profilePicture->getPathname());
                $base64Image = 'data:' . $profilePicture->getMimeType() . ';base64,' . base64_encode($imageData);

                // Store the base64 data directly in the database
                // If GD library is available, it will be optimized
                $validated['profile_picture'] = $this->optimizeBase64Image($base64Image);
            }
        } catch (\Exception $e) {
            // Log the error
            Log::error('Profile picture upload failed: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            // In development environment, show the actual error
            if (config('app.debug')) {
                return back()->withErrors(['profile_picture' => 'Error: ' . $e->getMessage()]);
            }

            return back()->withErrors(['profile_picture' => 'Failed to upload profile picture. Please try again.']);
        }

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Request to delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Set the deletion request timestamp
        $user->deletion_requested_at = now();
        $user->save();

        // Notify admins about the deletion request (implementation can be added later)
        // TODO: Add notification for admins

        return Redirect::route('profile.edit')->with('status', 'deletion-requested');
    }

    /**
     * Optimize a base64 encoded image to reduce its size.
     *
     * @param string $base64Image The base64 encoded image data
     * @return string The optimized base64 encoded image data
     */
    private function optimizeBase64Image(string $base64Image): string
    {
        try {
            // Check if GD library is available
            if (!extension_loaded('gd')) {
                Log::warning('GD library is not available. Image optimization skipped.');
                return $base64Image;
            }

            // Extract the MIME type and base64 data
            $parts = explode(';base64,', $base64Image);
            $mimeType = str_replace('data:', '', $parts[0] ?? '') ?: 'image/jpeg';
            $base64Data = $parts[1] ?? $base64Image;

            // Decode the base64 data
            $imageData = base64_decode($base64Data);
            if (!$imageData) {
                return $base64Image; // Return original if decoding fails
            }

            // Create a temporary file
            $tempFile = tempnam(sys_get_temp_dir(), 'img');
            file_put_contents($tempFile, $imageData);

            // Create an image resource from the temporary file
            $image = @imagecreatefromstring($imageData);
            if (!$image) {
                unlink($tempFile);
                return $base64Image; // Return original if image creation fails
            }

            // Get image dimensions
            $width = imagesx($image);
            $height = imagesy($image);

            // Resize if too large (max 500x500 pixels)
            if ($width > 500 || $height > 500) {
                // Calculate new dimensions while maintaining aspect ratio
                if ($width > $height) {
                    $newWidth = 500;
                    $newHeight = intval($height * (500 / $width));
                } else {
                    $newHeight = 500;
                    $newWidth = intval($width * (500 / $height));
                }

                // Create a new image with the new dimensions
                $newImage = imagecreatetruecolor($newWidth, $newHeight);

                // Preserve transparency for PNG images
                if ($mimeType === 'image/png') {
                    imagealphablending($newImage, false);
                    imagesavealpha($newImage, true);
                    $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
                    imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
                }

                // Resize the image
                imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

                // Replace the original image with the resized one
                imagedestroy($image);
                $image = $newImage;
            }

            // Start output buffering
            ob_start();

            // Output the image with compression
            switch ($mimeType) {
                case 'image/jpeg':
                    imagejpeg($image, null, 85); // 85% quality
                    break;
                case 'image/png':
                    imagepng($image, null, 6); // Compression level 6 (0-9)
                    break;
                case 'image/gif':
                    imagegif($image);
                    break;
                default:
                    imagejpeg($image, null, 85);
                    $mimeType = 'image/jpeg';
            }

            // Get the compressed image data
            $optimizedImageData = ob_get_clean();

            // Clean up
            imagedestroy($image);
            unlink($tempFile);

            // Return the optimized base64 encoded image
            return 'data:' . $mimeType . ';base64,' . base64_encode($optimizedImageData);
        } catch (\Exception $e) {
            // Log the error but return the original image
            Log::warning('Image optimization failed: ' . $e->getMessage());
            return $base64Image;
        }
    }
}
