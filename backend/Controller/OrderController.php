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
}