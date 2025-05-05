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

    public function convertSessionToUser($userId, $newSessionId) {
        $this->dbContext->convertSessionToUser($this->session_id, $userId, $newSessionId);
      
        $this->userId = $userId;
        $this->session_id = $newSessionId;
    }

    public function addItem($productId, $quantity) {
        $item = $this->getCartItem($productId);
        if (!$item) {
            $item = new CartItem();
            $item->productId = $productId;
            $item->quantity = $quantity;
            array_push($this->cartItems, $item);
        }else{
            $item->quantity += $quantity;
        }
        $this->dbContext->updateCartItem($this->userId,$this->session_id, $productId, $item->quantity);
    }

    public function removeItem($productId, $quantity) {
        $item = $this->getCartItem($productId);
        if( !$item) {
            return;
        }
        $item->quantity -= $quantity;
        $this->dbContext->updateCartItem($this->userId,$this->session_id, $productId, $item->quantity);
        if ($item->quantity <= 0) {
            array_splice($this->cartItems, array_search($item, $this->cartItems), 1);
        }
    }

    public function getCartItem($productId) {
        foreach ($this->cartItems as $item) {
            if ($item->productId == $productId) {
                return $item;
            }
        }
        return null;
    }


    public function getItemsCount() {
        $count = 0;
        foreach ($this->cartItems as $item) {
            $count += $item->quantity;
        }
        return $count;
        //return count($this->cartItems);
    }

    public function getTotalPrice() {
        $total = 0;
        foreach ($this->cartItems as $item) {
            $total += $item->rowPrice;
        }
        return $total;
    }


    public function getItems() {
        return $this->cartItems;
    }

    public function clearCart() {
        $this->cartItems = [];
    }
}


?>