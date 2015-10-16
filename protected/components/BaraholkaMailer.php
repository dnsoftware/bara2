<?php
/**
 * Created by PhpStorm.
 * User: daemon
 * Date: 15.10.15
 * Time: 23:06
 */

// ќтправщик писем на основе библиотеки PHPMailer

class BaraholkaMailer {

    // ќтправка мыла через Smtp
    // $connect_data - данные дл€ установки smtp соединени€ (массив ключ-значение)
    // $maildata - данные письма
    //          $maildata['from_mail'] - адрес почты откуда
    //          $maildata['from_name'] - им€ от кого (ставитс€ перед адресом почты)
    //          $maildata['mailto'] - адрес почты получател€
    //          $maildata['nameto'] - им€ кому (ставитс€ перед адресом почты)
    //          $maildata['html_tag'] - тело письма в формате HTML
    //          $maildata['subject'] - тема письма
    //          $maildata['message'] - тело письма
    public static function SendSmtpMail($connect_data, $maildata)
    {
        $mail = new PHPMailer;

        $mail->Host = $connect_data['host'];                         // Specify main and backup SMTP servers
        $mail->Username = $connect_data['username'];             // SMTP username
        $mail->Password = $connect_data['password'];                         // SMTP password

        //$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
        //$mail->Port = 587;                                    // TCP port to connect to

        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->SMTPAuth = true;                               // Enable SMTP authentication


        $mail->setFrom($connect_data['from_mail'], $connect_data['from_name']);

        $mail->addAddress($maildata['mailto'], $maildata['nameto']);     // Add a recipient
        $mail->isHTML($maildata['html_tag']);                                  // Set email format to HTML
        $mail->Subject = $maildata['subject'];
        $mail->Body    = $maildata['message'];


        if(!$mail->send()) {
            $result = 'Message could not be sent. ';
            $result .= 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            $result = 'ok';
        }

        return $result;

    }

} 