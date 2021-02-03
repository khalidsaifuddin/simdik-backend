<?php

use Illuminate\Http\Request;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
// Route::post('Auth/login', 'PenggunaController@authenticate');
// Route::prefix('Buku')->group(function () {
	// });

Route::prefix('Otentikasi')->group(function () {
	Route::post('masuk', 'PenggunaController@authenticate');
	Route::post('getPengguna', 'PenggunaController@getPengguna');
	Route::post('simpanPengguna', 'PenggunaController@simpanPengguna');
	Route::post('buatPengguna', 'PenggunaController@buatPengguna');
	Route::post('upload', 'PenggunaController@upload');
});

Route::get('/clear-cache', function() {
    Artisan::call('cache:clear');
    return "Cache is cleared";
});

Route::prefix('app')->group(function () {
	Route::post('getWilayah', 'AppController@getWilayah');
	Route::post('getGeoJsonBasic', 'AppController@getGeoJsonBasic');
});

Route::prefix('Sekolah')->group(function() {
	Route::post('getSekolah', 'SekolahController@getSekolah');
	Route::post('getCountSekolah', 'SekolahController@getCountSekolah');
	Route::post('getRekapSekolah', 'SekolahController@getRekapSekolah');
	Route::post('cekKoreg', 'SekolahController@cekKoreg');
});

Route::prefix('PesertaDidik')->group(function() {
	Route::post('getPesertaDidikJenisKelamin', 'PesertaDidikController@getPesertaDidikJenisKelamin');
	Route::post('getPesertaDidikTingkatKelas', 'PesertaDidikController@getPesertaDidikTingkatKelas');
	Route::post('getPesertaDidikTingkatKelasPie', 'PesertaDidikController@getPesertaDidikTingkatKelasPie');
	Route::post('getPesertaDidikUsia', 'PesertaDidikController@getPesertaDidikUsia');
});

Route::prefix('Gtk')->group(function() {
	Route::post('getGtkJenisKelamin', 'GtkController@getGtkJenisKelamin');
	Route::post('getGtkKualifikasi', 'GtkController@getGtkKualifikasi');
	Route::post('getGTKJenisPie', 'GtkController@getGTKJenisPie');
	Route::post('getGtkNUPTK', 'GtkController@getGtkNUPTK');
});

Route::prefix('Sarpras')->group(function() {
	Route::post('getRekapSekolahSarpras', 'SarprasController@getRekapSekolahSarpras');
	Route::post('getRekapSekolahSarprasWilayah', 'SarprasController@getRekapSekolahSarprasWilayah');
	Route::post('getSarprasKerusakanWilayah', 'SarprasController@getSarprasKerusakanWilayah');
	Route::post('getSarprasJenisWilayah', 'SarprasController@getSarprasJenisWilayah');
	Route::post('getSarprasKebutuhanRkbWilayah', 'SarprasController@getSarprasKebutuhanRkbWilayah');

	Route::post('getRekapSarprasRingkasan', 'RekapSarprasController@getRekapSarprasRingkasan');
	Route::post('getRekapSarprasRingkasanSp', 'RekapSarprasController@getRekapSarprasRingkasanSp');
	Route::post('getRekapSarprasTingkatKerusakan', 'RekapSarprasController@getRekapSarprasTingkatKerusakan');
	Route::post('getRekapSarprasTingkatKerusakanSp', 'RekapSarprasController@getRekapSarprasTingkatKerusakanSp');

	Route::get('getRekapSarprasRingkasanExcel', 'RekapSarprasController@getRekapSarprasRingkasanExcel');
	Route::get('getRekapSarprasRingkasanSpExcel', 'RekapSarprasController@getRekapSarprasRingkasanSpExcel');
	Route::get('getRekapSarprasTingkatKerusakanExcel', 'RekapSarprasController@getRekapSarprasTingkatKerusakanExcel');
	Route::get('getRekapSarprasTingkatKerusakanSpExcel', 'RekapSarprasController@getRekapSarprasTingkatKerusakanSpExcel');
});

Route::prefix('RaporDapodik')->group(function() {
	Route::get('unduhExcel', 'RaporDapodikController@unduhExcel');
	Route::post('getRefRaporDapodik', 'RaporDapodikController@getRefRaporDapodik');
	Route::post('getRaporDapodik', 'RaporDapodikController@getRaporDapodik');
	Route::post('getRaporDapodikSekolah', 'RaporDapodikController@getRaporDapodikSekolah');
	Route::post('getRaporDapodikIdentitas', 'RaporDapodikController@getRaporDapodikIdentitas');
	Route::post('getRaporDapodikPD', 'RaporDapodikController@getRaporDapodikPD');
	Route::post('getRaporDapodikPTK', 'RaporDapodikController@getRaporDapodikPTK');
	Route::post('getRaporDapodikRombel', 'RaporDapodikController@getRaporDapodikRombel');
	Route::post('getRaporDapodikSarpras', 'RaporDapodikController@getRaporDapodikSarpras');
	Route::post('getRaporDapodikRadar', 'RaporDapodikController@getRaporDapodikRadar');
	Route::post('getRaporDapodikAkuratRadar', 'RaporDapodikController@getRaporDapodikAkuratRadar');
	Route::post('getRaporDapodikMutakhirRadar', 'RaporDapodikController@getRaporDapodikMutakhirRadar');
	
	Route::get('getRaporDapodikExcel', 'RaporDapodikController@getRaporDapodikExcel');
	Route::get('getRaporDapodikSekolahExcel', 'RaporDapodikController@getRaporDapodikSekolahExcel');
	// Route::post('getRaporDapodikSekolahSp', 'RaporDapodikController@getRaporDapodikSekolahSp');
});

Route::prefix('Sekolah')->group(function() {
	Route::post('getRekapSekolahTotal', 'RekapSekolahController@getRekapSekolahTotal');
	Route::post('getRekapSekolahRingkasan', 'RekapSekolahController@getRekapSekolahRingkasan');
	Route::post('getRekapSekolahRingkasanSp', 'RekapSekolahController@getRekapSekolahRingkasanSp');
	Route::post('getRekapSekolahWaktuPenyelenggaraan', 'RekapSekolahController@getRekapSekolahWaktuPenyelenggaraan');
	Route::post('getRekapSekolahWaktuPenyelenggaraanSp', 'RekapSekolahController@getRekapSekolahWaktuPenyelenggaraanSp');
	Route::post('getRekapSekolahKurikulum', 'RekapSekolahController@getRekapSekolahKurikulum');
	Route::post('getRekapSekolahKurikulumSp', 'RekapSekolahController@getRekapSekolahKurikulumSp');
	Route::post('getRekapSekolahAkreditasi', 'RekapSekolahController@getRekapSekolahAkreditasi');
	Route::post('getRekapSekolahAkreditasiSp', 'RekapSekolahController@getRekapSekolahAkreditasiSp');

	Route::get('getRekapSekolahRingkasanExcel', 'RekapSekolahController@getRekapSekolahRingkasanExcel');
	Route::get('getRekapSekolahRingkasanSpExcel', 'RekapSekolahController@getRekapSekolahRingkasanSpExcel');
	Route::get('getRekapSekolahWaktuPenyelenggaraanExcel', 'RekapSekolahController@getRekapSekolahWaktuPenyelenggaraanExcel');
	Route::get('getRekapSekolahWaktuPenyelenggaraanSpExcel', 'RekapSekolahController@getRekapSekolahWaktuPenyelenggaraanSpExcel');
});


Route::prefix('GTK')->group(function() {
	Route::post('getRekapGTKRingkasan', 'RekapGTKController@getRekapGTKRingkasan');
	Route::post('getRekapGTKRingkasanSp', 'RekapGTKController@getRekapGTKRingkasanSp');
	Route::post('getRekapGTKAgama', 'RekapGTKController@getRekapGTKAgama');
	Route::post('getRekapGTKAgamaSp', 'RekapGTKController@getRekapGTKAgamaSp');

	Route::get('getRekapGTKRingkasanExcel', 'RekapGTKController@getRekapGTKRingkasanExcel');
	Route::get('getRekapGTKRingkasanSpExcel', 'RekapGTKController@getRekapGTKRingkasanSpExcel');
	Route::get('getRekapGTKAgamaExcel', 'RekapGTKController@getRekapGTKAgamaExcel');
	Route::get('getRekapGTKAgamaSpExcel', 'RekapGTKController@getRekapGTKAgamaSpExcel');
});

Route::prefix('PesertaDidik')->group(function() {
	Route::post('getRekapPesertaDidikRingkasan', 'RekapPesertaDidikController@getRekapPesertaDidikRingkasan');
	Route::post('getRekapPesertaDidikRingkasanSp', 'RekapPesertaDidikController@getRekapPesertaDidikRingkasanSp');
	Route::post('getRekapPesertaDidikNISN', 'RekapPesertaDidikController@getRekapPesertaDidikNISN');
	Route::post('getRekapPesertaDidikNISNSp', 'RekapPesertaDidikController@getRekapPesertaDidikNISNSp');
	Route::post('getRekapPesertaDidikTingkat', 'RekapPesertaDidikController@getRekapPesertaDidikRingkasan');
	Route::post('getRekapPesertaDidikTingkatSp', 'RekapPesertaDidikController@getRekapPesertaDidikRingkasanSp');
	Route::post('getRekapPesertaDidikAgama', 'RekapPesertaDidikController@getRekapPesertaDidikAgama');
	Route::post('getRekapPesertaDidikAgamaSp', 'RekapPesertaDidikController@getRekapPesertaDidikAgamaSp');

	Route::get('getRekapPesertaDidikRingkasanExcel', 'RekapPesertaDidikController@getRekapPesertaDidikRingkasanExcel');
	Route::get('getRekapPesertaDidikRingkasanSpExcel', 'RekapPesertaDidikController@getRekapPesertaDidikRingkasanSpExcel');
	Route::get('getRekapPesertaDidikNISNExcel', 'RekapPesertaDidikController@getRekapPesertaDidikNISNExcel');
	Route::get('getRekapPesertaDidikNISNSpExcel', 'RekapPesertaDidikController@getRekapPesertaDidikNISNSpExcel');
	Route::get('getRekapPesertaDidikAgamaExcel', 'RekapPesertaDidikController@getRekapPesertaDidikAgamaExcel');
	Route::get('getRekapPesertaDidikAgamaSpExcel', 'RekapPesertaDidikController@getRekapPesertaDidikAgamaSpExcel');
});


Route::prefix('ValidasiData')->group(function() {
	Route::post('getValidasiData', 'ValidasiDataController@getValidasiData');
	Route::post('getValidasiDataRecord', 'ValidasiDataController@getValidasiDataRecord');
	Route::post('simpanValidasiData', 'ValidasiDataController@simpanValidasiData');
	Route::post('getRekapValidasiBeranda', 'ValidasiDataController@getRekapValidasiBeranda');
});


Route::prefix('CustomQuery')->group(function() {
	Route::post('getKategoriCustomQuery', 'CustomQueryController@getKategoriCustomQuery');
	Route::post('runCustomQuery', 'CustomQueryController@runCustomQuery');
	Route::get('runCustomQueryExcel', 'CustomQueryController@runCustomQueryExcel');
});

Route::prefix('SPM')->group(function() {
	Route::post('InstrumenRoot', 'SPMController@InstrumenRoot');
	Route::get('InstrumenRootExcel', 'SPMController@InstrumenRootExcel');
	Route::post('getSPMKabupaten', 'SPMController@getSPMKabupaten');
	Route::post('getSPMKabupatenPerKecamatan', 'SPMController@getSPMKabupatenPerKecamatan');
	Route::post('getSPMKabupatenPerSekolah', 'SPMController@getSPMKabupatenPerSekolah');
	Route::post('getSPMUsiaSekolah', 'SPMController@getSPMUsiaSekolah');
	Route::post('getSPMLuarWilayah', 'SPMController@getSPMLuarWilayah');
	Route::post('getSPMSatuanPendidikan', 'SPMController@getSPMSatuanPendidikan');
	Route::post('getSPMPendidik', 'SPMController@getSPMPendidik');
	Route::post('getSPMKepsek', 'SPMController@getSPMKepsek');
	Route::post('getSPMTenagaPenunjang', 'SPMController@getSPMTenagaPenunjang');
	Route::post('getAnakTidakSekolah', 'SPMController@getAnakTidakSekolah');
	Route::post('getPDMiskin', 'SPMController@getPDMiskin');
	Route::post('getRekapBerandaSPM', 'SPMController@getRekapBerandaSPM');
	Route::post('simpanVervalAts', 'SPMController@simpanVervalAts');
	Route::post('simpanVervalPDMiskin', 'SPMController@simpanVervalPDMiskin');
	Route::post('getPenerimaSPM', 'SPMController@getPenerimaSPM');
	Route::post('rootRencanaPemenuhanSPM', 'SPMController@rootRencanaPemenuhanSPM');
	Route::post('simpanPenerimaSPM', 'SPMController@simpanPenerimaSPM');
	Route::post('getRencanaPemenuhanSPMFlat', 'SPMController@getRencanaPemenuhanSPMFlat');
	Route::post('simpanRencanaPemenuhanSPM', 'SPMController@simpanRencanaPemenuhanSPM');
	Route::post('tabel21', 'SPMController@tabel21');
	Route::post('getDinas', 'SPMController@getDinas');
	Route::post('simpanTabel21', 'SPMController@simpanTabel21');
	Route::post('tabel31', 'SPMController@tabel31');
	Route::post('simpanTabel31', 'SPMController@simpanTabel31');
	Route::post('tabel41', 'SPMController@tabel41');
	Route::post('tabel42', 'SPMController@tabel42');
	Route::post('tabel43', 'SPMController@tabel43');
	Route::post('simpantabel41', 'SPMController@simpantabel41');
	Route::post('simpantabel42', 'SPMController@simpantabel42');
	Route::post('getIndexPendidikan', 'SPMController@getIndexPendidikan');
	Route::post('simpanIndexPendidikan', 'SPMController@simpanIndexPendidikan');
});


Route::prefix('PMP')->group(function() {
	Route::get('getRekapPMP', 'PMPController@getRekapPMP');
	Route::get('getRekapProgresRaporMutu', 'PMPController@getRekapProgresRaporMutu');
	Route::get('getRekapProgresRaporMutuSp', 'PMPController@getRekapProgresRaporMutuSp');
	Route::get('getRekapPengirimanPMP', 'PMPController@getRekapPengirimanPMP');
	Route::get('getRekapPengirimanPMPSp', 'PMPController@getRekapPengirimanPMPSp');
	Route::post('getRaporSNP', 'PMPController@getRaporSNP');
	Route::get('getTimeline', 'PMPController@getTimeline');
	Route::post('ubahTimeline', 'PMPController@ubahTimeline');
});


Route::prefix('SBB')->group(function() {
	Route::post('getBantuan', 'SBBController@getBantuan');
	Route::post('getDonasi', 'SBBController@getDonasi');
	Route::post('simpanDonasi', 'SBBController@simpanDonasi');
	Route::post('upload', 'SBBController@upload');
});

Route::post('getBuku', 'BukuController@getBuku');
Route::post('getMapel', 'BukuController@getMapel');

Route::middleware('token')->group(function(){
	Route::options('{any}', function($any){ return Response('OK', 200); });
	Route::options('{a}/{b}', function($a, $b){ return Response('OK', 200); });
	Route::options('{a}/{b}/{c}', function($a,$b,$c){ return Response('OK', 200); });
	Route::options('{a}/{b}/{c}/{d}', function($a,$b,$c,$d){ return Response('OK', 200); });
	Route::options('{a}/{b}/{c}/{d}/{e}', function($a,$b,$c,$d,$e){ return Response('OK', 200); });
});

Route::options('{any}', function($any){ return Response('OK', 200); });
Route::options('{a}/{b}', function($a, $b){ return Response('OK', 200); });
Route::options('{a}/{b}/{c}', function($a,$b,$c){ return Response('OK', 200); });
Route::options('{a}/{b}/{c}/{d}', function($a,$b,$c,$d){ return Response('OK', 200); });
Route::options('{a}/{b}/{c}/{d}/{e}', function($a,$b,$c,$d,$e){ return Response('OK', 200); });
