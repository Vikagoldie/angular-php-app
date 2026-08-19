<?php

namespace webshop\Controller;

use webshop\Model\ProductModel;

class ProductController
{
    private const MAX_IMAGE_SIZE_BYTES = 2 * 1024 * 1024; // 2 MB
    private const ALLOWED_IMAGE_TYPES = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    private $productModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
    }

    public function index(array $query)
    {
        $search = $query['search'] ?? null;
        $sort = $query['sort'] ?? 'name';
        $direction = $query['direction'] ?? 'ASC';

        return $this->productModel->getAll($search, $sort, $direction);
    }

    public function show(int $id)
    {
        return $this->productModel->getById($id);
    }

    public function store(array $post)
    {
        $errors = $this->validateProduct($post);

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $id = $this->productModel->create($post);
        return ['success' => true, 'product_id' => $id];
    }

    public function update(int $id, array $post)
    {
        if (!$this->productModel->getById($id)) {
            return ['success' => false, 'message' => 'Produkt nicht gefunden.'];
        }

        $errors = $this->validateProduct($post);

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $this->productModel->update($id, $post);
        return ['success' => true];
    }

    public function destroy(int $id)
    {
        try {
            $this->productModel->delete($id);
            return ['success' => true];
        } catch (\PDOException $e) {
            // SQLSTATE 23000 = Integritätsverletzung, z. B. weil order_items
            // noch per Foreign Key auf dieses Produkt verweist. In diesem
            // Fall wird bewusst NICHT kaskadierend gelöscht, damit bestehende
            // Bestellungen nachvollziehbar bleiben.
            if ($e->getCode() === '23000') {
                return [
                    'success' => false,
                    'message' => 'Diese Geschenkbox kann nicht gelöscht werden, da bereits Bestellungen dafür existieren.'
                ];
            }

            throw $e;
        }
    }

    public function uploadImage(int $id, ?array $file)
    {
        if (!$this->productModel->getById($id)) {
            return ['success' => false, 'message' => 'Produkt nicht gefunden.'];
        }

        $errors = $this->validateImage($file);

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $extension = self::ALLOWED_IMAGE_TYPES[$this->detectMimeType($file['tmp_name'])];
        $filename = uniqid('product_', true) . '.' . $extension;
        $targetPath = __DIR__ . '/../api/uploads/products/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            return ['success' => false, 'message' => 'Die Datei konnte nicht gespeichert werden.'];
        }

        $relativePath = 'uploads/products/' . $filename;
        $this->productModel->updateImage($id, $relativePath);

        return ['success' => true, 'image_path' => $relativePath];
    }

    private function validateProduct(array $data): array
    {
        $errors = [];

        if (empty(trim($data['name'] ?? ''))) {
            $errors[] = "Produktname ist erforderlich.";
        }

        if (!isset($data['price']) || !is_numeric($data['price']) || $data['price'] < 0) {
            $errors[] = "Preis muss eine gültige positive Zahl sein.";
        }

        if (!isset($data['stock']) || filter_var($data['stock'], FILTER_VALIDATE_INT) === false || $data['stock'] < 0) {
            $errors[] = "Bestand muss eine gültige ganze Zahl sein.";
        }

        return $errors;
    }

    private function validateImage(?array $file): array
    {
        $errors = [];

        if ($file === null || !isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = "Es wurde keine gültige Bilddatei übertragen.";
            return $errors;
        }

        if ($file['size'] > self::MAX_IMAGE_SIZE_BYTES) {
            $errors[] = "Das Bild darf maximal 2 MB groß sein.";
        }

        if ($this->detectMimeType($file['tmp_name']) === null) {
            $errors[] = "Nur JPG-, PNG- oder WEBP-Bilder sind erlaubt.";
        }

        return $errors;
    }

    private function detectMimeType(string $tmpFilePath): ?string
    {
        $imageInfo = @getimagesize($tmpFilePath);
        $mime = $imageInfo['mime'] ?? null;

        return $mime !== null && isset(self::ALLOWED_IMAGE_TYPES[$mime]) ? $mime : null;
    }
}
