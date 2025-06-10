<?php

namespace App\Http\Controllers;

use App\Contracts\Services\YoomoneyServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class YoomoneyController extends Controller
{

    const YOOMONEY_SECRET_KEY = "test_27R2vMsXEKluYQYNmDXpfgEmzi64kp4r7Nqo_7YBLag";
    const YOOMONEY_SHOP_ID = 0;
    const YOOMONEY_SCID = 0;


    public function __construct(private readonly YoomoneyServiceInterface $yoomoneyService)
    {

    }

    public function processPaymentGet(Request $request)
    {



        return [$request->all()];
    }

    public function processPayment(Request $request)
    {

        $validated = $request->validate([
            'token' => 'required|string',
            'payment_method' => 'required|string',
            'amount' => 'required|numeric',
            'count' => 'required|integer',
            'user_id' => 'required|integer',
        ]);

        return $this->yoomoneyService->payment($validated);
    }

    public function getPaymentInfo(int $paymentId)
    {
        return $this->yoomoneyService->getPaymentInfo($paymentId);
    }

    public function paymentsCallback(Request $request)
    {

        $paymentId = $request->input('paymentId',0);

        if (empty($paymentId)) {
            Log::error('Callback called without paymentId', $request->all());
       //     return redirect()->route('payment.error')->with('error', 'Неверные параметры платежа');
        }

        try {
            // 1. Проверяем статус платежа через API ЮKassa
            $paymentInfo = $this->yoomoneyService->getPaymentInfo($paymentId);

          //  Log::error('Callback called without paymentId', $request->all());


            // 2. Обрабатываем результат
            if (isset($paymentInfo['status'] ) && $paymentInfo['status'] === 'succeeded') {
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


    public function paymentError(Request $request)
    {
        return [$request->all(),'paymentError'=>'paymentError'];
    }

    public function redirect()
    {
        return ['status' => 'true'];
    }

    public function notification()
    {
        return ['status' => 'true'];
    }
}
