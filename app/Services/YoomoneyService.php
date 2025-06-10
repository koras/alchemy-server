<?php

namespace App\Services;

use YooKassa\Client;

use YooKassa\Helpers\Random;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use YooKassa\Common\Exceptions\ApiException;
use YooKassa\Common\Exceptions\BadApiRequestException;
use YooKassa\Common\Exceptions\ForbiddenException;
use YooKassa\Common\Exceptions\InternalServerError;
use YooKassa\Common\Exceptions\NotFoundException;
use YooKassa\Common\Exceptions\ResponseProcessingException;
use YooKassa\Common\Exceptions\TooManyRequestsException;
use YooKassa\Common\Exceptions\UnauthorizedException;
use Illuminate\Support\Facades\Log;
use \YooKassa\Model\Payment\ConfirmationType;

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
    public function __construct()
    {
        $this->shopId = config('yookassa.shop_id');
        $this->secretKey = config('yookassa.secret_key');

        $this->urlCallback = config('yookassa.shop_domain') . self::RETURN_URL;

        $this->client = new Client();
        $this->client->setAuth($this->shopId, $this->secretKey);


//        $this->guzzleClient = new GuzzleClient([
//            'base_uri' => 'https://api.yookassa.ru/v3/',
//            'auth' => [$this->shopId, $this->secretKey],
//            'headers' => [
//                'Content-Type' => 'application/json',
//                'Idempotence-Key' => uniqid('key_', true),
//            ],
//        ]);
    }

    public function payment($params)
    {
        if (empty($params['token'])) {
            throw new \InvalidArgumentException('Payment token is required');
        }
        // https://chatgpt.com/c/6848b8bf-a05c-8012-8bbc-3110d3803832
        $idempotenceKey = uniqid('', true);
        $paymentData = [
            'amount' => [
                'value' => number_format($params['amount'], 2, '.', ''),
                'currency' => 'RUB',
            ],
            'payment_token' => $params['token'],
            //    'payment_token' => Random::str(36),
            'confirmation' => [
                // https://yookassa.ru/developers/payment-acceptance/getting-started/payment-process
                //https://yookassa.ru/developers/payment-acceptance/getting-started/payment-methods
           //     'type' => 'redirect',
               // 'type' => 'embedded',
                'type' => 'redirect',
                //   'type' =>  'external',
         //       'locale' => 'ru_RU',
            //    'return_url' => $this->urlCallback,
                'return_url' => 'yourapp://payment-return',
            ],
            'capture' => true,
            'description' => "Покупка {$params['count']} подсказок",
            'metadata' => [
                'user_id' => $params['user_id'],
                'count' => $params['count'],
            ],
        ];
        try {
            $response = $this->client->createPayment($paymentData, $idempotenceKey);
            Log::debug('YooKassa full response', [
                'response' => $response->jsonSerialize(),
                'payment_status' => $response->getStatus(),
                'paymentData' => $paymentData
            ]);

            $status = $response->getStatus();


            if ($status === 'succeeded') {
                return [
                    'status'     => 'success',
                    'payment_id' => $response->getId(),
                ];
            }


            if ($status === 'pending') {
                $confirmation = $response->getConfirmation();
                if (!$confirmation || !$confirmation->getConfirmationUrl()) {
                    throw new \RuntimeException('Платеж ожидает подтверждения, но confirmation_url отсутствует');
                }
                return [
                    'status'           => 'pending',
                    'confirmation_url' => $confirmation->getConfirmationUrl(),
                    'payment_id'       => $response->getId(),
                ];
            }

            return [
                'status'         => $status,
                'payment_id'     => $response->getId(),
            ];


        } catch (BadApiRequestException $e) {
            Log::error('YooKassa Bad Request: ' . $e->getMessage());
            throw new \RuntimeException('Ошибка в запросе к ЮKassa: ' . $e->getMessage());
        } catch (ForbiddenException $e) {
            Log::error('YooKassa Forbidden: ' . $e->getMessage());
            throw new \RuntimeException('Доступ запрещен: проверьте shopId и secretKey');
        } catch (NotFoundException $e) {
            Log::error('YooKassa Not Found: ' . $e->getMessage());
            throw new \RuntimeException('Ресурс не найден');
        } catch (ResponseProcessingException $e) {
            Log::error('YooKassa Response Processing Error: ' . $e->getMessage());
            throw new \RuntimeException('Ошибка обработки ответа от ЮKassa');
        } catch (TooManyRequestsException $e) {
            Log::error('YooKassa Too Many Requests: ' . $e->getMessage());
            throw new \RuntimeException('Слишком много запросов к ЮKassa');
        } catch (UnauthorizedException $e) {
            Log::error('YooKassa Unauthorized: ' . $e->getMessage());
            throw new \RuntimeException('Ошибка авторизации в ЮKassa');
        } catch (InternalServerError $e) {
            Log::error('YooKassa Internal Server Error: ' . $e->getMessage());
            throw new \RuntimeException('Внутренняя ошибка сервера ЮKassa');
        } catch (ApiException $e) {
            Log::error('YooKassa API Error: ' . $e->getMessage());
            throw new \RuntimeException('Ошибка API ЮKassa');
        } catch (\Exception $e) {
            Log::error('Unexpected error in YooKassa payment: ' . $e->getMessage()  .'  '. $e->getCode());
            throw new \RuntimeException('Неожиданная ошибка при обработке платежа');
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


    public function getPaymentInfo($paymentId = 0)
    {
        Log::info('Payment callback error: ' . $paymentId);
        return [$paymentId];
    }


    private function normalizePaymentMethod(string $method): string
    {
        return str_replace('PaymentMethod.', '', $method);
    }
}
