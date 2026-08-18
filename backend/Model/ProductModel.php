<?php

namespace webshop\Model;

use webshop\Model\Database;

class ProductModel extends Database
{
    public function getAll(?string $search = null, string $sort = 'name', string $direction = 'ASC')
    {
        $allowedSort = ['name', 'price', 'stock'];
        $allowedDirection = ['ASC', 'DESC'];

        if (!in_array($sort, $allowedSort, true)) {
            $sort = 'name';
        }

        if (!in_array($direction, $allowedDirection, true)) {
            $direction = 'ASC';
        }

        $pdo = $this->linkDB();

        if ($search !== null && trim($search) !== '') {
            $sql = "SELECT product_id, name, description, price, stock, image_path
                FROM products
                WHERE name LIKE :search
                ORDER BY $sort $direction";

            $stmt = $pdo->prepare($sql);
            $stmt->execute(['search' => '%' . $search . '%']);
        } else {
            $sql = "SELECT product_id, name, description, price, stock, image_path
                FROM products
                ORDER BY $sort $direction";

            $stmt = $pdo->prepare($sql);
            $stmt->execute();
        }

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getById(int $id)
    {
        $sql = "SELECT product_id, name, description, price, stock, image_path
                FROM products
                WHERE product_id = :id";

        $pdo = $this->linkDB();
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch();
    }

    public function create(array $data): int
    {
        $sql = "INSERT INTO products (name, description, price, stock)
                VALUES (:name, :description, :price, :stock)";

        $pdo = $this->linkDB();
        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            'name' => $data['name'],
            'description' => $data['description'],
            'price' => $data['price'],
            'stock' => $data['stock']
        ]);

        return (int) $pdo->lastInsertId();
    }

    public function update(int $id, array $data)
    {
        $sql = "UPDATE products
                SET name = :name,
                    description = :description,
                    price = :price,
                    stock = :stock
                WHERE product_id = :id";

        $pdo = $this->linkDB();
        $stmt = $pdo->prepare($sql);

        return $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'description' => $data['description'],
            'price' => $data['price'],
            'stock' => $data['stock']
        ]);
    }

    public function updateImage(int $id, string $imagePath): bool
    {
        $sql = "UPDATE products SET image_path = :image_path WHERE product_id = :id";

        $pdo = $this->linkDB();
        $stmt = $pdo->prepare($sql);

        return $stmt->execute([
            'id' => $id,
            'image_path' => $imagePath
        ]);
    }

    public function delete(int $id)
    {
        $sql = "DELETE FROM products WHERE product_id = :id";

        $pdo = $this->linkDB();
        $stmt = $pdo->prepare($sql);

        return $stmt->execute(['id' => $id]);
    }
}
