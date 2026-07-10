<?php

namespace webshop\Model;

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

        $sql = "SELECT product_id, name, description, price, stock
                FROM products
                WHERE (:search IS NULL OR name LIKE :searchLike)
                ORDER BY $sort $direction";

        $pdo = $this->linkDB();
        $stmt = $pdo->prepare($sql);
        $searchLike = $search ? "%$search%" : null;

        $stmt->bindValue(':search', $search, \PDO::PARAM_STR);
        $stmt->bindValue(':searchLike', $searchLike, \PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getById(int $id)
    {
        $sql = "SELECT product_id, name, description, price, stock
                FROM products
                WHERE product_id = :id";

        $pdo = $this->linkDB();
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch();
    }

    public function create(array $data)
    {
        $sql = "INSERT INTO products (name, description, price, stock)
                VALUES (:name, :description, :price, :stock)";

        $pdo = $this->linkDB();
        $stmt = $pdo->prepare($sql);

        return $stmt->execute([
            'name' => $data['name'],
            'description' => $data['description'],
            'price' => $data['price'],
            'stock' => $data['stock']
        ]);
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

    public function delete(int $id)
    {
        $sql = "DELETE FROM products WHERE product_id = :id";

        $pdo = $this->linkDB();
        $stmt = $pdo->prepare($sql);

        return $stmt->execute(['id' => $id]);
    }
}