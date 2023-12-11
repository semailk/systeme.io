<img src="https://avatars.githubusercontent.com/u/57795403?s=48&v=4">
<h1>Тестовое задание Systeme.io</h1>
<h4>1. Запустить проект (docker-compose up -d).</h4>
<h4>2. Подтянуть зависимости.</h4>
<h4>3. Зпустить фикстуры (php bin/console --env=test doctrine:fixtures:load).</h4>
<h4>4. Также для запуска теста (php bin/console do:mi:mi --env=test) сделать миграции (php bin/phpunit) запуск тестов. </h4>
<h4>5. Curl для получения итоговой цены для телефона(страна Италия, купон со скидкой 6%) </h4>
<div style="text-align: center; background: yellow">
curl -X POST -H "Content-Type: application/json" -d '{
    "product": 1,
    "taxNumber": "IT12345678900",
    "couponCode": "D15"
}' http://localhost/calculate-price
</div>
<h5 style="background: chocolate">Пример ответа: <span>{"totalPrice":114.68}</span></h5>
<h4>5. Curl для покупки продукта (страна Италия, купон со скидкой 6%) </h4>
<div style="text-align: center; background: green">
curl -X POST -H "Content-Type: application/json" -d '{
    "product": 1,
    "taxNumber": "IT12345678900",
    "couponCode": "D15",
    "paymentProcessor": "stripe"
}' http://localhost/purchase
</div>
<h5 style="background: chocolate">Пример ответа: <span>{"data":{"id":13,"total_price":114.68,"product_id":1,"created_at":"2023-12-11 15:17:43","coupon_id":1,"payment_type":"stripe"}}</span></h5>