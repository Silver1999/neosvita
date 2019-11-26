<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendExcelMail extends Mailable
{
	use Queueable, SerializesModels;
	public $attachFile = '';

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($attachFile)
    {
        $this->attachFile = $attachFile;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
		return $this->from('admin@neosvita.com')
			->view('mails.sendExcel')
			->attach($this->attachFile, [
				'as' => 'ВІДОМІСТЬ.xlsx',
				'mime' => 'vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			]);
    }
}
