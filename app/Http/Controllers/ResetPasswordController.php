<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPassword;
use App\Models\User;
use Carbon\Carbon;
use DB;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;


class ResetPasswordController extends Controller
{
    //

    public function htmlPage($data){

        $html = '
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns:v="urn:schemas-microsoft-com:vml">
        <head>
        </head>
            <body>
                <a href="'.$data.'">Click this link to reset your passwordssss</a>
            </body>
        </html>
        ';

        return $html;
    }

    public function sendEmail($email,$url)
    {
       
        $htmlPage = $this->htmlPage($url);
        $mail = new PHPMailer();
        $mail->SMTPDebug = SMTP::DEBUG_SERVER; 
        // configure an SMTP
        $mail->isMail();
        $mail->Host = 'ztrademm.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'service@ztrademm.com';
        $mail->Password = 'Qwertyuiop10!)';
        $mail->SMTPSecure = 'ssl'; 
        $mail->Port = 465;
        // $mail->SMTPOptions = array(
        // 'ssl' => array(
        // 'verify_peer' => false,
        // 'verify_peer_name' => false,
        // 'allow_self_signed' => true
    
        // )
        // );    
        $mail->setFrom('dev.ztrademyanmar@gmail.com', 'Reset Your Password');
        $mail->addAddress($email, 'user');
        $mail->Subject = 'Reset Your Password';
        // Set HTML
        $mail->isHTML(TRUE);
        $mail->Body = $htmlPage;
        // $mail->Body = '<html>Hi there, we are happy to <br>confirm your booking.</br> Please check the document in the attachment.</html>';
        // $mail->AltBody = 'Hi there, we are happy to confirm your booking. Please check the document in the attachment.';
        // add attachment
        
        //  $urlsite = 'https://api.npthosting.cyou/nonrole/resetpassword';
        //  $curl = curl_init($urlsite);
        //     $data = [
        //           'email' => $email,
        //           'htmlPage' => $htmlPage
        //         ];
                
        //         curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        //         // Set the CURLOPT_POST option to true for POST request
        //         curl_setopt($curl, CURLOPT_POST, true);
        //         // Set the request data as JSON using json_encode function
        //         curl_setopt($curl, CURLOPT_POSTFIELDS,  json_encode($data));
        //         // Set custom headers for RapidAPI Auth and Content-Type header
        //         curl_setopt($curl, CURLOPT_HTTPHEADER, [
        //           'X-RapidAPI-Host: https://api.npthosting.cyou',
                 
        //           'Content-Type: application/json'
        //         ]);

        // $response = curl_exec($curl);
        // curl_close($curl);
        // echo $response;
        
        $client = new Client(['referer' => true,
          'headers' => [
            'User-Agent' => '${YOUR TOOL NAME}/v1.0',
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
            'Accept-Encoding' => 'gzip, deflate, br',
            "Content-Type" => "application/json"
          ],]);

        $res = $client->request('POST', 'https://api.ztrademm.com/forgot/password', [
            'form_params' => [
                'email' => $email,
                'htmlPage' => $url
            ]
        ]);
        
        if ($res->getStatusCode() == 200) { // 200 OK
            $response_data = $res->getBody()->getContents();
        }
        
        // $response = Http::post('https://api.npthosting.cyou/nonrole/resetpassword', [
        //             'email' => $email,
        //             'htmlPage' => $htmlPage,
        //         ]);
        //         $jsonData = $response->body();
        //         dd($jsonData);
        echo "done";
       
        // send the message
        // if(!$mail->send()){
        //     echo 'Message could not be sent.';
        //     echo PHPMailer::ENCRYPTION_SMTPS.'Hello';
        //     echo 'Mailer Error: ' . $mail->ErrorInfo;
        // } else {
           
            
        //     echo 'Message has been sent';
        // }
        
        
        
       

    }
    
    public function passwordforgot(){
        return view('resetpassword.forgotpassword');
    }
    
    public function error(){
        return view('resetpassword.linkexpired');
    }
    
    
    public function home(){
        return view('resetpassword.successfullychanged');
    }
    public function forgetpassword(Request $request){
        $this->validate($request,[
            'email' => 'required|email',
        ]);
        $email = $request->get('email');
        $checkemail = User::where('email',$email)->first();
        if($checkemail){
            $token = \Str::random(64);
            //Create Password Reset Token
            DB::table('password_resets')->insert([
                'email' => $email,
                'token' => $token,
                'created_at' => Carbon::now()
            ]);
            $user = User::where('email',$email)->first();
            $status = 'https://api.ztrademm.com/reset-password/?token='.$token.'&email='.$email;
            // $status = 'https://api.ztrademm.com/reset-password/?email=' . $email;
            $this->sendEmail($email,$status);
            
            // $status = 'http://127.0.0.1:8000/reset-password/?token='.$token.'&email='.$email;
            // \Mail::to("micaljosh60@gmail.com")->send(new ResetPassword($status));  
            // return back()->with('success','Email Found! Please Check Your Mail');
        }
        else{
            return back()->with('error','Email Not Found! Please Try Again');
        }
    }
    public function resetpassword(Request $request){
        $email = $request->email;
        $token = $request->token;
        return view('resetpassword.password-reset',compact('token','email'));
    }
    public function updatepassword(Request $request){
        global $tokenfetched;
        $fields = $request->validate([
            'newpassword' => 'required|string',
        ]);
        $email = $request->useremail;
        $token = $request->usertoken;
        $fetch =  DB::table('password_resets')->where([
                'email' => $email
                ])->get('token');  
        foreach ($fetch as $token) {
            $tokenfetched =  $token->token;
        } 
        if($token = $tokenfetched){
            User::where('email',$email)->update([
                'password' => bcrypt($fields['newpassword']), 
            ]);
            DB::table('password_resets')->where([
                'email' => $email
            ])->delete();
            
            return redirect()->route('home');
            // return view('resetpassword.successfullychanged');
        }
        else{
            return redirect()->route('home');
            // return view('resetpassword.linkexpired');
        }
       
        
    }
}
