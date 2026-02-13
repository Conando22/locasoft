<?php
  /**
  * Requires the "PHP Email Form" library
  * The "PHP Email Form" library is available only in the pro version of the template
  * The library should be uploaded to: vendor/php-email-form/php-email-form.php
  * For more info and help: https://bootstrapmade.com/php-email-form/
  */

  // Replace contact@example.com with your real receiving email address
  $receiving_email_address = 'geral@locasoft.pt';

  header('Content-Type: application/json; charset=utf-8');

  // Simple anti-spam honeypot: form should send 'hp' empty
  $honeypot = isset($_POST['hp']) ? trim($_POST['hp']) : '';
  if (!empty($honeypot)) {
    // Likely a bot
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Spam detected']);
    exit;
  }

  if( file_exists($php_email_form = '../assets/vendor/php-email-form/php-email-form.php' )) {
    include( $php_email_form );
  } else {
    die( 'Unable to load the "PHP Email Form" Library!');
  }

  $contact = new PHP_Email_Form;
  $contact->ajax = true;

  // Basic sanitization and validation
  $name = isset($_POST['name']) ? strip_tags(trim($_POST['name'])) : '';
  $email = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL) : false;
  $subject = isset($_POST['subject']) ? strip_tags(trim($_POST['subject'])) : 'Contacto via website';
  $message = isset($_POST['message']) ? strip_tags(trim($_POST['message'])) : '';

  if (empty($name) || !$email || empty($message)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Por favor preencha os campos obrigatórios corretamente.']);
    exit;
  }

  $contact->to = $receiving_email_address;
  $contact->from_name = $name;
  $contact->from_email = $email;
  $contact->subject = $subject;

  // Uncomment below code if you want to use SMTP to send emails. You need to enter your correct SMTP credentials
  /*
  $contact->smtp = array(
    'host' => 'example.com',
    'username' => 'example',
    'password' => 'pass',
    'port' => '587'
  );
  */

  $contact->add_message( $name, 'From');
  $contact->add_message( $email, 'Email');
  if (!empty($_POST['phone'])) { $contact->add_message( strip_tags($_POST['phone']), 'Phone'); }
  $contact->add_message( $message, 'Message', 10);

  // Optionally: implement reCAPTCHA verification here before sending

  $send_result = $contact->send();
  if ($send_result) {
    echo json_encode(['status' => 'success', 'message' => 'Mensagem enviada com sucesso.']);
  } else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Erro ao enviar a mensagem.']);
  }
?>
