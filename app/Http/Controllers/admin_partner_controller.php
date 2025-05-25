<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Offer;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class admin_partner_controller extends Controller
{
    public function delete_offer($id)
    {

        $offer = Offer::find($id);
        $user = User::where('id',Auth::id())->first();
        $partner_user = User::where('id',$offer->user_id)->first();
        $offer->delete();

        if($user->role_id == 1)
        {
            $mailcontroller = resolve(MailController::class);
            $mailcontroller->sendEmail(" Dear user ,  your  offer  :
                                        \n" .$offer->title.
                                        "\n was deleted by our Admins. contact us for more info.
                                        \n عزيزي المشترك ، لقد قام أحد مشرفي التطبيق بحذف عرضك التالي :
                                        \n" . $offer->title.
                                        "\n لمعلومات أكثر يمكنك التواصل معنا  .", $partner_user->email);
        }
        return response()->json(['status'=>200 ,'message' => 'Offer deleted'] , status:200);
    }
    public function delete_voucher($id)
    {
        $voucher = Voucher::find($id);
        $voucher->delete();
        return response()->json(['status'=>200 ,'message' => 'Voucher deleted'] , status:200);
    }
}
