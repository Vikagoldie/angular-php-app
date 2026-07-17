<?php

namespace webshop\Controller;

use webshop\Model\ProductModel;

class ProductController
{
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

        $this->productModel->create($post);
        return ['success' => true];
    }

    public function update(int $id, array $post)
    {
        $errors = $this->validateProduct($post);

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $this->productModel->update($id, $post);
        return ['success' => true];
    }

    public function destroy(int $id)
    {
        $this->productModel->delete($id);
        return ['success' => true];
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
}