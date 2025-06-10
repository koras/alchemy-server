<?php

namespace App\Services;
use YooKassa\Client;

use YooKassa\Helpers\Random;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;

use Illuminate\Support\Facades\Log;

use App\Contracts\Services\YoomoneyServiceInterface;

class YoomoneyService implements YoomoneyServiceInterface
{
    private $shopId;
    private $secretKey;
    const RETURN_URL = '/api/yoomoney/payments/callback';
    private $urlCallback;
    private $guzzleClient;
    /**
     * https://git.yoomoney.ru/projects/SDK/repos/yookassa-sdk-php/browse/README.md
     * https://git.yoomoney.ru/projects/SDK/repos/yookassa-android-sdk/browse
     * @param Client $client
     */
    public function __construct(private Client $client)
    {
        $this->shopId = config('yookassa.shop_id');
        $this->secretKey = config('yookassa.secret_key');

        $this->urlCallback = config('yookassa.shop_domain') . self::RETURN_URL;
        $this->guzzleClient = new GuzzleClient([
            'base_uri' => 'https://api.yookassa.ru/v3/',
            'auth' => [$this->shopId, $this->secretKey],
            'headers' => [
                'Content-Type' => 'application/json',
                'Idempotence-Key' => uniqid('key_', true),
            ],
        ]);
    }

    public function payment($params)
    {
        if (empty($params['token'])) {
            throw new \InvalidArgumentException('Payment token is required');
        }

        $paymentData = [
            'amount' => [
                'value' => $params['amount'],
                'currency' => 'RUB',
            ],
       //     'payment_token' => $params['token'],
            'payment_token' => Random::str(36),
            'confirmation' => [
                'type' => 'redirect',
                'locale' => 'ru_RU',
                'return_url' => $this->urlCallback,
            ],
            'capture' => true,
            'description' => "Покупка {$params['count']} подсказок",
            'metadata' => [
                'user_id' => auth()->id(),
                'count' => $params['count'],
            ],
        ];
    //    dd($paymentData );
        try {
            $response = $this->guzzleClient->post('payments', [
                'json' => $paymentData
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true);

            return [
                'status' => 'success',
                'confirmation_url' => $responseData['confirmation']['confirmation_url'],
                'payment_id' => $responseData['id'],
            ];

        } catch (GuzzleException $e) {
            throw new \RuntimeException('YooKassa API error: ' . $e->getMessage());
        }
    }

    // Обработка возврата пользователя после оплаты
    public function callback(Request $request)
    {
        $paymentId = $request->input('paymentId');

        if (empty($paymentId)) {
            Log::error('Callback called without paymentId', $request->all());
            return redirect()->route('payment.error')->with('error', 'Неверные параметры платежа');
        }

        try {
            // 1. Проверяем статус платежа через API ЮKassa
            $paymentInfo = $this->yoomoneyService->getPaymentInfo($paymentId);

            // 2. Обрабатываем результат
            if ($paymentInfo['status'] === 'succeeded') {
                // Платеж успешен
                return redirect()->route('payment.success')
                    ->with('payment', $paymentInfo);
            } else {
                // Платеж в другом статусе (waiting_for_capture, pending, canceled)
                return redirect()->route('payment.process')
                    ->with('payment', $paymentInfo);
            }

        } catch (\Exception $e) {
            Log::error('Payment callback error: ' . $e->getMessage(), [
                'paymentId' => $paymentId,
                'exception' => $e
            ]);

            return redirect()->route('payment.error')
                ->with('error', 'Ошибка при проверке статуса платежа');
        }
    }


    public function getPaymentInfo($paymentId)
    {
        Log::info('Payment callback error: ' . $paymentId);
        return [$paymentId];
    }


    private function normalizePaymentMethod(string $method): string
    {
        return str_replace('PaymentMethod.', '', $method);
    }
}
