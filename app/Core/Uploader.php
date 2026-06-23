<?php
namespace App\Core;

class Uploader
{
    private string $uploadDir;

    public function __construct(string $subFolder = 'plats')
    {
        $this->uploadDir = PUBLIC_PATH . "/uploads/$subFolder/";
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
    }

    /**
     * Upload un fichier et retourne le chemin relatif (ou null en cas d'échec)
     */
    public function upload(array $file): ?string
    {
        if ($file['error'] !== UPLOAD_ERR_OK) return null;

        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file['type'], $allowed)) return null;
        if ($file['size'] > 5 * 1024 * 1024) return null;

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $ext;
        $target = $this->uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $target)) {
            return "/uploads/" . basename(dirname($this->uploadDir)) . "/$filename";
        }
        return null;
    }

    /**
     * Supprime un fichier
     */
    public function delete(string $path): bool
    {
        $full = PUBLIC_PATH . $path;
        if (file_exists($full) && is_file($full)) {
            return unlink($full);
        }
        return true;
    }
}