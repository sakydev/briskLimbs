<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\OAuth;
use League\OAuth2\Client\Provider\Google;

class Mail {
  
  function __construct() {
    $this->settings = new Settings();
    $this->errors = new Errors();

    $this->mailer = new PHPMailer();
    $this->mailer->isSMTP();
    $this->mailer->SMTPAuth = true;
    $this->mailer->CharSet = 'utf-8';
  }

  public function send($recipientMail, $recipientName, $subject, $message) {
		$provider = new Google('clientId' => $clientId, 'clientSecret' => $clientSecret,]);
		$this->mailer->setOAuth(
		    new OAuth([
          'provider' => $provider,
          'clientId' => $clientId,
          'clientSecret' => $clientSecret,
          'refreshToken' => $refreshToken,
          'userName' => $email,
	        ]
		    )
		);

		$this->mailer->setFrom($this->settings->get('mailer_sender_email'), $this->settings->get('mailer_sender_name'));
		$this->mailer->addAddress($recipientMail, $recipientName);
		$this->mailer->Subject = $subject;
		$this->mailer->msgHTML($message);
		//send the message, check for errors
		if ($this->mailer->send()) {
		  return true;
		} else {
		  $this->errors->add($this->mailer->ErrorInfo);
		}
  }
}