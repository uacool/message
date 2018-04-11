<?php
namespace Message\Email;

use Message\Email\PHPMailer\PHPMailer;

class Email
{
    protected $params;
    protected $config;

    public function __construct($params, $config)
    {
        $this->config = $config['msg'];
        $this->params = $params;
    }

    public function send()
    {
        $mail = new PHPMailer;
        $mail->isSMTP();                                                        // Set mailer to use SMTP
        $mail->Host = $this->config['Email']['host'];                           // Specify main and backup SMTP servers
        $mail->SMTPAuth = $this->config['Email']['smtpAuth'];                   // Enable SMTP authentication
        $mail->Username = $this->config['Email']['username'];                   // SMTP username
        $mail->Password = $this->config['Email']['password'];                   // SMTP password
        $mail->CharSet = $this->config['Email']['charset'];                     // SMTP charset
        $mail->setFrom($this->config['Email']['username'], $this->config['Email']['platform']);
        $mail->addReplyTo($this->config['Email']['username'], $this->config['Email']['platform']);
        $mail->isHTML($this->config['Email']['isHtml']);                        // Set email format to HTML
        $mail->Subject = $this->params['msgContent']['subject'];
        $mail->Body = $this->params['msgContent']['body'];
        $receivers = $this->params['receivers'];
        if (empty($receivers)) {
            throw new \Exception('Please set the mail receivers');
        }
        foreach ($receivers as $receiver) {
            $mail->addAddress($receiver);
        }
        if (!$mail->send()) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
            return false;
        } else {
            echo 'Message has been sent';
            return true;
        }
    }

}


