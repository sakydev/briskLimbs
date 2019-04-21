<?php

global $limbs, $users;

if (isset($_POST['contact-form'])) {
	unset($_POST['contact-form']);
	$form = $_POST;
	foreach ($form as $field => $value) {
		if (empty($value)) {
			$parameters['_errors'][] = "{$field} is required";
			$parameters['response'] = $_POST;
			break;
		}

		$$field = $value;
	}

	$mail = new Mail();
	$message = "Following is message by {$name} ($email) from {$country} sent using contact form at your website. <br>{$message}";
	if ($mail->contact($email, $subject, $message)) {
		$parameters['message'] = 'Message has been sent successfully';
	} else {
		$parameters['_errors'][] = 'Failed to send message';
	}
}

if (empty($limbs->settings->get('mailer_smpt_username')) && $users->isAdmin()) {
	$parameters['_errors'][] = "This an admin only message. It looks like emails are not configured properly. Please make sure SMTP is setup before receiving messages via contact form.";
}

$parameters['_title'] = 'Contact';
$limbs->display('contact.html', $parameters);
