<?php
namespace App\Services;

class UploadService
{
    private string $uploadDir;

    public function __construct()
    {
        $this->uploadDir = __DIR__ . '/../../public/uploads/';
        
        // Créer le dossier s'il n'existe pas
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
    }

    /**
     * Uploader une image
     */
    public function uploadImage(array $file, string $subfolder = ''): ?string
    {
        // Vérifier les erreurs
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        // Vérifier le type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) {
            return null;
        }

        // Vérifier la taille (max 5MB)
        if ($file['size'] > 5 * 1024 * 1024) {
            return null;
        }

        // Générer un nom unique
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $extension;

        // Déterminer le chemin
        $targetDir = $this->uploadDir . ($subfolder ? $subfolder . '/' : '');
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $targetPath = $targetDir . $filename;

        // Déplacer le fichier
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return '/uploads/' . ($subfolder ? $subfolder . '/' : '') . $filename;
        }

        return null;
    }

    /**
     * Supprimer une image
     */
    public function deleteImage(?string $path): bool
    {
        if (empty($path)) {
            return true;
        }

        $fullPath = __DIR__ . '/../../public' . $path;
        if (file_exists($fullPath) && is_file($fullPath)) {
            return unlink($fullPath);
        }

        return true;
    }
}