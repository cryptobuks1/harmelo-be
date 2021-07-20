<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class RegisterVerification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $receiver;
    protected $receiver_email;
    protected $verification_link;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($r, $e, $v)
    {
        $this->receiver = $r;
        $this->receiver_email = $e;
        $this->verification_link = $v;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $rec = $this->receiver;
        $rec_em = $this->receiver_email;
        $ver = $this->verification_link;

        Mail::send('emails.register-verification',
        [
            'receiver'=>$rec,
            'verification_link'=>$ver
        ],
        function($message) use($rec_em, $rec) {
            $message->to($rec_em, $rec)->subject('Welcome to Harmelo');
            $message->from('elbert.softwaredev@gmail.com','Harmelo');
        });
    }
}
