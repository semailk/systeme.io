<?php

namespace App\DataFixtures;

use App\Entity\Code;
use App\Entity\Coupon;
use App\Entity\Currency;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $products = [
            1 => [
                'name' => 'Iphone',
                'price' => 100
            ],
            2 => [
                'name' => 'Наушники',
                'price' => 20
            ],
            3 => [
                'name' => 'Чехол',
                'price' => 10
            ]
        ];

        $coupons = [
            0 => ['code' => 'D15', 'type' => 'percentage'],
            1 => ['code' => 'D16', 'type' => 'fixed'],
        ];
        foreach ($coupons as $coupon) {
            $coupon = new Coupon($coupon['code'], $coupon['type'], 6);
            $manager->persist($coupon);
        }

        $currency = new Currency(new Code('EUR'), 'евро');
        $manager->persist($currency);

        foreach ($products as $key => $product) {
            $product = new Product(
                $product['name'],
                $product['price']
            );

            $product->setId($key);
            $product->setCurrency($currency);
            $manager->persist($product);
        }

        $manager->flush();
    }
}
