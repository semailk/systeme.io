<?php

namespace App\Controller\Api;

use App\Dto\CalculatePriceRequest;
use App\Dto\PurchaseRequest;
use App\Service\CalculatePriceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class ShopController extends AbstractController
{
    public function __construct(
        private readonly CalculatePriceService $calculatePriceService,
        private readonly SerializerInterface $serializer,
    ) {
    }

    #[Route(path: '/calculate-price', methods: 'POST')]
    public function calculatePrice(Request $request, ValidatorInterface $validator): JsonResponse
    {
        /** @var CalculatePriceRequest $calculatePriceRequest */
        $calculatePriceRequest = $this->serializer->deserialize(
            $request->getContent(),
            CalculatePriceRequest::class,
            'json'
        );

        $errors = $validator->validate($calculatePriceRequest);

        if (count($errors) > 0) {
            $formattedErrors = [];
            foreach ($errors as $error) {
                $propertyPath = $error->getPropertyPath();
                $message = $error->getMessage();

                $formattedErrors[$propertyPath][] = $message;
            }

            return $this->json($formattedErrors, 400);
        }

        return new JsonResponse(['totalPrice' => $this->calculatePriceService->getCalculatePrice($calculatePriceRequest)]);
    }

    #[Route(path: '/purchase', methods: 'POST')]
    public function purchase(Request $request, ValidatorInterface $validator): JsonResponse
    {
        /** @var PurchaseRequest $purchaseRequest */
        $purchaseRequest = $this->serializer->deserialize(
            $request->getContent(),
            PurchaseRequest::class,
            'json'
        );

        $errors = $validator->validate($purchaseRequest);

        if (count($errors) > 0) {
            $formattedErrors = [];
            foreach ($errors as $error) {
                $propertyPath = $error->getPropertyPath();
                $message = $error->getMessage();

                $formattedErrors[$propertyPath][] = $message;
            }

            return $this->json($formattedErrors, 400);
        }
        $payment = $this->calculatePriceService->purchace($purchaseRequest);

        return new JsonResponse(['data' => [
            'id' => $payment->getId(),
            'total_price' => $payment->getTotalPrice(),
            'product_id' => $payment->getProduct()->getId(),
            'created_at' => $payment->getCreatedAt()->format('Y-m-d H:i:s'),
            'coupon_id' => $payment->getCoupon()?->getId(),
            'payment_type' => $payment->getType()
        ]], Response::HTTP_CREATED);
    }
}
