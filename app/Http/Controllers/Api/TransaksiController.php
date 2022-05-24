<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use App\Models\Transaksi;
use App\Models\Customer;
use DB;
use Carbon\Carbon;

class TransaksiController extends Controller
{
    public function index()
    {   
        $transaksi = DB::table('transaksis')
            ->join('pegawais', 'transaksis.id_pegawai', '=', 'pegawais.id_pegawai')
            ->join('customers', 'transaksis.id_customer', '=', 'customers.id_customer')
            ->leftjoin('drivers', 'transaksis.id_driver', '=', 'drivers.id_driver')
            ->leftjoin('promos', 'transaksis.id_promo', '=', 'promos.id_promo')
            ->join('mobils', 'transaksis.id_mobil', '=', 'mobils.id_mobil')
            ->select('transaksis.id_transaksi','transaksis.created_at','transaksis.hari','pegawais.nama_pegawai','customers.nama_customer','drivers.nama_driver','drivers.harga_driver','promos.kode_promo',
                     'mobils.nama_mobil','mobils.harga_sewa','transaksis.tanggal_mulai','transaksis.tanggal_selesai','transaksis.tanggal_kembali',
                     'transaksis.diskon','transaksis.denda','transaksis.sub_total','transaksis.total_biaya','transaksis.metode_pembayaran',
                     'transaksis.status_transaksi','transaksis.bukti_pembayaran','transaksis.rating')
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
            ->join('customers', 'transaksis.id_customer', '=', 'customers.id_customer')
            ->leftjoin('drivers', 'transaksis.id_driver', '=', 'drivers.id_driver')
            ->leftjoin('promos', 'transaksis.id_promo', '=', 'promos.id_promo')
            ->join('mobils', 'transaksis.id_mobil', '=', 'mobils.id_mobil')
            ->select('transaksis.id_transaksi','transaksis.created_at','transaksis.hari','pegawais.nama_pegawai','customers.nama_customer','drivers.nama_driver','drivers.harga_driver','promos.kode_promo',
                     'mobils.nama_mobil','mobils.harga_sewa','transaksis.tanggal_mulai','transaksis.tanggal_selesai','transaksis.tanggal_kembali',
                     'transaksis.diskon','transaksis.denda','transaksis.sub_total','transaksis.total_biaya','transaksis.metode_pembayaran',
                     'transaksis.status_transaksi','transaksis.bukti_pembayaran','transaksis.rating')
            ->where('transaksis.id_customer',$id) //masih error
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

    public function showDriver($dateStart,$dateEnd){
        $tanggalMulai = Carbon::Parse($dateStart)->format('Y-m-d H:i:s');
        $tanggalSelesai = Carbon::Parse($dateEnd)->format('Y-m-d H:i:s');

        $check = DB::table('drivers')
            ->select('drivers.id_driver','drivers.nama_driver','transaksis.tanggal_mulai','transaksis.tanggal_selesai')
            ->leftjoin('transaksis','drivers.id_driver','=','transaksis.id_driver')
            ->where([['transaksis.tanggal_mulai','>',$tanggalMulai],
                    ['transaksis.tanggal_mulai','<',$tanggalSelesai]])
            ->orwhere([['transaksis.tanggal_mulai','<',$tanggalMulai],
                    ['transaksis.tanggal_selesai','>',$tanggalSelesai]])        
            ->orwhere([['transaksis.tanggal_selesai','>',$tanggalMulai],
                    ['transaksis.tanggal_selesai','<',$tanggalSelesai]])
            ->get()->toArray();

        $arrayOfId = array_map(function ($item) {
            return $item->id_driver;
        }, $check);

        $driver = DB::table('drivers')
            ->select('id_driver','nama_driver')
            ->whereNotIn('id_driver', $arrayOfId)
            ->get();
        
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

    public function showMobil($dateStart,$dateEnd){
        $tanggalMulai = Carbon::Parse($dateStart)->format('Y-m-d H:i:s');
        $tanggalSelesai = Carbon::Parse($dateEnd)->format('Y-m-d H:i:s');

        $check = DB::table('mobils')
            ->select('mobils.id_mobil','mobils.nama_mobil','transaksis.tanggal_mulai','transaksis.tanggal_selesai')
            ->leftjoin('transaksis','mobils.id_mobil','=','transaksis.id_mobil')
            ->where([['transaksis.tanggal_mulai','>',$tanggalMulai],
                    ['transaksis.tanggal_mulai','<',$tanggalSelesai]])
            ->orwhere([['transaksis.tanggal_mulai','<',$tanggalMulai],
                    ['transaksis.tanggal_selesai','>',$tanggalSelesai]])        
            ->orwhere([['transaksis.tanggal_selesai','>',$tanggalMulai],
                    ['transaksis.tanggal_selesai','<',$tanggalSelesai]])
            ->get()->toArray();

        $arrayOfId = array_map(function ($item) {
            return $item->id_mobil;
        }, $check);

         $mobil = DB::table('mobils')
            ->select('id_mobil','nama_mobil')
            ->whereNotIn('id_mobil', $arrayOfId)
            ->get();

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

    public function showPromo(){
        $promo = DB::table('promos')
            ->select('id_promo','jenis_promo','potongan_harga')
            ->where('status_promo',1)
            ->get();

        if(count($promo)> 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $promo
            ], 200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ], 400);
    }

    public function destroy($id)
    {
        $transaksi = Transaksi::find($id);

        if(is_null($transaksi)){
            return response([
                'message' => 'Transaksi Not Found',
                'data' => null
            ], 404);
        }

        if($transaksi->delete()){
            return response([
                'message' => 'Delete Transaksi Success',
                'data' => $transaksi
            ], 200);
        }

        return response([
            'message' => 'Delete Transaksi Failed',
            'data' => null
        ], 400);
    }

    public function cekPromo($tanggalMulai,$tanggalSelesai,$id,$promo_id)
    {
        $customer = Customer::find($id);

        if($promo_id == 1){
            $tanggalLahirCust = Carbon::Parse($customer->tanggal_lahir)->format('m-d');
            $tanggalSewaM = Carbon::Parse($tanggalMulai)->format('m-d');
            $tanggalSewaS = Carbon::Parse($tanggalSelesai)->format('m-d');

            if(($tanggalLahirCust >= $tanggalSewaM) && ($tanggalLahirCust <= $tanggalSewaS)){
                return response([
                    'message' => 'Lolos Syarat',
                    'data' => 1
                ], 200);
            }

            return response([
                'message' => 'Tidak Lolos Syarat',
                'data' => 0
            ], 400);
        }
        else if($promo_id == 3){
            $saiki =  Carbon::now();
            $tanggalLahirCust = Carbon::Parse($customer->tanggal_lahir);

            $diff = $tanggalLahirCust->diffInYears($saiki);

            if(($diff >= 17) && ($diff <= 22)){
                return response([
                    'message' => 'Lolos Syarat',
                    'data' => 1
                ], 200);
            }

            return response([
                'message' => 'Tidak Lolos Syarat',
                'data' => 0
            ], 400);
        }
        
    }
}
