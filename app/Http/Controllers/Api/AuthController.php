<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Customer;
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function register(Request $request){
        $registrationData = $request->all();
        $validate = Validator::make($registrationData, [
            'nama_customer' => 'required|max:60',
            'tanggal_lahir' => 'required|date',
            'gender' => 'required|max:20',
            'alamat' => 'required|max:150',
            'nomor_telepon' => 'required|max:14',
            'foto' => 'required',
            'ktp' => 'required',
            'sim' => 'required',
            'email' => 'required|email:rfc,dns|unique:users',
        ]);

        if($validate->fails())
            return response(['message' => $validate->errors()], 400);
        
        $registrationData['password'] = bcrypt($request->tanggal_lahir);

        $user = User::create([
            'email' => $request->email,
            'jabatan' => '0',
            'password' => $registrationData['password']
        ]);

        $count= DB::table('customers')->count() +1;
        $date= Carbon::now()->format('ymd');

        if($count <10){
            $zero='-00';
        }else{$zero='-0';}

        if($request->hasFile('foto')) {
            $file = $request->file('foto');
            $image = public_path().'/images/';
            $file -> move($image, $file->getClientOriginalName());
            $image = '/images/'.$file->getClientOriginalName();
        }else
            $image=null;

        if($request->hasFile('sim')) {
            $fileSim = $request->file('sim');
            $image = public_path().'/images/';
            $fileSim -> move($image, $fileSim->getClientOriginalName());
            $image = '/images/'.$fileSim->getClientOriginalName();
        }else
            $image=null;

        if($request->hasFile('ktp')) {
            $fileKtp = $request->file('ktp');
            $image = public_path().'/images/';
            $fileKtp -> move($image, $fileKtp->getClientOriginalName());
            $image = '/images/'.$fileKtp->getClientOriginalName();
        }else
            $image=null;

        $user = Customer::create([
            'id_customer' => 'CUS'.$date.$zero.$count,
            'nama_customer' => $request->nama_customer,
            'tanggal_lahir' => $request->tanggal_lahir,
            'gender' => $request->gender,
            'alamat' => $request->alamat,
            'nomor_telepon' => $request->nomor_telepon,
            'foto' => $file->getClientOriginalName(),
            'ktp' => $fileKtp->getClientOriginalName(),
            'sim' => $fileSim->getClientOriginalName(),
            'email' => $request->email
        ]);

        return response([
            'message' => 'Register Success',
            'user' => $user
        ], 200);
    }

    public function login(Request $request){
        $loginData = $request->all();
        $validate = Validator::make($loginData, [
            'email' => 'required|email:rfc,dns',
            'password' => 'required'
        ]);

        if($validate->fails())
            return response(['message' => $validate->errors()], 400);

        if(!Auth::attempt($loginData))
            return response(['message' => 'Invalid Credentials'], 401);

        $user = Auth::user();
        $token = $user->createToken('Authentication Token')->accessToken;

        return response([
            'message' => 'Authenticated',
            'user' => $user,
            'token_type' => 'Bearer',
            'access_token' => $token
        ]);
    }

    public function logout(Request $request){
        $token = $request->user()->token();
        $token->revoke();
        $response = ["message"=>"You have successfully logout!!"];
        return response($response,200);
    }
}
