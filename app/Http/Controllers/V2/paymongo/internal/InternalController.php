<?php

namespace App\Http\Controllers\V2\paymongo\internal;

use App\Http\Controllers\Controller;
use App\Models\BankTransactionHistory;
use App\Models\UserPoints;
use Carbon\Carbon;
use Illuminate\Http\Request;

class InternalController extends Controller
{
    private $service_charge = 15;
    private $reward_percentage = .01;
    private $paymongo_public_key = 'pk_test_xNvQf5gVKzNFFbH5xQn9tKNq:'; //added ":" to the key
    private $paymongo_secret_key = 'sk_test_iuvgw8U3m6FfrRqeZN3xXAZH:'; //added ":" to the key

    public function __construct()
    {
        /**
         * Exclude functions from Token authentication -> except => ['function1', 'function2', ...]
         */
        $this->middleware('auth:api', ['except' => ['updateuserbanktransactionstatus']]);
    }

    public function getusertransactionall(Request $request) {
        $transaction = BankTransactionHistory::where('user_id', $request->u)
            ->where('status', 'completed')
            ->orderBy('id', 'DESC')
            ->paginate(5);

        $transaction->makeHidden(['source_id']);

        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => $transaction
        ]);
    }

    public function insertorupdateuserpoints(Request $request) {
        $curpoints = '';
        $user_id = $request->user_id;
        $to_add_points = $request->to_add_point;
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

    /*public function insertorupdateuserpoints(Request $request) {
        $curpoints = '';
        $user_id = $request->user_id;
        $user = UserPoints::where('user_id', $user_id)->get();
        $to_add_points = $request->points;

        if (UserPoints::where('id', $user_id)->exists()) {
            $update = UserPoints::where('user_id', $user_id)
                ->update([
                    'points' => strval(intval($user[0]->points) + intval($to_add_points)),
                    'updated_at' => Carbon::now()
                ]);

            if ($update) {
                return response()->json([
                    'msg' => 'ok',
                    'status' => 1,
                    'data' => UserPoints::where('user_id', $user_id)
                ]);
            }
        } else {
            $insert = new UserPoints();
            $insert->user_id = $user_id;
            $insert->points = $to_add_points;
            $insert->created_at = Carbon::now();
            $insert->updated_at = Carbon::now();

            if ($insert) {
                return response()->json([
                    'msg' => 'ok',
                    'status' => 1,
                    'data' => UserPoints::where('user_id', $user_id)
                ]);
            }
        }
    }*/

    private function retrievegcashsource($source_id) {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.paymongo.com/v1/sources/' . $source_id,
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
            return "cURL Error #:" . $err;
        } else {
            return $response;
        }
    }

    public function updateuserbanktransactionstatus(Request $request) {
        $source_id = BankTransactionHistory::select('source_id', )
            ->where('user_id', $request->user_id)
            ->where('status', 'pending')
            ->orderBy('id', 'DESC')
            ->take(1)
            ->get();

        $source_details = json_decode($this->retrievegcashsource($source_id[0]->source_id));

        $update = BankTransactionHistory::where('source_id', $source_id)
            ->where('user_id', $request->user_id)
            ->update([
                'status' => 'pending',
                'updated_at' => Carbon::now()
            ]);

        if ($update) {
            return response()->json([
                'msg' => 'ok',
                'status' => 1,
                'data' => $source_details
            ]);
        }

    }

    public function updatecardpaymentstatus(Request $request) {
        $up = BankTransactionHistory::where('source_id', '=', $request->source_id)
            ->update(['status'=>$request->status,'updated_at'=>Carbon::now()]);
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

    public function insertbanktransaction(Request $request)
    {

        $reward = number_format(( ( floatval(substr($request->amount, 0, (strlen($request->amount)-2))) - 15 ) * $this->reward_percentage ), 2);

        $bank = new BankTransactionHistory();
        $bank->user_id = $request->user_id;
        $bank->source_id = $request->source_id;
        $bank->amount = $request->amount; //check for paymongo amount format
        $bank->status = $request->status;
        $bank->bank = $request->bank;
        $bank->transaction_type = $request->transaction_type;
        $bank->reward_equivalent = $reward;
        $bank->product = $request->product;
        $bank->ddate = $request->ddate;
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
}
