<?php
/**
 * Created by PhpStorm.
 * User: daemon
 * Date: 15.10.15
 * Time: 23:06
 */

// ��������� ����� �� ������ ���������� PHPMailer

class BaraholkaMailer {

    // �������� ���� ����� Smtp
    // $connect_data - ������ ��� ��������� smtp ���������� (������ ����-��������)
    // $maildata - ������ ������
    //          $maildata['from_mail'] - ����� ����� ������
    //          $maildata['from_name'] - ��� �� ���� (�������� ����� ������� �����)
    //          $maildata['mailto'] - ����� ����� ����������
    //          $maildata['nameto'] - ��� ���� (�������� ����� ������� �����)
    //          $maildata['html_tag'] - ���� ������ � ������� HTML
    //          $maildata['subject'] - ���� ������
    //          $maildata['message'] - ���� ������
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