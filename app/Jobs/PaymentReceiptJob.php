<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class PaymentReceiptJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $recepientName;
    protected $recepientMail;
    protected $transactionDate;
    protected $productPrice;
    protected $productName;
    protected $productQty;
    protected $totalAmt;
    protected $serviceFee;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($recepient, $recepientmail, $transactiondate, $pprice, $pname, $pqty, $totalamt, $servicefee)
    {
        $this->recepientName = $recepient;
        $this->recepientMail = $recepientmail;
        $this->transactionDate = $transactiondate;
        $this->productPrice = $pprice;
        $this->productName = $pname;
        $this->productQty = $pqty;
        $this->totalAmt = $totalamt;
        $this->serviceFee = $servicefee;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $receipent =  $this->recepientName;
        $receipentmail =  $this->recepientMail;
        $date = $this->transactionDate;
        $price =  $this->productPrice;
        $product = $this->productName;
        $qty = $this->productQty;
        $total = $this->totalAmt;
        $service_fee = $this->serviceFee;

        Mail::send('emails.paymentreceipt',
        [
            'receiver'=>$receipent,
            'date'=>$date,
            'price'=>$price,
            'product'=>$product,
            'qty'=>$qty,
            'total'=>$total,
            'service_fee'=>$service_fee
        ],
        function($message) use($receipentmail, $receipent) {
            $message->to($receipentmail, $receipent)->subject('Purchase Receipt');
            $message->from('elbert.softwaredev@gmail.com','Harmelo Music');
        });
    }
}
