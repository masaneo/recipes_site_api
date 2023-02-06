<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;

class UserApiController extends Controller 
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return User::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $message = "";
        if(User::where('username', '=', $request['username'])->exists()){
            $message .= "Podana nazwa użytkownika jest zajęta! ";
        }
        if(User::where('email', '=', $request['email'])->exists()){
            $message .= "Podany adres e-mail jest zajęty! ";
        }

        if($message == "") {
            $user = User::create([
                'username' => $request['username'],
                'email' => $request['email'],
                'password' => Hash::make($request['password']),
                'api_token' => Str::random(60),
                'user_type' => UserType::where('name', '=', 'user')->first()->id,
            ]);

            if($user) {
                $this->sendVerificationEmail($user->email, $user->api_token, $user->username);
            }

            return $user;

        } else {
            return Response([
                "message" => $message
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return User::find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        $user->update($request->all());
        return $user;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return User::destroy($id);
    }

    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if(!$user || !Hash::check($request->password, $user->password)){
            return response([
                'message' => 'Nieprawidłowy adres e-mail lub hasło',
            ]);
        }

        if($user->email_verified_at == null) {
            return Response(["message" => "Adres e-mail nie został potwierdzony, sprawdź ponownie skrzynkę pocztową."]);
        }

        $token = $user->api_token;
        $admin = $user->user_type == UserType::where('name', '=', 'admin')->first()->id ? true : false;

        $response = [
            'user' => $user,
            'admin' => $admin,
            'token' => $token
        ];

        return response($response, 201);
    }

    public function sendVerificationEmail($email, $token, $username) {
        $host_url = "localhost:8080";
        $user = User::where('email', '=', $email)->get();

        if(count($user) > 0) {
            $data['email'] = $email;
            $data['title'] = "Weryfikacja adresu e-mail";
            $data['body'] = "Witaj, założyłeś konto w serwisie przepisowo. Aby z niego skorzystać musisz zweryfikować swój adres e-mail! Aby to uczynić kliknij w link poniżej";
            $data['url'] = "http://".$host_url."/verify?token=".$token;

            Mail::send('verifyMail', ['data' => $data], function($message) use ($data){
                $message->to($data['email'])->subject($data['title']);
            });
        }
    }

    public function verifyEmail(Request $req) {
        $user = User::where('api_token', '=', $req->token)->first();

        if($user->email_verified_at != null) {
            return Response(["message" => "Twój adres email jest już zweryfikowany."]);
        }

        if($user) { 
            $user->email_verified_at = now();

            $user->save();

            return Response(["message" => "Pomyślnie zweryfikowano adres email."]);
        } 
    }

    public function resendVerificationEmail(Request $req) {
        $user = User::where('email', '=', $req->email)->first();

        $this->sendVerificationEmail($user->email, $user->api_token, $user->username);
    }
}
