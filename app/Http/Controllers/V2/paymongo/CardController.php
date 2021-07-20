<?php

namespace App\Http\Controllers\V2\paymongo;

use App\Http\Controllers\Controller;
use App\Models\BankTransactionHistory;
use Illuminate\Http\Request;

class CardController extends Controller
{
    private $paymongo_public_key = 'pk_test_xNvQf5gVKzNFFbH5xQn9tKNq:'; //added ":" to the key
    private $paymongo_secret_key = 'sk_test_iuvgw8U3m6FfrRqeZN3xXAZH:'; //added ":" to the key

    public function __construct()
    {
        /**
         * Exclude functions from Token authentication -> except => ['function1', 'function2', ...]
         */
        $this->middleware('auth:api', ['except' => ['', '', '', '']]);
    }

    public function retrievepaymentintent(Request $request) {
        $curl = curl_init();

        /*$intent = BankTransactionHistory::where('source_id', '=', $request->payment_intent_id)
            ->where('status', '=', 'completed')->get();*/

        $intent = BankTransactionHistory::where('source_id', '=', $request->payment_intent_id)
            ->where('status', '=', 'pending')->get();
        if($intent->count() < 1) {
            return response()->json([
                'msg' => 'invalid_request',
                'status' => 0,
                'data' => []
            ]);
        }

        curl_setopt_array($curl, [
        CURLOPT_URL => 'https://api.paymongo.com/v1/payment_intents/'. $request->payment_intent_id . '?client_key=' . $request->client_key,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "Accept: application/json",
            'Authorization: Basic ' . base64_encode($this->paymongo_public_key)
        ],
        ]);

        $result = curl_exec($curl);
        $error = curl_error($curl);

        curl_close($curl);


        if ($error) {
            return response()->json([
                'msg' => $error,
                'status' => 0,
                'data' => []
            ]);
        } else {
            return response()->json([
                'msg' => 'ok',
                'status' => 1,
                'data' => json_decode($result)
            ]);
        }
    }

    public function attachtopaymentintent(Request $request) {
        $curl_postfields = json_encode([
            'data' => [
                'attributes' => [
                    'payment_method' => $request->payment_method_id,
                    'client_key' => $request->client_key,
                    'return_url' => $request->return_url
                ]
            ]
        ]);

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, 'https://api.paymongo.com/v1/payment_intents/' . $request->payment_intent_id . '/attach');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_postfields);
        curl_setopt($curl, CURLOPT_POST, 1);

        $headers = array();
        $headers[] = 'Accept: application/json';
        $headers[] = 'Authorization: Basic ' . base64_encode($this->paymongo_public_key); 
        $headers[] = 'Content-Type: application/json';
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($curl);
        $error = curl_error($curl);

        curl_close ($curl);

        if ($error) {
            return response()->json([
                'msg' => $error,
                'status' => 0,
                'data' => []
            ]);
        } else {
            return response()->json([
                'msg' => $error,
                'status' => 1,
                'data' => json_decode($result)
            ]);
        }
    }

    public function createpaymentmethod(Request $request) {
        $curl_postfields = json_encode([
            'data' => [
                'attributes' => [
                    'type' => 'card',
                    'details' => [
                        'card_number' => $request->card_number,
                        'exp_month' => intval(explode(' / ', $request->expiry)[0]),
                        'exp_year' => intval(explode(' / ', $request->expiry)[1]),
                        'cvc' => $request->cvc,
                    ],
                    'name' => $request->account_name,
                    'email' => $request->email,
                    'phone' => $request->phone
                ]
            ]
        ]);

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, 'https://api.paymongo.com/v1/payment_methods');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_postfields);
        curl_setopt($curl, CURLOPT_POST, 1);

        $headers = array();
        $headers[] = 'Accept: application/json';
        $headers[] = 'Authorization: Basic ' . base64_encode($this->paymongo_public_key); 
        $headers[] = 'Content-Type: application/json';
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($curl);
        $error = curl_error($curl);

        curl_close ($curl);

        if ($error) {
            return response()->json([
                'msg' => $error,
                'status' => 0,
                'data' => []
            ]);
        } else {
            return response()->json([
                'msg' => $error,
                'status' => 1,
                'data' => json_decode($result)
            ]);
        }
    }

    public function createpaymentintent(Request $request) {
        $curl_postfields = json_encode([
            'data' => [
                'attributes' => [
                    'amount' => intval($request->amount),
                    'payment_method_allowed' => ['card'],
                    'payment_method_options' => [
                        'card' => [
                            'request_three_d_secure' => 'any'
                        ]
                    ],
                    'statement_descriptor' => 'Harmelo',
                    'currency' => 'PHP'
                ]
            ]
        ]);

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, 'https://api.paymongo.com/v1/payment_intents');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_postfields);
        curl_setopt($curl, CURLOPT_POST, 1);

        $headers = array();
        $headers[] = 'Accept: application/json';
        $headers[] = 'Authorization: Basic ' . base64_encode($this->paymongo_secret_key); 
        $headers[] = 'Content-Type: application/json';
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($curl);
        $error = curl_error($curl);

        curl_close ($curl);

        if ($error) {
            return response()->json([
                'msg' => $error,
                'status' => 0,
                'data' => []
            ]);
        } else {
            return response()->json([
                'msg' => $error,
                'status' => 1,
                'data' => json_decode($result)
            ]);
        }

    }
}
