<?php

namespace App;

class ShopPlatformDefine
{
    CONST AMAZON = '';
    CONST WISH = '';
    CONST ALIEXPRESS = '';
    CONST EBAY = '';
    CONST ETSY = '';
    CONST WALMART = '';

    public static function getPlatforms()
    {
        return [
            'amazon' => '亚马逊',
            'wish' => 'Wish',
            'aliexpress' => '速卖通',
            'ebay' => 'eBay',
            'shopify' => 'Shopify',
            'shopee' => 'Shopee',
        ];
    }

    public static function getName($platform)
    {

    }
}
