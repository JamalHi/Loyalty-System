<?php
namespace app\Traits;
use App\Models\User;
use App\Http\Controllers\MailController;
trait OTP_generationTrait {
    public function trait_generate_otp($user,$body)
    {
        $user->timestamps = false;
        $otp = (String)rand(1000,9999);
        $check = User::where('verefication_code' , $otp)->first();
        while(!is_Null($check)){
            $otp = (String)rand(1000,9999);
            $check = User::where('verefication_code' , $otp)->first();
        }
        $user->verefication_code = $otp;

        $user->verefi_code_exp_date = now()->addMinutes(4);
        $user->save();

        $mailcontroller = resolve(MailController::class);
        $mailcontroller->sendEmail($body.(string)$user->verefication_code , $user->email);
    }
}
