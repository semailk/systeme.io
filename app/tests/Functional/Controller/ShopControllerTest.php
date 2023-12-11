<?php

namespace App\Tests\Functional\Controller;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\DataFixtures\Loader;

class ShopControllerTest extends WebTestCase
{
    use RefreshDatabaseTrait;
    private const CALCULATE_PRICE_URL = '/calculate-price';
    private const CALCULATE_PURCHASE = '/purchase';

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = self::createClient();
        $this->client->disableReboot();
        $loader = new Loader();
        $loader->loadFromFile(dirname(__DIR__, 3) . '/src/DataFixtures/AppFixtures.php');

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $this->client->getContainer()->get(EntityManagerInterface::class);
        $executor = new ORMExecutor($entityManager, new ORMPurger());
        $executor->execute($loader->getFixtures(), append: true);

    }

    public function testCalculatePrice(): void
    {
        $this->client->request(Request::METHOD_POST, self::CALCULATE_PRICE_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(
                [
                    'product' => 1,
                    'taxNumber' => 'IT12345678900',
                    'couponCode' => 'D15'
                ]));
        $responseContent = $this->client->getResponse()->getContent();
        $response = json_decode($responseContent, true);
        self::assertEquals(114.68, $response['totalPrice']);

        $this->client->request(Request::METHOD_POST, self::CALCULATE_PRICE_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(
                [
                    'product' => 1,
                    'taxNumber' => 'IT12345678900',
                    'couponCode' => 'D16'
                ]));

        $responseContent = $this->client->getResponse()->getContent();
        $response = json_decode($responseContent, true);

        self::assertEquals(116, $response['totalPrice']);

        $this->client->request(Request::METHOD_POST, self::CALCULATE_PRICE_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(
                [
                    'product' => 1,
                    'taxNumber' => 'DE12345678900',
                    'couponCode' => 'D15'
                ]));
        $responseContent = $this->client->getResponse()->getContent();
        $response = json_decode($responseContent, true);
        self::assertEquals(111.86, $response['totalPrice']);

        $this->client->request(Request::METHOD_POST, self::CALCULATE_PRICE_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(
                [
                    'product' => 1,
                    'taxNumber' => 'DE12345678900',
                    'couponCode' => 'D16'
                ]));
        $responseContent = $this->client->getResponse()->getContent();
        $response = json_decode($responseContent, true);
        self::assertEquals(113, $response['totalPrice']);

        $this->client->request(Request::METHOD_POST, self::CALCULATE_PRICE_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(
                [
                    'product' => 1,
                    'taxNumber' => 'FRET12345678900',
                    'couponCode' => 'D15'
                ]));
        $responseContent = $this->client->getResponse()->getContent();
        $response = json_decode($responseContent, true);

        self::assertEquals(112.8, $response['totalPrice']);

        $this->client->request(Request::METHOD_POST, self::CALCULATE_PRICE_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(
                [
                    'product' => 1,
                    'taxNumber' => 'FRAZ12345678900',
                    'couponCode' => 'D16'
                ]));
        $responseContent = $this->client->getResponse()->getContent();
        $response = json_decode($responseContent, true);
        self::assertEquals(114, $response['totalPrice']);

        $this->client->request(Request::METHOD_POST, self::CALCULATE_PRICE_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(
                [
                    'product' => 1,
                    'taxNumber' => 'GR125678900',
                    'couponCode' => 'D15'
                ]));
        $responseContent = $this->client->getResponse()->getContent();
        $response = json_decode($responseContent, true);

        self::assertEquals(116.56, $response['totalPrice']);

        $this->client->request(Request::METHOD_POST, self::CALCULATE_PRICE_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(
                [
                    'product' => 1,
                    'taxNumber' => 'GR123456780',
                    'couponCode' => 'D16'
                ]));
        $responseContent = $this->client->getResponse()->getContent();
        $response = json_decode($responseContent, true);
        self::assertEquals(118, $response['totalPrice']);
    }

    public function testPurchase(): void
    {
        $this->client->request(Request::METHOD_POST, self::CALCULATE_PURCHASE,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(
                [
                    'product' => 4,
                    'taxNumber' => 'DE12345678900',
                    'couponCode' => 'D16',
                    'paymentProcessor' => 'paypal'
                ]));

        /** @var array<array-key, array> $response */
        $response = json_decode($this->client->getResponse()->getContent(), true);

        self::assertArrayHasKey('data', $response);
        self::assertEquals(1, $response['data']['id']);
        self::assertEquals(113, $response['data']['total_price']);
        self::assertEquals(4, $response['data']['coupon_id']);
        self::assertEquals('paypal', $response['data']['payment_type']);

        $this->client->request(Request::METHOD_POST, self::CALCULATE_PURCHASE,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(
                [
                    'product' => 5,
                    'taxNumber' => 'IT12345678900',
                    'couponCode' => 'D15',
                    'paymentProcessor' => 'stripe'
                ]));

        /** @var array<array-key, array> $response */
        $response = json_decode($this->client->getResponse()->getContent(), true);

        self::assertArrayHasKey('data', $response);
        self::assertEquals(2, $response['data']['id']);
        self::assertEquals(22.936, $response['data']['total_price']);
        self::assertEquals(3, $response['data']['coupon_id']);
        self::assertEquals('stripe', $response['data']['payment_type']);
    }

    public function testValidate(): void
    {
       $this->client->request(Request::METHOD_POST, self::CALCULATE_PURCHASE,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(
                [
                ]));
        $responseValidate = json_decode($this->client->getResponse()->getContent(), true);

        self::assertArrayHasKey('product', $responseValidate);
        self::assertArrayHasKey('taxNumber', $responseValidate);
        self::assertArrayHasKey('paymentProcessor', $responseValidate);
        self::assertEquals('This value should not be blank.', $responseValidate['product'][0]);
        self::assertEquals('This value should not be blank.', $responseValidate['taxNumber'][0]);
        self::assertEquals('This value should not be blank.', $responseValidate['paymentProcessor'][0]);
    }
}