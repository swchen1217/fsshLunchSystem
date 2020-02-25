<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Verify extends Mailable
{
    use Queueable, SerializesModels;
    public $date;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($date)
    {
        $this->date = $date;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('lunch.fios@gmail.com', 'FIOS')
            ->view('emails.user_verify')
            ->subject('【鳳山高中午餐系統】帳號活動異常-使用者驗證')
            ->with(['verify_code' => $this->date['verify_code']]);
    }
}
