<?php

// Check if the storage directory exists
$storageDir = __DIR__ . '/storage/app/public/profile-pictures';
if (!file_exists($storageDir)) {
    // Create the directory recursively
    mkdir($storageDir, 0755, true);
    echo "Created directory: $storageDir\n";
} else {
    echo "Directory already exists: $storageDir\n";
}

// Check if the storage link exists
$publicStorageDir = __DIR__ . '/public/storage';
if (!file_exists($publicStorageDir)) {
    echo "Storage link does not exist. Run 'php artisan storage:link' to create it.\n";
} else {
    echo "Storage link exists: $publicStorageDir\n";
}

// Check if the profile-pictures directory is accessible
$publicProfilePicsDir = __DIR__ . '/public/storage/profile-pictures';
if (!file_exists($publicProfilePicsDir)) {
    mkdir($publicProfilePicsDir, 0755, true);
    echo "Created directory: $publicProfilePicsDir\n";
} else {
    echo "Directory already exists: $publicProfilePicsDir\n";
}

echo "Storage check completed.\n";
