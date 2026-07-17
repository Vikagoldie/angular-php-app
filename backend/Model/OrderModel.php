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
}