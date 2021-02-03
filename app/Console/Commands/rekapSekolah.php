<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Console\Commands\Log\guidGenerator;
use App\Console\Commands\Log\logRekapSekolah;

class rekapSekolah extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rekap:sekolah {--semester_id=20191} {--kode_wilayah=000000} {--kode_wilayah_kabupaten=000000} {--jenjang=dikdasmen}  {--tujuan=dikdasmen}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $semester_id = $this->option('semester_id');
        $kode_wilayah = $this->option('kode_wilayah');
        $kode_wilayah_kabupaten = $this->option('kode_wilayah_kabupaten');
        $jenjang = $this->option('jenjang');
        $tujuan = $this->option('tujuan');


        //the log starts here
        // echo guidGenerator::index();die;
        $log_rekap_id = guidGenerator::index();
        $log_total = 0;
        $log_update = 0;
        $log_insert = 0;
        $log_gagal = 0;

        $waktu_mulai = logRekapSekolah::index($log_rekap_id, ($kode_wilayah ? ($kode_wilayah_kabupaten != '000000' ? $kode_wilayah_kabupaten : $kode_wilayah) : '000000'), null, $jenjang);
        // die;
        //the log ends here

        switch ($jenjang) {
            case 'paud':
                $con_string = 'sqlsrv_paud';
                break;
            
            default:
                $con_string = 'sqlsrv';
                break;
        }
        
        switch ($tujuan) {
            case 'nufaza':
                $con_string_tujuan = 'sqlsrv_admin_sa_nufaza';
                break;
            
            default:
                $con_string_tujuan = 'sqlsrv_2';
                break;
        }

        // echo $semester_id;

        // $provinsi = DB::table(DB::raw('ref.mst_wilayah wilayah with(nolock)'))
        $provinsi = DB::connection($con_string)->table(DB::raw('ref.mst_wilayah wilayah with(nolock)'))
        ->where('wilayah.id_level_wilayah','=',1)
        ->whereNull('wilayah.expired_date');

        if($kode_wilayah != '000000'){
            $provinsi = $provinsi->where('kode_wilayah','=',$kode_wilayah);
        }

        $provinsi = $provinsi->get();

        for ($iProvinsi=0; $iProvinsi < sizeof($provinsi); $iProvinsi++) { 
            echo "[INF] [".($iProvinsi+1)."/".sizeof($provinsi)."] ".$provinsi[$iProvinsi]->{'nama'}.PHP_EOL;

            // $kabupaten = DB::table(DB::raw('ref.mst_wilayah wilayah with(nolock)'))
            $kabupaten = DB::connection($con_string)->table(DB::raw('ref.mst_wilayah wilayah with(nolock)'))
            ->where('wilayah.mst_kode_wilayah','=',$provinsi[$iProvinsi]->{'kode_wilayah'})
            ->whereNull('wilayah.expired_date');
            // ->get();

            if($kode_wilayah_kabupaten != '000000'){
                $kabupaten = $kabupaten->where('kode_wilayah','=',$kode_wilayah_kabupaten);
            }

            $kabupaten = $kabupaten->get();

            
            for ($iKabupaten=0; $iKabupaten < sizeof($kabupaten); $iKabupaten++) { 
                echo "[INF] [".($iProvinsi+1)."/".sizeof($provinsi)."] [".($iKabupaten+1)."/".sizeof($kabupaten)."] ".$provinsi[$iProvinsi]->{'nama'}." - ".$kabupaten[$iKabupaten]->{'nama'}.PHP_EOL;
                
                if(!$kode_wilayah_kabupaten){
                    
                    // log
                    $log_rekap_id_kabupaten = guidGenerator::index();
        
                    $waktu_mulai_kabupaten = logRekapSekolah::index($log_rekap_id_kabupaten, $kabupaten[$iKabupaten]->{'kode_wilayah'}, $log_rekap_id, $jenjang);
                    // log

                }
            
                // $kecamatan = DB::table(DB::raw('ref.mst_wilayah wilayah with(nolock)'))
                $kecamatan = DB::connection($con_string)->table(DB::raw('ref.mst_wilayah wilayah with(nolock)'))
                ->where('wilayah.mst_kode_wilayah','=',$kabupaten[$iKabupaten]->{'kode_wilayah'})
                ->whereNull('wilayah.expired_date')
                ->get();

                // $gage = substr($semester_id,4,1);

                for ($iKecamatan=0; $iKecamatan < sizeof($kecamatan); $iKecamatan++) { 
                    echo "[INF] [".($iProvinsi+1)."/".sizeof($provinsi)."] [".($iKabupaten+1)."/".sizeof($kabupaten)."] [".($iKecamatan+1)."/".sizeof($kecamatan)."] ".$provinsi[$iProvinsi]->{'nama'}." - ".$kabupaten[$iKabupaten]->{'nama'}." - ".$kecamatan[$iKecamatan]->{'nama'}.PHP_EOL;
                    
                    switch ($jenjang) {
                        case 'paud':
                            $p_pengguna = "pengguna";
                            $j_role_pengguna = "";
                            $c_role_peran = "pengguna.";
                            $c_role_expired_date = "";
                            $t_prasarana = "prasarana";
                            $c_prasarana_id = "prasarana_id";
                            $query_bangunan_1 = "";
                            $query_bangunan_2 = "";
                            $query_bangunan_3 = "";
                            break;
                        
                        default:
                            $p_pengguna = "man_akses.pengguna";
                            $j_role_pengguna = "JOIN man_akses.role_pengguna role WITH ( nolock ) ON pengguna.pengguna_id = role.pengguna_id";
                            $c_role_peran = "role.";
                            $c_role_expired_date = "AND role.expired_date IS NULL";
                            $t_prasarana = "ruang";
                            $c_prasarana_id = "id_ruang";
                            $query_bangunan_1 = "JOIN bangunan WITH ( nolock ) ON prasarana.id_bangunan = bangunan.id_bangunan
                                                JOIN bangunan_longitudinal WITH ( nolock ) ON bangunan.id_bangunan = bangunan_longitudinal.id_bangunan
                                                AND bangunan_longitudinal.soft_delete = 0 AND bangunan_longitudinal.semester_id = ".$semester_id." ";
                            $query_bangunan_2 = "JOIN bangunan WITH ( nolock ) ON prasarana.id_bangunan = bangunan.id_bangunan
                                                JOIN bangunan_longitudinal WITH ( nolock ) ON bangunan.id_bangunan = bangunan_longitudinal.id_bangunan ";
                            $query_bangunan_3 = "AND bangunan_longitudinal.soft_delete = 0 
                                                AND bangunan_longitudinal.semester_id = ".$semester_id." ";
                            break;
                    }

                    $sql = "
                    select
                        newid() as rekap_sekolah_id,
                        sekolah.sekolah_id,
                        getdate() as tanggal,
                        '".$semester_id."' as semester_id,
                        '".substr($semester_id,0,4)."' as tahun_ajaran_id,
                        sekolah.nama,
                        sekolah.npsn,
                        sekolah.bentuk_pendidikan_id,
                        sekolah.status_sekolah,
                        kec.kode_wilayah as kode_wilayah_kecamatan,
                        kec.nama as kecamatan,
                        kec.mst_kode_wilayah as mst_kode_wilayah_kecamatan,
                        kec.id_level_wilayah as id_level_wilayah_kecamatan,
                        kab.kode_wilayah as kode_wilayah_kabupaten,
                        kab.nama as kabupaten,
                        kab.mst_kode_wilayah as mst_kode_wilayah_kabupaten,
                        kab.id_level_wilayah as id_level_wilayah_kabupaten,
                        prop.kode_wilayah as kode_wilayah_propinsi,
                        prop.nama as propinsi,
                        prop.mst_kode_wilayah as mst_kode_wilayah_propinsi,
                        prop.id_level_wilayah as id_level_wilayah_propinsi,
                        guru.guru,
                        guru.guru_non_induk,
                        guru.pegawai,
                        guru.pegawai_non_induk,
                        jumlah_pd.pd,
                        rombels.rombel,
                        guru.guru_laki,
                        guru.guru_perempuan,
                        guru.pegawai_laki,
                        guru.pegawai_perempuan,
                        jumlah_pd.pd_laki,
                        jumlah_pd.pd_perempuan,
                        sinkron.jumlah_kirim,
                        akred.akreditasi_id,
                        akred.akreditasi_id_str,
                        jumlah_pd.pd_laki_0_tahun,
                        jumlah_pd.pd_laki_1_tahun,
                        jumlah_pd.pd_laki_2_tahun,
                        jumlah_pd.pd_laki_3_tahun,
                        jumlah_pd.pd_laki_4_tahun,
                        jumlah_pd.pd_laki_5_tahun,
                        jumlah_pd.pd_laki_6_tahun,
                        jumlah_pd.pd_laki_7_tahun,
                        jumlah_pd.pd_laki_8_tahun,
                        jumlah_pd.pd_laki_9_tahun,
                        jumlah_pd.pd_laki_10_tahun,
                        jumlah_pd.pd_laki_11_tahun,
                        jumlah_pd.pd_laki_12_tahun,
                        jumlah_pd.pd_laki_13_tahun,
                        jumlah_pd.pd_laki_14_tahun,
                        jumlah_pd.pd_laki_15_tahun,
                        jumlah_pd.pd_laki_16_tahun,
                        jumlah_pd.pd_laki_17_tahun,
                        jumlah_pd.pd_laki_18_tahun,
                        jumlah_pd.pd_laki_19_tahun,
                        jumlah_pd.pd_laki_20_tahun,
                        jumlah_pd.pd_laki_20_tahun_lebih,
                        jumlah_pd.pd_perempuan_0_tahun,
                        jumlah_pd.pd_perempuan_1_tahun,
                        jumlah_pd.pd_perempuan_2_tahun,
                        jumlah_pd.pd_perempuan_3_tahun,
                        jumlah_pd.pd_perempuan_4_tahun,
                        jumlah_pd.pd_perempuan_5_tahun,
                        jumlah_pd.pd_perempuan_6_tahun,
                        jumlah_pd.pd_perempuan_7_tahun,
                        jumlah_pd.pd_perempuan_8_tahun,
                        jumlah_pd.pd_perempuan_9_tahun,
                        jumlah_pd.pd_perempuan_10_tahun,
                        jumlah_pd.pd_perempuan_11_tahun,
                        jumlah_pd.pd_perempuan_12_tahun,
                        jumlah_pd.pd_perempuan_13_tahun,
                        jumlah_pd.pd_perempuan_14_tahun,
                        jumlah_pd.pd_perempuan_15_tahun,
                        jumlah_pd.pd_perempuan_16_tahun,
                        jumlah_pd.pd_perempuan_17_tahun,
                        jumlah_pd.pd_perempuan_18_tahun,
                        jumlah_pd.pd_perempuan_19_tahun,
                        jumlah_pd.pd_perempuan_20_tahun,
                        jumlah_pd.pd_perempuan_20_tahun_lebih,
                        jumlah_pd.pd_laki_islam,  
                        jumlah_pd.pd_laki_kristen,  
                        jumlah_pd.pd_laki_katholik,  
                        jumlah_pd.pd_laki_hindu,  
                        jumlah_pd.pd_laki_budha,  
                        jumlah_pd.pd_laki_konghucu,  
                        jumlah_pd.pd_laki_kepercayaan,  
                        jumlah_pd.pd_laki_lainnya,
                        jumlah_pd.pd_perempuan_islam,  
                        jumlah_pd.pd_perempuan_kristen,  
                        jumlah_pd.pd_perempuan_katholik,  
                        jumlah_pd.pd_perempuan_hindu,  
                        jumlah_pd.pd_perempuan_budha,  
                        jumlah_pd.pd_perempuan_konghucu,  
                        jumlah_pd.pd_perempuan_kepercayaan,  
                        jumlah_pd.pd_perempuan_lainnya,
                        jumlah_pd.pd_laki_agama_tidak_diisi,  
                        jumlah_pd.pd_perempuan_agama_tidak_diisi, 
                        ap.pd_angka_putus_sekolah_laki,
                        ap.pd_angka_putus_sekolah_perempuan,
                        ops.nama as nama_operator,
                        ops.username as email_operator,
                        ops.no_hp as hp_operator,
                        (case when kepsek.nama_kepsek is not null then kepsek.nama_kepsek else (case when plt.nama_kepsek is not null then concat(plt.nama_kepsek,' (PLT Kepsek)') else '(Kepsek belum diisi)' end) end) as nama_kepsek,
                        (case when kepsek.nama_kepsek is not null then kepsek.jenis_kelamin_kepsek else plt.jenis_kelamin_kepsek end) as jenis_kelamin_kepsek,
                        (case when kepsek.nama_kepsek is not null then kepsek.hp_kepsek else plt.hp_kepsek end) as hp_kepsek,
                        jumlah_pd.pd_tingkat_1_laki as pd_kelas_1_laki,
                        jumlah_pd.pd_tingkat_1_perempuan as pd_kelas_1_perempuan,
                        jumlah_pd.pd_tingkat_2_laki as pd_kelas_2_laki,
                        jumlah_pd.pd_tingkat_2_perempuan as pd_kelas_2_perempuan,
                        jumlah_pd.pd_tingkat_3_laki as pd_kelas_3_laki,
                        jumlah_pd.pd_tingkat_3_perempuan as pd_kelas_3_perempuan,
                        jumlah_pd.pd_tingkat_4_laki as pd_kelas_4_laki,
                        jumlah_pd.pd_tingkat_4_perempuan as pd_kelas_4_perempuan,
                        jumlah_pd.pd_tingkat_5_laki as pd_kelas_5_laki,
                        jumlah_pd.pd_tingkat_5_perempuan as pd_kelas_5_perempuan,
                        jumlah_pd.pd_tingkat_6_laki as pd_kelas_6_laki,
                        jumlah_pd.pd_tingkat_6_perempuan as pd_kelas_6_perempuan,
                        jumlah_pd.pd_tingkat_7_laki as pd_kelas_7_laki,
                        jumlah_pd.pd_tingkat_7_perempuan as pd_kelas_7_perempuan,
                        jumlah_pd.pd_tingkat_8_laki as pd_kelas_8_laki,
                        jumlah_pd.pd_tingkat_8_perempuan as pd_kelas_8_perempuan,
                        jumlah_pd.pd_tingkat_9_laki as pd_kelas_9_laki,
                        jumlah_pd.pd_tingkat_9_perempuan as pd_kelas_9_perempuan,
                        jumlah_pd.pd_tingkat_10_laki as pd_kelas_10_laki,
                        jumlah_pd.pd_tingkat_10_perempuan as pd_kelas_10_perempuan,
                        jumlah_pd.pd_tingkat_11_laki as pd_kelas_11_laki,
                        jumlah_pd.pd_tingkat_11_perempuan as pd_kelas_11_perempuan,
                        jumlah_pd.pd_tingkat_12_laki as pd_kelas_12_laki,
                        jumlah_pd.pd_tingkat_12_perempuan as pd_kelas_12_perempuan,
                        jumlah_pd.pd_tingkat_13_laki as pd_kelas_13_laki,
                        jumlah_pd.pd_tingkat_13_perempuan as pd_kelas_13_perempuan,
                        rombels.rombel_tingkat_1 as rombel_kelas_1,
                        rombels.rombel_tingkat_2 as rombel_kelas_2,
                        rombels.rombel_tingkat_3 as rombel_kelas_3,
                        rombels.rombel_tingkat_4 as rombel_kelas_4,
                        rombels.rombel_tingkat_5 as rombel_kelas_5,
                        rombels.rombel_tingkat_6 as rombel_kelas_6,
                        rombels.rombel_tingkat_7 as rombel_kelas_7,
                        rombels.rombel_tingkat_8 as rombel_kelas_8,
                        rombels.rombel_tingkat_9 as rombel_kelas_9,
                        rombels.rombel_tingkat_10 as rombel_kelas_10,
                        rombels.rombel_tingkat_11 as rombel_kelas_11,
                        rombels.rombel_tingkat_12 as rombel_kelas_12,
                        rombels.rombel_tingkat_13 as rombel_kelas_13,
                        jumlah_pd.pd_domisili_kabupaten as pd_domisili_kabupaten,
                        jumlah_pd.pd_domisili_luar_kabupaten as pd_domisili_luar_kabupaten,
                        jumlah_pd.pd_domisili_provinsi as pd_domisili_provinsi,
                        jumlah_pd.pd_domisili_luar_provinsi as pd_domisili_luar_provinsi,
                        (case when rombels.rombel_total_K13 > 0 then 'K13' else 'KTSP' end) as kurikulum,
                        sekolah.mbs as mbs,
                        seklong.waktu_penyelenggaraan_id,
                        seklong.waktu as waktu_penyelenggaraan_id_str,
                        seklong.sumber_listrik_id,
                        seklong.sumber_listrik as sumber_listrik_id_str,
                        seklong.akses_internet_id,
                        seklong.akses_internet as akses_internet_id_str,
                        sekolah.soft_delete,
                        kerusakan_sarpras.ruang_kelas_baik,
                        kerusakan_sarpras.ruang_kelas_rusak_ringan,
                        kerusakan_sarpras.ruang_kelas_rusak_sedang,
                        kerusakan_sarpras.ruang_kelas_rusak_berat,
                        kerusakan_sarpras.ruang_kelas_perlu_dibangun_ulang,
                        kerusakan_sarpras.ruang_kelas_total,
                        kerusakan_sarpras.perpustakaan_baik,
                        kerusakan_sarpras.perpustakaan_rusak_ringan,
                        kerusakan_sarpras.perpustakaan_rusak_sedang,
                        kerusakan_sarpras.perpustakaan_rusak_berat,
                        kerusakan_sarpras.perpustakaan_perlu_dibangun_ulang,
                        kerusakan_sarpras.perpustakaan_total,
                        kerusakan_sarpras.lab_fisika_baik,
                        kerusakan_sarpras.lab_fisika_rusak_ringan,
                        kerusakan_sarpras.lab_fisika_rusak_sedang,
                        kerusakan_sarpras.lab_fisika_rusak_berat,
                        kerusakan_sarpras.lab_fisika_perlu_dibangun_ulang,
                        kerusakan_sarpras.lab_fisika_total,
                        kerusakan_sarpras.lab_kimia_baik,
                        kerusakan_sarpras.lab_kimia_rusak_ringan,
                        kerusakan_sarpras.lab_kimia_rusak_sedang,
                        kerusakan_sarpras.lab_kimia_rusak_berat,
                        kerusakan_sarpras.lab_kimia_perlu_dibangun_ulang,
                        kerusakan_sarpras.lab_kimia_total,
                        kerusakan_sarpras.lab_biologi_baik,
                        kerusakan_sarpras.lab_biologi_rusak_ringan,
                        kerusakan_sarpras.lab_biologi_rusak_sedang,
                        kerusakan_sarpras.lab_biologi_rusak_berat,
                        kerusakan_sarpras.lab_biologi_perlu_dibangun_ulang,
                        kerusakan_sarpras.lab_biologi_total,
                        kerusakan_sarpras.lab_komputer_baik,
                        kerusakan_sarpras.lab_komputer_rusak_ringan,
                        kerusakan_sarpras.lab_komputer_rusak_sedang,
                        kerusakan_sarpras.lab_komputer_rusak_berat,
                        kerusakan_sarpras.lab_komputer_perlu_dibangun_ulang,
                        kerusakan_sarpras.lab_komputer_total,
                        kerusakan_sarpras.lab_bahasa_baik,
                        kerusakan_sarpras.lab_bahasa_rusak_ringan,
                        kerusakan_sarpras.lab_bahasa_rusak_sedang,
                        kerusakan_sarpras.lab_bahasa_rusak_berat,
                        kerusakan_sarpras.lab_bahasa_perlu_dibangun_ulang,
                        kerusakan_sarpras.lab_bahasa_total,
                        kerusakan_sarpras.lab_ips_baik,
                        kerusakan_sarpras.lab_ips_rusak_ringan,
                        kerusakan_sarpras.lab_ips_rusak_sedang,
                        kerusakan_sarpras.lab_ips_rusak_berat,
                        kerusakan_sarpras.lab_ips_perlu_dibangun_ulang,
                        kerusakan_sarpras.lab_ips_total,
                        kerusakan_sarpras.lab_multimedia_baik,
                        kerusakan_sarpras.lab_multimedia_rusak_ringan,
                        kerusakan_sarpras.lab_multimedia_rusak_sedang,
                        kerusakan_sarpras.lab_multimedia_rusak_berat,
                        kerusakan_sarpras.lab_multimedia_perlu_dibangun_ulang,
                        kerusakan_sarpras.lab_multimedia_total,
                        kerusakan_sarpras.ruang_guru_baik,
                        kerusakan_sarpras.ruang_guru_rusak_ringan,
                        kerusakan_sarpras.ruang_guru_rusak_sedang,
                        kerusakan_sarpras.ruang_guru_rusak_berat,
                        kerusakan_sarpras.ruang_guru_perlu_dibangun_ulang,
                        kerusakan_sarpras.ruang_guru_total,
                        kerusakan_sarpras.ruang_kepsek_baik,
                        kerusakan_sarpras.ruang_kepsek_rusak_ringan,
                        kerusakan_sarpras.ruang_kepsek_rusak_sedang,
                        kerusakan_sarpras.ruang_kepsek_rusak_berat,
                        kerusakan_sarpras.ruang_kepsek_perlu_dibangun_ulang,
                        kerusakan_sarpras.ruang_kepsek_total,
                        kerusakan_sarpras.ruang_tu_baik,
                        kerusakan_sarpras.ruang_tu_rusak_ringan,
                        kerusakan_sarpras.ruang_tu_rusak_sedang,
                        kerusakan_sarpras.ruang_tu_rusak_berat,
                        kerusakan_sarpras.ruang_tu_perlu_dibangun_ulang,
                        kerusakan_sarpras.ruang_tu_total,
                        kerusakan_sarpras.ruan_osis_baik,
                        kerusakan_sarpras.ruan_osis_rusak_ringan,
                        kerusakan_sarpras.ruan_osis_rusak_sedang,
                        kerusakan_sarpras.ruan_osis_rusak_berat,
                        kerusakan_sarpras.ruan_osis_perlu_dibangun_ulang,
                        kerusakan_sarpras.ruan_osis_total,
                        kerusakan_sarpras.toilet_guru_baik,
                        kerusakan_sarpras.toilet_guru_rusak_ringan,
                        kerusakan_sarpras.toilet_guru_rusak_sedang,
                        kerusakan_sarpras.toilet_guru_rusak_berat,
                        kerusakan_sarpras.toilet_guru_perlu_dibangun_ulang,
                        kerusakan_sarpras.toilet_guru_total,
                        kerusakan_sarpras.toilet_siswa_laki_baik,
                        kerusakan_sarpras.toilet_siswa_laki_rusak_ringan,
                        kerusakan_sarpras.toilet_siswa_laki_rusak_sedang,
                        kerusakan_sarpras.toilet_siswa_laki_rusak_berat,
                        kerusakan_sarpras.toilet_siswa_laki_perlu_dibangun_ulang,
                        kerusakan_sarpras.toilet_siswa_laki_total,
                        kerusakan_sarpras.toilet_siswa_perempuan_baik,
                        kerusakan_sarpras.toilet_siswa_perempuan_rusak_ringan,
                        kerusakan_sarpras.toilet_siswa_perempuan_rusak_sedang,
                        kerusakan_sarpras.toilet_siswa_perempuan_rusak_berat,
                        kerusakan_sarpras.toilet_siswa_perempuan_perlu_dibangun_ulang,
                        kerusakan_sarpras.toilet_siswa_perempuan_total,
                        kerusakan_sarpras.asrama_siswa_baik,
                        kerusakan_sarpras.asrama_siswa_rusak_ringan,
                        kerusakan_sarpras.asrama_siswa_rusak_sedang,
                        kerusakan_sarpras.asrama_siswa_rusak_berat,
                        kerusakan_sarpras.asrama_siswa_perlu_dibangun_ulang,
                        kerusakan_sarpras.asrama_siswa_total,
                        kerusakan_sarpras.rumah_dinas_guru_baik,
                        kerusakan_sarpras.rumah_dinas_guru_rusak_ringan,
                        kerusakan_sarpras.rumah_dinas_guru_rusak_sedang,
                        kerusakan_sarpras.rumah_dinas_guru_rusak_berat,
                        kerusakan_sarpras.rumah_dinas_guru_perlu_dibangun_ulang,
                        kerusakan_sarpras.rumah_dinas_guru_total,
                        kerusakan_sarpras.uks_baik,
                        kerusakan_sarpras.uks_rusak_ringan,
                        kerusakan_sarpras.uks_rusak_sedang,
                        kerusakan_sarpras.uks_rusak_berat,
                        kerusakan_sarpras.uks_perlu_dibangun_ulang,
                        kerusakan_sarpras.uks_total,
                        kerusakan_sarpras.gudang_baik,
                        kerusakan_sarpras.gudang_rusak_ringan,
                        kerusakan_sarpras.gudang_rusak_sedang,
                        kerusakan_sarpras.gudang_rusak_berat,
                        kerusakan_sarpras.gudang_perlu_dibangun_ulang,
                        kerusakan_sarpras.gudang_total
                    FROM
                        sekolah WITH ( nolock )
                        JOIN ref.bentuk_pendidikan bp WITH ( nolock ) ON bp.bentuk_pendidikan_id = sekolah.bentuk_pendidikan_id
                        LEFT JOIN yayasan WITH ( nolock ) ON yayasan.yayasan_id = sekolah.yayasan_id
                        JOIN ref.mst_wilayah kec WITH ( nolock ) ON kec.kode_wilayah = LEFT ( sekolah.kode_wilayah, 6 )
                        JOIN ref.mst_wilayah kab WITH ( nolock ) ON kab.kode_wilayah = kec.mst_kode_wilayah
                        JOIN ref.mst_wilayah prop WITH ( nolock ) ON prop.kode_wilayah = kab.mst_kode_wilayah
                        LEFT JOIN (
                        SELECT
                            ROW_NUMBER () OVER ( PARTITION BY sekolah_longitudinal.sekolah_id ORDER BY sekolah_longitudinal.semester_id DESC ) AS urutan,
                            sekolah_longitudinal.*,
                            waktu.nama AS waktu,
                            sumber_listrik.nama AS sumber_listrik,
                            akses_internet.nama AS akses_internet 
                        FROM
                            sekolah_longitudinal WITH ( nolock )
                            JOIN ref.waktu_penyelenggaraan waktu WITH ( nolock ) ON waktu.waktu_penyelenggaraan_id = sekolah_longitudinal.waktu_penyelenggaraan_id
                            JOIN ref.sumber_listrik sumber_listrik WITH ( nolock ) ON sumber_listrik.sumber_listrik_id = sekolah_longitudinal.sumber_listrik_id
                            JOIN ref.akses_internet akses_internet WITH ( nolock ) ON akses_internet.akses_internet_id = sekolah_longitudinal.akses_internet_id
                            JOIN sekolah WITH ( nolock ) ON sekolah.sekolah_id = sekolah_longitudinal.sekolah_id 
                        WHERE
                            sekolah_longitudinal.Soft_delete = 0 
                                
                        ) seklong ON seklong.sekolah_id = sekolah.sekolah_id 
                        AND seklong.urutan = 1
                        LEFT JOIN (
                        SELECT
                            ROW_NUMBER () OVER ( PARTITION BY sekolah.sekolah_id ORDER BY akred_sp_tmt DESC ) AS urutan,
                            sekolah.sekolah_id,
                            akreditasi.akreditasi_id,
                            akreditasi.nama AS akreditasi_id_str,
                            akreditasi_sp.akred_sp_sk AS sk_akreditasi,
                            akreditasi_sp.akred_sp_tmt,
                            akreditasi_sp.akred_sp_tst 
                        FROM
                            akreditasi_sp WITH ( nolock )
                            JOIN sekolah WITH ( nolock ) ON sekolah.sekolah_id = akreditasi_sp.sekolah_id
                            JOIN ref.akreditasi akreditasi WITH ( nolock ) ON akreditasi.akreditasi_id = akreditasi_sp.akreditasi_id 
                        WHERE
                            akreditasi_sp.Soft_delete = 0 
                            AND sekolah.Soft_delete = 0 
                                
                        ) AS akred ON akred.sekolah_id = sekolah.sekolah_id 
                        AND akred.urutan = 1
                        LEFT JOIN (
                        SELECT
                            ROW_NUMBER () OVER ( PARTITION BY ptkt.sekolah_id ORDER BY ptkt.tanggal_surat_tugas DESC ) AS urutan,
                            ptkt.sekolah_id,
                            ptk.nama AS nama_kepsek,
                            ptk.jenis_kelamin as jenis_kelamin_kepsek,
                            ptk.nip AS nip_kepsek,
                            ptk.no_hp AS hp_kepsek,
                            ptkt.tanggal_surat_tugas 
                        FROM
                            ptk WITH ( nolock )
                            JOIN ptk_terdaftar ptkt WITH ( nolock ) ON ptkt.ptk_id = ptk.ptk_id 
                            AND ptkt.Soft_delete = 0 
                            AND ptkt.tahun_ajaran_id = ".substr($semester_id,0,4)." 
                            AND ptkt.jenis_keluar_id
                            IS NULL JOIN sekolah WITH ( nolock ) ON sekolah.sekolah_id = ptkt.sekolah_id 
                            AND sekolah.Soft_delete = 0 
                                
                        WHERE
                            ptk.jenis_ptk_id = 20 
                            AND ptk.Soft_delete = 0 
                        ) kepsek ON kepsek.sekolah_id = sekolah.sekolah_id 
                        AND kepsek.urutan = 1
                        LEFT JOIN (
                        SELECT
                            ptkd.sekolah_id,
                            SUM ( CASE WHEN status_kepegawaian_id IN ( 1, 2, 3 ) AND ptkd.ptk_induk = 1 AND ptk.jenis_ptk_id IN ( 3, 4, 5, 6, 12, 13, 14 ) THEN 1 ELSE 0 END ) AS guru_pns,
                            SUM ( CASE WHEN status_kepegawaian_id IN ( 4 ) AND ptkd.ptk_induk = 1 AND ptk.jenis_ptk_id IN ( 3, 4, 5, 6, 12, 13, 14 ) THEN 1 ELSE 0 END ) AS guru_tetap_yayasan,
                            SUM ( CASE WHEN status_kepegawaian_id IN ( 8 ) AND ptkd.ptk_induk = 1 AND ptk.jenis_ptk_id IN ( 3, 4, 5, 6, 12, 13, 14 ) THEN 1 ELSE 0 END ) AS guru_honorer,
                            SUM ( CASE WHEN status_kepegawaian_id NOT IN ( 1, 2, 3, 4, 8 ) AND ptkd.ptk_induk = 1 AND ptk.jenis_ptk_id IN ( 3, 4, 5, 6, 12, 13, 14 ) THEN 1 ELSE 0 END ) AS guru_status_lainnya,
                            SUM ( CASE WHEN ptkd.ptk_induk = 1 AND ptk.jenis_ptk_id IN ( 3, 4, 5, 6, 12, 13, 14 ) THEN 1 ELSE 0 END ) AS guru,
                            SUM ( CASE WHEN ptkd.ptk_induk != 1 AND ptk.jenis_ptk_id IN ( 3, 4, 5, 6, 12, 13, 14 ) THEN 1 ELSE 0 END ) AS guru_non_induk,
                            SUM ( CASE WHEN ptkd.ptk_induk = 1 AND ptk.jenis_ptk_id NOT IN ( 3, 4, 5, 6, 12, 13, 14 ) THEN 1 ELSE 0 END ) AS pegawai,
                            SUM ( CASE WHEN ptkd.ptk_induk != 1 AND ptk.jenis_ptk_id NOT IN ( 3, 4, 5, 6, 12, 13, 14 ) THEN 1 ELSE 0 END ) AS pegawai_non_induk,
                            SUM ( CASE WHEN ptkd.ptk_induk = 1 AND ptk.jenis_ptk_id IN ( 3, 4, 5, 6, 12, 13, 14 ) AND ptk.jenis_kelamin = 'L' THEN 1 ELSE 0 END ) AS guru_laki,
                            SUM ( CASE WHEN ptkd.ptk_induk = 1 AND ptk.jenis_ptk_id IN ( 3, 4, 5, 6, 12, 13, 14 ) AND ptk.jenis_kelamin = 'P' THEN 1 ELSE 0 END ) AS guru_perempuan,
                            SUM ( CASE WHEN ptkd.ptk_induk = 1 AND ptk.jenis_ptk_id NOT IN ( 3, 4, 5, 6, 12, 13, 14 ) AND ptk.jenis_kelamin = 'L' THEN 1 ELSE 0 END ) AS pegawai_laki,
                            SUM ( CASE WHEN ptkd.ptk_induk = 1 AND ptk.jenis_ptk_id NOT IN ( 3, 4, 5, 6, 12, 13, 14 ) AND ptk.jenis_kelamin = 'P' THEN 1 ELSE 0 END ) AS pegawai_perempuan  
                        FROM
                            ptk ptk WITH ( nolock )
                            JOIN ptk_terdaftar ptkd WITH ( nolock ) ON ptk.ptk_id = ptkd.ptk_id
                            JOIN ref.tahun_ajaran ta WITH ( nolock ) ON ta.tahun_ajaran_id = ptkd.tahun_ajaran_id 
                        WHERE
                            ptk.Soft_delete = 0 
                            AND ptkd.Soft_delete = 0 
                            -- AND ptkd.ptk_induk = 1 
                            AND ptkd.tahun_ajaran_id = '".substr($semester_id,0,4)."' 
                            -- AND ptk.jenis_ptk_id IN ( 3, 4, 5, 6, 12, 13, 14 ) 
                            AND ( ptkd.tgl_ptk_keluar > ta.tanggal_selesai OR ptkd.jenis_keluar_id IS NULL ) 
                        GROUP BY
                            ptkd.sekolah_id 
                        ) guru ON guru.sekolah_id = sekolah.sekolah_id
                        LEFT JOIN (
                        SELECT
                            sekolah.sekolah_id,
                            SUM ( 1 ) AS jumlah_kirim,
                            MAX ( begin_sync ) AS tanggal_kirim 
                        FROM
                            sync_log WITH ( nolock )
                            JOIN ".$p_pengguna." pengguna WITH ( nolock ) ON pengguna.pengguna_id = sync_log.pengguna_id
                            JOIN sekolah WITH ( nolock ) ON sekolah.sekolah_id = pengguna.sekolah_id 
                        WHERE
                            alamat_ip != 'PREFILL' 
                            AND is_success = 1 
                                
                            AND begin_sync > '".substr($semester_id,0,4)."-".(substr($semester_id,5,1) == 1 ? '08' : '01')."-01' 
                        GROUP BY
                            sekolah.sekolah_id 
                        ) sinkron ON sinkron.sekolah_id = sekolah.sekolah_id
                        LEFT JOIN (
                        SELECT
                            sekolah.sekolah_id,
                            SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 1 THEN 1 ELSE 0 END ) AS rombel_tingkat_1,
                            SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 1 AND kurikulum.nama_kurikulum LIKE '%KTSP%' THEN 1 ELSE 0 END ) AS rombel_tingkat_1_ktsp,
                            SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 1 AND kurikulum.nama_kurikulum LIKE '%2013%' THEN 1 ELSE 0 END ) AS rombel_tingkat_1_K13,
                            SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 2 THEN 1 ELSE 0 END ) AS rombel_tingkat_2,
                            SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 2 AND kurikulum.nama_kurikulum LIKE '%KTSP%' THEN 1 ELSE 0 END ) AS rombel_tingkat_2_ktsp,
                            SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 2 AND kurikulum.nama_kurikulum LIKE '%2013%' THEN 1 ELSE 0 END ) AS rombel_tingkat_2_K13,
                            SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 3 THEN 1 ELSE 0 END ) AS rombel_tingkat_3,
                            SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 3 AND kurikulum.nama_kurikulum LIKE '%KTSP%' THEN 1 ELSE 0 END ) AS rombel_tingkat_3_ktsp,
                            SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 3 AND kurikulum.nama_kurikulum LIKE '%2013%' THEN 1 ELSE 0 END ) AS rombel_tingkat_3_K13,
                            SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 4 THEN 1 ELSE 0 END ) AS rombel_tingkat_4,
                            SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 4 AND kurikulum.nama_kurikulum LIKE '%KTSP%' THEN 1 ELSE 0 END ) AS rombel_tingkat_4_ktsp,
                            SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 4 AND kurikulum.nama_kurikulum LIKE '%2013%' THEN 1 ELSE 0 END ) AS rombel_tingkat_4_K13,
                            SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 5 THEN 1 ELSE 0 END ) AS rombel_tingkat_5,
                            SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 5 AND kurikulum.nama_kurikulum LIKE '%KTSP%' THEN 1 ELSE 0 END ) AS rombel_tingkat_5_ktsp,
                            SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 5 AND kurikulum.nama_kurikulum LIKE '%2013%' THEN 1 ELSE 0 END ) AS rombel_tingkat_5_K13,
                            SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 6 THEN 1 ELSE 0 END ) AS rombel_tingkat_6,
                            SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 6 AND kurikulum.nama_kurikulum LIKE '%KTSP%' THEN 1 ELSE 0 END ) AS rombel_tingkat_6_ktsp,
                            SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 6 AND kurikulum.nama_kurikulum LIKE '%2013%' THEN 1 ELSE 0 END ) AS rombel_tingkat_6_K13,
                            SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 6 THEN 1 ELSE 0 END ) AS rombel_tingkat_7,
                            SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 6 AND kurikulum.nama_kurikulum LIKE '%KTSP%' THEN 1 ELSE 0 END ) AS rombel_tingkat_7_ktsp,
                            SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 6 AND kurikulum.nama_kurikulum LIKE '%2013%' THEN 1 ELSE 0 END ) AS rombel_tingkat_7_K13,
                            SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 8 THEN 1 ELSE 0 END ) AS rombel_tingkat_8,
                            SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 8 AND kurikulum.nama_kurikulum LIKE '%KTSP%' THEN 1 ELSE 0 END ) AS rombel_tingkat_8_ktsp,
                            SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 8 AND kurikulum.nama_kurikulum LIKE '%2013%' THEN 1 ELSE 0 END ) AS rombel_tingkat_8_K13,
                            SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 9 THEN 1 ELSE 0 END ) AS rombel_tingkat_9,
                            SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 9 AND kurikulum.nama_kurikulum LIKE '%KTSP%' THEN 1 ELSE 0 END ) AS rombel_tingkat_9_ktsp,
                            SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 9 AND kurikulum.nama_kurikulum LIKE '%2013%' THEN 1 ELSE 0 END ) AS rombel_tingkat_9_K13,
                            SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 10 THEN 1 ELSE 0 END ) AS rombel_tingkat_10,
                            SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 10 AND kurikulum.nama_kurikulum LIKE '%KTSP%' THEN 1 ELSE 0 END ) AS rombel_tingkat_10_ktsp,
                            SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 10 AND kurikulum.nama_kurikulum LIKE '%2013%' THEN 1 ELSE 0 END ) AS rombel_tingkat_10_K13,
                            SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 11 THEN 1 ELSE 0 END ) AS rombel_tingkat_11,
                            SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 11 AND kurikulum.nama_kurikulum LIKE '%KTSP%' THEN 1 ELSE 0 END ) AS rombel_tingkat_11_ktsp,
                            SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 11 AND kurikulum.nama_kurikulum LIKE '%2013%' THEN 1 ELSE 0 END ) AS rombel_tingkat_11_K13,
                            SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 12 THEN 1 ELSE 0 END ) AS rombel_tingkat_12,
                            SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 12 AND kurikulum.nama_kurikulum LIKE '%KTSP%' THEN 1 ELSE 0 END ) AS rombel_tingkat_12_ktsp,
                            SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 12 AND kurikulum.nama_kurikulum LIKE '%2013%' THEN 1 ELSE 0 END ) AS rombel_tingkat_12_K13,
                            SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 12 THEN 1 ELSE 0 END ) AS rombel_tingkat_13,
                            SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 12 AND kurikulum.nama_kurikulum LIKE '%KTSP%' THEN 1 ELSE 0 END ) AS rombel_tingkat_13_ktsp,
                            SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 12 AND kurikulum.nama_kurikulum LIKE '%2013%' THEN 1 ELSE 0 END ) AS rombel_tingkat_13_K13,
                            SUM ( 1 ) AS rombel,
                            SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id IN ( 1,2,3,4,5,6,7,8,9,10,11,12,13 ) AND kurikulum.nama_kurikulum LIKE '%KTSP%' THEN 1 ELSE 0 END ) AS rombel_total_ktsp,
                            SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id IN ( 1,2,3,4,5,6,7,8,9,10,11,12,13 ) AND kurikulum.nama_kurikulum LIKE '%2013%' THEN 1 ELSE 0 END ) AS rombel_total_K13 
                        FROM
                            rombongan_belajar WITH ( nolock )
                            JOIN ref.kurikulum kurikulum WITH ( nolock ) ON kurikulum.kurikulum_id = rombongan_belajar.kurikulum_id
                            JOIN sekolah WITH ( nolock ) ON sekolah.sekolah_id = rombongan_belajar.sekolah_id
                            JOIN ref.mst_wilayah kec WITH ( nolock ) ON kec.kode_wilayah = LEFT ( sekolah.kode_wilayah, 6 )
                            JOIN ref.mst_wilayah kab WITH ( nolock ) ON kab.kode_wilayah = kec.mst_kode_wilayah
                            JOIN ref.mst_wilayah prop WITH ( nolock ) ON prop.kode_wilayah = kab.mst_kode_wilayah 
                            WHERE
                            rombongan_belajar.jenis_rombel IN ( 1, 8, 9, 10, 11, 12, 13 ) 
                            AND rombongan_belajar.Soft_delete = 0 
                            AND rombongan_belajar.semester_id = ".$semester_id." 
                            AND kec.kode_wilayah = '".$kecamatan[$iKecamatan]->{'kode_wilayah'}."'
                        GROUP BY
                            rombongan_belajar.sekolah_id,
                            sekolah.sekolah_id,
                            sekolah.nama,
                            sekolah.npsn,
                            prop.nama,
                            kab.nama,
                            kec.nama 
                        ) rombels ON rombels.sekolah_id = sekolah.sekolah_id
                        LEFT JOIN (
                        SELECT
                            prasarana.sekolah_id,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 1 AND ( persentase = 0 ) THEN 1 ELSE 0 END ) ) AS ruang_kelas_baik,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 1 AND ( persentase > 0 AND persentase <= 30 ) THEN 1 ELSE 0 END ) ) AS ruang_kelas_rusak_ringan,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 1 AND ( persentase > 30 AND persentase <= 45 ) THEN 1 ELSE 0 END ) ) AS ruang_kelas_rusak_sedang,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 1 AND ( persentase > 45 AND persentase <= 65 ) THEN 1 ELSE 0 END ) ) AS ruang_kelas_rusak_berat,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 1 AND ( persentase > 65 ) THEN 1 ELSE 0 END ) ) AS ruang_kelas_perlu_dibangun_ulang,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 1 AND ( prasarana.".$c_prasarana_id." IS NOT NULL ) THEN 1 ELSE 0 END ) ) AS ruang_kelas_total,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 10 AND ( persentase = 0 ) THEN 1 ELSE 0 END ) ) AS perpustakaan_baik,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 10 AND ( persentase > 0 AND persentase <= 30 ) THEN 1 ELSE 0 END ) ) AS perpustakaan_rusak_ringan,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 10 AND ( persentase > 30 AND persentase <= 45 ) THEN 1 ELSE 0 END ) ) AS perpustakaan_rusak_sedang,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 10 AND ( persentase > 45 AND persentase <= 65 ) THEN 1 ELSE 0 END ) ) AS perpustakaan_rusak_berat,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 10 AND ( persentase > 65 ) THEN 1 ELSE 0 END ) ) AS perpustakaan_perlu_dibangun_ulang,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 10 AND ( prasarana.".$c_prasarana_id." IS NOT NULL ) THEN 1 ELSE 0 END ) ) AS perpustakaan_total,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 4 AND ( persentase = 0 ) THEN 1 ELSE 0 END ) ) AS lab_fisika_baik,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 4 AND ( persentase > 0 AND persentase <= 30 ) THEN 1 ELSE 0 END ) ) AS lab_fisika_rusak_ringan,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 4 AND ( persentase > 30 AND persentase <= 45 ) THEN 1 ELSE 0 END ) ) AS lab_fisika_rusak_sedang,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 4 AND ( persentase > 45 AND persentase <= 65 ) THEN 1 ELSE 0 END ) ) AS lab_fisika_rusak_berat,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 4 AND ( persentase > 65 ) THEN 1 ELSE 0 END ) ) AS lab_fisika_perlu_dibangun_ulang,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 4 AND ( prasarana.".$c_prasarana_id." IS NOT NULL ) THEN 1 ELSE 0 END ) ) AS lab_fisika_total,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 3 AND ( persentase = 0 ) THEN 1 ELSE 0 END ) ) AS lab_kimia_baik,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 3 AND ( persentase > 0 AND persentase <= 30 ) THEN 1 ELSE 0 END ) ) AS lab_kimia_rusak_ringan,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 3 AND ( persentase > 30 AND persentase <= 45 ) THEN 1 ELSE 0 END ) ) AS lab_kimia_rusak_sedang,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 3 AND ( persentase > 45 AND persentase <= 65 ) THEN 1 ELSE 0 END ) ) AS lab_kimia_rusak_berat,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 3 AND ( persentase > 65 ) THEN 1 ELSE 0 END ) ) AS lab_kimia_perlu_dibangun_ulang,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 3 AND ( prasarana.".$c_prasarana_id." IS NOT NULL ) THEN 1 ELSE 0 END ) ) AS lab_kimia_total,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 5 AND ( persentase = 0 ) THEN 1 ELSE 0 END ) ) AS lab_biologi_baik,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 5 AND ( persentase > 0 AND persentase <= 30 ) THEN 1 ELSE 0 END ) ) AS lab_biologi_rusak_ringan,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 5 AND ( persentase > 30 AND persentase <= 45 ) THEN 1 ELSE 0 END ) ) AS lab_biologi_rusak_sedang,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 5 AND ( persentase > 45 AND persentase <= 65 ) THEN 1 ELSE 0 END ) ) AS lab_biologi_rusak_berat,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 5 AND ( persentase > 65 ) THEN 1 ELSE 0 END ) ) AS lab_biologi_perlu_dibangun_ulang,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 5 AND ( prasarana.".$c_prasarana_id." IS NOT NULL ) THEN 1 ELSE 0 END ) ) AS lab_biologi_total,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 8 AND ( persentase = 0 ) THEN 1 ELSE 0 END ) ) AS lab_komputer_baik,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 8 AND ( persentase > 0 AND persentase <= 30 ) THEN 1 ELSE 0 END ) ) AS lab_komputer_rusak_ringan,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 8 AND ( persentase > 30 AND persentase <= 45 ) THEN 1 ELSE 0 END ) ) AS lab_komputer_rusak_sedang,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 8 AND ( persentase > 45 AND persentase <= 65 ) THEN 1 ELSE 0 END ) ) AS lab_komputer_rusak_berat,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 8 AND ( persentase > 65 ) THEN 1 ELSE 0 END ) ) AS lab_komputer_perlu_dibangun_ulang,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 8 AND ( prasarana.".$c_prasarana_id." IS NOT NULL ) THEN 1 ELSE 0 END ) ) AS lab_komputer_total,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 6 AND ( persentase = 0 ) THEN 1 ELSE 0 END ) ) AS lab_bahasa_baik,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 6 AND ( persentase > 0 AND persentase <= 30 ) THEN 1 ELSE 0 END ) ) AS lab_bahasa_rusak_ringan,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 6 AND ( persentase > 30 AND persentase <= 45 ) THEN 1 ELSE 0 END ) ) AS lab_bahasa_rusak_sedang,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 6 AND ( persentase > 45 AND persentase <= 65 ) THEN 1 ELSE 0 END ) ) AS lab_bahasa_rusak_berat,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 6 AND ( persentase > 65 ) THEN 1 ELSE 0 END ) ) AS lab_bahasa_perlu_dibangun_ulang,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 6 AND ( prasarana.".$c_prasarana_id." IS NOT NULL ) THEN 1 ELSE 0 END ) ) AS lab_bahasa_total,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 7 AND ( persentase = 0 ) THEN 1 ELSE 0 END ) ) AS lab_ips_baik,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 7 AND ( persentase > 0 AND persentase <= 30 ) THEN 1 ELSE 0 END ) ) AS lab_ips_rusak_ringan,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 7 AND ( persentase > 30 AND persentase <= 45 ) THEN 1 ELSE 0 END ) ) AS lab_ips_rusak_sedang,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 7 AND ( persentase > 45 AND persentase <= 65 ) THEN 1 ELSE 0 END ) ) AS lab_ips_rusak_berat,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 7 AND ( persentase > 65 ) THEN 1 ELSE 0 END ) ) AS lab_ips_perlu_dibangun_ulang,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 7 AND ( prasarana.".$c_prasarana_id." IS NOT NULL ) THEN 1 ELSE 0 END ) ) AS lab_ips_total,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 9 AND ( persentase = 0 ) THEN 1 ELSE 0 END ) ) AS lab_multimedia_baik,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 9 AND ( persentase > 0 AND persentase <= 30 ) THEN 1 ELSE 0 END ) ) AS lab_multimedia_rusak_ringan,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 9 AND ( persentase > 30 AND persentase <= 45 ) THEN 1 ELSE 0 END ) ) AS lab_multimedia_rusak_sedang,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 9 AND ( persentase > 45 AND persentase <= 65 ) THEN 1 ELSE 0 END ) ) AS lab_multimedia_rusak_berat,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 9 AND ( persentase > 65 ) THEN 1 ELSE 0 END ) ) AS lab_multimedia_perlu_dibangun_ulang,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 9 AND ( prasarana.".$c_prasarana_id." IS NOT NULL ) THEN 1 ELSE 0 END ) ) AS lab_multimedia_total,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 23 AND ( persentase = 0 ) THEN 1 ELSE 0 END ) ) AS ruang_guru_baik,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 23 AND ( persentase > 0 AND persentase <= 30 ) THEN 1 ELSE 0 END ) ) AS ruang_guru_rusak_ringan,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 23 AND ( persentase > 30 AND persentase <= 45 ) THEN 1 ELSE 0 END ) ) AS ruang_guru_rusak_sedang,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 23 AND ( persentase > 45 AND persentase <= 65 ) THEN 1 ELSE 0 END ) ) AS ruang_guru_rusak_berat,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 23 AND ( persentase > 65 ) THEN 1 ELSE 0 END ) ) AS ruang_guru_perlu_dibangun_ulang,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 23 AND ( prasarana.".$c_prasarana_id." IS NOT NULL ) THEN 1 ELSE 0 END ) ) AS ruang_guru_total,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 22 AND ( persentase = 0 ) THEN 1 ELSE 0 END ) ) AS ruang_kepsek_baik,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 22 AND ( persentase > 0 AND persentase <= 30 ) THEN 1 ELSE 0 END ) ) AS ruang_kepsek_rusak_ringan,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 22 AND ( persentase > 30 AND persentase <= 45 ) THEN 1 ELSE 0 END ) ) AS ruang_kepsek_rusak_sedang,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 22 AND ( persentase > 45 AND persentase <= 65 ) THEN 1 ELSE 0 END ) ) AS ruang_kepsek_rusak_berat,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 22 AND ( persentase > 65 ) THEN 1 ELSE 0 END ) ) AS ruang_kepsek_perlu_dibangun_ulang,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 22 AND ( prasarana.".$c_prasarana_id." IS NOT NULL ) THEN 1 ELSE 0 END ) ) AS ruang_kepsek_total,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 24 AND ( persentase = 0 ) THEN 1 ELSE 0 END ) ) AS ruang_tu_baik,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 24 AND ( persentase > 0 AND persentase <= 30 ) THEN 1 ELSE 0 END ) ) AS ruang_tu_rusak_ringan,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 24 AND ( persentase > 30 AND persentase <= 45 ) THEN 1 ELSE 0 END ) ) AS ruang_tu_rusak_sedang,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 24 AND ( persentase > 45 AND persentase <= 65 ) THEN 1 ELSE 0 END ) ) AS ruang_tu_rusak_berat,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 24 AND ( persentase > 65 ) THEN 1 ELSE 0 END ) ) AS ruang_tu_perlu_dibangun_ulang,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 24 AND ( prasarana.".$c_prasarana_id." IS NOT NULL ) THEN 1 ELSE 0 END ) ) AS ruang_tu_total,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 25 AND ( persentase = 0 ) THEN 1 ELSE 0 END ) ) AS ruan_osis_baik,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 25 AND ( persentase > 0 AND persentase <= 30 ) THEN 1 ELSE 0 END ) ) AS ruan_osis_rusak_ringan,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 25 AND ( persentase > 30 AND persentase <= 45 ) THEN 1 ELSE 0 END ) ) AS ruan_osis_rusak_sedang,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 25 AND ( persentase > 45 AND persentase <= 65 ) THEN 1 ELSE 0 END ) ) AS ruan_osis_rusak_berat,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 25 AND ( persentase > 65 ) THEN 1 ELSE 0 END ) ) AS ruan_osis_perlu_dibangun_ulang,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 25 AND ( prasarana.".$c_prasarana_id." IS NOT NULL ) THEN 1 ELSE 0 END ) ) AS ruan_osis_total,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN ( 26, 27 ) AND ( persentase = 0 ) THEN 1 ELSE 0 END ) ) AS toilet_guru_baik,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN ( 26, 27 ) AND ( persentase > 0 AND persentase <= 30 ) THEN 1 ELSE 0 END ) ) AS toilet_guru_rusak_ringan,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN ( 26, 27 ) AND ( persentase > 30 AND persentase <= 45 ) THEN 1 ELSE 0 END ) ) AS toilet_guru_rusak_sedang,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN ( 26, 27 ) AND ( persentase > 45 AND persentase <= 65 ) THEN 1 ELSE 0 END ) ) AS toilet_guru_rusak_berat,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN ( 26, 27 ) AND ( persentase > 65 ) THEN 1 ELSE 0 END ) ) AS toilet_guru_perlu_dibangun_ulang,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN ( 26, 27 ) AND ( prasarana.".$c_prasarana_id." IS NOT NULL ) THEN 1 ELSE 0 END ) ) AS toilet_guru_total,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN ( 28 ) AND ( persentase = 0 ) THEN 1 ELSE 0 END ) ) AS toilet_siswa_laki_baik,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN ( 28 ) AND ( persentase > 0 AND persentase <= 30 ) THEN 1 ELSE 0 END ) ) AS toilet_siswa_laki_rusak_ringan,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN ( 28 ) AND ( persentase > 30 AND persentase <= 45 ) THEN 1 ELSE 0 END ) ) AS toilet_siswa_laki_rusak_sedang,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN ( 28 ) AND ( persentase > 45 AND persentase <= 65 ) THEN 1 ELSE 0 END ) ) AS toilet_siswa_laki_rusak_berat,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN ( 28 ) AND ( persentase > 65 ) THEN 1 ELSE 0 END ) ) AS toilet_siswa_laki_perlu_dibangun_ulang,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN ( 28 ) AND ( prasarana.".$c_prasarana_id." IS NOT NULL ) THEN 1 ELSE 0 END ) ) AS toilet_siswa_laki_total,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN ( 29 ) AND ( persentase = 0 ) THEN 1 ELSE 0 END ) ) AS toilet_siswa_perempuan_baik,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN ( 29 ) AND ( persentase > 0 AND persentase <= 30 ) THEN 1 ELSE 0 END ) ) AS toilet_siswa_perempuan_rusak_ringan,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN ( 29 ) AND ( persentase > 30 AND persentase <= 45 ) THEN 1 ELSE 0 END ) ) AS toilet_siswa_perempuan_rusak_sedang,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN ( 29 ) AND ( persentase > 45 AND persentase <= 65 ) THEN 1 ELSE 0 END ) ) AS toilet_siswa_perempuan_rusak_berat,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN ( 29 ) AND ( persentase > 65 ) THEN 1 ELSE 0 END ) ) AS toilet_siswa_perempuan_perlu_dibangun_ulang,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN ( 29 ) AND ( prasarana.".$c_prasarana_id." IS NOT NULL ) THEN 1 ELSE 0 END ) ) AS toilet_siswa_perempuan_total,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN ( 37 ) AND ( persentase = 0 ) THEN 1 ELSE 0 END ) ) AS asrama_siswa_baik,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN ( 37 ) AND ( persentase > 0 AND persentase <= 30 ) THEN 1 ELSE 0 END ) ) AS asrama_siswa_rusak_ringan,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN ( 37 ) AND ( persentase > 30 AND persentase <= 45 ) THEN 1 ELSE 0 END ) ) AS asrama_siswa_rusak_sedang,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN ( 37 ) AND ( persentase > 45 AND persentase <= 65 ) THEN 1 ELSE 0 END ) ) AS asrama_siswa_rusak_berat,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN ( 37 ) AND ( persentase > 65 ) THEN 1 ELSE 0 END ) ) AS asrama_siswa_perlu_dibangun_ulang,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN ( 37 ) AND ( prasarana.".$c_prasarana_id." IS NOT NULL ) THEN 1 ELSE 0 END ) ) AS asrama_siswa_total,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN ( 33 ) AND ( persentase = 0 ) THEN 1 ELSE 0 END ) ) AS rumah_dinas_guru_baik,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN ( 33 ) AND ( persentase > 0 AND persentase <= 30 ) THEN 1 ELSE 0 END ) ) AS rumah_dinas_guru_rusak_ringan,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN ( 33 ) AND ( persentase > 30 AND persentase <= 45 ) THEN 1 ELSE 0 END ) ) AS rumah_dinas_guru_rusak_sedang,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN ( 33 ) AND ( persentase > 45 AND persentase <= 65 ) THEN 1 ELSE 0 END ) ) AS rumah_dinas_guru_rusak_berat,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN ( 33 ) AND ( persentase > 65 ) THEN 1 ELSE 0 END ) ) AS rumah_dinas_guru_perlu_dibangun_ulang,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN ( 33 ) AND ( prasarana.".$c_prasarana_id." IS NOT NULL ) THEN 1 ELSE 0 END ) ) AS rumah_dinas_guru_total,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN ( 14 ) AND ( persentase = 0 ) THEN 1 ELSE 0 END ) ) AS uks_baik,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN ( 14 ) AND ( persentase > 0 AND persentase <= 30 ) THEN 1 ELSE 0 END ) ) AS uks_rusak_ringan,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN ( 14 ) AND ( persentase > 30 AND persentase <= 45 ) THEN 1 ELSE 0 END ) ) AS uks_rusak_sedang,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN ( 14 ) AND ( persentase > 45 AND persentase <= 65 ) THEN 1 ELSE 0 END ) ) AS uks_rusak_berat,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN ( 14 ) AND ( persentase > 65 ) THEN 1 ELSE 0 END ) ) AS uks_perlu_dibangun_ulang,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN ( 14 ) AND ( prasarana.".$c_prasarana_id." IS NOT NULL ) THEN 1 ELSE 0 END ) ) AS uks_total,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN ( 30 ) AND ( persentase = 0 ) THEN 1 ELSE 0 END ) ) AS gudang_baik,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN ( 30 ) AND ( persentase > 0 AND persentase <= 30 ) THEN 1 ELSE 0 END ) ) AS gudang_rusak_ringan,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN ( 30 ) AND ( persentase > 30 AND persentase <= 45 ) THEN 1 ELSE 0 END ) ) AS gudang_rusak_sedang,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN ( 30 ) AND ( persentase > 45 AND persentase <= 65 ) THEN 1 ELSE 0 END ) ) AS gudang_rusak_berat,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN ( 30 ) AND ( persentase > 65 ) THEN 1 ELSE 0 END ) ) AS gudang_perlu_dibangun_ulang,
                            ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN ( 30 ) AND ( prasarana.".$c_prasarana_id." IS NOT NULL ) THEN 1 ELSE 0 END ) ) AS gudang_total,
                            '.' AS '-' 
                        FROM
                            ".$t_prasarana." prasarana WITH ( nolock )
                            JOIN ".$t_prasarana."_longitudinal prl WITH ( nolock ) ON prl.".$c_prasarana_id." = prasarana.".$c_prasarana_id."
                            AND prl.soft_delete = 0 AND prl.semester_id = ".$semester_id."
                            ".$query_bangunan_1."
                            LEFT JOIN (
                            SELECT
                                prasarana_longitudinal.".$c_prasarana_id.",
                                round((
                                        ( rusak_pondasi * 12 / 100 ) + ( rusak_sloop_kolom_balok * 25 / 100 ) + ( rusak_plester_struktur * 2 / 100 ) + ( rusak_kudakuda_atap * 5 / 100 ) + ( rusak_kaso_atap * 3.20 / 100 ) + ( rusak_reng_atap * 1.50 / 100 ) + ( rusak_tutup_atap * 2.50 / 100 ) + ( rusak_rangka_plafon * 3 / 100 ) + ( rusak_tutup_plafon * 4 / 100 ) + ( rusak_bata_dinding * 7 / 100 ) + ( rusak_plester_dinding * 2.20 / 100 ) + ( rusak_daun_jendela * 1.60 / 100 ) + ( rusak_daun_pintu * 1.25 / 100 ) + ( rusak_kusen * 2.60 / 100 ) + ( rusak_tutup_lantai * 12 / 100 ) + ( rusak_inst_listrik * 4.75 / 100 ) + ( rusak_inst_air * 2.45 / 100 ) + ( rusak_drainase * 1.50 / 100 ) + ( rusak_finish_struktur * 1 / 100 ) + ( rusak_finish_plafon * 1.20 / 100 ) + ( rusak_finish_dinding * 2.50 / 100 ) + ( rusak_finish_kpj * 1.75 / 100 ) 
                                        ),
                                    2 
                                ) persentase 
                            FROM
                                ".$t_prasarana."_longitudinal prasarana_longitudinal WITH ( nolock )
                                JOIN ".$t_prasarana." prasarana WITH ( nolock ) ON prasarana.".$c_prasarana_id." = prasarana_longitudinal.".$c_prasarana_id."
                                ".$query_bangunan_2."
                            WHERE
                                prasarana_longitudinal.soft_delete = 0 
                                AND prasarana_longitudinal.semester_id = ".$semester_id." 
                                ".$query_bangunan_3."
                            ) persen ON persen.".$c_prasarana_id." = prasarana.".$c_prasarana_id." 
                        WHERE
                            prasarana.soft_delete = 0 
                        GROUP BY
                            prasarana.sekolah_id 
                        ) kerusakan_sarpras ON kerusakan_sarpras.sekolah_id = sekolah.sekolah_id
                        LEFT JOIN (
                        SELECT 
                            sekolah.sekolah_id,
                            SUM ( 1 ) AS pd,
                            SUM ( CASE WHEN peserta_didik.jenis_kelamin = 'L' THEN 1 ELSE 0 END ) AS pd_laki,
                            SUM ( CASE WHEN peserta_didik.jenis_kelamin = 'P' THEN 1 ELSE 0 END ) AS pd_perempuan,
                            SUM ( CASE WHEN peserta_didik.kebutuhan_khusus_id IS NOT NULL AND peserta_didik.kebutuhan_khusus_id != 0 AND peserta_didik.jenis_kelamin = 'L' THEN 1 ELSE 0 END ) AS pd_kebutuhan_khusus_laki,
                            SUM ( CASE WHEN peserta_didik.kebutuhan_khusus_id IS NOT NULL AND peserta_didik.kebutuhan_khusus_id != 0 AND peserta_didik.jenis_kelamin = 'P' THEN 1 ELSE 0 END ) AS pd_kebutuhan_khusus_perempuan,
                            SUM ( CASE WHEN peserta_didik.penerima_kip = 1 THEN 1 ELSE 0 END ) AS penerima_kip,
                            SUM ( CASE WHEN peserta_didik.layak_PIP = 1 THEN 1 ELSE 0 END ) AS layak_pip,
                            SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 1 AND anggota_rombel.jenis_pendaftaran_id = 5 THEN 1 ELSE 0 END ) AS pd_mengulang_1,
                            SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 2 AND anggota_rombel.jenis_pendaftaran_id = 5 THEN 1 ELSE 0 END ) AS pd_mengulang_2,
                            SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 3 AND anggota_rombel.jenis_pendaftaran_id = 5 THEN 1 ELSE 0 END ) AS pd_mengulang_3,
                            SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 4 AND anggota_rombel.jenis_pendaftaran_id = 5 THEN 1 ELSE 0 END ) AS pd_mengulang_4,
                            SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 5 AND anggota_rombel.jenis_pendaftaran_id = 5 THEN 1 ELSE 0 END ) AS pd_mengulang_5,
                            SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 6 AND anggota_rombel.jenis_pendaftaran_id = 5 THEN 1 ELSE 0 END ) AS pd_mengulang_6,
                            SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 7 AND anggota_rombel.jenis_pendaftaran_id = 5 THEN 1 ELSE 0 END ) AS pd_mengulang_7,
                            SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 8 AND anggota_rombel.jenis_pendaftaran_id = 5 THEN 1 ELSE 0 END ) AS pd_mengulang_8,
                            SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 9 AND anggota_rombel.jenis_pendaftaran_id = 5 THEN 1 ELSE 0 END ) AS pd_mengulang_9,
                            SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 10 AND anggota_rombel.jenis_pendaftaran_id = 5 THEN 1 ELSE 0 END ) AS pd_mengulang_10,
                            SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 11 AND anggota_rombel.jenis_pendaftaran_id = 5 THEN 1 ELSE 0 END ) AS pd_mengulang_11,
                            SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 12 AND anggota_rombel.jenis_pendaftaran_id = 5 THEN 1 ELSE 0 END ) AS pd_mengulang_12,
                            SUM ( CASE WHEN anggota_rombel.jenis_pendaftaran_id = 5 THEN 1 ELSE 0 END ) AS pd_mengulang_total,
                            SUM ( CASE WHEN ( DATEDIFF( HOUR, peserta_didik.tanggal_lahir, GETDATE()) / 8766 ) < 16 THEN 1 ELSE 0 END ) AS pd_usia_kurang_16,
                            SUM (
                            CASE
                                    
                                WHEN (
                                    ( DATEDIFF( HOUR, peserta_didik.tanggal_lahir, GETDATE()) / 8766 ) >= 16 
                                    AND ( DATEDIFF( HOUR, peserta_didik.tanggal_lahir, GETDATE()) / 8766 ) <= 18 
                                    ) THEN
                                    1 ELSE 0 
                                END 
                                ) AS pd_usia_16_18,
                                SUM ( CASE WHEN ( DATEDIFF( HOUR, peserta_didik.tanggal_lahir, GETDATE()) / 8766 ) > 18 THEN 1 ELSE 0 END ) AS pd_usia_kurang_18,
                                SUM (
                                CASE
                    
                                WHEN peserta_didik.jenis_kelamin = 'L' 
                                AND ( rombongan_belajar.tingkat_pendidikan_id IN ( 1, 7, 10 ) OR anggota_rombel.jenis_pendaftaran_id = 1 ) THEN
                                1 ELSE 0 
                                END 
                                ) AS pd_baru_laki,
                                SUM (
                                CASE
                    
                                WHEN peserta_didik.jenis_kelamin = 'P' 
                                AND ( rombongan_belajar.tingkat_pendidikan_id IN ( 1, 7, 10 ) OR anggota_rombel.jenis_pendaftaran_id = 1 ) THEN
                                1 ELSE 0 
                                END 
                                ) AS pd_baru_perempuan,
                                SUM (
                                CASE
                    
                                WHEN peserta_didik.jenis_kelamin IN ( 'L', 'P' ) 
                                AND ( rombongan_belajar.tingkat_pendidikan_id IN ( 1, 7, 10 ) OR anggota_rombel.jenis_pendaftaran_id = 1 ) THEN
                                1 ELSE 0 
                                END 
                                ) AS pd_baru_total,
                                SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 1 THEN 1 ELSE 0 END ) AS pd_tingkat_1_total,
                                SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 1 AND peserta_didik.jenis_kelamin = 'L' THEN 1 ELSE 0 END ) AS pd_tingkat_1_laki,
                                SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 1 AND peserta_didik.jenis_kelamin = 'P' THEN 1 ELSE 0 END ) AS pd_tingkat_1_perempuan,
                                SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 2 THEN 1 ELSE 0 END ) AS pd_tingkat_2_total,
                                SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 2 AND peserta_didik.jenis_kelamin = 'L' THEN 1 ELSE 0 END ) AS pd_tingkat_2_laki,
                                SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 2 AND peserta_didik.jenis_kelamin = 'P' THEN 1 ELSE 0 END ) AS pd_tingkat_2_perempuan,
                                SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 3 THEN 1 ELSE 0 END ) AS pd_tingkat_3_total,
                                SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 3 AND peserta_didik.jenis_kelamin = 'L' THEN 1 ELSE 0 END ) AS pd_tingkat_3_laki,
                                SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 3 AND peserta_didik.jenis_kelamin = 'P' THEN 1 ELSE 0 END ) AS pd_tingkat_3_perempuan,
                                SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 4 THEN 1 ELSE 0 END ) AS pd_tingkat_4_total,
                                SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 4 AND peserta_didik.jenis_kelamin = 'L' THEN 1 ELSE 0 END ) AS pd_tingkat_4_laki,
                                SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 4 AND peserta_didik.jenis_kelamin = 'P' THEN 1 ELSE 0 END ) AS pd_tingkat_4_perempuan,
                                SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 5 THEN 1 ELSE 0 END ) AS pd_tingkat_5_total,
                                SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 5 AND peserta_didik.jenis_kelamin = 'L' THEN 1 ELSE 0 END ) AS pd_tingkat_5_laki,
                                SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 5 AND peserta_didik.jenis_kelamin = 'P' THEN 1 ELSE 0 END ) AS pd_tingkat_5_perempuan,
                                SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 6 THEN 1 ELSE 0 END ) AS pd_tingkat_6_total,
                                SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 6 AND peserta_didik.jenis_kelamin = 'L' THEN 1 ELSE 0 END ) AS pd_tingkat_6_laki,
                                SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 6 AND peserta_didik.jenis_kelamin = 'P' THEN 1 ELSE 0 END ) AS pd_tingkat_6_perempuan,
                                SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 7 THEN 1 ELSE 0 END ) AS pd_tingkat_7_total,
                                SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 7 AND peserta_didik.jenis_kelamin = 'L' THEN 1 ELSE 0 END ) AS pd_tingkat_7_laki,
                                SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 7 AND peserta_didik.jenis_kelamin = 'P' THEN 1 ELSE 0 END ) AS pd_tingkat_7_perempuan,
                                SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 8 THEN 1 ELSE 0 END ) AS pd_tingkat_8_total,
                                SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 8 AND peserta_didik.jenis_kelamin = 'L' THEN 1 ELSE 0 END ) AS pd_tingkat_8_laki,
                                SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 8 AND peserta_didik.jenis_kelamin = 'P' THEN 1 ELSE 0 END ) AS pd_tingkat_8_perempuan,
                                SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 9 THEN 1 ELSE 0 END ) AS pd_tingkat_9_total,
                                SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 9 AND peserta_didik.jenis_kelamin = 'L' THEN 1 ELSE 0 END ) AS pd_tingkat_9_laki,
                                SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 9 AND peserta_didik.jenis_kelamin = 'P' THEN 1 ELSE 0 END ) AS pd_tingkat_9_perempuan,
                                SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 10 THEN 1 ELSE 0 END ) AS pd_tingkat_10_total,
                                SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 10 AND peserta_didik.jenis_kelamin = 'L' THEN 1 ELSE 0 END ) AS pd_tingkat_10_laki,
                                SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 10 AND peserta_didik.jenis_kelamin = 'P' THEN 1 ELSE 0 END ) AS pd_tingkat_10_perempuan,
                                SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 11 THEN 1 ELSE 0 END ) AS pd_tingkat_11,
                                SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 11 AND peserta_didik.jenis_kelamin = 'L' THEN 1 ELSE 0 END ) AS pd_tingkat_11_laki,
                                SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 11 AND peserta_didik.jenis_kelamin = 'P' THEN 1 ELSE 0 END ) AS pd_tingkat_11_perempuan,
                                SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 12 THEN 1 ELSE 0 END ) AS pd_tingkat_12,
                                SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 12 AND peserta_didik.jenis_kelamin = 'L' THEN 1 ELSE 0 END ) AS pd_tingkat_12_laki,
                                SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 12 AND peserta_didik.jenis_kelamin = 'P' THEN 1 ELSE 0 END ) AS pd_tingkat_12_perempuan,
                                SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 12 THEN 1 ELSE 0 END ) AS pd_tingkat_13,
                                SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 12 AND peserta_didik.jenis_kelamin = 'L' THEN 1 ELSE 0 END ) AS pd_tingkat_13_laki,
                                SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 12 AND peserta_didik.jenis_kelamin = 'P' THEN 1 ELSE 0 END ) AS pd_tingkat_13_perempuan,
                                SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 10 AND kurikulum.nama_kurikulum LIKE '%umum%' THEN 1 ELSE 0 END ) AS pd_tingkat_10_umum,
                                SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 10 AND kurikulum.nama_kurikulum LIKE '%IPA%' THEN 1 ELSE 0 END ) AS pd_tingkat_10_ipa,
                                SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 10 AND kurikulum.nama_kurikulum LIKE '%IPS%' THEN 1 ELSE 0 END ) AS pd_tingkat_10_ips,
                                SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 10 AND kurikulum.nama_kurikulum LIKE '%bahasa%' THEN 1 ELSE 0 END ) AS pd_tingkat_10_bahasa,
                                SUM (
                                CASE
                    
                                WHEN (
                                rombongan_belajar.tingkat_pendidikan_id = 10 
                                AND ( kurikulum.nama_kurikulum NOT LIKE '%umum%' AND kurikulum.nama_kurikulum NOT LIKE '%IPA%' AND kurikulum.nama_kurikulum NOT LIKE '%IPS%' AND kurikulum.nama_kurikulum NOT LIKE '%bahasa%' ) 
                                ) THEN
                                1 ELSE 0 
                                END 
                                ) AS pd_tingkat_10_jurusan_lain,
                                SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 11 AND kurikulum.nama_kurikulum LIKE '%umum%' THEN 1 ELSE 0 END ) AS pd_tingkat_11_umum,
                                SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 11 AND kurikulum.nama_kurikulum LIKE '%IPA%' THEN 1 ELSE 0 END ) AS pd_tingkat_11_ipa,
                                SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 11 AND kurikulum.nama_kurikulum LIKE '%IPS%' THEN 1 ELSE 0 END ) AS pd_tingkat_11_ips,
                                SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 11 AND kurikulum.nama_kurikulum LIKE '%bahasa%' THEN 1 ELSE 0 END ) AS pd_tingkat_11_bahasa,
                                SUM (
                                CASE
                    
                                WHEN (
                                    rombongan_belajar.tingkat_pendidikan_id = 11 
                                    AND ( kurikulum.nama_kurikulum NOT LIKE '%umum%' AND kurikulum.nama_kurikulum NOT LIKE '%IPA%' AND kurikulum.nama_kurikulum NOT LIKE '%IPS%' AND kurikulum.nama_kurikulum NOT LIKE '%bahasa%' ) 
                                    ) THEN
                                    1 ELSE 0 
                                END 
                                ) AS pd_tingkat_11_jurusan_lain,
                                SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 12 AND kurikulum.nama_kurikulum LIKE '%umum%' THEN 1 ELSE 0 END ) AS pd_tingkat_12_umum,
                                SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 12 AND kurikulum.nama_kurikulum LIKE '%IPA%' THEN 1 ELSE 0 END ) AS pd_tingkat_12_ipa,
                                SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 12 AND kurikulum.nama_kurikulum LIKE '%IPS%' THEN 1 ELSE 0 END ) AS pd_tingkat_12_ips,
                                SUM ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id = 12 AND kurikulum.nama_kurikulum LIKE '%bahasa%' THEN 1 ELSE 0 END ) AS pd_tingkat_12_bahasa,
                                SUM (
                                CASE
                            WHEN (
                                rombongan_belajar.tingkat_pendidikan_id = 12 
                                AND ( kurikulum.nama_kurikulum NOT LIKE '%umum%' AND kurikulum.nama_kurikulum NOT LIKE '%IPA%' AND kurikulum.nama_kurikulum NOT LIKE '%IPS%' AND kurikulum.nama_kurikulum NOT LIKE '%bahasa%' ) 
                                ) THEN
                                1 ELSE 0 
                            END 
                            ) AS pd_tingkat_12_jurusan_lain,
                            SUM ( CASE WHEN ( DATEDIFF( HOUR, peserta_didik.tanggal_lahir, GETDATE()) / 8766 ) = 0 AND peserta_didik.jenis_kelamin = 'L' then 1 else 0 end ) as pd_laki_0_tahun, 
                            SUM ( CASE WHEN ( DATEDIFF( HOUR, peserta_didik.tanggal_lahir, GETDATE()) / 8766 ) = 1 AND peserta_didik.jenis_kelamin = 'L' then 1 else 0 end ) as pd_laki_1_tahun, 
                            SUM ( CASE WHEN ( DATEDIFF( HOUR, peserta_didik.tanggal_lahir, GETDATE()) / 8766 ) = 2 AND peserta_didik.jenis_kelamin = 'L' then 1 else 0 end ) as pd_laki_2_tahun, 
                            SUM ( CASE WHEN ( DATEDIFF( HOUR, peserta_didik.tanggal_lahir, GETDATE()) / 8766 ) = 3 AND peserta_didik.jenis_kelamin = 'L' then 1 else 0 end ) as pd_laki_3_tahun, 
                            SUM ( CASE WHEN ( DATEDIFF( HOUR, peserta_didik.tanggal_lahir, GETDATE()) / 8766 ) = 4 AND peserta_didik.jenis_kelamin = 'L' then 1 else 0 end ) as pd_laki_4_tahun, 
                            SUM ( CASE WHEN ( DATEDIFF( HOUR, peserta_didik.tanggal_lahir, GETDATE()) / 8766 ) = 5 AND peserta_didik.jenis_kelamin = 'L' then 1 else 0 end ) as pd_laki_5_tahun, 
                            SUM ( CASE WHEN ( DATEDIFF( HOUR, peserta_didik.tanggal_lahir, GETDATE()) / 8766 ) = 6 AND peserta_didik.jenis_kelamin = 'L' then 1 else 0 end ) as pd_laki_6_tahun, 
                            SUM ( CASE WHEN ( DATEDIFF( HOUR, peserta_didik.tanggal_lahir, GETDATE()) / 8766 ) = 7 AND peserta_didik.jenis_kelamin = 'L' then 1 else 0 end ) as pd_laki_7_tahun, 
                            SUM ( CASE WHEN ( DATEDIFF( HOUR, peserta_didik.tanggal_lahir, GETDATE()) / 8766 ) = 8 AND peserta_didik.jenis_kelamin = 'L' then 1 else 0 end ) as pd_laki_8_tahun, 
                            SUM ( CASE WHEN ( DATEDIFF( HOUR, peserta_didik.tanggal_lahir, GETDATE()) / 8766 ) = 9 AND peserta_didik.jenis_kelamin = 'L' then 1 else 0 end ) as pd_laki_9_tahun, 
                            SUM ( CASE WHEN ( DATEDIFF( HOUR, peserta_didik.tanggal_lahir, GETDATE()) / 8766 ) = 10 AND peserta_didik.jenis_kelamin = 'L' then 1 else 0 end ) as pd_laki_10_tahun, 
                            SUM ( CASE WHEN ( DATEDIFF( HOUR, peserta_didik.tanggal_lahir, GETDATE()) / 8766 ) = 11 AND peserta_didik.jenis_kelamin = 'L' then 1 else 0 end ) as pd_laki_11_tahun, 
                            SUM ( CASE WHEN ( DATEDIFF( HOUR, peserta_didik.tanggal_lahir, GETDATE()) / 8766 ) = 12 AND peserta_didik.jenis_kelamin = 'L' then 1 else 0 end ) as pd_laki_12_tahun, 
                            SUM ( CASE WHEN ( DATEDIFF( HOUR, peserta_didik.tanggal_lahir, GETDATE()) / 8766 ) = 13 AND peserta_didik.jenis_kelamin = 'L' then 1 else 0 end ) as pd_laki_13_tahun, 
                            SUM ( CASE WHEN ( DATEDIFF( HOUR, peserta_didik.tanggal_lahir, GETDATE()) / 8766 ) = 14 AND peserta_didik.jenis_kelamin = 'L' then 1 else 0 end ) as pd_laki_14_tahun, 
                            SUM ( CASE WHEN ( DATEDIFF( HOUR, peserta_didik.tanggal_lahir, GETDATE()) / 8766 ) = 15 AND peserta_didik.jenis_kelamin = 'L' then 1 else 0 end ) as pd_laki_15_tahun, 
                            SUM ( CASE WHEN ( DATEDIFF( HOUR, peserta_didik.tanggal_lahir, GETDATE()) / 8766 ) = 16 AND peserta_didik.jenis_kelamin = 'L' then 1 else 0 end ) as pd_laki_16_tahun, 
                            SUM ( CASE WHEN ( DATEDIFF( HOUR, peserta_didik.tanggal_lahir, GETDATE()) / 8766 ) = 17 AND peserta_didik.jenis_kelamin = 'L' then 1 else 0 end ) as pd_laki_17_tahun, 
                            SUM ( CASE WHEN ( DATEDIFF( HOUR, peserta_didik.tanggal_lahir, GETDATE()) / 8766 ) = 18 AND peserta_didik.jenis_kelamin = 'L' then 1 else 0 end ) as pd_laki_18_tahun, 
                            SUM ( CASE WHEN ( DATEDIFF( HOUR, peserta_didik.tanggal_lahir, GETDATE()) / 8766 ) = 19 AND peserta_didik.jenis_kelamin = 'L' then 1 else 0 end ) as pd_laki_19_tahun, 
                            SUM ( CASE WHEN ( DATEDIFF( HOUR, peserta_didik.tanggal_lahir, GETDATE()) / 8766 ) = 20 AND peserta_didik.jenis_kelamin = 'L' then 1 else 0 end ) as pd_laki_20_tahun, 
                            SUM ( CASE WHEN ( DATEDIFF( HOUR, peserta_didik.tanggal_lahir, GETDATE()) / 8766 ) > 20 AND peserta_didik.jenis_kelamin = 'L' then 1 else 0 end ) as pd_laki_20_tahun_lebih, 
                            SUM ( CASE WHEN ( DATEDIFF( HOUR, peserta_didik.tanggal_lahir, GETDATE()) / 8766 ) = 0 AND peserta_didik.jenis_kelamin = 'P' then 1 else 0 end ) as pd_perempuan_0_tahun,
                            SUM ( CASE WHEN ( DATEDIFF( HOUR, peserta_didik.tanggal_lahir, GETDATE()) / 8766 ) = 1 AND peserta_didik.jenis_kelamin = 'P' then 1 else 0 end ) as pd_perempuan_1_tahun, 
                            SUM ( CASE WHEN ( DATEDIFF( HOUR, peserta_didik.tanggal_lahir, GETDATE()) / 8766 ) = 2 AND peserta_didik.jenis_kelamin = 'P' then 1 else 0 end ) as pd_perempuan_2_tahun, 
                            SUM ( CASE WHEN ( DATEDIFF( HOUR, peserta_didik.tanggal_lahir, GETDATE()) / 8766 ) = 3 AND peserta_didik.jenis_kelamin = 'P' then 1 else 0 end ) as pd_perempuan_3_tahun, 
                            SUM ( CASE WHEN ( DATEDIFF( HOUR, peserta_didik.tanggal_lahir, GETDATE()) / 8766 ) = 4 AND peserta_didik.jenis_kelamin = 'P' then 1 else 0 end ) as pd_perempuan_4_tahun, 
                            SUM ( CASE WHEN ( DATEDIFF( HOUR, peserta_didik.tanggal_lahir, GETDATE()) / 8766 ) = 5 AND peserta_didik.jenis_kelamin = 'P' then 1 else 0 end ) as pd_perempuan_5_tahun, 
                            SUM ( CASE WHEN ( DATEDIFF( HOUR, peserta_didik.tanggal_lahir, GETDATE()) / 8766 ) = 6 AND peserta_didik.jenis_kelamin = 'P' then 1 else 0 end ) as pd_perempuan_6_tahun, 
                            SUM ( CASE WHEN ( DATEDIFF( HOUR, peserta_didik.tanggal_lahir, GETDATE()) / 8766 ) = 7 AND peserta_didik.jenis_kelamin = 'P' then 1 else 0 end ) as pd_perempuan_7_tahun, 
                            SUM ( CASE WHEN ( DATEDIFF( HOUR, peserta_didik.tanggal_lahir, GETDATE()) / 8766 ) = 8 AND peserta_didik.jenis_kelamin = 'P' then 1 else 0 end ) as pd_perempuan_8_tahun, 
                            SUM ( CASE WHEN ( DATEDIFF( HOUR, peserta_didik.tanggal_lahir, GETDATE()) / 8766 ) = 9 AND peserta_didik.jenis_kelamin = 'P' then 1 else 0 end ) as pd_perempuan_9_tahun, 
                            SUM ( CASE WHEN ( DATEDIFF( HOUR, peserta_didik.tanggal_lahir, GETDATE()) / 8766 ) = 10 AND peserta_didik.jenis_kelamin = 'P' then 1 else 0 end ) as pd_perempuan_10_tahun, 
                            SUM ( CASE WHEN ( DATEDIFF( HOUR, peserta_didik.tanggal_lahir, GETDATE()) / 8766 ) = 11 AND peserta_didik.jenis_kelamin = 'P' then 1 else 0 end ) as pd_perempuan_11_tahun, 
                            SUM ( CASE WHEN ( DATEDIFF( HOUR, peserta_didik.tanggal_lahir, GETDATE()) / 8766 ) = 12 AND peserta_didik.jenis_kelamin = 'P' then 1 else 0 end ) as pd_perempuan_12_tahun, 
                            SUM ( CASE WHEN ( DATEDIFF( HOUR, peserta_didik.tanggal_lahir, GETDATE()) / 8766 ) = 13 AND peserta_didik.jenis_kelamin = 'P' then 1 else 0 end ) as pd_perempuan_13_tahun, 
                            SUM ( CASE WHEN ( DATEDIFF( HOUR, peserta_didik.tanggal_lahir, GETDATE()) / 8766 ) = 14 AND peserta_didik.jenis_kelamin = 'P' then 1 else 0 end ) as pd_perempuan_14_tahun, 
                            SUM ( CASE WHEN ( DATEDIFF( HOUR, peserta_didik.tanggal_lahir, GETDATE()) / 8766 ) = 15 AND peserta_didik.jenis_kelamin = 'P' then 1 else 0 end ) as pd_perempuan_15_tahun, 
                            SUM ( CASE WHEN ( DATEDIFF( HOUR, peserta_didik.tanggal_lahir, GETDATE()) / 8766 ) = 16 AND peserta_didik.jenis_kelamin = 'P' then 1 else 0 end ) as pd_perempuan_16_tahun, 
                            SUM ( CASE WHEN ( DATEDIFF( HOUR, peserta_didik.tanggal_lahir, GETDATE()) / 8766 ) = 17 AND peserta_didik.jenis_kelamin = 'P' then 1 else 0 end ) as pd_perempuan_17_tahun, 
                            SUM ( CASE WHEN ( DATEDIFF( HOUR, peserta_didik.tanggal_lahir, GETDATE()) / 8766 ) = 18 AND peserta_didik.jenis_kelamin = 'P' then 1 else 0 end ) as pd_perempuan_18_tahun, 
                            SUM ( CASE WHEN ( DATEDIFF( HOUR, peserta_didik.tanggal_lahir, GETDATE()) / 8766 ) = 19 AND peserta_didik.jenis_kelamin = 'P' then 1 else 0 end ) as pd_perempuan_19_tahun, 
                            SUM ( CASE WHEN ( DATEDIFF( HOUR, peserta_didik.tanggal_lahir, GETDATE()) / 8766 ) = 20 AND peserta_didik.jenis_kelamin = 'P' then 1 else 0 end ) as pd_perempuan_20_tahun, 
                            SUM ( CASE WHEN ( DATEDIFF( HOUR, peserta_didik.tanggal_lahir, GETDATE()) / 8766 ) > 20 AND peserta_didik.jenis_kelamin = 'P' then 1 else 0 end ) as pd_perempuan_20_tahun_lebih,
                            SUM ( CASE WHEN peserta_didik.agama_id = 1 AND peserta_didik.jenis_kelamin = 'L' then 1 else 0 end ) as pd_laki_islam,  
                            SUM ( CASE WHEN peserta_didik.agama_id = 2 AND peserta_didik.jenis_kelamin = 'L' then 1 else 0 end ) as pd_laki_kristen,  
                            SUM ( CASE WHEN peserta_didik.agama_id = 3 AND peserta_didik.jenis_kelamin = 'L' then 1 else 0 end ) as pd_laki_katholik,  
                            SUM ( CASE WHEN peserta_didik.agama_id = 4 AND peserta_didik.jenis_kelamin = 'L' then 1 else 0 end ) as pd_laki_hindu,  
                            SUM ( CASE WHEN peserta_didik.agama_id = 5 AND peserta_didik.jenis_kelamin = 'L' then 1 else 0 end ) as pd_laki_budha,  
                            SUM ( CASE WHEN peserta_didik.agama_id = 6 AND peserta_didik.jenis_kelamin = 'L' then 1 else 0 end ) as pd_laki_konghucu,  
                            SUM ( CASE WHEN peserta_didik.agama_id = 7 AND peserta_didik.jenis_kelamin = 'L' then 1 else 0 end ) as pd_laki_kepercayaan,  
                            SUM ( CASE WHEN peserta_didik.agama_id = 98 AND peserta_didik.jenis_kelamin = 'L' then 1 else 0 end ) as pd_laki_agama_tidak_diisi,  
                            SUM ( CASE WHEN peserta_didik.agama_id = 99 AND peserta_didik.jenis_kelamin = 'L' then 1 else 0 end ) as pd_laki_lainnya,
                            SUM ( CASE WHEN peserta_didik.agama_id = 1 AND peserta_didik.jenis_kelamin = 'P' then 1 else 0 end ) as pd_perempuan_islam,  
                            SUM ( CASE WHEN peserta_didik.agama_id = 2 AND peserta_didik.jenis_kelamin = 'P' then 1 else 0 end ) as pd_perempuan_kristen,  
                            SUM ( CASE WHEN peserta_didik.agama_id = 3 AND peserta_didik.jenis_kelamin = 'P' then 1 else 0 end ) as pd_perempuan_katholik,  
                            SUM ( CASE WHEN peserta_didik.agama_id = 4 AND peserta_didik.jenis_kelamin = 'P' then 1 else 0 end ) as pd_perempuan_hindu,  
                            SUM ( CASE WHEN peserta_didik.agama_id = 5 AND peserta_didik.jenis_kelamin = 'P' then 1 else 0 end ) as pd_perempuan_budha,  
                            SUM ( CASE WHEN peserta_didik.agama_id = 6 AND peserta_didik.jenis_kelamin = 'P' then 1 else 0 end ) as pd_perempuan_konghucu,  
                            SUM ( CASE WHEN peserta_didik.agama_id = 7 AND peserta_didik.jenis_kelamin = 'P' then 1 else 0 end ) as pd_perempuan_kepercayaan,  
                            SUM ( CASE WHEN peserta_didik.agama_id = 98 AND peserta_didik.jenis_kelamin = 'P' then 1 else 0 end ) as pd_perempuan_agama_tidak_diisi,  
                            SUM ( CASE WHEN peserta_didik.agama_id = 99 AND peserta_didik.jenis_kelamin = 'P' then 1 else 0 end ) as pd_perempuan_lainnya,
                            SUM ( CASE WHEN LEFT(peserta_didik.kode_wilayah,2) = LEFT(prop.kode_wilayah,2) THEN 1 else 0 end ) as pd_domisili_provinsi,
                            SUM ( CASE WHEN LEFT(peserta_didik.kode_wilayah,2) != LEFT(prop.kode_wilayah,2) THEN 1 else 0 end ) as pd_domisili_luar_provinsi,
                            SUM ( CASE WHEN LEFT(peserta_didik.kode_wilayah,4) = LEFT(kab.kode_wilayah,4) THEN 1 else 0 end ) as pd_domisili_kabupaten,
                            SUM ( CASE WHEN LEFT(peserta_didik.kode_wilayah,4) != LEFT(kab.kode_wilayah,4) THEN 1 else 0 end ) as pd_domisili_luar_kabupaten
                        FROM
                            anggota_rombel WITH ( nolock )
                            JOIN rombongan_belajar WITH ( nolock ) ON rombongan_belajar.rombongan_belajar_id = anggota_rombel.rombongan_belajar_id
                            JOIN peserta_didik WITH ( nolock ) ON peserta_didik.peserta_didik_id = anggota_rombel.peserta_didik_id
                            JOIN registrasi_peserta_didik rpd WITH ( nolock ) ON rpd.peserta_didik_id = peserta_didik.peserta_didik_id
                            JOIN ptk WITH ( nolock ) ON ptk.ptk_id = rombongan_belajar.ptk_id
                            JOIN sekolah WITH ( nolock ) ON sekolah.sekolah_id = rombongan_belajar.sekolah_id -- 	JOIN yayasan ON yayasan.yayasan_id = sekolah.yayasan_id
                            JOIN ref.mst_wilayah kec WITH ( nolock ) ON kec.kode_wilayah = LEFT ( sekolah.kode_wilayah, 6 )
                            JOIN ref.mst_wilayah kab WITH ( nolock ) ON kab.kode_wilayah = kec.mst_kode_wilayah
                            JOIN ref.mst_wilayah prop WITH ( nolock ) ON prop.kode_wilayah = kab.mst_kode_wilayah
                            JOIN ref.kurikulum kurikulum WITH ( nolock ) ON kurikulum.kurikulum_id = rombongan_belajar.kurikulum_id 
                        WHERE
                            rombongan_belajar.jenis_rombel IN ( 1, 8, 9, 10, 11, 12, 13 ) 
                            AND rombongan_belajar.Soft_delete = 0 
                            AND anggota_rombel.Soft_delete = 0 
                            AND rombongan_belajar.semester_id = ".$semester_id." 
                            AND peserta_didik.Soft_delete = 0 
                            AND rpd.Soft_delete = 0 
                            AND ptk.Soft_delete = 0 
                            AND ( rpd.jenis_keluar_id IS NULL ) 
                            AND kec.kode_wilayah = '".$kecamatan[$iKecamatan]->{'kode_wilayah'}."'
                        GROUP BY
                            rombongan_belajar.sekolah_id,
                            sekolah.sekolah_id,
                            sekolah.nama,
                            sekolah.npsn,
                            prop.nama,
                            kab.nama,
                            kec.nama 
                        ) jumlah_pd ON jumlah_pd.sekolah_id = sekolah.sekolah_id
                    LEFT JOIN (
                        SELECT
                            s.sekolah_id AS sekolah_id,
                            SUM ( CASE WHEN pd.jenis_kelamin = 'L' THEN 1 ELSE 0 END ) AS pd_angka_putus_sekolah_laki,
                            SUM ( CASE WHEN pd.jenis_kelamin = 'P' THEN 1 ELSE 0 END ) AS pd_angka_putus_sekolah_perempuan 
                        FROM
                            peserta_didik pd WITH ( nolock )
                            JOIN (
                            SELECT
                                ar.peserta_didik_id,
                                MAX ( rb.tingkat_pendidikan_id ) AS tingkat_pendidikan_id 
                            FROM
                                anggota_rombel ar WITH ( nolock )
                                JOIN rombongan_belajar rb WITH ( nolock ) ON rb.rombongan_belajar_id = ar.rombongan_belajar_id 
                            WHERE
                                ar.soft_delete = 0 
                                AND rb.soft_delete = 0 
                                AND rb.jenis_rombel IN ( 1, 8, 9, 10, 11, 12, 13 ) 
                                AND rb.semester_id IN ( ".$semester_id." ) 
                            GROUP BY
                                ar.peserta_didik_id 
                            ) arb ON arb.peserta_didik_id = pd.peserta_didik_id
                            JOIN registrasi_peserta_didik rpd WITH ( nolock ) ON pd.peserta_didik_id = rpd.peserta_didik_id
                            JOIN sekolah s WITH ( nolock ) ON s.sekolah_id = rpd.sekolah_id
                            JOIN ref.mst_wilayah kec WITH ( nolock ) ON kec.kode_wilayah = LEFT ( s.kode_wilayah, 6 )
                            JOIN ref.mst_wilayah kab WITH ( nolock ) ON kab.kode_wilayah = kec.mst_kode_wilayah
                            JOIN ref.mst_wilayah prop WITH ( nolock ) ON prop.kode_wilayah = kab.mst_kode_wilayah 
                        WHERE
                            rpd.Soft_delete = 0 
                            AND pd.Soft_delete = 0 
                            AND rpd.jenis_keluar_id = '5' 
                            AND s.soft_delete = 0 
                            AND kec.kode_wilayah = '".$kecamatan[$iKecamatan]->{'kode_wilayah'}."'
                        GROUP BY
                            s.sekolah_id
                    ) ap on ap.sekolah_id = sekolah.sekolah_id
                    LEFT JOIN (
                        SELECT
                            ROW_NUMBER () OVER ( PARTITION BY pengguna.sekolah_id ORDER BY pengguna.last_update DESC ) AS urutan,
                            pengguna.pengguna_id,
                            pengguna.sekolah_id,
                            pengguna.nama,
                            pengguna.username,
                            pengguna.no_hp
                        FROM
                            ".$p_pengguna." pengguna WITH ( nolock ) 
                            ".$j_role_pengguna." 
                            JOIN sekolah s WITH ( nolock ) ON s.sekolah_id = pengguna.sekolah_id and s.soft_delete = 0
                            JOIN ref.mst_wilayah kec WITH ( nolock ) ON kec.kode_wilayah = LEFT ( s.kode_wilayah, 6 )
                            JOIN ref.mst_wilayah kab WITH ( nolock ) ON kab.kode_wilayah = kec.mst_kode_wilayah
                            JOIN ref.mst_wilayah prop WITH ( nolock ) ON prop.kode_wilayah = kab.mst_kode_wilayah
                        WHERE
                            ".$c_role_peran."peran_id = 10 
                            AND pengguna.Soft_delete = 0 
                            ".$c_role_expired_date." 
                            AND aktif = 1
                            AND kec.kode_wilayah = '".$kecamatan[$iKecamatan]->{'kode_wilayah'}."'
                    ) ops on ops.sekolah_id = sekolah.sekolah_id and ops.urutan = 1
                    LEFT JOIN (
                        SELECT
                            ROW_NUMBER () OVER ( PARTITION BY ptkt.sekolah_id ORDER BY tt.tmt_tambahan DESC, tt.Last_update DESC, ptk.last_update DESC ) AS urutan,
                            ptkt.sekolah_id,
                            ptk.nama AS nama_kepsek,
                            ptk.jenis_kelamin as jenis_kelamin_kepsek,
                            ptk.nip AS nip_kepsek,
                            ptk.no_hp AS hp_kepsek,
                            tt.tmt_tambahan,
                            tt.tst_tambahan,
                            tt.jabatan_ptk_id 
                        FROM
                            ptk WITH ( nolock )
                            JOIN ptk_terdaftar ptkt WITH ( nolock ) ON ptkt.ptk_id = ptk.ptk_id 
                            AND ptkt.Soft_delete = 0 
                            AND ptkt.tahun_ajaran_id = ".substr($semester_id,0,4)."  
                            AND ptkt.jenis_keluar_id
                            IS NULL JOIN sekolah WITH ( nolock ) ON sekolah.sekolah_id = ptkt.sekolah_id 
                            AND sekolah.Soft_delete = 0
                            JOIN tugas_tambahan tt WITH ( nolock ) ON tt.sekolah_id = sekolah.sekolah_id 
                            AND tt.jabatan_ptk_id = 33 
                            AND tt.Soft_delete = 0 
                            AND tt.tst_tambahan IS NULL 
                            JOIN ref.mst_wilayah kec WITH ( nolock ) ON kec.kode_wilayah = LEFT ( sekolah.kode_wilayah, 6 )
                            JOIN ref.mst_wilayah kab WITH ( nolock ) ON kab.kode_wilayah = kec.mst_kode_wilayah
                            JOIN ref.mst_wilayah prop WITH ( nolock ) ON prop.kode_wilayah = kab.mst_kode_wilayah
                        WHERE
                            ptk.Soft_delete = 0
                        AND kec.kode_wilayah = '".$kecamatan[$iKecamatan]->{'kode_wilayah'}."'
                    ) plt on plt.sekolah_id = sekolah.sekolah_id and plt.urutan = 1
                    where kec.kode_wilayah = '".$kecamatan[$iKecamatan]->{'kode_wilayah'}."'
                    ";

                    // echo $sql;die;

                    $fetch = DB::connection($con_string)->select(DB::raw($sql));
                    // $fetch = DB::select(DB::raw($sql));

                    //log
                    $log_total = $log_total+(int)sizeof($fetch);
                    //end of log

                    for ($iSekolah=0; $iSekolah < sizeof($fetch); $iSekolah++) { 

                        // echo json_encode($fetch[$iSekolah]);
                        $record = $fetch[$iSekolah];

                        $strKey = '';
                        $strValue = '';
                        $strUpdate = '';

                        foreach ($record as $key => $value) {
                            $strKey .= ",".$key;
                            $strValue .= ",'".str_replace("'","",$value)."'";
                            $strUpdate .= ",".$key."='".str_replace("'","",$value)."'";
                        }

                        $strKey = substr($strKey,1,strlen($strKey));
                        $strValue = substr($strValue,1,strlen($strValue));
                        $strUpdate = substr($strUpdate,1,strlen($strUpdate));

                        $sql_check = "SELECT * FROM dbo.rekap_sekolah rekap_sekolah WITH ( nolock ) WHERE rekap_sekolah.sekolah_id = '".$fetch[$iSekolah]->{'sekolah_id'}."' AND rekap_sekolah.semester_id = '".$fetch[$iSekolah]->{'semester_id'}."'";
                        $fetch_check = DB::connection($con_string_tujuan)->select($sql_check);

                        $sql_upsert = "IF NOT EXISTS ( SELECT * FROM dbo.rekap_sekolah rekap_sekolah WITH ( nolock ) WHERE rekap_sekolah.sekolah_id = '".$fetch[$iSekolah]->{'sekolah_id'}."' AND rekap_sekolah.semester_id = '".$fetch[$iSekolah]->{'semester_id'}."' ) 
                            INSERT INTO dbo.rekap_sekolah (
                                {$strKey}
                            ) values (
                                {$strValue}
                            ) ELSE UPDATE dbo.rekap_sekolah SET 
                                {$strUpdate}
                            WHERE
                                sekolah_id = '".$fetch[$iSekolah]->{'sekolah_id'}."'
                                AND semester_id = '".$fetch[$iSekolah]->{'semester_id'}."'";
                        
                        // echo $sql_upsert;die;

                        try {
                            DB::connection($con_string_tujuan)->statement($sql_upsert);
                            echo "[INF] [".($iProvinsi+1)."/".sizeof($provinsi)."] [".($iKabupaten+1)."/".sizeof($kabupaten)."] [".($iKecamatan+1)."/".sizeof($kecamatan)."] [".($iSekolah+1)."/".sizeof($fetch)."] ".$fetch[$iSekolah]->{'nama'}." [BERHASIL]".PHP_EOL;
                        
                            if(sizeof($fetch_check) > 0){
                                //update
                                $log_update++;
                            }else{
                                //insert
                                $log_insert++;
                            }

                        } catch (\Illuminate\Database\QueryException $ex) {
                            echo "[INF] [".($iProvinsi+1)."/".sizeof($provinsi)."] [".($iKabupaten+1)."/".sizeof($kabupaten)."] [".($iKecamatan+1)."/".sizeof($kecamatan)."] [".($iSekolah+1)."/".sizeof($fetch)."] ".$fetch[$iSekolah]->{'nama'}." [GAGAL]".PHP_EOL;                            
                            $log_gagal++;
                            logRekapSekolah::rekapGagal($log_rekap_id, ($kode_wilayah ? ($kode_wilayah_kabupaten ? $kode_wilayah_kabupaten : $kode_wilayah) : '000000'), $fetch[$iSekolah]->{'sekolah_id'}, $ex->getMessage()   );
                        }

                        // echo $sql_upsert.PHP_EOL;

                    }
                }
                
                if(!$kode_wilayah_kabupaten){
                    // echo $log_rekap_id_kabupaten.PHP_EOL;
                    logRekapSekolah::update($log_rekap_id_kabupaten, $waktu_mulai_kabupaten, $log_total, $log_update, $log_insert, $log_gagal);
                }
            }

        }

        logRekapSekolah::update($log_rekap_id, $waktu_mulai, $log_total, $log_update, $log_insert, $log_gagal);
    }
}
