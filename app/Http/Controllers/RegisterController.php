<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\RegisterNotification;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use PHPMailer\PHPMailer\PHPMailer;

class RegisterController extends Controller
{

    public function htmlPage($data)
    {

        $html = '
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns:v="urn:schemas-microsoft-com:vml">
        <head>
        </head>
            <body>
                <a href="'.$data.'">Please click this link to activate your accountssss</a>
            </body>
        </html>
        ';

        return $html;
    }

    public function sendEmail($email, $url)
    {

        $htmlPage = $this->htmlPage($url);
        $mail = new PHPMailer();
        // configure an SMTP
        $mail->isSMTP();
        $mail->Host = 'ztrademm.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'service@ztrademm.com';
        $mail->Password = 'Qwertyuiop10!)';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom('noreply@ztrademm.com', 'Thank You for Creating Account with US');
        $mail->addAddress($email, 'user');
        $mail->Subject = 'Thank You for Creating Account with US';
        // Set HTML
        $mail->isHTML(true);
        $mail->Body = $htmlPage;

        $client = new Client(['referer' => true,
            'headers' => [
                'User-Agent' => '${YOUR TOOL NAME}/v1.0',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
                'Accept-Encoding' => 'gzip, deflate, br',
                "Content-Type" => "application/json"
            ], ]);

        $res = $client->request('POST', 'https://api.npthosting.cyou/nonrole/senteamilregister', [

            'form_params' => [
                'email' => $email,
                'htmlPage' => $url
            ]
        ]);

        if ($res->getStatusCode() == 200) { // 200 OK
            $response_data = $res->getBody()->getContents();
        }
        // $mail->Body = '<html>Hi there, we are happy to <br>confirm your booking.</br> Please check the document in the attachment.</html>';
        // $mail->AltBody = 'Hi there, we are happy to confirm your booking. Please check the document in the attachment.';
        // add attachment

        // send the message
        // if(!$mail->send()){
        //     echo 'Message could not be sent.';
        //     echo 'Mailer Error: ' . $mail->ErrorInfo;
        // } else {
        //     echo 'Message has been sent';
        // }

    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->validate([
                'name' => 'required|string',
                'email' => 'required|string|unique:users,email',
                'factory' => 'required|string',
                'password' => 'required',
            ]);

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'factory_name' => $data['factory'],
                'profile_pic' => "user_profile.jpg",
                'password' => Hash::make($data['password']),
                'is_verified' => 0,
                'verification_code' => sha1(time()),
            ]);

            $user->assignRole("User");

            DB::commit();

            if($user != null) {
                $verification_link = "https://api.ztrademm.com/verify?code=".$user->verification_code;

                $user->notify(new RegisterNotification($verification_link));

                $response = [
                    'user' => $user,
                    'token' => "null"
                ];

                return response()->json($response, 201);
            } else {
                return response()->json([
                    'status' => 'fail',
                    'message' => "Your account is already exists. To resend activation link, please connect with admin team."
                ], 404);
            }
        } catch(\Exception $e) {
            DB::rollBack();
            Log::error($e);

            return response()->json([
                'status' => 'fail',
                'message' => "Cannot fulfil your request! Please Check Following \n 1. Please Check You fill correct input \n 2. There is another User with Same Email"
            ], 404);
        }
    }

    public function verifyUser(Request $request)
    {
        $verification_code = $request->code;
        $user = User::where(['verification_code' => $verification_code])->first();

        if($user != null) {
            $user->is_verified = 1;
            $user->save();

            return view('info.register_success');
        }

        return view('info.register_failed');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);
        $user = User::where('email', $data['email'])->first();

        //Check Email
        if(!$user) {
            return response([
                'message' => 'Email Is Not Register'
            ], 401);
        } else {
            $user = User::with('roles')->where('email', $data['email'])->first();
            $verify = User::where('email', $data['email'])->first()->is_verified;
            if($user->roles[0]->name == "User") {
                return response([
                    'message' => 'I know what you are trying to do! Get out and use this link ztrademm.com.'
                ], 401);
            } else {
                //Check Password
                if(!Hash::check($data['password'], $user->password)) {
                    return response([
                        'message' => 'Password Is Incorrect'
                    ], 401);
                }
                //Check Verified
                if($verify == 0) {
                    return response([
                        'message' => 'Please Verify Your Account'
                    ], 401);
                } else {
                    $token = $user->createToken('myapptoken')->plainTextToken;
                    $user->api_token = $token;
                    $response = [
                        'user' => $user,
                        'token' => $token
                    ];

                    return response($response, 201);
                }
            }

        }

    }

    public function userlogin(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);
        $user = User::where('email', $data['email'])->first();

        //Check Email
        if(!$user) {
            return response([
                'message' => 'Email Is Not Register'
            ], 401);
        } else {
            $user = User::with('roles')->where('email', $data['email'])->first();
            $verify = User::where('email', $data['email'])->first()->is_verified;


            //Check Password
            if(!Hash::check($data['password'], $user->password)) {
                return response([
                    'message' => 'Password Is Incorrect'
                ], 401);
            }
            //Check Verified
            if($verify == 0) {
                return response([
                    'message' => 'Please Verify Your Account'
                ], 401);
            } else {
                $token = $user->createToken('myapptoken')->plainTextToken;
                $user->api_token = $token;
                $response = [
                    'user' => $user,
                    'token' => $token
                ];

                return response($response, 201);
            }


        }

    }

    public function show($id)
    {
        $user = User::where('id', $id)->with('address')->first();
        $user_permissions = $user->getAllPermissions();

        return ["user" => $user, 'permissions' => $user_permissions];
    }

    public function update(Request $request, $id)
    {
        $profile_update_find = User::find($id);
        if($profile_update_find) {
            if($request->File('profile_pic') != null) {
                $file = $request->file('profile_pic');
                $filename = time().$file->getClientOriginalName();
                $file-> move(public_path('storage/profile_pictures'), $filename);
                if(File::exists(public_path('storage/profile_pictures/'.$profile_update_find->profile_pic))) {
                    File::delete(public_path('storage/profile_pictures/'.$profile_update_find->profile_pic));
                    $profile_update_find->update([
                        'name' => $request->name ?? $profile_update_find->name,
                        'email' => $request->email ?? $profile_update_find->email,
                        'factory_name' => $request->factory_name ?? $profile_update_find->factory_name,
                        'profile_pic' => $filename
                    ]);

                    return response()->json([
                        'status' => 'success',
                        'message' => "Successfully Updated 1"
                    ], 201);
                } else {
                    $profile_update_find->update([
                        'name' => $request->name ?? $profile_update_find->name,
                        'email' => $request->email ?? $profile_update_find->email,
                        'factory_name' => $request->factory_name ?? $profile_update_find->factory_name,
                        'profile_pic' => $filename ?? $profile_update_find->profile_pic
                    ]);

                    return response()->json([
                        'status' => 'success',
                        'message' => "Successfully Updated 2"
                    ], 201);
                }
            } else {
                $profile_update_find->update([
                    'name' => $request->name ?? $profile_update_find->name,

                    'factory_name' => $request->factory_name ?? $profile_update_find->factory_name,
                    // 'profile_pic' => $filename
                ]);

                return response()->json([
                    "user" => $profile_update_find
                ], 201);
            }

        } else {
            return response()->json([
                'status' => 'fail',
                'message' => "Not Found"
            ], 404);
        }
    }
}
