<?php
namespace App\Services\WorkerService;


use App\Models\Worker;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Validator;
use App\Notifications\VerificationNotification;


class WorkerRegisterService {
    protected $model;

    function __construct()
    {
        $this->model = new Worker;
    }

    function validation($request)
    {
        $data = $request->all();
        $rules = $request->rules();

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        return $validator;
    }

    public function store($data,$request) {
    $worker = $this->model->create(array_merge(
        $data->validated(),
        [
        'password' => bcrypt($request->password),
        'photo' => $request->file('photo')->store('workers')
        ]
    ));
    return $worker->email;
    }

    public function generateToken($email) {
        $token = substr(md5(rand(0,9).$email.time()),0,32);
        $worker = $this->model->whereEmail($email)->first();
        $worker->verification_token = $token;
        //send mobile
        $message = "Login OTP is ".$token;
        $account_sid = getenv("TWILIO_ACCOUNT_SID");
        $auth_token = getenv("TWILIO_AUTH_TOKEN");
        $twilio_number = getenv("TWILIO_SMS_FROM");
        $client = new Client($account_sid,$auth_token);
        $client->messages->create('+963984117854',[
            "from"=>$twilio_number,
            "body"=>$message
        ]);
        $worker->save();
        return $worker;
    }

    public function sendEMail($worker) {
         $worker->notify(new VerificationNotification($worker));
    }

    public function register($request) {
        $data = $this->validation($request);
        $email = $this->store($data,$request);
        $worker = $this->generateToken($email);
        $this->sendEMail($worker);
        return response()->json([
            'message'=>"account has been created please check your email"
        ]);
    }
}
