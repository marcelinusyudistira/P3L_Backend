<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\PegawaiController;
use App\Http\Controllers\Api\DriverController;
use App\Http\Controllers\Api\MobilController;
use App\Http\Controllers\Api\MitraController;
use App\Http\Controllers\Api\PromoController;
use App\Http\Controllers\Api\DetailJadwalController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', 'Api\AuthController@register');
Route::post('login', 'Api\AuthController@login');

Route::group(['middleware' => 'auth:api'], function (){
    Route::get('customer', 'Api\CustomerController@index');
    Route::get('customer/{id}', 'Api\CustomerController@show');
    Route::get('showProfile/{email}', 'Api\CustomerController@showProfile');
    Route::post('customer', 'Api\CustomerController@store');
    Route::put('customer/{id}', 'Api\CustomerController@update');
    Route::post('storeTransaksi/{email}', 'Api\CustomerController@storeTransaksi');
    Route::post('storeWithDriver/{email}', 'Api\CustomerController@storeWithDriver');
    Route::get('showDriver/{tanggal}', 'Api\TransaksiController@showDriver');
    Route::get('showMobil/{tanggal}', 'Api\TransaksiController@showMobil');
    // Route::put('updateTransaksi/{id}', 'Api\CustomerController@updateTransaksi');
    Route::put('uploadBuktiBayar/{id}', 'Api\CustomerController@uploadBuktiBayar');
    Route::get('cekTgl/{id}', 'Api\CustomerController@cekTgl');
});

Route::group(['middleware' => 'auth:api'], function (){
    Route::get('pegawai', 'Api\PegawaiController@index');
    Route::get('pegawai/{id}', 'Api\PegawaiController@show');
    Route::post('pegawai', 'Api\PegawaiController@store');
    Route::put('pegawai/{id}', 'Api\PegawaiController@update');
    Route::delete('pegawai/{id}', 'Api\PegawaiController@destroy');
    Route::put('updateTransaksi/{id}', 'Api\PegawaiController@updateTransaksi');
});

Route::put('verifikasiPenyewaan/{id}', 'Api\PegawaiController@verifikasiPenyewaan');
Route::put('verifikasiPembayaran/{id}', 'Api\PegawaiController@verifikasiPembayaran');

Route::group(['middleware' => 'auth:api'], function (){
    Route::get('driver', 'Api\DriverController@index');
    Route::get('driver/{id}', 'Api\DriverController@show');
    Route::post('driver', 'Api\DriverController@store');
    Route::put('driver/{id}', 'Api\DriverController@update');
    Route::delete('driver/{id}', 'Api\DriverController@destroy');
});

Route::group(['middleware' => 'auth:api'], function (){
    Route::get('mitra', 'Api\MitraController@index');
    Route::get('mitra/{id}', 'Api\MitraController@show');
    Route::post('mitra', 'Api\MitraController@store');
    Route::put('mitra/{id}', 'Api\MitraController@update');
    Route::delete('mitra/{id}', 'Api\MitraController@destroy');
});

Route::group(['middleware' => 'auth:api'], function (){
    Route::get('mobil', 'Api\MobilController@index');
    Route::get('mobil/{id}', 'Api\MobilController@show');
    Route::post('mobil', 'Api\MobilController@store');
    Route::put('mobil/{id}', 'Api\MobilController@update');
    Route::delete('mobil/{id}', 'Api\MobilController@destroy');
    Route::put('perpanjang/{id}', 'Api\MobilController@perpanjangKontrak');
    Route::get('showDedline', 'Api\MobilController@showDedline');
});

Route::group(['middleware' => 'auth:api'], function (){
    Route::get('promo', 'Api\PromoController@index');
    Route::get('promo/{id}', 'Api\PromoController@show');
    Route::post('promo', 'Api\PromoController@store');
    Route::put('promo/{id}', 'Api\PromoController@update');
    Route::delete('promo/{id}', 'Api\PromoController@destroy');
});

Route::group(['middleware' => 'auth:api'], function (){
    Route::get('detailJadwal', 'Api\DetailJadwalController@index');
    Route::get('detailJadwal/{id}', 'Api\DetailJadwalController@show');
    Route::post('detailJadwal', 'Api\DetailJadwalController@store');
    Route::put('detailJadwal/{id}', 'Api\DetailJadwalController@update');
    Route::delete('detailJadwal/{id}', 'Api\DetailJadwalController@destroy');
    Route::get('showPegawai/{role}', 'Api\DetailJadwalController@showPegawai');
    Route::get('indexHari/{role}/{hari}', 'Api\DetailJadwalController@indexHari');
    Route::get('searchJadwal/{role}/{nama}', 'Api\DetailJadwalController@searchJadwal');
});

Route::group(['middleware' => 'auth:api'], function (){
    Route::get('showTransaksi', 'Api\TransaksiController@index');
    Route::get('showTransaksi/{id}', 'Api\TransaksiController@show');
    Route::get('showDriver/{dateStart}/{dateEnd}', 'Api\TransaksiController@showDriver');
    Route::get('showMobil/{dateStart}/{dateEnd}', 'Api\TransaksiController@showMobil');
    Route::get('showPromo', 'Api\TransaksiController@showPromo');
    Route::delete('deleteTransaksi/{id}', 'Api\TransaksiController@destroy');
    Route::get('cekPromo/{tanggalMulai}/{tanggalSelesai}/{id}/{promo_id}', 'Api\TransaksiController@cekPromo');
});

Route::group(['middleware' => 'auth:api'], function (){
    Route::post('logout', 'Api\AuthController@logout');
});