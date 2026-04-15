<?php

namespace App\Services;

class ShippingService {
    public static function calculate(string $state): float {
        return 15.00;
    }
}