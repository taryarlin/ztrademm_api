<?php

namespace App\Http\Controllers;

use App\Mail\SignupEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


class MailController extends Controller
{
    public static function sendSignupEmail($name, $email, $verification_code){
        $data =[
            'name' => $name,
            'verification_code' => $verification_code
        ]; 
        $this->sendEmail();
        // Mail::to($email)->send(new SignupEmail($data));
    }

    public function sendEmail()
    {
        error_log("this goes here");
        $htmlPage = $this->htmlPage("Hi");
        $mail = new PHPMailer();
        // configure an SMTP
        $mail->isSMTP();
        $mail->Host = 'ztrademm.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'service@ztrademm.com';
        $mail->Password = 'Qwertyuiop10!)';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;
        
        $mail->setFrom('noreply@ztrademm.com', 'Z Trade User Auth');
        $mail->addAddress('micaljohn60@gmail.com', 'user');
        $mail->Subject = 'Thanks for choosing Our Hotel!';
        // Set HTML
        $mail->isHTML(TRUE);
        $mail->Body = $htmlPage;
        // $mail->Body = '<html>Hi there, we are happy to <br>confirm your booking.</br> Please check the document in the attachment.</html>';
        $mail->AltBody = 'Hi there, we are happy to confirm your booking. Please check the document in the attachment.';
        // add attachment
       
        // send the message
        if(!$mail->send()){
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            echo 'Message has been sent';
        }

    }
}
