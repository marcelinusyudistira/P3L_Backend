<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PegawaiController extends Controller
{
    public function index()
    {
        $pegawai = Pegawai::all();

        if(count($pegawai)> 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $pegawai
            ], 200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ], 400);
    }

    public function show($id)
    {
        $pegawai = Pegawai::find($id);

        if(!is_null($pegawai)){
            return response([
                'message' => 'Retrieve Pegawai Success',
                'data' => $pegawai
            ], 200);
        }

        return response([
            'message' => 'Pegawai Not Found',
            'data' => null
        ], 404);
    }

    public function store(Request $request){
        $storeData = $request->all();
        $validate = Validator::make($storeData, [
            'id_role' => 'required',
            'nama_pegawai' => 'required|max:60',
            'tanggal_lahir' => 'required|date',
            'gender' => 'required|max:20',
            'alamat' => 'required',
            'nomor_telepon' => 'required|max:14',
            'email' => 'required|email:rfc,dns||unique:users'
        ]);

        if($validate->fails())
            return response(['message' => $validate->errors()], 400);

        $count= DB::table('pegawais')->count() +1;

        $storeData['password'] = bcrypt($request->tanggal_lahir);

        $pegawai = User::create([
            'email' => $request->email,
            'jabatan' => $request->id_role,
            'password' => $storeData['password']
        ]);

        $pegawai = Pegawai::create([
            'id_pegawai' => 'PEG-'.$count,
            'id_role' => $request->id_role,
            'nama_pegawai' => $request->nama_pegawai,
            'tanggal_lahir' => $request->tanggal_lahir,
            'gender' => $request->gender,
            'alamat' => $request->alamat,
            'nomor_telepon' => $request->nomor_telepon,
            'email' => $request->email
        ]);

        return response([
            'message' => 'Add Pegawai Success',
            'data' => $pegawai
        ], 200);
    }

    public function destroy($id)
    {
        $pegawai = Pegawai::find($id);

        if(is_null($pegawai)){
            return response([
                'message' => 'Pegawai Not Found',
                'data' => null
            ], 404);
        }

        if($pegawai->delete()){
            return response([
                'message' => 'Delete Pegawai Success',
                'data' => $pegawai
            ], 200);
        }

        return response([
            'message' => 'Delete Pegawai Failed',
            'data' => null
        ], 400);
    }

    public function update(Request $request, $id){
        $pegawai = Pegawai::find($id);

        if(is_null($pegawai)){
            return response([
                'message' => 'Pegawai Not Found',
                'data' => null
            ], 404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData, [
            'id_role' => 'required',
            'nama_pegawai' => 'required|max:60',
            'alamat' => 'required',
            'nomor_telepon' => 'required|max:14',
        ]);

        if($validate->fails())
            return response(['message' => $validate->errors()], 400);

        $pegawai->id_role = $updateData['id_role'];
        $pegawai->nama_pegawai = $updateData['nama_pegawai'];
        $pegawai->alamat = $updateData['alamat'];
        $pegawai->nomor_telepon = $updateData['nomor_telepon'];

        if($pegawai->save()) {
            return response([
                'message' => 'Update Pegawai Success',
                'data' => $pegawai
            ], 200);
        }
        
        return response([
            'message' => 'Update Pegawai Failed',
            'data' => null
        ], 400);
    }
}
