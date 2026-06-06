<?php
/**
 * Shared helper to upload a profile photo.
 * Returns the filename (string) on success, or null if no file / on error.
 * Allowed: jpg, jpeg, png, webp — max 2 MB.
 */
function uploadFoto(array $file, string $prefix): ?string {
    if (!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK || empty($file['tmp_name'])) {
        return null;
    }

    $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);

    if (!in_array($mime, $allowed_types, true)) {
        return null;
    }

    if ($file['size'] > 2 * 1024 * 1024) {
        return null;
    }

    $ext_map = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $ext = $ext_map[$mime];
    $filename = $prefix . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;

    $upload_dir = __DIR__ . '/../../assets/images/profiles/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    if (!move_uploaded_file($file['tmp_name'], $upload_dir . $filename)) {
        return null;
    }

    return $filename;
}
