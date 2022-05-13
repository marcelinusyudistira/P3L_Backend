<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use App\Models\Transaksi;
use DB;

class TransaksiController extends Controller
{
    public function index()
    {   
        $transaksi = DB::table('transaksis')
            ->join('pegawais', 'transaksis.id_pegawai', '=', 'pegawais.id_pegawai')
            ->join('customers', 'transaksis.id_customer', '=', 'jadwal_pegawais.id_customer')
            ->join('drivers', 'transaksis.id_driver', '=', 'pegawais.id_driver')
            ->join('promos', 'transaksis.id_promo', '=', 'jadwal_pegawais.id_promo')
            ->join('mobils', 'transaksis.id_mobil', '=', 'pegawais.id_mobil')
            ->select('transaksis.*','pegawais.id_pegawai','pegawais.nama_pegawai','customers.id_customer','customers.nama_customer',
                     'drivers.id_driver','drivers.nama_driver','promos.id_promo','promos.kode_promo','promos.jenis_promo','mobils.id_mobil',
                     'mobils.nama_mobil')
            ->get();

        if(count($transaksi)> 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $transaksi
            ], 200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ], 400);
    }

    public function show($id)
    {   
        $transaksi = DB::table('transaksis')
            ->join('pegawais', 'transaksis.id_pegawai', '=', 'pegawais.id_pegawai')
            ->join('customers', 'transaksis.id_customer', '=', 'jadwal_pegawais.id_customer')
            ->join('drivers', 'transaksis.id_driver', '=', 'pegawais.id_driver')
            ->join('promos', 'transaksis.id_promo', '=', 'jadwal_pegawais.id_promo')
            ->join('mobils', 'transaksis.id_mobil', '=', 'pegawais.id_mobil')
            ->select('transaksis.*','pegawais.id_pegawai','pegawais.nama_pegawai','customers.id_customer','custoemrs.nama_customer',
                     'drivers.id_driver','drivers.nama_driver','promos.id_promo','promos.kode_promo','promos.jenis_promo','mobils.id_mobil',
                     'mobils.nama_mobil')
            ->where('id_transaksi',$id)
            ->get();

        if(count($transaksi)> 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $transaksi
            ], 200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ], 400);
    }

    // public function store(Request $request){
    //     $storeData = $request->all();
    //     $validate = Validator::make($storeData, [
    //         'id_mobil' => 'required',
    //         'id_promo' => 'nullable',
    //         'id_driver' => 'nullable',
    //         'tanggal_mulai' => 'required|date',
    //         'tanggal_selesai' => 'required|date',
    //         'metode_pembayaran' => 'required',
    //     ]);

    //     $count= DB::table('transaksis')->count() +1;
    //     $date= Carbon::now()->format('ymd');

    //     if($count <10){
    //         $zero='-00';
    //     }else{$zero='-0';}

    //     if($validate->fails())
    //         return response(['message' => $validate->errors()], 400);
        
    //     $transaksi = Transaksi::create([
    //         'id_transaksi' =>'TRN'.$date.'01'.$zero.$count,
    //         'jabatan' => '5',
    //         'password' => $storeData['password']
    //     ]);

    //     return response([
    //         'message' => 'Add Customer Success',
    //         'data' => $customer
    //     ], 200);
    // }

    public function showDriver($tanggal){
        $driver = DB::table('drivers')
        ->join('transaksis','drivers.id_driver','=','transaksis.id_driver')
        ->select('drivers.id_driver','drivers.nama_drivers')
        ->whereNotBetween($tanggal,['transaksis.tanggal_mulai','transaksis.tanggal_selesai'])->get();

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

    public function showMobil($tanggal){
        $mobil = DB::table('mobils')
        ->join('transaksis','transaksis.id_mobil','=','mobils.id_mobil')
        ->select('mobils.id_mobil','mobils.nama_mobil')
        ->whereNotBetween($tanggal,['transaksis.tanggal_mulai','transaksis.tanggal_selesai'])->get();

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
