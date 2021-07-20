<?php

namespace App\Http\Controllers\V2\paymongo;

use App\Http\Controllers\Controller;
use App\Models\BankTransactionHistory;
use App\Models\UserPoints;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GCashController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */

    private $service_charge = 15;
    private $reward_percentage = .01;
    private $paymongo_public_key = 'pk_test_xNvQf5gVKzNFFbH5xQn9tKNq:'; //added ":" to the key
    private $paymongo_secret_key = 'sk_test_iuvgw8U3m6FfrRqeZN3xXAZH:'; //added ":" to the key

    public function __construct()
    {
        /**
         * Exclude functions from Token authentication -> except => ['function1', 'function2', ...]
         */
        $this->middleware('auth:api', ['except' => []]);
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

    private function insertorupdateuserpoints($user_id, $to_add_points) {
        $curpoints = '';
        $user = UserPoints::where('user_id', $user_id)->get(); //update if user already has points, insert if none

        if (UserPoints::where('user_id', $user_id)->exists()) {
            $update = UserPoints::where('user_id', $user_id)
                ->update([
                    'points' => strval(intval($user[0]->points) + (intval($to_add_points) - $this->service_charge)),
                    'rewards' => strval(intval(intval($user[0]->points) + (intval($to_add_points) - $this->service_charge)) * $this->reward_percentage),
                    'updated_at' => Carbon::now()
                ]);

            if ($update) {
                return response()->json([
                    'msg' => 'ok',
                    'status' => 1,
                    'data' => UserPoints::where('user_id', $user_id)->get()->toArray()
                ]);
            }
        } else {
            $insert = new UserPoints();
            $insert->user_id = $user_id;
            $insert->points = strval(intval($to_add_points) - $this->service_charge);
            $insert->rewards = strval(intval(intval($to_add_points) - $this->service_charge) * $this->reward_percentage);
            $insert->created_at = Carbon::now();
            $insert->updated_at = Carbon::now();

            if ($insert->save()) {
                return response()->json([
                    'msg' => 'ok',
                    'status' => 1,
                    'data' => UserPoints::where('user_id', $user_id)->get()->toArray()
                ]);
            }
        }
    }

    //CREATE PAYMENT
    public function creategcashpayment(Request $request)
    {
        $curl_postfields = json_encode([
            'data' => [
                'attributes' => [
                    'amount' => intval($request->amount),
                    'currency' => 'PHP',
                    'statement_descriptor' => 'Thank you for trusting Harmelo! Use your points and enjoy our services!',
                    'source' => [
                        'id' => $request->source_id,
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
        } else {
            $update = BankTransactionHistory::where('source_id', $request->source_id)
            ->where('user_id', $request->user_id)
            ->update([
                'status' => 'completed',
                'updated_at' => Carbon::now()
            ]);

            if ($update) {
                return $this->insertorupdateuserpoints($request->user_id, $request->to_add_points); //Service charge not deducted
            }
        }
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
                        'success' => config('customvar.gcash_redirect_success'),
                        'failed' => config('customvar.gcash__redirect_fail')
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

    public function cancelpayment(Request $request) {
        $update = BankTransactionHistory::where('source_id', '=', $request->source_id)->update(['status'=>'cancelled','updated_at'=>Carbon::now()]);

        if ($update) {
            return response()->json([
                'msg' => 'ok',
                'status' => 1,
                'data' => []
            ]);
        } 
        return response()->json([
            'msg' => 'bad',
            'status' => 0,
            'data' => []
        ]);
    } 

    //RETRIEVE A SOURCE
    public function retrievegcashsource(Request $request) {

        $source_id = BankTransactionHistory::select('source_id', )
            ->where('user_id', $request->user_id)
            ->where('status', 'pending')
            ->orderBy('id', 'DESC')
            ->take(1)
            ->get();

        if ($source_id->count() < 1) {
            return response()->json([
                'msg' => 'No source created',
                'status' => 0,
                'data' => []
            ]);
        }

        $curl = curl_init();

        curl_setopt_array($curl, [
        CURLOPT_URL => 'https://api.paymongo.com/v1/sources/' . $source_id[0]->source_id,
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

        $update = BankTransactionHistory::where('source_id', $source_id[0]->source_id)
            ->where('user_id', $request->user_id)
            ->update([
                'status' => 'chargeable',
                'updated_at' => Carbon::now()
            ]);

        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => json_decode($response)
        ]);
    }
}
