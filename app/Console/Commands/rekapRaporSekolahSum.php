<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class rekapRaporSekolahSum extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rekap:rapor_sekolah_sum {--semester_id=20191}';

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
        // $semester_id = '20191';
        $semester_id = $this->option('semester_id');

        //ambil data wilayah
        $provinsi = DB::table(DB::raw('ref.mst_wilayah wilayah with(nolock)'))
        ->where('wilayah.id_level_wilayah','=',1)
        ->whereNull('wilayah.expired_date')
        ->get();

        for ($iProvinsi=0; $iProvinsi < sizeof($provinsi); $iProvinsi++) { 
            echo "[INF] [".($iProvinsi+1)."/".sizeof($provinsi)."] ".$provinsi[$iProvinsi]->{'nama'}.PHP_EOL;

            $kabupaten = DB::table(DB::raw('ref.mst_wilayah wilayah with(nolock)'))
            ->where('wilayah.mst_kode_wilayah','=',$provinsi[$iProvinsi]->{'kode_wilayah'})
            ->whereNull('wilayah.expired_date')
            ->get();

            for ($iKabupaten=0; $iKabupaten < sizeof($kabupaten); $iKabupaten++) { 
                echo "[INF] [".($iProvinsi+1)."/".sizeof($provinsi)."] [".($iKabupaten+1)."/".sizeof($kabupaten)."] ".$provinsi[$iProvinsi]->{'nama'}." - ".$kabupaten[$iKabupaten]->{'nama'}.PHP_EOL;
            
                $kecamatan = DB::table(DB::raw('ref.mst_wilayah wilayah with(nolock)'))
                ->where('wilayah.mst_kode_wilayah','=',$kabupaten[$iKabupaten]->{'kode_wilayah'})
                ->whereNull('wilayah.expired_date')
                ->get();

                for ($iKecamatan=0; $iKecamatan < sizeof($kecamatan); $iKecamatan++) { 
                    echo "[INF] [".($iProvinsi+1)."/".sizeof($provinsi)."] [".($iKabupaten+1)."/".sizeof($kabupaten)."] [".($iKecamatan+1)."/".sizeof($kecamatan)."] ".$provinsi[$iProvinsi]->{'nama'}." - ".$kabupaten[$iKabupaten]->{'nama'}." - ".$kecamatan[$iKecamatan]->{'nama'}.PHP_EOL;
                
                    $fetch = DB::select(DB::raw("SELECT
                        sekolah.sekolah_id,
                        '".$semester_id."' as semester_id,
                        sekolah.bentuk_pendidikan_id,
                        sekolah.status_sekolah,
                        sekolah.nama,
                        sekolah.npsn,
                        kec.kode_wilayah as kode_wilayah_kecamatan,
                        kab.kode_wilayah as kode_wilayah_kabupaten,
                        prop.kode_wilayah as kode_wilayah_provinsi,
                        negara.kode_wilayah as kode_wilayah_negara,
                        kec.nama as kecamatan,
                        kab.nama as kabupaten,
                        prop.nama as provinsi,
                        negara.nama as negara,
                        kec.id_level_wilayah as id_level_wilayah_kecamatan,
                        kab.id_level_wilayah as id_level_wilayah_kabupaten,
                        prop.id_level_wilayah as id_level_wilayah_provinsi,
                        negara.id_level_wilayah as id_level_wilayah_negara,
                        ISNULL(round(rapor_sekolah.nilai_rapor,2),0)	as rapor_sekolah,
                        ISNULL(round(rapor_pd.nilai_rapor,2),0)	as rapor_pd,
                        ISNULL(round(rapor_ptk.nilai_rapor,2),0) as rapor_ptk,
                        ISNULL(round(rapor_rombel.nilai_rapor,2),0) as rapor_rombel,
                        ISNULL(round(rapor_sarpras.nilai_rapor,2),0) as rapor_sarpras,
                        round((
                            (
                                ISNULL(round(rapor_sekolah.nilai_rapor,2),0) +
                                ISNULL(round(rapor_pd.nilai_rapor,2),0) +
                                ISNULL(round(rapor_ptk.nilai_rapor,2),0) +
                                ISNULL(round(rapor_rombel.nilai_rapor,2),0) +
                                ISNULL(round(rapor_sarpras.nilai_rapor,2),0)
                            ) / cast(5 as float) 
                        ),2) as rapor_akhir,
                        getdate() as create_date,
                        getdate() as last_update,
                        sekolah.soft_delete as soft_delete,
                        '2BF85354-F67D-4D24-B284-7DB9C8836933' as updater_id
                    FROM 
                        sekolah with(nolock)
                    LEFT JOIN (
                        SELECT
                                rpd.sekolah_id,
                                ISNULL(AVG(
                                (
                                    ( CASE WHEN ( 
                                        peserta_didik.nama IS NOT NULL 						        
                                        AND peserta_didik.nama != ''									
                                        AND peserta_didik.nama not like '%[0-9]%'			
                                    ) THEN 1 ELSE 0 END ) + 
                                    ( CASE WHEN ( 
                                        peserta_didik.tanggal_lahir IS NOT NULL 			
                                        AND peserta_didik.tanggal_lahir != '' 				
                                    ) THEN 1 ELSE 0 END ) + 
                                    ( CASE WHEN 
                                    ( 
                                        peserta_didik.nisn IS NOT NULL 								
                                        AND peserta_didik.nisn != '' 									
                                        AND LEN(peserta_didik.nisn) = 10							
                                    ) THEN 1 ELSE 0 END ) +
                                    ( CASE WHEN
                                    (
                                        peserta_didik.nomor_telepon_seluler IS NOT NULL
                                        AND peserta_didik.nomor_telepon_seluler != ''
                                    ) THEN 1 ELSE 0 END ) +
                                    ( CASE WHEN
                                    (
                                        peserta_didik.email IS NOT NULL
                                        AND peserta_didik.email != ''
                                    ) THEN 1 ELSE 0 END ) +
                                    ( CASE WHEN
                                    (
                                        peserta_didik.nama_ibu_kandung IS NOT NULL
                                        AND peserta_didik.nama_ibu_kandung != ''
                                    ) THEN 1 ELSE 0 END ) +
                                    ( CASE WHEN
                                    (
                                        peserta_didik.nama_ayah IS NOT NULL
                                        AND peserta_didik.nama_ayah != ''
                                    ) THEN 1 ELSE 0 END ) +
                                    ( CASE WHEN
                                    (
                                        peserta_didik.pekerjaan_id_ibu IS NOT NULL
                                        AND peserta_didik.pekerjaan_id_ibu != ''
                                    ) THEN 1 ELSE 0 END ) +
                                    ( CASE WHEN
                                    (
                                        peserta_didik.pekerjaan_id_ayah IS NOT NULL
                                        AND peserta_didik.pekerjaan_id_ayah != ''
                                    ) THEN 1 ELSE 0 END ) +
                                    (
                                        CASE WHEN peserta_didik.penerima_KIP = 1 then ( case when peserta_didik.no_KIP IS NOT NULl then 1 else 0 end ) else 1 end
                                    ) +
                                    ( CASE WHEN
                                    (
                                        pdl.tinggi_badan IS NOT NULL
                                        AND ISNUMERIC(pdl.tinggi_badan) = 1
                                        AND pdl.tinggi_badan != 0
                                    ) THEN 1 ELSE 0 END ) +
                                    ( CASE WHEN
                                    (
                                        pdl.berat_badan IS NOT NULL
                                        AND ISNUMERIC(pdl.berat_badan) = 1
                                        AND pdl.berat_badan != 0 					
                                    ) THEN 1 ELSE 0 END ) +
                                    ( CASE WHEN
                                    (
                                        peserta_didik.lintang IS NOT NULL
                                        AND peserta_didik.lintang != 0 	
                                    ) THEN 1 ELSE 0 END ) +
                                    ( CASE WHEN
                                    (
                                        peserta_didik.bujur IS NOT NULL 		
                                        AND peserta_didik.bujur != 0 				
                                    ) THEN 1 ELSE 0 END )
                                ) 
                                / CAST ( 14 AS FLOAT ) * 100 
                            ),0) AS nilai_rapor
                        FROM
                                peserta_didik peserta_didik with(nolock)
                            JOIN (
                                SELECT
                                    ROW_NUMBER () OVER ( PARTITION BY anggota_rombel.peserta_didik_id ORDER BY rombongan_belajar.tingkat_pendidikan_id DESC ) AS urutan,
                                    anggota_rombel.*,
                                    rombongan_belajar.sekolah_id 
                                FROM
                                    anggota_rombel anggota_rombel with(nolock)
                                    JOIN rombongan_belajar rombongan_belajar with(nolock) ON rombongan_belajar.rombongan_belajar_id = anggota_rombel.rombongan_belajar_id
                                    JOIN ptk ptk with(nolock) ON ptk.ptk_id = rombongan_belajar.ptk_id
                                    JOIN sekolah sekolah with(nolock) ON sekolah.sekolah_id = rombongan_belajar.sekolah_id 
                                    JOIN ref.mst_wilayah kec with(nolock) on kec.kode_wilayah = LEFT(sekolah.kode_wilayah,6)
                                    JOIN ref.mst_wilayah kab with(nolock) on kab.kode_wilayah = kec.mst_kode_wilayah
                                    JOIN ref.mst_wilayah prop with(nolock) on prop.kode_wilayah = kab.mst_kode_wilayah
                                    JOIN ref.mst_wilayah negara with(nolock) on negara.kode_wilayah = prop.mst_kode_wilayah
                                WHERE
                                    anggota_rombel.Soft_delete = 0 
                                    AND rombongan_belajar.Soft_delete = 0 
                                    AND ptk.Soft_delete = 0 
                                    AND sekolah.Soft_delete = 0 
                                    AND rombongan_belajar.jenis_rombel IN ( 1, 8, 9, 10, 11, 12, 13 ) 
                                    AND rombongan_belajar.semester_id = ".$semester_id." 
                                    AND kec.kode_wilayah = '".$kecamatan[$iKecamatan]->{'kode_wilayah'}."'
                            ) ar ON ar.peserta_didik_id = peserta_didik.peserta_didik_id AND ar.urutan = 1
                            JOIN (
                                SELECT
                                    ROW_NUMBER () OVER ( PARTITION BY registrasi_peserta_didik.peserta_didik_id ORDER BY registrasi_peserta_didik.tanggal_masuk_sekolah DESC ) AS urutan,
                                    registrasi_peserta_didik.* 
                                FROM
                                        registrasi_peserta_didik registrasi_peserta_didik with(nolock)
                                    JOIN sekolah on sekolah.sekolah_id = registrasi_peserta_didik.sekolah_id
                                    JOIN ref.mst_wilayah kec with(nolock) on kec.kode_wilayah = LEFT(sekolah.kode_wilayah,6)
                                    JOIN ref.mst_wilayah kab with(nolock) on kab.kode_wilayah = kec.mst_kode_wilayah
                                    JOIN ref.mst_wilayah prop with(nolock) on prop.kode_wilayah = kab.mst_kode_wilayah
                                    JOIN ref.mst_wilayah negara with(nolock) on negara.kode_wilayah = prop.mst_kode_wilayah
                                WHERE
                                    registrasi_peserta_didik.Soft_delete = 0 
                                AND sekolah.soft_delete = 0
                                AND jenis_keluar_id IS NULL 
                                
                                AND kec.kode_wilayah = '".$kecamatan[$iKecamatan]->{'kode_wilayah'}."'
                            ) rpd ON rpd.peserta_didik_id = peserta_didik.peserta_didik_id AND rpd.sekolah_id = ar.sekolah_id
                            LEFT JOIN peserta_didik_longitudinal pdl with(nolock) on pdl.peserta_didik_id = peserta_didik.peserta_didik_id and pdl.semester_id = ".$semester_id."
                        WHERE
                                peserta_didik.Soft_delete = 0
                                
                            GROUP BY
                                rpd.sekolah_id
                    ) rapor_pd on rapor_pd.sekolah_id = sekolah.sekolah_id
                    LEFT JOIN (
                        SELECT
                            sekolah.sekolah_id,
                            (
                                (
                                    ( CASE WHEN kepsek.nama IS NOT NULL AND kepsek.nama != '' AND kepsek.nama NOT LIKE '[!@#$%&*]' THEN 1 ELSE 0 END ) + 
                                    ( CASE WHEN kepsek.email IS NOT NULL AND kepsek.email != '' THEN 1 ELSE 0 END ) + 
                                    ( CASE WHEN kepsek.no_hp IS NOT NULL AND kepsek.no_hp != '' AND kepsek.no_hp LIKE '%[0-9]%' THEN 1 ELSE 0 END ) + 
                                    ( CASE WHEN sekolah.email IS NOT NULL AND sekolah.email != '' THEN 1 ELSE 0 END ) + 
                                    ( CASE WHEN bp.bentuk_pendidikan_id IS NOT NULL AND bp.bentuk_pendidikan_id != '' THEN 1 ELSE 0 END ) + 
                                    ( CASE WHEN sekolah.status_sekolah IS NOT NULL AND sekolah.status_sekolah IN ( 1, 2 ) THEN 1 ELSE 0 END ) + 
                                    ( CASE WHEN sekolah.nama IS NOT NULL AND sekolah.nama NOT LIKE '[!@#$%&*]' THEN 1 ELSE 0 END ) + 
                                    ( CASE WHEN sekolah.status_sekolah = 1 THEN ( CASE WHEN seklong.partisipasi_bos = 1 THEN 1 ELSE 0 END ) ELSE 1 END ) + 
                                    ( CASE WHEN seklong.akses_internet_id IS NOT NULL THEN 1 ELSE ( CASE WHEN seklong.akses_internet_2_id IS NOT NULL THEN 1 ELSE 0 END ) END ) + 
                                    ( CASE WHEN seklong.sumber_listrik_id IS NOT NULL THEN 1 ELSE 0 END ) + 
                                    ( CASE WHEN sekolah.status_sekolah = 1 THEN ( CASE WHEN sekolah.status_kepemilikan_id = 1 THEN 1 ELSE 0 END ) ELSE 1 END ) + 
                                    ( CASE WHEN seklong.waktu_penyelenggaraan_id IS NOT NULL THEN 1 ELSE 0 END ) + 
                                    ( CASE WHEN sekolah.sk_pendirian_sekolah IS NOT NULL THEN 1 ELSE 0 END ) + 
                                    ( CASE WHEN sekolah.tanggal_sk_pendirian IS NOT NULL THEN 1 ELSE 0 END ) + 
                                    ( CASE WHEN sekolah.sk_izin_operasional IS NOT NULL THEN 1 ELSE 0 END ) + 
                                    ( CASE WHEN sekolah.tanggal_sk_izin_operasional IS NOT NULL THEN 1 ELSE 0 END ) + 
                                    ( CASE WHEN seklong.sumber_listrik_id != 1 THEN ( CASE WHEN seklong.daya_listrik IS NOT NULL AND seklong.daya_listrik < 100000 THEN 1 ELSE 0 END ) ELSE 1 END ) + 
                                    ( CASE WHEN seklong.sertifikasi_iso_id IS NOT NULL THEN 1 ELSE 0 END ) 
                                ) / CAST(18 as float) * 100 
                            ) AS nilai_rapor 
                        FROM
                            sekolah with(nolock)
                            LEFT JOIN (
                                SELECT
                                    ROW_NUMBER () OVER ( PARTITION BY ptk_terdaftar.sekolah_id ORDER BY tugas_tambahan.tmt_tambahan DESC ) AS urutan,
                                    tugas_tambahan.tmt_tambahan,
                                    ptk.ptk_id,
                                    ptk_terdaftar.sekolah_id,
                                    ptk.jenis_ptk_id,
                                    tugas_tambahan.jabatan_ptk_id,
                                    ptk.nama,
                                    ptk.no_hp,
                                    ptk.email 
                                FROM
                                    ptk with(nolock)
                                    JOIN ptk_terdaftar with(nolock) ON ptk.ptk_id = ptk_terdaftar.ptk_id
                                    JOIN sekolah on sekolah.sekolah_id = ptk_terdaftar.sekolah_id
                                    JOIN tugas_tambahan with(nolock) ON tugas_tambahan.ptk_id = ptk.ptk_id AND tugas_tambahan.jabatan_ptk_id IN ( 2, 33 ) AND tugas_tambahan.tst_tambahan IS NULL 
                                    JOIN ref.mst_wilayah kec with(nolock) on kec.kode_wilayah = LEFT(sekolah.kode_wilayah,6)
                                    JOIN ref.mst_wilayah kab with(nolock) on kab.kode_wilayah = kec.mst_kode_wilayah
                                    JOIN ref.mst_wilayah prop with(nolock) on prop.kode_wilayah = kab.mst_kode_wilayah
                                    JOIN ref.mst_wilayah negara with(nolock) on negara.kode_wilayah = prop.mst_kode_wilayah
                                WHERE
                                    ptk.jenis_ptk_id = 20 
                                    AND ptk.Soft_delete = 0 
                                    AND ptk_terdaftar.Soft_delete = 0 
                                    AND ptk_terdaftar.jenis_keluar_id IS NULL 
                                    AND ptk_terdaftar.tahun_ajaran_id = 2019 
                                    AND tugas_tambahan.Soft_delete = 0 
                                    AND kec.kode_wilayah = '".$kecamatan[$iKecamatan]->{'kode_wilayah'}."'
                            ) kepsek ON kepsek.sekolah_id = sekolah.sekolah_id AND kepsek.urutan = 1
                            LEFT JOIN sekolah_longitudinal seklong with(nolock) ON seklong.sekolah_id = sekolah.sekolah_id AND seklong.semester_id = ".$semester_id."
                            JOIN ref.bentuk_pendidikan bp with(nolock) ON bp.bentuk_pendidikan_id = sekolah.bentuk_pendidikan_id
                            JOIN ref.mst_wilayah kec with(nolock) on kec.kode_wilayah = LEFT(sekolah.kode_wilayah,6)
                            JOIN ref.mst_wilayah kab with(nolock) on kab.kode_wilayah = kec.mst_kode_wilayah
                            JOIN ref.mst_wilayah prop with(nolock) on prop.kode_wilayah = kab.mst_kode_wilayah
                            JOIN ref.mst_wilayah negara with(nolock) on negara.kode_wilayah = prop.mst_kode_wilayah	
                        WHERE
                            sekolah.Soft_delete = 0
                            AND kec.kode_wilayah = '".$kecamatan[$iKecamatan]->{'kode_wilayah'}."'
                    ) rapor_sekolah on rapor_sekolah.sekolah_id = sekolah.sekolah_id
                    LEFT JOIN (
                        SELECT
                            ptkd.sekolah_id,
                            ISNULL (
                            AVG ( ( cast ( ( 
                                ( case when ptk.status_kepegawaian_id in ( 1, 2, 3, 10 ) then 
                                    ( case when ptk.sk_cpns is null then 0 else 1 end )
                                    else
                                    1
                                    end ) +
                                ( case when ptk.status_kepegawaian_id in ( 1, 2, 3, 10 ) then 
                                    ( case when ptk.tgl_cpns is null then 0 else 1 end )
                                    else
                                    1
                                    end ) +
                                ( case when ptk.status_kepegawaian_id in ( 1, 2, 3, 10 ) then 
                                    ( case when ptk.tmt_pns is null then 0 else 1 end )
                                    else
                                    1
                                    end ) +
                                ( case when ptk.status_kepegawaian_id in ( 1, 2, 3, 10 ) then 
                                    ( case when ptk.pangkat_golongan_id is null then 0 else 1 end )
                                    else
                                    1
                                    end ) +
                                ( case when ptk.status_perkawinan = 1 then 
                                    ( case when ptk.nama_suami_istri is null then 0 else 1 end )
                                    else
                                    1
                                    end ) +
                                ( case when ptk.status_perkawinan = 1 then 
                                    ( case when ptk.pekerjaan_suami_istri is null then 0 else 1 end )
                                    else
                                    1
                                    end ) +
                                ( case when ptk.sk_pengangkatan is null or ptk.sk_pengangkatan = '-' then 0 else 1 end ) +
                                ( case when ptk.tmt_pengangkatan is null then 0 else 1 end ) +
                                ( case when ptk.nama_ibu_kandung is null or ptk.nama_ibu_kandung = '-' then 0 else 1 end ) +
                                ( case when ptk.nik is null or ptk.nik = '-' then 0 else 1 end ) +
                                ( case when ptk.nuptk is null or ptk.nuptk = '-' then 0 else 1 end ) +
                                ( case when ptk.desa_kelurahan is null or ptk.desa_kelurahan = '-' then 0 else 1 end ) +
                                ( case when ptk.no_hp is null or ptk.no_hp = '-' then 0 else 1 end ) +
                                ( case when ptk.email is null or ptk.email = '-' then 0 else 1 end ) +
                                ( case when ptk.npwp is null or ptk.npwp = '-' then 0 else 1 end ) +
                                ( CASE WHEN 
                                ( 
                                    ptk.nama IS NOT NULL 								-- nggak null
                                    AND ptk.nama != ''									-- nggak string kosong
                                    AND ptk.nama not like '%[0-9]%'			-- nggak mengandung angka
                                ) THEN 1 ELSE 0 END ) +
                                ( CASE WHEN 
                                ( 
                                    ptk.tanggal_lahir IS NOT NULL 			-- nggak null
                                    AND ptk.tanggal_lahir != '' 				-- nggak string kosong
                                ) THEN 1 ELSE 0 END ) + 
                                ( 
                                CASE 
                                WHEN ptk.no_telepon_rumah IS NULL THEN 1
                                WHEN ( ptk.no_telepon_rumah IS NOT NULL AND ptk.no_telepon_rumah like '%[0-9]%' ) THEN 1 
                                ELSE 0 END 
                                ) +
                                ( case when ptk.status_kepegawaian_id in ( 1, 2, 3, 10 ) then 
                                    ( case when ptk.nip is null then 0 else 1 end )
                                    else
                                    1
                                    end )
                            ) as float(24) ) / 19 ) * 100 ),
                            0 ) as nilai_rapor
                        FROM
                            ptk ptk WITH ( nolock )
                            JOIN ptk_terdaftar ptkd WITH ( nolock ) ON ptk.ptk_id = ptkd.ptk_id
                            JOIN ref.tahun_ajaran ta WITH ( nolock ) ON ta.tahun_ajaran_id = ptkd.tahun_ajaran_id 
                            JOIN sekolah with(nolock) on sekolah.sekolah_id = ptkd.sekolah_id
                            JOIN ref.mst_wilayah kec with(nolock) on kec.kode_wilayah = LEFT(sekolah.kode_wilayah,6)
                            JOIN ref.mst_wilayah kab with(nolock) on kab.kode_wilayah = kec.mst_kode_wilayah
                            JOIN ref.mst_wilayah prop with(nolock) on prop.kode_wilayah = kab.mst_kode_wilayah
                            JOIN ref.mst_wilayah negara with(nolock) on negara.kode_wilayah = prop.mst_kode_wilayah
                        WHERE
                            ptk.Soft_delete = 0 
                            AND ptkd.Soft_delete = 0 
                            AND ptkd.ptk_induk = 1 
                            AND ptkd.tahun_ajaran_id = '2019' 
                            AND ptk.jenis_ptk_id IN ( 3, 4, 5, 6, 12, 13, 14 ) 
                            AND ( ptkd.tgl_ptk_keluar > ta.tanggal_selesai OR ptkd.jenis_keluar_id IS NULL ) 
                            AND kec.kode_wilayah = '".$kecamatan[$iKecamatan]->{'kode_wilayah'}."'
                        GROUP BY
                            ptkd.sekolah_id
                    ) rapor_ptk on rapor_ptk.sekolah_id = sekolah.sekolah_id
                    LEFT JOIN (
                        SELECT 
                            rombongan_belajar.sekolah_id,
                            ISNULL (
                            AVG ( 
                                (
                                    ( CASE WHEN rombongan_belajar.ptk_id IS NOT NULL THEN 1 ELSE 0 END ) + 
                                    ( CASE WHEN rombongan_belajar.id_ruang IS NOT NULL THEN 1 ELSE 0 END ) + 
                                    ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id IS NOT NULL THEN 1 ELSE 0 END ) +
                                    ( CASE WHEN anggota.jumlah_anggota_rombel IS NOT NULL THEN 1 ELSE 0 END )
                                ) / CAST( 4 as float ) * 100
                            ),
                            0 ) AS nilai_rapor 
                        FROM
                            rombongan_belajar WITH ( nolock )
                            JOIN sekolah WITH ( nolock ) ON sekolah.sekolah_id = rombongan_belajar.sekolah_id
                            JOIN ref.mst_wilayah kec WITH ( nolock ) ON kec.kode_wilayah = LEFT ( sekolah.kode_wilayah, 6 )
                            JOIN ref.mst_wilayah kab WITH ( nolock ) ON kab.kode_wilayah = kec.mst_kode_wilayah
                            JOIN ref.mst_wilayah prop WITH ( nolock ) ON prop.kode_wilayah = kab.mst_kode_wilayah
                            LEFT JOIN ( 
                                SELECT
                                    rombongan_belajar.rombongan_belajar_id,
                                    SUM ( 1 ) AS jumlah_anggota_rombel 
                                FROM
                                    anggota_rombel with(nolock)
                                    JOIN rombongan_belajar with(nolock) ON rombongan_belajar.rombongan_belajar_id = anggota_rombel.rombongan_belajar_id
                                    JOIN sekolah with(nolock) ON sekolah.sekolah_id = rombongan_belajar.sekolah_id 
                                    JOIN ref.mst_wilayah kec with(nolock) on kec.kode_wilayah = LEFT(sekolah.kode_wilayah,6)
                                    JOIN ref.mst_wilayah kab with(nolock) on kab.kode_wilayah = kec.mst_kode_wilayah
                                    JOIN ref.mst_wilayah prop with(nolock) on prop.kode_wilayah = kab.mst_kode_wilayah
                                    JOIN ref.mst_wilayah negara with(nolock) on negara.kode_wilayah = prop.mst_kode_wilayah
                                WHERE
                                    anggota_rombel.Soft_delete = 0 
                                    AND rombongan_belajar.Soft_delete = 0 
                                    AND rombongan_belajar.semester_id = ".$semester_id."
                                    AND rombongan_belajar.jenis_rombel IN ( 1, 8, 9, 10, 11, 12, 13 )
                                    AND sekolah.Soft_delete = 0
                                    
                                    AND kec.kode_wilayah = '".$kecamatan[$iKecamatan]->{'kode_wilayah'}."'
                                GROUP BY
                                    rombongan_belajar.rombongan_belajar_id
                            ) anggota ON anggota.rombongan_belajar_id = rombongan_belajar.rombongan_belajar_id 
                        WHERE
                            rombongan_belajar.jenis_rombel IN ( 1, 8, 9, 10, 11, 12, 13 ) 
                            AND rombongan_belajar.Soft_delete = 0 
                            AND sekolah.soft_delete = 0
                            AND rombongan_belajar.semester_id = ".$semester_id."
                            
                            AND kec.kode_wilayah = '".$kecamatan[$iKecamatan]->{'kode_wilayah'}."'
                        GROUP BY rombongan_belajar.sekolah_id
                    ) rapor_rombel on rapor_rombel.sekolah_id = sekolah.sekolah_id
                    LEFT JOIN (
                        SELECT
                            prasarana.sekolah_id,
                            AVG (
                                (
                                ( CASE WHEN prasarana.panjang > 0 AND prasarana.panjang < 100 THEN 1 ELSE 0 END ) + 
                                ( CASE WHEN prasarana.lebar > 0 AND prasarana.lebar < 100 THEN 1 ELSE 0 END ) + 
                                ( CASE WHEN persen.persentase IS NOT NULL THEN 1 ELSE 0 END ) + 
                                ( CASE WHEN alat.jumlah > 0 THEN 1 ELSE 0 END ) + 
                                ( CASE WHEN prasarana.jenis_prasarana_id = 2 THEN ( CASE WHEN alat.jumlah_meja_siswa > 0 AND alat.jumlah_kursi_siswa > 0 THEN 1 ELSE 0 END ) ELSE 1 END ) 
                            ) / 5 * 100 
                            ) AS nilai_rapor 
                        FROM
                            ruang prasarana WITH ( nolock )
                            JOIN ruang_longitudinal prl WITH ( nolock ) ON prl.id_ruang = prasarana.id_ruang AND prl.semester_id = ".$semester_id."
                            JOIN bangunan WITH ( nolock ) ON prasarana.id_bangunan = bangunan.id_bangunan
                            JOIN bangunan_longitudinal WITH ( nolock ) ON bangunan.id_bangunan = bangunan_longitudinal.id_bangunan
                            LEFT JOIN (
                            SELECT
                                prasarana_longitudinal.id_ruang,
                                round((
                                        ( rusak_pondasi * 12 / 100 ) + ( rusak_sloop_kolom_balok * 25 / 100 ) + ( rusak_plester_struktur * 2 / 100 ) + ( rusak_kudakuda_atap * 5 / 100 ) + ( rusak_kaso_atap * 3.20 / 100 ) + ( rusak_reng_atap * 1.50 / 100 ) + ( rusak_tutup_atap * 2.50 / 100 ) + ( rusak_rangka_plafon * 3 / 100 ) + ( rusak_tutup_plafon * 4 / 100 ) + ( rusak_bata_dinding * 7 / 100 ) + ( rusak_plester_dinding * 2.20 / 100 ) + ( rusak_daun_jendela * 1.60 / 100 ) + ( rusak_daun_pintu * 1.25 / 100 ) + ( rusak_kusen * 2.60 / 100 ) + ( rusak_tutup_lantai * 12 / 100 ) + ( rusak_inst_listrik * 4.75 / 100 ) + ( rusak_inst_air * 2.45 / 100 ) + ( rusak_drainase * 1.50 / 100 ) + ( rusak_finish_struktur * 1 / 100 ) + ( rusak_finish_plafon * 1.20 / 100 ) + ( rusak_finish_dinding * 2.50 / 100 ) + ( rusak_finish_kpj * 1.75 / 100 ) 
                                        ),
                                    2 
                                ) persentase 
                            FROM
                                ruang_longitudinal prasarana_longitudinal WITH ( nolock )
                                JOIN ruang prasarana WITH ( nolock ) ON prasarana.id_ruang = prasarana_longitudinal.id_ruang
                                JOIN bangunan WITH ( nolock ) ON prasarana.id_bangunan = bangunan.id_bangunan
                                JOIN bangunan_longitudinal WITH ( nolock ) ON bangunan.id_bangunan = bangunan_longitudinal.id_bangunan 
                            WHERE
                                prasarana_longitudinal.soft_delete = 0 
                                AND prasarana_longitudinal.semester_id = ".$semester_id." 
                            ) persen ON persen.id_ruang = prasarana.id_ruang
                            JOIN sekolah ON sekolah.sekolah_id = bangunan.sekolah_id
                            JOIN ref.mst_wilayah kec WITH ( nolock ) ON kec.kode_wilayah = LEFT ( sekolah.kode_wilayah, 6 )
                            JOIN ref.mst_wilayah kab WITH ( nolock ) ON kab.kode_wilayah = kec.mst_kode_wilayah
                            JOIN ref.mst_wilayah prop WITH ( nolock ) ON prop.kode_wilayah = kab.mst_kode_wilayah
                            LEFT OUTER JOIN (
                            SELECT
                                alat.id_ruang,
                                SUM ( 1 ) AS jumlah,
                                SUM ( CASE WHEN alat.jenis_sarana_id = 1 THEN 1 ELSE 0 END ) AS jumlah_meja_siswa,
                                SUM ( CASE WHEN alat.jenis_sarana_id = 2 THEN 1 ELSE 0 END ) AS jumlah_kursi_siswa 
                            FROM
                                alat 
                            WHERE
                                alat.soft_delete = 0 
                            GROUP BY
                                alat.id_ruang 
                            ) alat ON alat.id_ruang = prasarana.id_ruang 
                        WHERE
                            prasarana.soft_delete = 0 
                            AND sekolah.soft_delete = 0 
                        GROUP BY
                            prasarana.sekolah_id
                    ) rapor_sarpras on rapor_sarpras.sekolah_id = sekolah.sekolah_id
                    JOIN ref.mst_wilayah kec with(nolock) on kec.kode_wilayah = LEFT(sekolah.kode_wilayah,6)
                    JOIN ref.mst_wilayah kab with(nolock) on kab.kode_wilayah = kec.mst_kode_wilayah
                    JOIN ref.mst_wilayah prop with(nolock) on prop.kode_wilayah = kab.mst_kode_wilayah
                    JOIN ref.mst_wilayah negara with(nolock) on negara.kode_wilayah = prop.mst_kode_wilayah
                    WHERE kec.kode_wilayah = '".$kecamatan[$iKecamatan]->{'kode_wilayah'}."'"));

                    for ($iSekolah=0; $iSekolah < sizeof($fetch); $iSekolah++) { 
                        // echo "[INF] [".($iProvinsi+1)."/".sizeof($provinsi)."] [".($iKabupaten+1)."/".sizeof($kabupaten)."] [".($iKecamatan+1)."/".sizeof($kecamatan)."] [".($iSekolah+1)."/".sizeof($fetch)."] ".$fetch[$iSekolah]->{'nama'}.".".PHP_EOL;
                    
                        //datanya diproses ke db

                        try {
                            DB::connection('sqlsrv_3')->statement("IF NOT EXISTS ( SELECT * FROM rekap.rekap_rapor_dapodik_sekolah rekap_rapor WITH ( nolock ) WHERE sekolah_id = '".$fetch[$iSekolah]->{'sekolah_id'}."' AND semester_id = '".$fetch[$iSekolah]->{'semester_id'}."' ) 
                            INSERT INTO rekap.rekap_rapor_dapodik_sekolah (
                                sekolah_id,
                                semester_id,
                                bentuk_pendidikan_id,
                                status_sekolah,
                                nama,
                                npsn,
                                kode_wilayah_kecamatan,
                                kode_wilayah_kabupaten,
                                kode_wilayah_provinsi,
                                kode_wilayah_negara,
                                kecamatan,
                                kabupaten,
                                provinsi,
                                negara,
                                id_level_wilayah_kecamatan,
                                id_level_wilayah_kabupaten,
                                id_level_wilayah_provinsi,
                                id_level_wilayah_negara,
                                rapor_sekolah,
                                rapor_pd,
                                rapor_ptk,
                                rapor_rombel,
                                rapor_sarpras,
                                rapor_akhir,
                                create_date,
                                last_update,
                                soft_delete,
                                updater_id 
                            )
                            VALUES
                            (
                                '".$fetch[$iSekolah]->{'sekolah_id'}."',
                                '".$fetch[$iSekolah]->{'semester_id'}."',
                                '".$fetch[$iSekolah]->{'bentuk_pendidikan_id'}."',
                                '".$fetch[$iSekolah]->{'status_sekolah'}."',
                                '".$fetch[$iSekolah]->{'nama'}."',
                                '".$fetch[$iSekolah]->{'npsn'}."',
                                '".$fetch[$iSekolah]->{'kode_wilayah_kecamatan'}."',
                                '".$fetch[$iSekolah]->{'kode_wilayah_kabupaten'}."',
                                '".$fetch[$iSekolah]->{'kode_wilayah_provinsi'}."',
                                '".$fetch[$iSekolah]->{'kode_wilayah_negara'}."',
                                '".$fetch[$iSekolah]->{'kecamatan'}."',
                                '".$fetch[$iSekolah]->{'kabupaten'}."',
                                '".$fetch[$iSekolah]->{'provinsi'}."',
                                '".$fetch[$iSekolah]->{'negara'}."',
                                '".$fetch[$iSekolah]->{'id_level_wilayah_kecamatan'}."',
                                '".$fetch[$iSekolah]->{'id_level_wilayah_kabupaten'}."',
                                '".$fetch[$iSekolah]->{'id_level_wilayah_provinsi'}."',
                                '".$fetch[$iSekolah]->{'id_level_wilayah_negara'}."',
                                '".$fetch[$iSekolah]->{'rapor_sekolah'}."',
                                '".$fetch[$iSekolah]->{'rapor_pd'}."',
                                '".$fetch[$iSekolah]->{'rapor_ptk'}."',
                                '".$fetch[$iSekolah]->{'rapor_rombel'}."',
                                '".$fetch[$iSekolah]->{'rapor_sarpras'}."',
                                '".$fetch[$iSekolah]->{'rapor_akhir'}."',
                                '".$fetch[$iSekolah]->{'create_date'}."',
                                '".$fetch[$iSekolah]->{'last_update'}."',
                                '".$fetch[$iSekolah]->{'soft_delete'}."',
                                '".$fetch[$iSekolah]->{'updater_id'}."'  
                            ) ELSE 
                            UPDATE rekap.rekap_rapor_dapodik_sekolah 
                            SET 
                                bentuk_pendidikan_id        = '".$fetch[$iSekolah]->{'bentuk_pendidikan_id'}."',
                                status_sekolah              = '".$fetch[$iSekolah]->{'status_sekolah'}."',
                                nama                        = '".$fetch[$iSekolah]->{'nama'}."',
                                npsn                        = '".$fetch[$iSekolah]->{'npsn'}."',
                                kode_wilayah_kecamatan      = '".$fetch[$iSekolah]->{'kode_wilayah_kecamatan'}."',
                                kode_wilayah_kabupaten      = '".$fetch[$iSekolah]->{'kode_wilayah_kabupaten'}."',
                                kode_wilayah_provinsi       = '".$fetch[$iSekolah]->{'kode_wilayah_provinsi'}."',
                                kode_wilayah_negara         = '".$fetch[$iSekolah]->{'kode_wilayah_negara'}."',
                                kecamatan                   = '".$fetch[$iSekolah]->{'kecamatan'}."',
                                kabupaten                   = '".$fetch[$iSekolah]->{'kabupaten'}."',
                                provinsi                    = '".$fetch[$iSekolah]->{'provinsi'}."',
                                negara                      = '".$fetch[$iSekolah]->{'negara'}."',
                                id_level_wilayah_kecamatan  = '".$fetch[$iSekolah]->{'id_level_wilayah_kecamatan'}."',
                                id_level_wilayah_kabupaten  = '".$fetch[$iSekolah]->{'id_level_wilayah_kabupaten'}."',
                                id_level_wilayah_provinsi   = '".$fetch[$iSekolah]->{'id_level_wilayah_provinsi'}."',
                                id_level_wilayah_negara     = '".$fetch[$iSekolah]->{'id_level_wilayah_negara'}."',
                                rapor_sekolah               = '".$fetch[$iSekolah]->{'rapor_sekolah'}."',
                                rapor_pd                    = '".$fetch[$iSekolah]->{'rapor_pd'}."',
                                rapor_ptk                   = '".$fetch[$iSekolah]->{'rapor_ptk'}."',
                                rapor_rombel                = '".$fetch[$iSekolah]->{'rapor_rombel'}."',
                                rapor_sarpras               = '".$fetch[$iSekolah]->{'rapor_sarpras'}."',
                                rapor_akhir                 = '".$fetch[$iSekolah]->{'rapor_akhir'}."',
                                create_date                 = '".$fetch[$iSekolah]->{'create_date'}."',
                                last_update                 = '".$fetch[$iSekolah]->{'last_update'}."',
                                soft_delete                 = '".$fetch[$iSekolah]->{'soft_delete'}."',
                                updater_id                  = '".$fetch[$iSekolah]->{'updater_id'}."'
                            WHERE
                                sekolah_id = '".$fetch[$iSekolah]->{'sekolah_id'}."' 
                                AND semester_id = '".$fetch[$iSekolah]->{'semester_id'}."'");

                            echo "[INF] [".($iProvinsi+1)."/".sizeof($provinsi)."] [".($iKabupaten+1)."/".sizeof($kabupaten)."] [".($iKecamatan+1)."/".sizeof($kecamatan)."] [".($iSekolah+1)."/".sizeof($fetch)."] ".$fetch[$iSekolah]->{'nama'}." [BERHASIL]".PHP_EOL;
                            
                            Redis::set( 'rekap_rapor_dapodik_sekolah:'.$fetch[$iSekolah]->{'sekolah_id'}.':'.$fetch[$iSekolah]->{'semester_id'}, json_encode($fetch[$iSekolah]) );
                            Redis::lrem( 'rekap_rapor_dapodik_sekolah:all:'.$semester_id, 0, $fetch[$iSekolah]->{'sekolah_id'}.':'.$fetch[$iSekolah]->{'semester_id'} );
                            Redis::lrem( 'rekap_rapor_dapodik_sekolah:'.trim($fetch[$iSekolah]->{'kode_wilayah_provinsi'}).':'.$semester_id, 0, $fetch[$iSekolah]->{'sekolah_id'}.':'.$fetch[$iSekolah]->{'semester_id'} );
                            Redis::lrem( 'rekap_rapor_dapodik_sekolah:'.trim($fetch[$iSekolah]->{'kode_wilayah_kabupaten'}).':'.$semester_id, 0, $fetch[$iSekolah]->{'sekolah_id'}.':'.$fetch[$iSekolah]->{'semester_id'} );
                            Redis::lrem( 'rekap_rapor_dapodik_sekolah:'.trim($fetch[$iSekolah]->{'kode_wilayah_kecamatan'}).':'.$semester_id, 0, $fetch[$iSekolah]->{'sekolah_id'}.':'.$fetch[$iSekolah]->{'semester_id'} );
                            
                            if((int)$fetch[$iSekolah]->{'soft_delete'} == 0){
                                Redis::rpush( 'rekap_rapor_dapodik_sekolah:all:'.$semester_id, $fetch[$iSekolah]->{'sekolah_id'}.':'.$fetch[$iSekolah]->{'semester_id'} );
                                Redis::rpush( 'rekap_rapor_dapodik_sekolah:'.trim($fetch[$iSekolah]->{'kode_wilayah_provinsi'}).':'.$semester_id, $fetch[$iSekolah]->{'sekolah_id'}.':'.$fetch[$iSekolah]->{'semester_id'} );
                                Redis::rpush( 'rekap_rapor_dapodik_sekolah:'.trim($fetch[$iSekolah]->{'kode_wilayah_kabupaten'}).':'.$semester_id, $fetch[$iSekolah]->{'sekolah_id'}.':'.$fetch[$iSekolah]->{'semester_id'} );
                                Redis::rpush( 'rekap_rapor_dapodik_sekolah:'.trim($fetch[$iSekolah]->{'kode_wilayah_kecamatan'}).':'.$semester_id, $fetch[$iSekolah]->{'sekolah_id'}.':'.$fetch[$iSekolah]->{'semester_id'} );
                            }

                            //rapor dapodik periodik
                            // 1990-12-31
                            $bulan = substr($fetch[$iSekolah]->{'last_update'},5,2);

                            // echo $bulan.PHP_EOL;die;

                            DB::connection('sqlsrv_3')->statement("IF NOT EXISTS ( SELECT * FROM rekap.rekap_rapor_dapodik_periodik rekap_rapor WITH ( nolock ) WHERE sekolah_id = '".$fetch[$iSekolah]->{'sekolah_id'}."' AND semester_id = '".$fetch[$iSekolah]->{'semester_id'}."' ) 
                            INSERT INTO rekap.rekap_rapor_dapodik_periodik (
                                sekolah_id,
                                semester_id,
                                akurat_sekolah_bulan_".$bulan.",
                                akurat_pd_bulan_".$bulan.",
                                akurat_ptk_bulan_".$bulan.",
                                akurat_sarpras_bulan_".$bulan.",
                                akurat_rombel_bulan_".$bulan.",
                                akurat_bulan_".$bulan."
                            )
                            VALUES
                            (
                                '".$fetch[$iSekolah]->{'sekolah_id'}."',
                                '".$fetch[$iSekolah]->{'semester_id'}."',
                                '".$fetch[$iSekolah]->{'rapor_sekolah'}."',
                                '".$fetch[$iSekolah]->{'rapor_pd'}."',
                                '".$fetch[$iSekolah]->{'rapor_ptk'}."',
                                '".$fetch[$iSekolah]->{'rapor_sarpras'}."',
                                '".$fetch[$iSekolah]->{'rapor_rombel'}."',
                                '".$fetch[$iSekolah]->{'rapor_akhir'}."'
                            ) ELSE 
                            UPDATE rekap.rekap_rapor_dapodik_periodik 
                            SET 
                                akurat_sekolah_bulan_".$bulan." = '".$fetch[$iSekolah]->{'rapor_sekolah'}."',
                                akurat_pd_bulan_".$bulan." = '".$fetch[$iSekolah]->{'rapor_pd'}."',
                                akurat_ptk_bulan_".$bulan." = '".$fetch[$iSekolah]->{'rapor_ptk'}."',
                                akurat_sarpras_bulan_".$bulan." = '".$fetch[$iSekolah]->{'rapor_sarpras'}."',
                                akurat_rombel_bulan_".$bulan." = '".$fetch[$iSekolah]->{'rapor_rombel'}."',
                                akurat_bulan_".$bulan." = '".$fetch[$iSekolah]->{'rapor_akhir'}."'
                            WHERE
                                sekolah_id = '".$fetch[$iSekolah]->{'sekolah_id'}."' 
                                AND semester_id = '".$fetch[$iSekolah]->{'semester_id'}."'");

                        } catch (\Throwable $th) {
                            echo "[INF] [".($iProvinsi+1)."/".sizeof($provinsi)."] [".($iKabupaten+1)."/".sizeof($kabupaten)."] [".($iKecamatan+1)."/".sizeof($kecamatan)."] [".($iSekolah+1)."/".sizeof($fetch)."] ".$fetch[$iSekolah]->{'nama'}." [GAGAL]".PHP_EOL;
                        }
                    }
                }
            }

        }
    }
}
