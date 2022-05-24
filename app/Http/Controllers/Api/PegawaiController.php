<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use App\Models\Pegawai;
use App\Models\User;
use App\Models\Transaksi;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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

    //cek verif pembayaran
    public function updateTransaksi(Request $request, $id){
        $transaksi = Transaksi::find($id);

        if(is_null($transaksi)){
            return response([
                'message' => 'Transaksi Not Found',
                'data' => null
            ], 404);
        }

        if(is_null($transaksi->id_driver)){
            $updateData = $request->all();
            $validate = Validator::make($updateData, [
                'tanggal_kembali' => 'required|date',
            ]);
            
            if($validate->fails())
                return response(['message' => $validate->errors()], 400);

            // ======================== cek denda
            $mobil = DB::table('mobils')
                ->join('transaksis','transaksis.id_mobil','=','mobils.id_mobil')
                ->select('mobils.id_mobil','mobils.nama_mobil','mobils.harga_sewa')
                ->where('mobils.id_mobil',$transaksi->id_mobil)
                ->get();
        
            $biayaDenda = $mobil[0]->harga_sewa;

            $dateSelesai = Carbon::Parse($transaksi->tanggal_selesai);
            $dedlineKembali = $dateSelesai->addHours(3)->format('Y-m-d H:i:s');

            if($request->tanggal_kembali > $dedlineKembali){
                $transaksi->total_biaya = $transaksi->total_biaya + $biayaDenda;
                $transaksi->denda = $biayaDenda;
            }

            $transaksi->tanggal_kembali = $updateData['tanggal_kembali'];

            if($transaksi->save()) {
                return response([
                    'message' => 'Update Transaksi Success',
                    'data' => $transaksi
                ], 200);
            }
            
            return response([
                'message' => 'Update Transaksi Failed',
                'data' => null
            ], 400);
        }else{
            $updateData = $request->all();
            $validate = Validator::make($updateData, [
                'tanggal_kembali' => 'required|date',
            ]);
            
            if($validate->fails())
                return response(['message' => $validate->errors()], 400);

            // ======================== cek denda
            $mobil = DB::table('mobils')
                ->join('transaksis','transaksis.id_mobil','=','mobils.id_mobil')
                ->select('mobils.id_mobil','mobils.nama_mobil','mobils.harga_sewa')
                ->where('mobils.id_mobil',$transaksi->id_mobil)
                ->get();
        
            $biayaDenda = $mobil[0]->harga_sewa;

            $dateSelesai = Carbon::Parse($transaksi->tanggal_selesai);
            $dedlineKembali = $dateSelesai->addHours(3)->format('Y-m-d H:i:s');
            $totalBayar = $transaksi->total_biaya;

            if($request->tanggal_kembali > $dedlineKembali){
                $transaksi->total_biaya = $totalBayar + $biayaDenda;
                $transaksi->denda = $biayaDenda;
            }
            else{
                $transaksi->total_biaya = $totalBayar + 0;
            }

            $transaksi->tanggal_kembali = $updateData['tanggal_kembali'];

            if($transaksi->save()) {
                return response([
                    'message' => 'Update Transaksi Success',
                    'data' => $transaksi
                ], 200);
            }
            
            return response([
                'message' => 'Update Transaksi Failed',
                'data' => null
            ], 400);

            //==========================================
            $driver = DB::table('drivers')
                ->join('transaksis','transaksis.id_driver','=','drivers.id_driver')
                ->select('drivers.id_driver','drivers.nama_driver','drivers.harga_driver')
                ->where('drivers.id_driver',$id)
                ->get();          

            $driver[0]->status_driver = 'Available';

            if($driver->save()) {
                return response([
                    'message' => 'Update Status Driver Success',
                    'data' => $driver
                ], 200);
            }
        }
    }

    public function verifikasiPenyewaan($id){
        $transaksi = Transaksi::find($id);

        if(is_null($transaksi)){
            return response([
                'message' => 'Transaksi tidak ditemukan',
                'data' => null
            ], 404);
        }

        $transaksi->status_transaksi = 'Sedang Berjalan';

        if($transaksi->save()) {
            return response([
                'message' => 'Update Transaksi Success',
                'data' => $transaksi
            ], 200);
        }
        
        return response([
            'message' => 'Update Transaksi Failed',
            'data' => null
        ], 400);
    }

    public function verifikasiPembayaran($id){
        $transaksi = Transaksi::find($id);

        if(is_null($transaksi)){
            return response([
                'message' => 'Transaksi tidak ditemukan',
                'data' => null
            ], 404);
        }

        $transaksi->status_transaksi = 'Selesai';
        
        if($transaksi->save()) {
            return response([
                'message' => 'Update Transaksi Success',
                'data' => $transaksi
            ], 200);
        }
        
        return response([
            'message' => 'Update Transaksi Failed',
            'data' => null
        ], 400);
    }
}
