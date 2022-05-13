<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use App\Models\DetailJadwal;
use DB;

class DetailJadwalController extends Controller
{
    public function index()
    {   
        $detailJadwal = DB::table('detail_jadwals')
            ->join('pegawais', 'detail_jadwals.id_pegawai', '=', 'pegawais.id_pegawai')
            ->join('jadwal_pegawais', 'detail_jadwals.id_jadwal', '=', 'jadwal_pegawais.id_jadwal')
            ->select('detail_jadwals.id_detail', 'detail_jadwals.id_pegawai', 'detail_jadwals.id_jadwal', 'pegawais.nama_pegawai', 
            'pegawais.id_role', 'jadwal_pegawais.hari', 'jadwal_pegawais.shift')
            ->orderBy('jadwal_pegawais.shift') 
            ->get();

        if(count($detailJadwal)> 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $detailJadwal
            ], 200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ], 400);
    }

    public function showPegawai($role)
    {   
        $pegawai = DB::table('pegawais')
            ->select('id_pegawai','nama_pegawai','id_role')
            ->where('id_role',$role)
            ->get();

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
        $detailJadwal = DB::table('detail_jadwals')
            ->join('pegawais', 'detail_jadwals.id_pegawai', '=', 'pegawais.id_pegawai')
            ->join('jadwal_pegawais', 'detail_jadwals.id_jadwal', '=', 'jadwal_pegawais.id_jadwal')
            ->select('detail_jadwals.id_detail', 'detail_jadwals.id_pegawai', 'detail_jadwals.id_jadwal', 'pegawais.nama_pegawai', 
            'pegawais.id_role', 'jadwal_pegawais.hari', 'jadwal_pegawais.shift')
            ->where('id_detail',$id)
            ->get();

        if(!is_null($detailJadwal)){
            return response([
                'message' => 'Retrieve Detail Jadwal Success',
                'data' => $detailJadwal
            ], 200);
        }

        return response([
            'message' => 'Detail Jadwal Not Found',
            'data' => null
        ], 404);
    }

    public function store(Request $request){
        $storeData = $request->all();
        $validate = Validator::make($storeData, [
            'id_pegawai' => 'required',
            'id_jadwal' => 'required',
        ]);

        if($validate->fails())
            return response(['message' => $validate->errors()], 400);
        
        $countDetail = DetailJadwal::where('id_pegawai',$request->id_pegawai)->count();

        if($countDetail < 6){
            $detailJadwal = DetailJadwal::create($storeData);
            return response([
                'message' => 'Add Jadwal Pegawai Success',
                'data' => $detailJadwal
            ], 200);
        }else{
            return response([
                'message' => 'Jadwal Pegawai melebihi batas',
                'data' => null
            ], 404);
        }
    }

    public function destroy($id)
    {
        $detailJadwal = DetailJadwal::find($id);

        if(is_null($detailJadwal)){
            return response([
                'message' => 'Detail Jadwal Not Found',
                'data' => null
            ], 404);
        }

        if($detailJadwal->delete()){
            return response([
                'message' => 'Delete Detail Jadwal Success',
                'data' => $detailJadwal
            ], 200);
        }

        return response([
            'message' => 'Delete Detail Jadwal Failed',
            'data' => null
        ], 400);
    }

    public function update(Request $request, $id){
        $detailJadwal = DetailJadwal::find($id);

        if(is_null($detailJadwal)){
            return response([
                'message' => 'Detail Jadwal Not Found',
                'data' => null
            ], 404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData, [
            'id_pegawai' => 'required',
            'id_jadwal' => 'required',
        ]);

        if($validate->fails())
            return response(['message' => $validate->errors()], 400);

        $detailJadwal->id_pegawai = $updateData['id_pegawai'];
        $detailJadwal->id_jadwal = $updateData['id_jadwal'];

        if($detailJadwal->save()) {
            return response([
                'message' => 'Update Detail Jadwal Success',
                'data' => $detailJadwal
            ], 200);
        }
        
        return response([
            'message' => 'Update Detail Jadwal Failed',
            'data' => null
        ], 400);
    }

    public function indexHari($role, $hari)
    {   
        $detailJadwal = DB::table('detail_jadwals')
            ->join('pegawais', 'detail_jadwals.id_pegawai', '=', 'pegawais.id_pegawai')
            ->join('jadwal_pegawais', 'detail_jadwals.id_jadwal', '=', 'jadwal_pegawais.id_jadwal')
            ->select('detail_jadwals.id_detail', 'detail_jadwals.id_pegawai', 'detail_jadwals.id_jadwal', 'pegawais.nama_pegawai', 
            'pegawais.id_role', 'jadwal_pegawais.hari', 'jadwal_pegawais.shift')
            ->where('pegawais.id_role',$role)
            ->where('jadwal_pegawais.hari',$hari)
            ->orderBy('jadwal_pegawais.shift') 
            ->get();

        if(count($detailJadwal)> 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $detailJadwal
            ], 200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ], 400);
    }

    public function searchJadwal($role,$nama)
    {   
        $detailJadwal = DB::table('detail_jadwals')
            ->join('pegawais', 'detail_jadwals.id_pegawai', '=', 'pegawais.id_pegawai')
            ->join('jadwal_pegawais', 'detail_jadwals.id_jadwal', '=', 'jadwal_pegawais.id_jadwal')
            ->select('detail_jadwals.id_detail', 'detail_jadwals.id_pegawai', 'detail_jadwals.id_jadwal', 'pegawais.nama_pegawai', 
                     'jadwal_pegawais.hari', 'jadwal_pegawais.shift')
            ->where('pegawais.nama_pegawai',$nama)
            ->where('pegawais.id_role',$role)
            ->orderBy('jadwal_pegawais.shift') 
            ->get();

        if(count($detailJadwal)> 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $detailJadwal
            ], 200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ], 400);
    }

}
