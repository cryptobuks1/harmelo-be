<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Luigel\Paymongo\Facades\Paymongo;

class PayMongoController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */

    private $paymongo_public_key = 'pk_test_xNvQf5gVKzNFFbH5xQn9tKNq:'; //added ":" to the key
    private $paymongo_secret_key = 'sk_test_iuvgw8U3m6FfrRqeZN3xXAZH:'; //added ":" to the key

    public function __construct()
    {
        /**
         * Exclude functions from Token authentication -> except => ['function1', 'function2', ...]
         */
        $this->middleware('auth:api', ['except' => ['creategcashsource', 'retrievegcashsource']]);
    }

    /**
     * E-WALLETS PAYMENT PROCESS
     * 1. CREATE SOURCE RESOURCE - PASS PUBLIC API KEY AS HEADER
     * 2. RETRIEVE SOURCE ID
     * 3. CREATE PAYMENT = PASS SECRET API KEY, USE THE RETRIEVED SOURCE ID
     * 4. LIST OF ALL PAYMENTS - OPTIONAL
     * 5. RETRIEVE A PAYMENT - OPTIONAL
     *
     * Check https://developers.paymongo.com/reference#list-all-payments for documentation
     */

    //CREATE PAYMENT
    public function creategcashpayment(Request $request)
    {
        $curl_postfields = json_encode([
            'data' => [
                'attributes' => [
                    'amount' => intval($request->total_amount . '00'),
                    'description' => $request->notes,
                    'currency' => 'PHP',
                    'statement_descriptor' => 'Thank you for trusting Harmelo! Use your points and enjoy our services!',
                    'source' => [
                        'id' => $request->transaction_source,
                        'type' => 'source',
                    ]
                ]
            ]
        ]);

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, 'https://api.paymongo.com/v1/payments');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_postfields);
        curl_setopt($curl, CURLOPT_POST, 1);

        $headers = array();
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
        }

        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => json_decode($result)
        ]);

        curl_setopt_array($curl, [
        CURLOPT_URL => "https://api.paymongo.com/v1/payments",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_HTTPHEADER => [
            "Accept: application/json",
            "Content-Type: application/json"
        ],
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);

        curl_close($curl);

        if ($error) {
            return response()->json([
                'msg' => $error,
                'status' => 0,
                'data' => []
            ]);
        }

        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => json_decode($result)
        ]);
    }

    //CREATE SOURCE RESOURCE
    public function creategcashsource(Request $request) {

        $curl_postfields = json_encode([
            'data' => [
                'attributes' => [
                    'type' => 'gcash',
                    'amount' => intval($request->total_amount . '00'),
                    'currency' => 'PHP',
                    'redirect' => [
                        'success' => 'http://localhost:8081/payment/success',
                        'failed' => 'http://localhost:8081/payment/failed',
                    ],
                    'billing' => [
                        'name' => $request->account_name,
                        'phone' => $request->account_number,
                        'email' => $request->email,
                    ]
                ]
            ]
        ]);
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api.paymongo.com/v1/sources');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curl_postfields);
        curl_setopt($ch, CURLOPT_POST, 1);

        $headers = array();
        $headers[] = 'Authorization: Basic ' . base64_encode($this->paymongo_public_key); 
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        $error = curl_error($ch);

        curl_close ($ch);

        if ($error) {
            return response()->json([
                'msg' => $error,
                'status' => 0,
                'data' => []
            ]);
        }

        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => json_decode($result)
        ]);
    }

    //RETRIEVE A SOURCE
    public function retrievegcashsource(Request $request) {

        $curl = curl_init();

        curl_setopt_array($curl, [
        CURLOPT_URL => 'https://api.paymongo.com/v1/sources/' . $request->source_id,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "Accept: application/json",
            "Authorization: Basic cGtfdGVzdF94TnZRZjVnVkt6TkZGYkg1eFFuOXRLTnE6"
        ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return response()->json([
                'msg' => $err,
                'status' => 0,
                'data' => []
            ]);
        }
        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => $response
        ]);
    }
}
