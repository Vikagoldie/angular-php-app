<?php

namespace webshop\Controller;

use webshop\Model\OrderModel;

class OrderController
{
    private $orderModel;

    public function __construct()
    {
        $this->orderModel = new OrderModel();
    }

    public function overview()
    {
        return $this->orderModel->getOrderOverview();
    }

    public function myOrders(int $userId)
    {
        return $this->orderModel->getOrdersByUser($userId);
    }

    public function checkout(int $userId, array $items)
    {
        $errors = $this->validateItems($items);

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        try {
            $orderId = $this->orderModel->createOrder($userId, $items);
            return ['success' => true, 'order_id' => $orderId];
        } catch (\RuntimeException $e) {
            // z. B. "nicht genügend Lagerbestand" - für den Benutzer verständlich
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    private function validateItems(array $items): array
    {
        $errors = [];

        if (empty($items)) {
            $errors[] = "Der Warenkorb ist leer.";
            return $errors;
        }

        foreach ($items as $item) {
            if (!isset($item['product_id']) || !is_numeric($item['product_id'])) {
                $errors[] = "Ungültige Produkt-ID im Warenkorb.";
            }

            if (!isset($item['quantity']) || filter_var($item['quantity'], FILTER_VALIDATE_INT) === false || $item['quantity'] < 1) {
                $errors[] = "Ungültige Menge im Warenkorb.";
            }
        }

        return $errors;
    }
}
