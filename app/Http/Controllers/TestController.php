<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use OmiseAccount;
use OmiseCharge;
use Predis\Command\Redis\FUNCTIONS;

class TestController extends ApiController
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function test(Request $request)
    {
        return $this->success($this->charge(10000, 'jpy', '112233'));
        $user = $request->user();

        return $this->success($this->getChargeTestData());
        $publickey = env('OMISE_PUBLIC_KEY');
        $secretkey = env('OMISE_SECRET_KEY');
        $account = OmiseAccount::retrieve($publickey, $secretkey);

        $user = $account; // your email will be printed on a screen.
        Log::info(json_encode($user));

        dd($user);
        return;

        $charge = OmiseCharge::create(array(
            'amount' => 10025,
            'currency' => 'jpy',
            'card' => '4445566'
        ));

        echo ($charge['status']);

        print('<pre>');
        print_r($charge);
        print('</pre>');

        dd($charge);

        return $this->success($user);
    }

    private function charge($amount, $currency, $card)
    {
        try {
            $charge = OmiseCharge::create(array(
                'amount' => $amount,
                'currency' => $currency,
                'card' => $card
            ), env('OMISE_PUBLIC_KEY'), env('OMISE_SECRET_KEY'));
            $result = [
                'id' => $charge['id'],
                'amount' => $charge['amount'],
                'currency' => $charge['currency'],
                'card' => $charge['card'],
            ];
            Log::info('支付成功', ['charge' => $charge->toArray(), 'result' => $result]);
        } catch (\Exception $e) {
            Log::error('支付失败', ['code' => $e->getCode(), 'message' => $e->getMessage()]);
            throw new \Exception('支付失败');
        }

        return $charge;
    }
}
