<?php


class SubscriptionProduct{
    public $priceId;
    public $productId;
    public $title;
    public $price;
    public $interval; // in months
    public $description;
}

class SubscriptionHelper {
    public function GetAllSubscriptionPlans(){

        //fetch from stripe
        $stripe = new \Stripe\StripeClient($_ENV['STRIPE_SECRET']);
        $subscriptions = [];

        $stripeSubscriptions = $stripe->plans->all();

        foreach ($stripeSubscriptions as $stripeSub) {
            $sub = new SubscriptionProduct();
            $sub->priceId = $stripeSub->id;
            $sub->productId = $stripeSub->product;
            //$sub->title = $stripeSub->name;
            $sub->price = $stripeSub->amount / 100; // Convert from cents to dollars
            $sub->interval = "per " . $stripeSub->interval_count . " " . $stripeSub->interval;
            //$sub->description = $stripeSub->description;
            $subscriptions[] = $sub;
        }
        // loop and add product details to each subscription
        foreach ($subscriptions as $sub) {
            $product = $stripe->products->retrieve($sub->productId);
            $sub->title = $product->name;
            $sub->description = $product->description;
        }
//        var_dump($stripeSubscriptions);
        return $subscriptions;
    }
    // Placeholder for future subscription-related methods
}

?>