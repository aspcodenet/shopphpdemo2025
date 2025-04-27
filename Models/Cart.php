<?php

class Cart {
    private $dbContext;
    private $session_id;
    private $userId;
    private $cartItems = [];

    public function __construct($dbContext, $session_id, $userId = null) {
        $this->dbContext = $dbContext;
        $this->session_id = $session_id;
        $this->userId = $userId;
        $this->cartItems = $this->dbContext->getCartItems($userId,$session_id);

    }

    public function addItem($productId, $quantity) {
        if (isset($this->cartItems[$productId])) {
            $this->cartItems[$productId] += $quantity;
        } else {
            $this->cartItems[$productId] = $quantity;
        }
        $this->dbContext->updateCartItem($this->userId,$this->session_id,  $productId, $this->cartItems[$productId]);
    }

    public function removeItem($productId, $quantity) {
        if (isset($this->cartItems[$productId])) {
            $this->cartItems[$productId] -= $quantity;
            $this->dbContext->updateCartItem($this->userId,$this->session_id, $productId, $this->cartItems[$productId]);
            if ($this->cartItems[$productId] <= 0) {
                unset($this->cartItems[$productId]);
            } 
        }

    }

    public function getItems() {
        return $this->cartItems;
    }

    public function clearCart() {
        $this->cartItems = [];
    }
}


?>