<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SendApplicationNonMemberJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userName;
    protected $userMail;
    protected $userID;
    protected $fileName;
    protected $fileAttachment;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($name, $mail, $filename, $attachment)
    {
        $this->userName = $name;
        $this->userMail = $mail;
        $this->fileName = $filename;
        $this->fileAttachment = $attachment;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user_name = $this->userName;
        $user_mail = $this->userMail;
        $file_name = $this->fileName;
        $file_attachment = $this->fileAttachment;

        Storage::disk('s3')->put('public/attachment/applications/' . $file_name,
            file_get_contents($file_attachment), 'public'
        );

        Mail::send('emails.applicationv2',
        [
            'receiver'=>$user_name
        ],
        function($message) use($user_mail, $user_name) {
            $message->to($user_mail, $user_name)->subject('Harmelo OnBoarding');
            $message->from('elbert.softwaredev@gmail.com','Harmelo MusicED');
        });
    }
}
