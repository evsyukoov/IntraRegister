<?php

require_once("PHPMailer/src/PHPMailer.php");
require_once("PHPMailer/src/Exception.php");
require_once("PHPMailer/src/OAuth.php");
require_once("PHPMailer/src/POP3.php");
require_once("PHPMailer/src/SMTP.php");

use PHPMailer\PHPMailer\PHPMailer;

class MailSender
{
    private $email;
    private $hash;

    /**
     * MailSender constructor.
     * @param $email
     * @param $hash
     */
    public function __construct($email, $hash)
    {
        $this->email = $email;
        $this->hash = $hash;
    }


    public function  sendEmail()
    {
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = "smtp.mail.ru";
        $mail->CharSet = "utf-8";
        $mail->SMTPAuth = true;
        $mail->Username = "";
        $mail->Password = "";
        $mail->Port = 465;
        $mail->SMTPSecure = "ssl";
        $mail->Subject = "exams APP";
        $mail->isHTML(true);
        $mail->setFrom("", "ccarl's_App");
        $mail->addAddress("evsyukoov@mail.ru");
        // Сообщение для Email
        $message  = '
                <html>
                <head>
                <title>Submit Your Email address</title>
                </head>
                <body>
                <p>To submit go on <a href="http://localhost:63342/IntraAPI/website/backend/confirmed.php?hash=' . $this->hash . '">link</a></p>
                </body>
                </html> ';
        $mail->Body = $message;
        try {
            if ($mail->send() == true)
                echo "Письмо с просьбой подтвердить email ушло на вашу почту";
        }
        catch (Exception $e) {
            echo "Что-то пошло не так в отправке сообщения";
        }
    }

}