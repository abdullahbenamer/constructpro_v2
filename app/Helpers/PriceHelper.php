<?php

class PriceHelper {

public static function unitPrice($product, $qty, $unit = 'meter') {

    $base = $product->retail_price / $product->units_per_stock;

    // apply loss factor for partial selling
    $loss_factor = 1.15;

    if ($unit === 'meter') {
        return $base * $loss_factor;
    }

    return $product->retail_price;
}
}