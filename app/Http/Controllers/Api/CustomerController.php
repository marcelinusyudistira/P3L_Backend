<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use App\Models\Customer;
use App\Models\Transaksi;
use Carbon\Carbon;
use DB;
use DateTime;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::all();

        if(count($customers)> 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $customers
            ], 200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ], 400);
    }

    public function show($id)
    {
        $customer = Customer::find($id);

        if(!is_null($customer)){
            return response([
                'message' => 'Retrieve Customer Success',
                'data' => $customer
            ], 200);
        }

        return response([
            'message' => 'Customer Not Found',
            'data' => null
        ], 404);
    }

    public function showProfile($email)
    {
        $customer = DB::table('customers')
                    ->select('id_customer','nama_customer','tanggal_lahir','gender','alamat','nomor_telepon',
                    'foto')->where('email',$email)->get();

        if(!is_null($customer)){
            return response([
                'message' => 'Retrieve Customer Success',
                'data' => $customer
            ], 200);
        }

        return response([
            'message' => 'Customer Not Found',
            'data' => null
        ], 404);   
    }

    // public function store(Request $request){
    //     $storeData = $request->all();
    //     $validate = Validator::make($storeData, [
    //         'nama_customer' => 'required|max:60',
    //         'tanggal_lahir' => 'required|date',
    //         'gender' => 'required|max:20',
    //         'alamat' => 'required|max:150',
    //         'nomor_telepon' => 'required|max:14',
    //         'ktp' => 'required',
    //         'sim' => 'required',
    //         'email' => 'required|email:rfc,dns|unique:customers'
    //     ]);

    //     $count= DB::table('customers')->count() +1;
    //     $date= Carbon::now()->format('ymd');

    //     if($validate->fails())
    //         return response(['message' => $validate->errors()], 400);

    //     $customer = Customer::create([
    //         'id_customer' => 'CUS'.$date.'-0'.$count,
    //         'nama_customer' => $request->nama_customer,
    //         'tanggal_lahir' => $request->tanggal_lahir,
    //         'gender' => $request->gender,
    //         'alamat' => $request->alamat,
    //         'nomor_telepon' => $request->nomor_telepon,
    //         'ktp' => $request->ktp,
    //         'sim' => $request->sim,
    //         'email' => $request->email
    //     ]);

    //     return response([
    //         'message' => 'Add Customer Success',
    //         'data' => $customer
    //     ], 200);
    // }

    public function storeTransaksi(Request $request, $email){
        $customer = DB::table('customers')
                    ->select('id_customer','nama_customer')->where('email',$email)->get();
        $id_customer = $customer[0]->id_customer;

        $storeData = $request->all();
        $validate = Validator::make($storeData, [
            'id_pegawai' => 'required',
            'id_mobil' => 'required',
            'id_promo' => 'nullable',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date',
            'tanggal_kembali' => 'nullable',
            'metode_pembayaran' => 'nullable',
            'total_biaya' => 'nullable',
            'denda' => 'nullable',
            'diskon' => 'nullable',
            'rating' => 'nullable',
            'status_transaksi' => 'nullable',
        ]);

        if($validate->fails())
            return response(['message' => $validate->errors()], 400);

        $count= DB::table('transaksis')->count() +1;
        $date= Carbon::now()->format('ymd');
        // $dateTransaksi= Carbon::now()->format('Y-m-d H:i:s');

        if($count <10){
            $zero='-00';
        }else{$zero='-0';}

        //======================= hitung diskon
        if(!empty($request->id_promo)){
            $promo = DB::table('promos')
                ->select('id_promo','kode_promo','potongan_harga')
                ->where('id_promo',$request->id_promo)
                ->get();

            $potonganHarga = $promo[0]->potongan_harga;
        }else{
            $potonganHarga = null;
        }

        //============================ harga mobil
        $mobil = DB::table('mobils')
                ->select('id_mobil','nama_mobil','harga_sewa')
                ->where('id_mobil',$request->id_mobil)
                ->get();
        
        $biayaMobil = $mobil[0]->harga_sewa;

        //============================= lama pinjam
        
        $dateMulai = new DateTime($request->tanggal_mulai);
        $dateSelesai = new DateTime($request->tanggal_selesai);

        $diff = $dateMulai->diff( $dateSelesai );
        $days = $diff->format('%a');

        //============================= total biaya
        $subTotal = $biayaMobil * $days;
        $diskon = $potonganHarga * $subTotal;
        $totalBiaya = $subTotal - $diskon;

        //=============================================
        
        $transaksi = Transaksi::create([
            'id_transaksi' =>'TRN'.$date.'00'.$zero.$count,
            'id_pegawai' => $request->id_pegawai,
            'id_promo' => $request->id_promo,
            'id_customer' => $id_customer,
            'id_mobil' => $request->id_mobil,
            // 'id_driver' => $request->id_driver,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'tanggal_kembali' => $request->tanggal_kembali,
            'metode_pembayaran' => $request->metode_pembayaran,
            'sub_total' => $subTotal,
            'total_biaya' => $totalBiaya,
            'diskon' => $diskon,
            'status_transaksi' => 'Belum Diverifikasi',
            // 'tanggal_transaksi' => $dateTransaksi,
        ]);

        return response([
            'message' => 'Add Transaksi Success',
            'data' => $transaksi
        ], 200);
    }

    public function storeWithDriver(Request $request, $email){
        $customer = DB::table('customers')
                    ->select('id_customer','nama_customer')->where('email',$email)->get();
        $id_customer = $customer[0]->id_customer;

        $storeData = $request->all();
        $validate = Validator::make($storeData, [
            'id_pegawai' => 'required',
            'id_mobil' => 'required',
            'id_promo' => 'nullable',
            'id_driver' => 'required',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date',
            'tanggal_kembali' => 'nullable',
            'metode_pembayaran' => 'nullable',
            'total_biaya' => 'nullable',
            'denda' => 'nullable',
            'diskon' => 'nullable',
            'rating' => 'nullable',
            'status_transaksi' => 'nullable',
        ]);

        if($validate->fails())
            return response(['message' => $validate->errors()], 400);

        $count= DB::table('transaksis')->count() +1;
        $date= Carbon::now()->format('ymd');
        // $dateTransaksi= Carbon::now()->format('Y-m-d H:i:s');

        if($count <10){
            $zero='-00';
        }else{$zero='-0';}

        //======================= hitung diskon
        if(!empty($request->id_promo)){
            $promo = DB::table('promos')
                ->select('id_promo','kode_promo','potongan_harga')
                ->where('id_promo',$request->id_promo)
                ->get();

            $potonganHarga = $promo[0]->potongan_harga;
        }else{
            $potonganHarga = null;
        }

        //============================ harga mobil
        $mobil = DB::table('mobils')
                ->select('id_mobil','nama_mobil','harga_sewa')
                ->where('id_mobil',$request->id_mobil)
                ->get();
        
        $biayaMobil = $mobil[0]->harga_sewa;

        //============================ harga driver
        $driver = DB::table('mobils')
                ->select('id_mobil','nama_mobil','harga_sewa')
                ->where('id_mobil',$request->id_mobil)
                ->get();
        
        $biayaMobil = $mobil[0]->harga_sewa;

        //============================= lama pinjam
        $dateMulai = new DateTime($request->tanggal_mulai);
        $dateSelesai = new DateTime($request->tanggal_selesai);

        $diff = $dateMulai->diff( $dateSelesai );
        $days = $diff->format('%a');

        //============================= total biaya
        $subTotal = $biayaMobil * $days;
        $diskon = $potonganHarga * $subTotal;
        $totalBiaya = $subTotal - $diskon;

        //=============================================
        
        $transaksi = Transaksi::create([
            'id_transaksi' =>'TRN'.$date.'00'.$zero.$count,
            'id_pegawai' => $request->id_pegawai,
            'id_promo' => $request->id_promo,
            'id_customer' => $id_customer,
            'id_mobil' => $request->id_mobil,
            // 'id_driver' => $request->id_driver,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'tanggal_kembali' => $request->tanggal_kembali,
            'metode_pembayaran' => $request->metode_pembayaran,
            'sub_total' => $subTotal,
            'total_biaya' => $totalBiaya,
            'diskon' => $diskon,
            'status_transaksi' => 'Belum Diverifikasi',
            // 'tanggal_transaksi' => $dateTransaksi,
        ]);

        return response([
            'message' => 'Add Transaksi Success',
            'data' => $transaksi
        ], 200);
    }

    public function update(Request $request, $id){
        $customer = Customer::find($id);

        if(is_null($customer)){
            return response([
                'message' => 'Customer Not Found',
                'data' => null
            ], 404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData, [
            'nama_customer' => 'required|max:60',
            'gender' => 'required|max:20',
            'alamat' => 'required|max:150',
            'nomor_telepon' => 'required|max:14',
            'foto' => 'nullable',
            'sim' => 'nullable',
        ]);

        if($request->hasFile('foto')) {
            $file = $request->file('foto');
            $image = public_path().'/images/';
            $file -> move($image, $file->getClientOriginalName());
            $image = '/images/'.$file->getClientOriginalName();
            $customer->foto=$image;
        }else
            $image=null;

        if($request->hasFile('sim')) {
            $fileSim = $request->file('sim');
            $imageSim = public_path().'/images/';
            $fileSim -> move($image, $fileSim->getClientOriginalName());
            $imageSim = '/images/'.$fileSim->getClientOriginalName();
            $customer->sim=$imageSim;
        }else
            $image=null;


        if($validate->fails())
            return response(['message' => $validate->errors()], 400);

        $customer->nama_customer = $updateData['nama_customer'];
        // $customer->tanggal_lahir = $updateData['tanggal_lahir'];
        $customer->gender = $updateData['gender'];
        $customer->alamat = $updateData['alamat'];
        $customer->nomor_telepon = $updateData['nomor_telepon'];
        // $customer->foto = $file->getClientOriginalName();
        // $customer->ktp = $updateData['ktp'];
        // $customer->sim = $fileSim->getClientOriginalName();

        if($customer->save()) {
            return response([
                'message' => 'Update Customer Success',
                'data' => $customer
            ], 200);
        }
        
        return response([
            'message' => 'Update Customer Failed',
            'data' => null
        ], 400);
    }
}
