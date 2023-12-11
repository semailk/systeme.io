<?php

namespace App\Service\PaymentProcessor;

use App\Command\Payment\Create\Command;
use App\Command\Payment\Create\Handler;
use App\Entity\Payment;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class StripePaymentProcessor
{

    public static function processPayment(Handler $handler, Command $command): Payment
    {
        try {
            $payment = $handler->handle($command);
        }catch (\Exception $exception){
            throw new BadRequestHttpException($exception->getMessage());
        }
        return $payment;
    }
}