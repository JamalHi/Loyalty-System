<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\Role;
use App\Models\User;
use App\Models\Client;
use App\Models\Cashier;
use App\Models\Charity;
use App\Models\Partner;
use App\Models\History;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\AuthenticationException;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Validation\Rule as ValidationRule;
use App\Traits\NotificationTrait;
use App\Traits\OTP_generationTrait;


class auth_controller extends Controller
{
    use NotificationTrait;
    use OTP_generationTrait;

    public function createAccountAdmin(UserRequest $request)
    {
        $profile_pic=null;
        if($request->hasFile('prof_img') )
        {
            $profile_pic = time() . '.' . $request->prof_img->getClientOriginalExtension();
            $profile_pic = $request->file('prof_img')->store('upload','public');
            $request->prof_img->move(public_path('upload'),$profile_pic);
        }

        $request['password'] = Hash::make($request['password']);

        $user = User::query()->create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => $request->password,
            'prof_img' => $profile_pic,
            'active' => 1,
            'role_id' =>1,
        ]);
        //add token to user
        $tokenResult = $user->createToken('personal Access Token');

        $data["user"] = $user ;
        $data["token_type"] = 'Bearer';
        $data["access_token"] = $tokenResult->accessToken;

        return response()->json([$data , 'status' => 200 , 'message' => 'Account created successfully']);
    }

    public function createAccountPartner(UserRequest $request)
    {
        $validator = validator::make($request->all(), [
            'location' => ['required' , 'string' ],
            'about' => ['required' , 'string' ],
            'points' => ['required','numeric'],
            'category' => ['required'],
            'service' => ['required' , 'numeric'],
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->all(),status:400);
        }
        if($request->service >100)
        {
            return response()->json(['status' => 400 , 'message' => 'Wrong service'],status:400);
        }

        $request['password'] = Hash::make($request['password']);

        $user = User::query()->create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => $request->password,
            'active' => 1,
            'role_id' =>2,
        ]);

        $tokenResult = $user->createToken('personal Access Token');

        $new_partner = Partner::query()->create([
            'location' => $request->location,
            'about'=> $request->about,
            'points'=> $request->points,
            'service' => $request->service,
            'category' => $request->category,
            'user_id' => $user->id,
        ]);

        if($request->points != null)
        {
            $history= History ::query()->create([
                'operation' => "buy points",
                'transfer_points' => $request->points,
                'transfer_time' =>now(),
                'from_user' => Auth::id(),
                'to_user' => $user->id
            ]);
        }
        $data["user"] = $user ;
        $data["partner_data"] = $new_partner;
        $data["token_type"] = 'Bearer';
        $data["access_token"] = $tokenResult->accessToken;

        return response()->json([$data , 'status' => 200 , 'message' => 'Account created successfully']);
    }

    public function add_parnter_image(Request $request , $id)
    {
        $user = User::find($id);
        $validator = validator::make($request->all(), [
            'prof_img' => 'image| max:100000|mimes:jpeg,png,jpg,gif,svg',
            'id_image' => 'required |image| max:100000|mimes:jpeg,png,jpg,gif,svg',
            'commercial_record' => 'required |image| max:100000|mimes:jpeg,png,jpg,gif,svg',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->all(),status:400);
        }
        //$profile_pic = null;
        if($request->hasFile('prof_img') ){
            $profile_pic = time() . '.' . $request->prof_img->getClientOriginalExtension();
            $profile_pic = $request->file('prof_img')->store('upload','public');
            $request->prof_img->move(public_path('upload'),$profile_pic);

            $user->prof_img = $profile_pic;
            $user->save();
        }

        $partner = Partner::where('user_id',$id)->first();
        if($request->hasFile('id_image') ){
            $id_pic = time() . '.' . $request->id_image->getClientOriginalExtension();
            $id_pic = $request->file('id_image')->store('upload','public');
            $request->id_image->move(public_path('upload'),$id_pic);
            $partner->id_image = $id_pic;
            $partner->save();
        }
        if($request->hasFile('commercial_record') ){
            $cr_pic = time() . '.' . $request->commercial_record->getClientOriginalExtension();
            $cr_pic = $request->file('commercial_record')->store('upload','public');
            $request->commercial_record->move(public_path('upload'),$cr_pic);
            $partner->commercial_record = $cr_pic;
            $partner->save();
        }

        $data["user"] = $user;
        $data["partner"] = $partner;

        return response()->json([$data , 'status' => 200 , 'message' => 'Images addeded successfully']);
    }

    public function createAccountCashier(UserRequest $request)
    {
        $profile_pic = null;
        if($request->hasFile('prof_img')){
            $profile_pic = time() . '.' . $request->prof_img->getClientOriginalExtension();
            $profile_pic = $request->file('prof_img')->store('upload','public');
            $request->prof_img->move(public_path('upload'),$profile_pic);
            }

        $request['password'] = Hash::make($request['password']);

        $user = User::query()->create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => $request->password,
            'prof_img' => $profile_pic,
            'active' => 1,
            'role_id' =>3,
        ]);

        $tokenResult = $user->createToken('personal Access Token');

        $new_cashier = Cashier::query()->create([
            'user_id' => $user->id,
            'partner_id' => Auth::id(),
        ]);

        $data["user"] = $user ;
        $data["cashier_data"] = $new_cashier;
        $data["token_type"] = 'Bearer';
        $data["access_token"] = $tokenResult->accessToken;

        return response()->json([$data , 'status' => 200 , 'message' => 'Account created successfully']);
    }

    public function createAccountClient(UserRequest $request)
    {
        $request['password'] = Hash::make($request['password']);
        $profile_pic=null;
        //$uploadedFileUrl=null;
        if($request->hasFile('prof_img')){
            $profile_pic = time() . '.' . $request->prof_img->getClientOriginalExtension();

            //jamal edit
            //$uploadedFileUrl = cloudinary()->upload($request->file('prof_img')->getRealPath())->getSecurePath();
            //$uploadedFileUrl = Cloudinary::upload($request->file('prof_img')->getRealPath())->getSecurePath();
            //$profile_pic = Cloudinary::upload($request->file('prof_img')->getRealPath())->getSecurePath();

            $profile_pic = $request->file('prof_img')->store('upload','public');
            $request->prof_img->move(public_path('upload'),$profile_pic);
        }
        $user = User::query()->create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => $request->password,
            'prof_img' => $profile_pic,
            //our active status is 0 by default
            'role_id' =>4,
        ]);

        $tokenResult = $user->createToken('personal Access Token');

        $new_client = Client::query()->create([
            'points' => 0,
            'special_points' => 0,
            'points_exp_date' => now(),
            'special_exp_date' => now(),
            'user_id' => $user->id,
        ]);

        $data["user"] = $user ;
        $data["client"] = $new_client;
        $data["token_type"] = 'Bearer';
        $data["access_token"] = $tokenResult->accessToken;

        return response()->json([$data , 'status' => 200 , 'message' => 'Account created successfully']);
    }

    public function createAccountCharity(UserRequest $request)
    {
        $validator = validator::make($request->all(), [
            'location' => ['required' , 'string' ],
            'about' => ['required' , 'string' ],
            'id_image' => ['required' , 'image' ],
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->all(),status:400);
        }

        $profile_pic=null;
        if($request->hasFile('prof_img')){
            $profile_pic = time() . '.' . $request->prof_img->getClientOriginalExtension();
            $profile_pic = $request->file('prof_img')->store('upload','public');
            $request->prof_img->move(public_path('upload'),$profile_pic);
        }

        if($request->hasFile('id_image') ){
            $id_pic = time() . '.' . $request->id_image->getClientOriginalExtension();
            $id_pic = $request->file('id_image')->store('upload','public');
            $request->id_image->move(public_path('upload'),$id_pic);
        }

        $request['password'] = Hash::make($request['password']);

        $user = User::query()->create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => $request->password,
            'prof_img' => $profile_pic,
            'active' => 1,
            'role_id' =>5,
        ]);

        $tokenResult = $user->createToken('personal Access Token');

        $new_charity = Charity::query()->create([
            'location' => $request->location,
            'about'=> $request->about,
            'id_image' => $id_pic,
            'points' => 0,
            'points_exp_date' => now(),
            'user_id' => $user->id,
        ]);

        $data["user"] = $user ;
        $data["charity_data"] = $new_charity;
        $data["token_type"] = 'Bearer';
        $data["access_token"] = $tokenResult->accessToken;

        return response()->json([$data , 'status' => 200 , 'message' => 'Account created successfully']);
    }

    public function login(Request $request)
    {
        $validator = validator::make($request->all(), [
            'email' => ['required' , 'string' , 'email' ],
            'password' => ['required' , 'string' , 'min:8'],
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->all() , status:422);
        }

        $user = User::where('email',$request->email)->first();
        if(!is_Null($user))
        {
            if(!Hash::check($request->password , $user->password))
            {
                if($user->password_counter < 5)
                {
                    $user->password_counter += 1 ;
                    $user->save();
                    return response()->json(['status'=>400,'message'=>'Wrong password'],status:400);
                }
                else
                {
                    $user->active = 0;
                    $user->save();
                    return response()->json(['status'=>403,'message'=>'You are blocked , verify your account again'],status:403);
                }
            }
        }

        $credentials = request(['email', 'password']);
        if(!Auth::attempt($credentials)){
            throw new AuthenticationException();
        }

        $user = $request->user();
        //add token to user
        $tokenResult = $user->createToken('personal Access Token');//->accessToken;

        $user = User::where('id' , '=' , auth()->id())->first();
        $role = Role::where('id' , '=' , $user->role_id)->first();

        $user->password_counter = 0;
        $user->save();

        $data["user"] = $user;
        if($user->role_id == 2){
            $data["partner"] = Partner::where('user_id',$user->id)->first();
        }
        if($user->role_id == 3){
            //this edit is for raghad
            $cashier = Cashier::where('user_id',$user->id)->first();
            $partner_user = User::where('id',$cashier->partner_id)->first();
            $data["partner"] = Partner::where('user_id',$partner_user->id)->first();
        }
        if($user->role_id == 4 ){
            $data["client"] = Client::where('user_id',$user->id)->first();
        }
        if($user->role_id == 5 ){
            $data["charity"] = Charity::where('user_id',$user->id)->first();
        }

        $data["role"] = $role;
        $data["token_type"] = 'Bearer';
        $data["access_token"] = $tokenResult->accessToken;

        return response()->json([$data,'status'=>200,'message'=>'logged In successfully']);
    }

    public function partner_password(Request $request)
    {
        $user = User::find(Auth::id());
        $validator = validator::make($request->all(), [
            'password' => ['required', 'string', 'min:8'
                        ,'regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x]).*$/',
                        'confirmed'],
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->all(),status:400);
        }
        $request['password'] = Hash::make($request['password']);
        if($request->password != null){
            $user->update(['password' => $request->password]);
            $user->save();
        }

        $partner = Partner::where('user_id',$user->id)->first();
        $partner->first_login = 1;
        $partner->save();

        $user->Partner;

        return response()->json([$user,'status'=>200,'message'=>'updated successfully']);
    }

    public function logout(Request $request ){
        $request->user()->token()->revoke();
        $user = User::find(Auth::id());
        $user->device_token = null;
        $user->save();
        return response()->json(['message' => 'logged out ','status'=>200]);
    }

    public function generate_otp(request $request)
    {
        $user = User::where('id',Auth::id())->first();
        if(is_Null($user))
        {
            return response()->json([$user,'status'=>404 ,'message' => 'user not found'] , status:404);
        }
        $body = "Email verification notice.\nYour verification code is : ";
        $this->trait_generate_otp($user,$body);
        return response()->json(['message' => 'otp sent successfully' , 'status' => 200], status:200);
    }

    public function generate_otp_with_email(request $request)
    {
        if($request->email != null)
        {
            $user = User::where('email',$request->email)->first();
            if(is_Null($user))
            {
                return response()->json([$user,'status'=>404 ,'message' => 'user not found'] , status:404);
            }
            $body = "Password reset message.\nSecurity code is: ";
            $this->trait_generate_otp($user,$body);
            return response()->json(['message' => 'otp sent successfully' , 'status' => 200], status:200);
        }
    }

    public function reset_password(request $request)
    {
        $validator = validator::make($request->all(), [
            'password' => ['required', 'string', 'min:8'
                            ,'regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x]).*$/',
                            'confirmed'],
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->all() , status:422);
        }
        $user = User::where('email',$request->email)->first();

        $request['password'] = Hash::make($request['password']);

        $user->password = $request->password;
        $user->password_counter = 0;
        $user->save();
        return response()->json(['message' => 'password reset successfully.' , 'status' => 200], status:200);
    }

    public function otp_verification(request $request)
    {
        $user = User::where('email',$request->email)->first();

        if(now() > $user->verefi_code_exp_date)
        {
            return response()->json(['message' => 'your code has expired!' , 'status' => 403]);
        }

        if($request->otp_from_user == $user->verefication_code)
        {
            $user->active = 1;
            $user->verefication_code = null;
            $user->verefi_code_exp_date = null;
            $user->save();
            return response()->json(['message' => 'otp verification done successfully!' , 'status' => 200]);
        }

        return response()->json(['message' => 'otp verification faild!' , 'status' => 403]);
    }

    public function email_verification(Request $request)
    {
        //register -  block
        $user = User::where('id',Auth::id())->first();

        if(now() > $user->verefi_code_exp_date)
        {
            return response()->json(['message' => 'your code has expired!' , 'status' => 403]);
        }

        if($request->otp_from_user == $user->verefication_code)
        {
            $user->active = 1;
            $user->verefication_code = null;
            $user->verefi_code_exp_date=null;
            $user->save();
            return response()->json(['message' => 'verification done successfully!' , 'status' => 200]);
        }

        return response()->json(['message' => 'verification faild!' , 'status' => 403]);
    }

    public function update_account(Request $request)
    {
        $user = User::find(Auth::id());
            $validator = validator::make($request->all(), [
                //'name' => [ 'string' , 'max:255'],
                //'password' => [ 'string' , 'min:8'],
                'prof_img' => [ 'image'],
                'confirm_password'=>[ 'string' , 'min:8'],
                'phone' => ['min:10', 'max:10' , ValidationRule::unique(table: 'users')]
            ]);
            if($validator->fails()){
                return response()->json($validator->errors()->all(),status:400);
            }
            $hash_pass = Hash::make($request['confirm_password']);

            if(!Hash::check($request->confirm_password , $user->password))
            {
                return response()->json(['status'=>400,'message'=>'Wrong password'],status:400);
            }

            if($request->hasFile('prof_img')){
                $file = $user->prof_img;
                if(File::exists(public_path($file)))
                {
                    File::delete(public_path($file));
                }
                if(Storage::disk('public')->exists($file))
                {
                    Storage::disk('public')->delete($file);
                }

                $img_name = time() . '.' . $request->prof_img->getClientOriginalExtension();
                $img_name = $request->file('prof_img')->store('upload','public');
                $request->prof_img->move(public_path('upload'),$img_name);
                if($img_name != null){
                    $user->update(['prof_img' => $img_name]);
                    $user->save();
                }
            }

            if($request->email != null){
                $validator = validator::make($request->all(), [
                    'email' => ['string','email', 'max:255',
                                ValidationRule::unique(table: 'users'),
                                'regex:/(.*)@(gmail)\.com/i',
                                ]
                ]);
                if($validator->fails()){
                    return response()->json($validator->errors()->all(),status:400);
                }
                $user->update(['email' => $request->email]);
                $user->save();
            }

            if($request->name != null){
                $user->update(['name' => $request->name]);
                $user->save();
            }

            if($request->password != null){
                $validator = validator::make($request->all(), [
                    'password' => ['string', 'min:8'
                    ,'regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x]).*$/'],
                ]);
                if($validator->fails()){
                    return response()->json($validator->errors()->all(),status:400);
                }

                $request['password'] = Hash::make($request['password']);
                $user->update(['password' => $request->password]);
                $user->save();
            }

            if($request->phone != null){
                $user->update(['phone' => $request->phone]);
                $user->save();
            }

            $data["user"] = $user ;

            if($user->role_id == 2)
            {
                $partner = Partner::where('user_id' , $user->id)->first();
                if($request->location != null)
                {
                    $partner->update(['location' => $request->location]);
                    $partner->save();
                }
                if($request->about != null)
                {
                    $partner->update(['about' => $request->about]);
                    $partner->save();
                }
                if($request->category != null)
                {
                    $partner->update(['category' => $request->category]);
                    $partner->save();
                }
                $data["partner"] = $partner;
            }

            if($user->role_id == 4){
                $client = $user->Client;
            }

            if($user->role_id == 5)
            {
                $charity = Charity::where('user_id' , $user->id)->first();
                if($request->location != null)
                {
                    $charity->update(['location' => $request->location]);
                    $charity->save();
                }
                if($request->about != null)
                {
                    $charity->update(['about' => $request->about]);
                    $charity->save();
                }
                //$data["charity"] = $charity;
                $user->Charity;
            }
            return response()->json([$data,'status'=>200,'message'=>'updated successfully']);
    }

    public function update_email(Request $request)
    {
        $user = User::find(Auth::id());
        $validator = validator::make($request->all(), [
            'email' => ['string','email', 'max:255',
                        ValidationRule::unique(table: 'users'),
                        'regex:/(.*)@(gmail)\.com/i',
                        ],
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->all(),status:400);
        }

        if($request->email != null){
            $user->update(['email' => $request->email]);
            $user->save();
        }

        return response()->json(['status'=>200,'message'=>'success']);
    }

    public function show_profile($id)
    {
        $user = User::find($id);
        if($user->role_id == 2)
        {
            $user->Partner;
        }
        if($user->role_id == 3)
        {
            $user->Cashier;
        }
        if($user->role_id == 4)
        {
            $client = $user->Client;
            if($client->points_exp_date < now())
            {
                $client->points = 0;
                $client->save();
            }
            if($client->special_exp_date < now())
            {
                $client->special_points = 0;
                $client->save();
            }
        }

        if($user->role_id == 5)
        {
            $charity =  $user->Charity;
            if($charity->points_exp_date < now())
            {
                $charity->points = 0;
                $charity->save();
            }
        }
        return response()->json([$user,'status'=>200,'message'=>'show successfully'] , status : 200);
    }
}
