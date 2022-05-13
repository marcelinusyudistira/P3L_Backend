<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use App\Models\Driver;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DriverController extends Controller
{
    public function index()
    {
        $driver = Driver::all();

        if(count($driver)> 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $driver
            ], 200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ], 400);
    }

    public function show($id)
    {
        $driver = Driver::find($id);

        if(!is_null($driver)){
            return response([
                'message' => 'Retrieve Driver Success',
                'data' => $driver
            ], 200);
        }

        return response([
            'message' => 'Driver Not Found',
            'data' => null
        ], 404);
    }

    public function store(Request $request){
        $storeData = $request->all();
        $validate = Validator::make($storeData, [
            'nama_driver' => 'required|max:60',
            'tanggal_lahir' => 'required|date',
            'gender' => 'required|max:20',
            'alamat' => 'required',
            'nomor_telepon' => 'required|max:14',
            'email' => 'required|email:rfc,dns||unique:users',
            'foto' => 'required',
            'harga_driver' => 'required',
            'status_driver' => 'required',
            'bahasa' => 'required',
            'rerata_rating' => 'nullable',
            'sim_driver' => 'required',
            'surat_bebas_napza' => 'required',
            'surat_kesehatan_jiwa' => 'required',
            'surat_kesehatan_jasmani' => 'required',
            'skck' => 'required',
        ]);

        if($validate->fails())
            return response(['message' => $validate->errors()], 400);

        $count= DB::table('drivers')->count() +1;

        if($request->hasFile('foto')) {
            $file = $request->file('foto');
            $image = public_path().'/images/';
            $file -> move($image, $file->getClientOriginalName());
            $image = '/images/'.$file->getClientOriginalName();
        }else
            $image=null;

        if($request->hasFile('sim_driver')) {
            $fileSim = $request->file('sim_driver');
            $image = public_path().'/images/';
            $fileSim -> move($image, $fileSim->getClientOriginalName());
            $image = '/images/'.$fileSim->getClientOriginalName();
        }else
            $image=null;

        if($request->hasFile('surat_bebas_napza')) {
            $fileSbn = $request->file('surat_bebas_napza');
            $image = public_path().'/images/';
            $fileSbn -> move($image, $fileSbn->getClientOriginalName());
            $image = '/images/'.$fileSbn->getClientOriginalName();
        }else
            $image=null;

        if($request->hasFile('surat_kesehatan_jiwa')) {
            $fileSkji = $request->file('surat_kesehatan_jiwa');
            $image = public_path().'/images/';
            $fileSkji -> move($image, $fileSkji->getClientOriginalName());
            $image = '/images/'.$fileSkji->getClientOriginalName();
        }else
            $image=null;

        if($request->hasFile('surat_kesehatan_jasmani')) {
            $fileSkja = $request->file('surat_kesehatan_jasmani');
            $image = public_path().'/images/';
            $fileSkja -> move($image, $fileSkja->getClientOriginalName());
            $image = '/images/'.$fileSkja->getClientOriginalName();
        }else
            $image=null;

        if($request->hasFile('skck')) {
            $fileSkck = $request->file('skck');
            $image = public_path().'/images/';
            $fileSkck -> move($image, $fileSkck->getClientOriginalName());
            $image = '/images/'.$fileSkck->getClientOriginalName();
        }else
            $image=null;

        $storeData['password'] = bcrypt($request->tanggal_lahir);

        $driver = User::create([
            'email' => $request->email,
            'jabatan' => '5',
            'password' => $storeData['password']
        ]);

        $driver = Driver::create([
            'id_driver' => 'DRV-'.$count,
            'nama_driver' => $request->nama_driver,
            'tanggal_lahir' => $request->tanggal_lahir,
            'gender' => $request->gender,
            'alamat' => $request->alamat,
            'nomor_telepon' => $request->nomor_telepon,
            'email' => $request->email,
            'foto' => $file->getClientOriginalName(),
            'harga_driver' => $request->harga_driver,
            'status_driver' => $request->status_driver,
            'bahasa' => $request->bahasa,
            'rerata_rating' => $request->rerata_rating,
            'sim_driver' => $fileSim->getClientOriginalName(),
            'surat_bebas_napza' => $fileSbn->getClientOriginalName(),
            'surat_kesehatan_jiwa' => $fileSkji->getClientOriginalName(),
            'surat_kesehatan_jasmani' => $fileSkja->getClientOriginalName(),
            'skck' => $fileSkck->getClientOriginalName()
        ]);

        return response([
            'message' => 'Add Driver Success',
            'data' => $driver
        ], 200);
    }

    public function destroy($id)
    {
        $driver = Driver::find($id);

        if(is_null($driver)){
            return response([
                'message' => 'Driver Not Found',
                'data' => null
            ], 404);
        }

        if($driver->delete()){
            return response([
                'message' => 'Delete Driver Success',
                'data' => $driver
            ], 200);
        }

        return response([
            'message' => 'Delete Driver Failed',
            'data' => null
        ], 400);
    }

    public function update(Request $request, $id){
        $driver = Driver::find($id);

        if(is_null($driver)){
            return response([
                'message' => 'Pegawai Not Found',
                'data' => null
            ], 404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData, [
            'nama_driver' => 'required|max:60',
            // 'tanggal_lahir' => 'required|date',
            'gender' => 'required|max:20',
            'alamat' => 'required',
            'nomor_telepon' => 'required|max:14',
            // 'foto' => 'required',
            'harga_driver' => 'required',
            'status_driver' => 'required',
            'bahasa' => 'required',
            'rerata_rating' => 'nullable',
            // 'sim_driver' => 'required',
            // 'surat_bebas_napza' => 'required',
            // 'surat_kesehatan_jiwa' => 'required',
            // 'surat_kesehatan_jasmani' => 'required',
            // 'skck' => 'required',
        ]);

        if($validate->fails())
            return response(['message' => $validate->errors()], 400);

        $driver->nama_driver = $updateData['nama_driver'];
        // $driver->tanggal_lahir = $updateData['tanggal_lahir'];
        $driver->gender = $updateData['gender'];
        $driver->alamat = $updateData['alamat'];
        $driver->nomor_telepon = $updateData['nomor_telepon'];
        // $driver->foto = $updateData['foto'];
        $driver->harga_driver = $updateData['harga_driver'];
        $driver->status_driver = $updateData['status_driver'];
        $driver->bahasa = $updateData['bahasa'];
        $driver->rerata_rating = $updateData['rerata_rating'];
        // $driver->sim_driver = $updateData['sim_driver'];
        // $driver->surat_bebas_napza = $updateData['surat_bebas_napza'];
        // $driver->surat_kesehatan_jiwa = $updateData['surat_kesehatan_jiwa'];
        // $driver->surat_kesehatan_jasmani = $updateData['surat_kesehatan_jasmani'];
        // $driver->skck = $updateData['skck'];

        if($driver->save()) {
            return response([
                'message' => 'Update Driver Success',
                'data' => $driver
            ], 200);
        }
        
        return response([
            'message' => 'Update Driver Failed',
            'data' => null
        ], 400);
    }
}
