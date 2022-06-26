<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use App\Models\Mobil;
use Carbon\Carbon;
use DB;

class MobilController extends Controller
{
    public function index()
    {
        $mobil = Mobil::all();

        if(count($mobil)> 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $mobil
            ], 200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ], 400);
    }

    public function show($id)
    {
        $mobil = Mobil::find($id);

        if(!is_null($mobil)){
            return response([
                'message' => 'Retrieve Mobil Success',
                'data' => $mobil
            ], 200);
        }

        return response([
            'message' => 'Mobil Not Found',
            'data' => null
        ], 404);
    }

    public function store(Request $request){
        $storeData = $request->all();
        $validate = Validator::make($storeData, [
            'id_mitra' => 'nullable',
            'nama_mobil' => 'required',
            'plat_nomor' => 'required|max:20',
            'tipe_mobil' => 'required',
            'transmisi' => 'required',
            'jenis_bbm' => 'required',
            'volume_bbm' => 'required',
            'warna_mobil' => 'required',
            'kapasitas' => 'required',
            'fasilitas' => 'nullable',
            'foto' => 'nullable',
            'tanggal_servis' => 'required|date',
            'harga_sewa' => 'required',
            'no_stnk' => 'required',
            'kategori_aset' => 'required',
            'kontrak_mulai' => 'nullable|date',
            'kontrak_selesai' => 'nullable|date',
        ]);

        if($validate->fails())
            return response(['message' => $validate->errors()], 400);

        if($request->hasFile('foto')) {
            $file = $request->file('foto');
            $image = public_path().'/images/';
            $file -> move($image, $file->getClientOriginalName());
            $image = '/images/'.$file->getClientOriginalName();
        }else
            $image=null;
        
        $storeData['foto'] = $file->getClientOriginalName();

        $mobil = Mobil::create($storeData);

        return response([
            'message' => 'Add Mobil Success',
            'data' => $mobil
        ], 200);
    }

    public function destroy($id)
    {
        $mobil = Mobil::find($id);

        if(is_null($mobil)){
            return response([
                'message' => 'Mobil Not Found',
                'data' => null
            ], 404);
        }

        if($mobil->delete()){
            return response([
                'message' => 'Delete Mobil Success',
                'data' => $mobil
            ], 200);
        }

        return response([
            'message' => 'Delete Mobil Failed',
            'data' => null
        ], 400);
    }

    public function update(Request $request, $id){    
        $mobil = Mobil::find($id);
        
        if(is_null($mobil)){
            return response([
                'message' => 'Mobil Not Found',
                'data' => null
            ], 404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData, [
            'id_mitra' => 'nullable',
            'nama_mobil' => 'required',
            'plat_nomor' => 'required|max:20',
            'tipe_mobil' => 'required',
            'transmisi' => 'required',
            'jenis_bbm' => 'required',
            'volume_bbm' => 'required',
            'warna_mobil' => 'required',
            'kapasitas' => 'required',
            'fasilitas' => 'nullable',
            'foto' => 'nullable',
            'tanggal_servis' => 'required|date',
            'harga_sewa' => 'required',
            'no_stnk' => 'required',
            'kategori_aset' => 'required',
            // 'kontrak_mulai' => 'nullable|date',
            // 'kontrak_selesai' => 'nullable|date',
        ]);

        if($validate->fails())
            return response(['message' => $validate->errors()], 400);

        $mobil->id_mitra = $updateData['id_mitra'];
        $mobil->nama_mobil = $updateData['nama_mobil'];
        $mobil->plat_nomor = $updateData['plat_nomor'];
        $mobil->tipe_mobil = $updateData['tipe_mobil'];
        $mobil->transmisi = $updateData['transmisi'];
        $mobil->jenis_bbm = $updateData['jenis_bbm'];
        $mobil->volume_bbm = $updateData['volume_bbm'];
        $mobil->warna_mobil = $updateData['warna_mobil'];
        $mobil->kapasitas = $updateData['kapasitas'];
        $mobil->fasilitas = $updateData['fasilitas'];
        $mobil->tanggal_servis = $updateData['tanggal_servis'];
        $mobil->harga_sewa = $updateData['harga_sewa'];
        $mobil->no_stnk = $updateData['no_stnk'];
        $mobil->kategori_aset = $updateData['kategori_aset'];
        // $mobil->kontrak_mulai = $updateData['kontrak_mulai'];
        // $mobil->kontrak_selesai = $updateData['kontrak_selesai'];

        if($mobil->save()) {
            return response([
                'message' => 'Update Mobil Success',
                'data' => $mobil
            ], 200);
        }
        
        return response([
            'message' => 'Update Mobil Failed',
            'data' => null
        ], 400);
    }

    public function perpanjangKontrak(Request $request, $id)
    {
       $mobil = Mobil::find($id);
        
        if(is_null($mobil)){
            return response([
                'message' => 'Mobil Not Found',
                'data' => null
            ], 404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData, [
            'kontrak_selesai' => 'nullable|date',
        ]);

        if($validate->fails())
            return response(['message' => $validate->errors()], 400);

        $mobil->kontrak_selesai = $updateData['kontrak_selesai'];

        if($mobil->save()) {
            return response([
                'message' => 'Perpanjang Kontrak Success',
                'data' => $mobil
            ], 200);
        }
        
        return response([
            'message' => 'Perpanjang Kontrak Failed',
            'data' => null
        ], 400);
    }

    public function showDedline()
    {
        $dateNow = Carbon::now()->format('Y-m-d');
        $dateFirst = Carbon::now()->addDays(30)->format('Y-m-d');

        $mobil = DB::table('mobils')
                    ->select('id_mobil','id_mitra','nama_mobil','plat_nomor','tipe_mobil','transmisi',
                    'jenis_bbm','volume_bbm','warna_mobil','kapasitas','foto','fasilitas','tanggal_servis',
                    'harga_sewa','no_stnk','kategori_aset','kontrak_mulai','kontrak_selesai')
                    ->whereBetween('kontrak_selesai',[$dateFirst,$dateNow])->get();

        //Reservation::whereBetween('reservation_from', [$from, $to])->get();

        if(count($mobil)> 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $mobil
            ], 200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ], 400);
    }
}
