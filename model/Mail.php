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
    $this->mailer->Username = $this->settings->get('mailer_smpt_username');
    $this->mailer->Password = $this->settings->get('mailer_smpt_password');;
    $this->mailer->SMTPAuth = true;
    $this->mailer->SMTPDebug = false; // use 1 or 2 when testing
  }

  public function send($subject, $message, $recipientMail, $recipientName) {
		$this->mailer->setFrom($this->settings->get('mailer_sender_email'), $this->settings->get('mailer_sender_name'));
		$this->mailer->addAddress($recipientMail, $recipientName);
		$this->mailer->Subject = $subject;
		$this->mailer->msgHTML($message);
		return $this->mailer->send() ? true : $this->errors->add($this->mailer->ErrorInfo);
  }

  public function contact($email, $subject, $message) {
    $this->mailer->setFrom($email, $this->settings->get('title') . ' Contact Form');
    $this->mailer->addAddress($this->settings->get('mailer_sender_email'), $this->settings->get('mailer_sender_name'));
    $this->mailer->Subject = $subject;
    $this->mailer->msgHTML($message);
    return $this->mailer->send() ? true : $this->errors->add($this->mailer->ErrorInfo);
  }
}