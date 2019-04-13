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
    $this->mailer->CharSet = 'utf-8';
    $this->mailer->Host = $this->settings->get('mailer_host');
    $this->mailer->Port = $this->settings->get('mailer_port');
    $this->mailer->SMTPSecure = $this->settings->get('mailer_smtp_secure');
    $this->mailer->SMTPAuth = true;
    $this->mailer->AuthType = $this->settings->get('mailer_authtype');
    $this->mailer->SMTPDebug = false; // use 1 or 2 when testing
  }

  public function send($subject, $message, $recipientMail, $recipientName) {
  	$clientId = $this->settings->get('mailer_clientid');
  	$clientSecret = $this->settings->get('mailer_clientsecret');

		$provider = new Google([
			'clientId' => $clientId, 
			'clientSecret' => $clientSecret,
		]);
		$this->mailer->setOAuth(
		    new OAuth([
          'provider' => $provider,
          'clientId' => $clientId,
          'clientSecret' => $clientSecret,
          'refreshToken' => $this->settings->get('mailer_refreshtoken'),
          'userName' => $this->settings->get('mailer_sender_email'),
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