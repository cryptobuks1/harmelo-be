<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use App\Models\BookingRevenue;
use App\Models\Events;
use App\Models\Payments;
use App\Models\UserPoints;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PaymentsController extends Controller
{
    //Constants
    private $service_charge = 15;
    private $reward_percentage = .2;
    private $markup = 0.15;
    private $paymongo_public_key;
    private $paymongo_secret_key;

    public function __construct()
    {
        $this->paymongo_public_key = config('customvar.paymongo_public_key');
        $this->paymongo_secret_key = config('customvar.paymongo_secret_key');
        /**
         * Exclude functions from Token authentication -> except => ['function1', 'function2', ...]
         */
        $this->middleware('auth:api', ['except' => []]);
    }

    public function addRevenue(Request $request) {
        $exist = BookingRevenue::where('user_id', $request->user_id)->first(); //number_format("15050",2,".","");
        //$net_revenue = strval( ((number_format($request->revenue) - $this->service_charge) - ((number_format($request->revenue) - $this->service_charge) * $this->markup)) ); //(price - paymentfee) - ((price - paymentfee) * markup) = total revenue
        $net_revenue = strval($request->revenue);
        if (!$exist) {
            $revenue = new BookingRevenue();
            $revenue->user_id = $request->user_id;
            $revenue->total_revenue = strval(number_format($net_revenue,2,'.',''));
            $revenue->created_at = Carbon::now();
            $revenue->updated_at = Carbon::now();
            $save = $revenue->save();
            if ($save) {
                return response()->json([
                    'msg' => 'ok',
                    'status' => 1,
                    'data' => $save
                ]);
            }
        } else {
            $update = BookingRevenue::where('user_id', $request->user_id)
                ->update([
                    'total_revenue' => strval(number_format($exist->total_revenue,2,'.','') + number_format($net_revenue,2,'.','')),
                    'updated_at' => Carbon::now()
                ]);

            if ($update) {
                return response()->json([
                    'msg' => 'ok',
                    'status' => 1,
                    'data' => BookingRevenue::where('user_id', $request->user_id)->first()
                ]);
            }
        }
    }

    private function getSelectedEvent($evt_id) {
        $evt = Events::where('id', $evt_id)->first();

        return $evt;
    }

    public function getEventByIDOnly(Request $request) {
        $evt = Events::where('id', $request->id)->first();

        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => $evt
        ]);
    }

    /**
     * GCASH
     */
    public function createSource(Request $request) {
        $curl_postfields = json_encode([
            'data' => [
                'attributes' => [
                    'type' => $request->type,
                    'amount' => intval($request->total_amount),
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

    public function insertbanktransaction(Request $request)
    {

        $reward = ( ( floatval(substr($request->amount, 0, (strlen($request->amount)-2))) - 15 ) * $this->reward_percentage );


        $bank = new Payments();
        $bank->recepient_id = $request->recepient_id;
        $bank->payor_id = $request->payor_id;
        $bank->product_id = $request->product_id;
        $bank->amount = $request->amount; //check for paymongo amount format
        $bank->source_id = $request->source_id;
        $bank->payment_method = $request->payment_method;
        $bank->payment_status = $request->payment_status;
        $bank->payment_reward = $reward;
        $bank->description = $request->description;
        $bank->created_at = Carbon::now();
        $bank->updated_at = Carbon::now();
        $response = $bank->save();

        if ($response) {
            return response()->json([
                'msg' => 'ok',
                'status' => 1,
                'data' => $response
            ]);
        }
        return response()->json([
            'msg' => 'bad',
            'status' => 0,
            'data' => []
        ]);
    }

    public function retrieveSource(Request $request) {

        $source_id = Payments::where('source_id', $request->source_id)
            ->where('payor_id', $request->payor_id)
            ->where('payment_status', 'pending')
            ->orderBy('id', 'DESC')
            ->first();

        if (!$source_id) {
            return response()->json([
                'msg' => 'No source created',
                'status' => 0,
                'data' => [],
                'evt_data' => []
            ]);
        }

        $curl = curl_init();

        curl_setopt_array($curl, [
        CURLOPT_URL => 'https://api.paymongo.com/v1/sources/' . $source_id->source_id,
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
                'data' => [],
                'evt_data' => []
            ]);
        }

        $update = Payments::where('id', $source_id->id)
            ->update([
                'payment_status' => 'chargeable',
                'updated_at' => Carbon::now()
            ]);

        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => json_decode($response),
            'evt_data' => $this->getSelectedEvent($source_id->product_id)
        ]);
    }

    public function createPayment(Request $request)
    {
        $curl_postfields = json_encode([
            'data' => [
                'attributes' => [
                    'amount' => intval($request->amount),
                    'currency' => 'PHP',
                    'statement_descriptor' => 'Thank you for trusting Harmelo! Enjoy our services!',
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
            $update = Payments::where('source_id', $request->source_id)
            ->where('payor_id', $request->payor_id)
            ->where('payment_status', 'chargeable')
            ->update([
                'payment_status' => 'completed',
                'updated_at' => Carbon::now()
            ]);

            $equiv_pts = Payments::where('source_id', $request->source_id)
            ->where('payor_id', $request->payor_id)->first();

            if ($update) {
                return $this->insertorupdateuserpoints($request->payor_id, $equiv_pts->payment_reward);
            }
        }
    }

    private function insertorupdateuserpoints($user_id, $to_add_points) {
        $curpoints = '';
        $user = UserPoints::where('user_id', $user_id)->get(); //update if user already has points, insert if none

        if (UserPoints::where('user_id', $user_id)->exists()) {
            $update = UserPoints::where('user_id', $user_id)
                ->update([
                    'points' => strval(intval($user[0]->points) + intval($to_add_points)),
                    'updated_at' => Carbon::now()
                ]);

            if ($update) {
                return response()->json([
                    'msg' => 'ok',
                    'status' => 1,
                    'data' => UserPoints::where('user_id', $user_id)->first()
                ]);
            }
        } else {
            $insert = new UserPoints();
            $insert->user_id = $user_id;
            $insert->points = strval(intval($to_add_points));
            $insert->created_at = Carbon::now();
            $insert->updated_at = Carbon::now();

            if ($insert->save()) {
                return response()->json([
                    'msg' => 'ok',
                    'status' => 1,
                    'data' => UserPoints::where('user_id', $user_id)->first()
                ]);
            }
        }
    }

    public function cancelPayment(Request $request) {
        $update = Payments::where('source_id', '=', $request->source_id)->update(['payment_status'=>'cancelled','updated_at'=>Carbon::now()]);

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

    /**
     * VISA/MASTERCARD
     */

    public function createPaymentIntent(Request $request) {
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

    public function createPaymentMethod(Request $request) {
        $curl_postfields = json_encode([
            'data' => [
                'attributes' => [
                    'type' => 'card',
                    'details' => [
                        'card_number' => $request->number,
                        'exp_month' => intval($request->expiry_month),
                        'exp_year' => intval($request->expiry_year),
                        'cvc' => $request->cvv,
                    ],
                    'name' => $request->name,
                    'email' => $request->email
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

    public function attachToPaymentIntent(Request $request) {
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

    public function insertOrUpdatePoints(Request $request) {
        $user = UserPoints::where('user_id', $request->payor_id)->first();

        $equiv_pts = Payments::where('source_id', $request->source_id)
            ->where('payor_id', $request->payor_id)->first();

        if ($user) {
            $update = UserPoints::where('user_id', $request->payor_id)
                ->update([
                    'points' => strval(intval($user->points) + intval($equiv_pts->payment_reward)),
                    'updated_at' => Carbon::now()
                ]);
            if ($update) {
                return response()->json([
                    'msg' => 'ok',
                    'status' => 1,
                    'data' => UserPoints::where('user_id', $request->payor_id)->first()
                ]);
            }
        } else {
            $insert = new UserPoints();
            $insert->user_id = $request->payor_id;
            $insert->points = intval($equiv_pts->payment_reward);
            $insert->created_at = Carbon::now();
            $insert->updated_at = Carbon::now();

            if ($insert->save()) {
                return response()->json([
                    'msg' => 'ok',
                    'status' => 1,
                    'data' => UserPoints::where('user_id', $request->payor_id)->first()
                ]);
            }
        }
    }

    public function retrievepaymentintent(Request $request) {
        $curl = curl_init();

        /*$intent = BankTransactionHistory::where('source_id', '=', $request->payment_intent_id)
            ->where('status', '=', 'completed')->get();*/

        $intent = Payments::where('source_id', '=', $request->source_id)
            ->where('payment_status', '=', 'pending')->first();
        if(!$intent) {
            return response()->json([
                'msg' => 'invalid_request',
                'status' => 0,
                'data' => [],
                'evt_data' => []
            ]);
        }

        curl_setopt_array($curl, [
        CURLOPT_URL => 'https://api.paymongo.com/v1/payment_intents/'. $request->source_id . '?client_key=' . $request->client_key,
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
                'data' => [],
                'evt_data' => []
            ]);
        } else {
            return response()->json([
                'msg' => 'ok',
                'status' => 1,
                'data' => json_decode($result),
                'evt_data' => $this->getSelectedEvent($intent->product_id)
            ]);
        }
    }

    public function updatePaymentStatus(Request $request) {
        $up = Payments::where('source_id', '=', $request->source_id)
            ->update(['payment_status'=>$request->status,'updated_at'=>Carbon::now()]);
        if ($up) {
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
}
