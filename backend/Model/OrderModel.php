<?php

namespace webshop\Model;

class OrderModel extends Database
{
    public function getOrderOverview()
    {
        $sql = "SELECT o.order_id,
                       o.order_date,
                       o.status,
                       o.total_amount,
                       u.firstname,
                       u.lastname,
                       p.name AS product_name,
                       oi.quantity,
                       oi.unit_price
                FROM orders o
                JOIN users u ON u.user_id = o.user_id
                JOIN order_items oi ON oi.order_id = o.order_id
                JOIN products p ON p.product_id = oi.product_id
                ORDER BY o.order_date DESC";

        $pdo = $this->linkDB();
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getOrdersByUser(int $userId)
    {
        $sql = "SELECT o.order_id,
                       o.order_date,
                       o.status,
                       o.total_amount,
                       p.name AS product_name,
                       oi.quantity,
                       oi.unit_price
                FROM orders o
                JOIN order_items oi ON oi.order_id = o.order_id
                JOIN products p ON p.product_id = oi.product_id
                WHERE o.user_id = :user_id
                ORDER BY o.order_date DESC";

        $pdo = $this->linkDB();
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function createOrder(int $userId, array $items): int
    {
        $pdo = $this->linkDB();

        try {
            $pdo->beginTransaction();

            $productStmt = $pdo->prepare(
                "SELECT price, stock FROM products WHERE product_id = :id FOR UPDATE"
            );

            $totalAmount = 0;
            $lineItems = [];

            foreach ($items as $item) {
                $productId = (int) $item['product_id'];
                $quantity = (int) $item['quantity'];

                $productStmt->execute(['id' => $productId]);
                $product = $productStmt->fetch(\PDO::FETCH_ASSOC);

                if (!$product) {
                    throw new \RuntimeException("Produkt Nr. $productId wurde nicht gefunden.");
                }

                if ($quantity < 1 || (int) $product['stock'] < $quantity) {
                    throw new \RuntimeException("Nicht genügend Lagerbestand für Produkt Nr. $productId.");
                }

                $unitPrice = $product['price'];
                $totalAmount += $unitPrice * $quantity;

                $lineItems[] = [
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                ];
            }

            $orderStmt = $pdo->prepare(
                "INSERT INTO orders (user_id, status, total_amount)
                 VALUES (:user_id, :status, :total_amount)"
            );
            $orderStmt->execute([
                'user_id' => $userId,
                'status' => 'offen',
                'total_amount' => $totalAmount,
            ]);
            $orderId = (int) $pdo->lastInsertId();

            $itemStmt = $pdo->prepare(
                "INSERT INTO order_items (order_id, product_id, quantity, unit_price)
                 VALUES (:order_id, :product_id, :quantity, :unit_price)"
            );
            $stockStmt = $pdo->prepare(
                "UPDATE products SET stock = stock - :quantity WHERE product_id = :id"
            );

            foreach ($lineItems as $line) {
                $itemStmt->execute([
                    'order_id' => $orderId,
                    'product_id' => $line['product_id'],
                    'quantity' => $line['quantity'],
                    'unit_price' => $line['unit_price'],
                ]);

                $stockStmt->execute([
                    'quantity' => $line['quantity'],
                    'id' => $line['product_id'],
                ]);
            }

            $pdo->commit();

            return $orderId;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
