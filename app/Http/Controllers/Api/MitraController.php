<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use App\Models\Mitra;

class MitraController extends Controller
{
    public function index()
    {
        $mitra = Mitra::all();

        if(count($mitra)> 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $mitra
            ], 200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ], 400);
    }

    public function show($id)
    {
        $mitra = Mitra::find($id);

        if(!is_null($mitra)){
            return response([
                'message' => 'Retrieve Mitra Success',
                'data' => $mitra
            ], 200);
        }

        return response([
            'message' => 'Mitra Not Found',
            'data' => null
        ], 404);
    }

    public function store(Request $request){
        $storeData = $request->all();
        $validate = Validator::make($storeData, [
            'nama_mitra' => 'required',
            'no_ktp' => 'required|max:16',
            'alamat' => 'required',
            'nomor_telepon' => 'required|max:14',
        ]);

        if($validate->fails())
            return response(['message' => $validate->errors()], 400);

        $mitra = Mitra::create($storeData);

        return response([
            'message' => 'Add Mitra Success',
            'data' => $mitra
        ], 200);
    }

    public function destroy($id)
    {
        $mitra = Mitra::find($id);

        if(is_null($mitra)){
            return response([
                'message' => 'Mitra Not Found',
                'data' => null
            ], 404);
        }

        if($mitra->delete()){
            return response([
                'message' => 'Delete Mitra Success',
                'data' => $mitra
            ], 200);
        }

        return response([
            'message' => 'Delete Mitra Failed',
            'data' => null
        ], 400);
    }

    public function update(Request $request, $id){
        $mitra = Mitra::find($id);

        if(is_null($mitra)){
            return response([
                'message' => 'Mitra Not Found',
                'data' => null
            ], 404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData, [
            'nama_mitra' => 'required',
            'no_ktp' => 'required|max:16',
            'alamat' => 'required',
            'nomor_telepon' => 'required|max:14',
        ]);

        if($validate->fails())
            return response(['message' => $validate->errors()], 400);

        $mitra->nama_mitra = $updateData['nama_mitra'];
        $mitra->no_ktp = $updateData['no_ktp'];
        $mitra->alamat = $updateData['alamat'];
        $mitra->nomor_telepon = $updateData['nomor_telepon'];

        if($mitra->save()) {
            return response([
                'message' => 'Update Mitra Success',
                'data' => $mitra
            ], 200);
        }
        
        return response([
            'message' => 'Update Mitra Failed',
            'data' => null
        ], 400);
    }
}
