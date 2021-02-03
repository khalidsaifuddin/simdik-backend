<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class rekapRaporPD extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rekap:rapor_pd {--semester_id=20191}';

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
        //
        $limit = 10000;
        $nomor_global = 1;
        // $semester_id = '20191';
        $semester_id = $this->option('semester_id');

        // $sekolah_total = DB::select(DB::raw("select sum(1) as total from sekolah where soft_delete = 0"))[0]->{'total'};
        $sekolah_total = 1;

        for ($i=0; $i < $sekolah_total; $i++) { 
            if($i % $limit == 0){
                echo "[INF] Mengambil data sekolah dari ".($i+1)." sampai ".($i+$limit).PHP_EOL;

                $sekolahs = DB::select(DB::raw("select * from sekolah where soft_delete = 0 order by nama desc OFFSET ".$i." ROWS FETCH NEXT ".($i+$limit)." ROWS ONLY"));
                
                for ($iSekolah=0; $iSekolah < sizeof($sekolahs); $iSekolah++) { 
                    echo "[INF] ".$nomor_global.". Mengambil data peserta didik ".$sekolahs[$iSekolah]->{'nama'}." - ".$sekolahs[$iSekolah]->{'kode_wilayah'}.PHP_EOL;
                    
                    DB::connection('sqlsrv_3')->table('rapor_pd')
                    ->where('sekolah_id', '=', $sekolahs[$iSekolah]->{'sekolah_id'})
                    ->where('semester_id', '=', $semester_id)
                    ->delete();

                    $listRaporPdSekolah = Redis::lrange( 'rapor_pd:'.$sekolahs[$iSekolah]->{'sekolah_id'}.':'.$semester_id,0,-1 );
                    
                    for ($iRaporPd=0; $iRaporPd < sizeof($listRaporPdSekolah); $iRaporPd++) { 
                        Redis::lrem( 'rapor_pd:all:'.$semester_id, 0, $listRaporPdSekolah[$iRaporPd] );
                    }
                    Redis::del( 'rapor_pd:'.$sekolahs[$iSekolah]->{'sekolah_id'}.':'.$semester_id );

                    $sql = "SELECT
                        newid() AS rapor_pd_id,
                        ".$semester_id." AS semester_id,
                        peserta_didik.peserta_didik_id,
                        rpd.sekolah_id ,
                        peserta_didik.nama,
                        peserta_didik.nisn,
                        (
                            (CASE WHEN (peserta_didik.nama IS NOT NULL-- nggak null
                            AND peserta_didik.nama !=''-- nggak string kosong
                            AND peserta_didik.nama NOT LIKE '%[0-9]%'-- nggak mengandung angka
                            ) THEN '' ELSE 
                                (CASE 
                                WHEN peserta_didik.nama IS NULL THEN 'Nama kosong, '
                                WHEN peserta_didik.nama = '' THEN 'Nama kosong, '
                                WHEN peserta_didik.nama LIKE '%[0-9]%' THEN 'Nama mengandung angka, '
                                ELSE ''
                                END)
                            END)
                            +
                            (CASE WHEN (peserta_didik.tanggal_lahir IS NOT NULL-- nggak null
                            AND peserta_didik.tanggal_lahir !=''-- nggak string kosong
                            ) THEN '' ELSE 
                                (CASE 
                                WHEN peserta_didik.tanggal_lahir IS NULL THEN 'Tanggal Lahir kosong, '
                                WHEN peserta_didik.tanggal_lahir = '' THEN 'Tanggal Lahir kosong, '
                                WHEN peserta_didik.tanggal_lahir < '1920-01-01' THEN 'Tanggal Lahir tidak wajar (terlalu tua)'
                                WHEN peserta_didik.tanggal_lahir < '2019-01-01' THEN 'Tanggal Lahir tidak wajar (terlalu muda)'
                                ELSE ''
                                END)
                            END)
                            +
                            (CASE WHEN (peserta_didik.nisn IS NOT NULL-- nggak null
                            AND peserta_didik.nisn !=''-- nggak string kosong
                            AND LEN(peserta_didik.nisn)=10 -- panjang pas 10
                            ) THEN '' ELSE 
                                (CASE 
                                WHEN peserta_didik.nisn IS NULL THEN 'NISN kosong, '
                                WHEN peserta_didik.nisn = '' THEN 'NISN kosong'
                                WHEN LEN(peserta_didik.nisn)>10 THEN 'NISN terlalu panjang, '
                                WHEN LEN(peserta_didik.nisn)<10 THEN 'NISN terlalu pendek, '
                                ELSE ''
                                END)
                            END)
                            +
                            (CASE WHEN (peserta_didik.nomor_telepon_seluler IS NOT NULL-- nggak null
                            AND peserta_didik.nomor_telepon_seluler !=''-- nggak string kosong
                            ) THEN '' ELSE  
                                (CASE 
                                WHEN peserta_didik.nomor_telepon_seluler IS NULL THEN 'No HP kosong, '
                                WHEN peserta_didik.nomor_telepon_seluler ='' THEN 'No HP kosong, '
                                WHEN peserta_didik.nama LIKE '%[a-z][A-Z]%' THEN 'No HP mengandung karakter selain angka, '
                                ELSE '' 
                                END)
                            END)
                            +
                            (CASE WHEN (peserta_didik.email IS NOT NULL-- nggak null
                            AND peserta_didik.email !=''-- nggak string kosong
                            ) THEN '' ELSE
                                (CASE 
                                WHEN peserta_didik.email IS NULL THEN 'Email kosong, '
                                WHEN peserta_didik.email !='' THEN 'Email kosong, '
                                ELSE ''
                                END)
                            END)
                            +
                            (CASE WHEN (peserta_didik.nama_ibu_kandung IS NOT NULL-- nggak null
                            AND peserta_didik.nama_ibu_kandung !=''-- nggak string kosong
                            AND peserta_didik.nama_ibu_kandung NOT LIKE '%[0-9]%'-- nggak mengandung angka
                            AND peserta_didik.nama_ibu_kandung NOT LIKE '%ibu%'-- nggak mengandung kata ibu
                            ) THEN '' ELSE 
                                (CASE 
                                WHEN peserta_didik.nama_ibu_kandung IS NULL THEN 'Nama Ibu Kandung kosong, '
                                WHEN peserta_didik.nama_ibu_kandung !='' THEN 'Nama Ibu Kandung kosong, '
                                WHEN peserta_didik.nama_ibu_kandung LIKE '%[0-9]%' THEN 'Nama Ibu Kandung mengandung angka, '
                                WHEN peserta_didik.nama_ibu_kandung LIKE '%ibu%' THEN 'Nama Ibu Kandung mengandung kata ibu'
                                ELSE ''
                                END)
                            END)
                            +
                            (CASE WHEN (peserta_didik.nama_ayah IS NOT NULL-- nggak null
                            AND peserta_didik.nama_ayah !=''-- nggak string kosong
                            AND peserta_didik.nama_ayah NOT LIKE '%[0-9]%'-- nggak mengandung angka
                            AND peserta_didik.nama_ayah NOT LIKE '%ayah%'-- nggak mengandung ayah
                            AND peserta_didik.nama_ayah NOT LIKE '%bapak%'-- nggak mengandung bapak
                            ) THEN '' ELSE 
                                (CASE 
                                WHEN peserta_didik.nama_ayah IS NULL THEN 'Nama Ayah kosong, '
                                WHEN peserta_didik.nama_ayah !='' THEN 'Nama Ayah kosong, '
                                WHEN peserta_didik.nama_ayah LIKE '%[0-9]%' THEN 'Nama Ayah mengandung angka, '
                                WHEN peserta_didik.nama_ayah LIKE '%ayah%' THEN 'Nama Ibu Kandung mengandung kata ayah'
                                WHEN peserta_didik.nama_ayah LIKE '%bapak%' THEN 'Nama Ibu Kandung mengandung kata bapak'
                                ELSE '' 
                                END)
                            END)
                            +
                            (CASE WHEN (peserta_didik.pekerjaan_id_ibu IS NOT NULL-- nggak null
                            AND peserta_didik.pekerjaan_id_ibu !=''-- nggak string kosong
                            ) THEN '' ELSE 
                                (CASE
                                WHEN peserta_didik.pekerjaan_id_ibu IS NULL THEN 'Pekerjaan ibu kosong, '
                                WHEN peserta_didik.pekerjaan_id_ibu !='' THEN 'Pekerjaan ibu kosong, '
                                ELSE ''
                                END)
                            END)
                            +
                            (CASE WHEN (peserta_didik.pekerjaan_id_ayah IS NOT NULL-- nggak null
                            AND peserta_didik.pekerjaan_id_ayah !=''-- nggak string kosong
                            ) THEN '' ELSE 
                                (CASE
                                WHEN peserta_didik.pekerjaan_id_ayah IS NULL THEN 'Pekerjaan ayah kosong, '
                                WHEN peserta_didik.pekerjaan_id_ayah !='' THEN 'Pekerjaan ayah kosong, '
                                ELSE ''
                                END)
                            END)
                            +
                            ( CASE WHEN peserta_didik.penerima_KIP = 1 THEN ( CASE WHEN peserta_didik.no_KIP IS NOT NULL THEN '' ELSE 'No KIP Penerima KIP kosong, ' END ) ELSE '' END )
                            +
                            (CASE WHEN (pdl.tinggi_badan IS NOT NULL-- nggak null
                            AND ISNUMERIC(pdl.tinggi_badan)= 1 -- nggak string kosong
                            AND pdl.tinggi_badan != 0 -- nggak 0
                            ) THEN '' ELSE 
                                (CASE 
                                WHEN pdl.tinggi_badan IS NULL THEN 'Tinggi badan kosong, '
                                WHEN ISNUMERIC(pdl.tinggi_badan) = 1 THEN 'Tinggi badan kosong, '
                                WHEN pdl.tinggi_badan != 0 THEN 'Tinggi badan terisi angka 0, '
                                ELSE '' 
                                END)
                            END)
                            +
                            (CASE WHEN (pdl.berat_badan IS NOT NULL-- nggak null
                            AND ISNUMERIC(pdl.berat_badan)= 1 -- nggak string kosong
                            AND pdl.berat_badan != 0 -- nggak 0
                            ) THEN '' ELSE
                                (CASE 
                                WHEN pdl.berat_badan IS NULL THEN 'Berat badan kosong, '
                                WHEN ISNUMERIC(pdl.berat_badan) = 1 THEN 'Berat badan kosong, '
                                WHEN pdl.berat_badan != 0 THEN 'Berat badan terisi angka 0, '
                                ELSE '' 
                                END)
                            END)
                            +
                            (CASE WHEN (peserta_didik.lintang IS NOT NULL-- nggak null
                            AND peserta_didik.lintang != 0 -- nggak 0
                            ) THEN '' ELSE
                                (CASE 
                                WHEN peserta_didik.lintang IS NULL THEN 'Koordinat lintang kosong, '
                                WHEN peserta_didik.lintang != 0  THEN 'Koordinat lintang kosong, '
                                ELSE '' 
                                END)
                            END)
                            +
                            (CASE WHEN (peserta_didik.bujur IS NOT NULL-- nggak null
                            AND peserta_didik.bujur != 0 -- nggak 0
                            ) THEN '' ELSE 
                                (CASE 
                                WHEN peserta_didik.bujur IS NULL THEN 'Koordinat bujur kosong, '
                                WHEN peserta_didik.bujur != 0  THEN 'Koordinat bujur kosong, '
                                ELSE '' 
                                END)
                            END)
                        ) as keterangan,
                        (
                            (
                            ( CASE WHEN 
                            ( 
                            peserta_didik.nama IS NOT NULL 						                -- nggak null
                                AND peserta_didik.nama != ''									-- nggak string kosong
                                AND peserta_didik.nama not like '%[0-9]%'			            -- nggak mengandung angka
                            ) THEN 1 ELSE 0 END ) + 
                            ( CASE WHEN 
                            ( 
                                peserta_didik.tanggal_lahir IS NOT NULL 			            -- nggak null
                                AND peserta_didik.tanggal_lahir != '' 				            -- nggak string kosong
                            ) THEN 1 ELSE 0 END ) + 
                            ( CASE WHEN 
                            ( 
                            peserta_didik.nisn IS NOT NULL 								        -- nggak null
                                AND peserta_didik.nisn != '' 									-- nggak string kosong
                                AND LEN(peserta_didik.nisn) = 10							    -- panjang pas 10
                            ) THEN 1 ELSE 0 END ) +
                            ( CASE WHEN
                            (
                                peserta_didik.nomor_telepon_seluler IS NOT NULL 				-- nggak null
                                AND peserta_didik.nomor_telepon_seluler != '' 					-- nggak string kosong
                            ) THEN 1 ELSE 0 END ) +
                            ( CASE WHEN
                            (
                                peserta_didik.email IS NOT NULL 								-- nggak null
                                AND peserta_didik.email != '' 									-- nggak string kosong
                            ) THEN 1 ELSE 0 END ) +
                            ( CASE WHEN
                            (
                                peserta_didik.nama_ibu_kandung IS NOT NULL 						-- nggak null
                                AND peserta_didik.nama_ibu_kandung != '' 						-- nggak string kosong
                                AND peserta_didik.nama_ibu_kandung not like '%[0-9]%'			-- nggak mengandung angka
                                AND peserta_didik.nama_ibu_kandung NOT LIKE '%ibu%'             -- nggak mengandung kata ibu
                            ) THEN 1 ELSE 0 END ) +
                            ( CASE WHEN
                            (
                                peserta_didik.nama_ayah IS NOT NULL 							-- nggak null
                                AND peserta_didik.nama_ayah != '' 								-- nggak string kosong
                                AND peserta_didik.nama_ayah not like '%[0-9]%'			        -- nggak mengandung angka
                                AND peserta_didik.nama_ayah NOT LIKE '%ayah%'                   -- nggak mengandung kata ayah
                                AND peserta_didik.nama_ayah NOT LIKE '%bapak%'                  -- nggak mengandung kata bapak
                            ) THEN 1 ELSE 0 END ) +
                            ( CASE WHEN
                            (
                                peserta_didik.pekerjaan_id_ibu IS NOT NULL 						-- nggak null
                                AND peserta_didik.pekerjaan_id_ibu != '' 						-- nggak string kosong
                            ) THEN 1 ELSE 0 END ) +
                            ( CASE WHEN
                            (
                                peserta_didik.pekerjaan_id_ayah IS NOT NULL 					-- nggak null
                                AND peserta_didik.pekerjaan_id_ayah != '' 						-- nggak string kosong
                            ) THEN 1 ELSE 0 END ) +
                            (
                                CASE WHEN peserta_didik.penerima_KIP = 1 then ( case when peserta_didik.no_KIP IS NOT NULl then 1 else 0 end ) else 1 end
                            ) +
                            ( CASE WHEN
                            (
                                pdl.tinggi_badan IS NOT NULL 								    -- nggak null
                                AND ISNUMERIC(pdl.tinggi_badan) = 1 				            -- nggak string kosong
                                AND pdl.tinggi_badan != 0										-- nggak 0
                            ) THEN 1 ELSE 0 END ) +
                            ( CASE WHEN
                            (
                                pdl.berat_badan IS NOT NULL 								    -- nggak null
                                AND ISNUMERIC(pdl.berat_badan) = 1 			                    -- nggak string kosong
                                AND pdl.berat_badan != 0 										-- nggak 0
                            ) THEN 1 ELSE 0 END ) +
                            ( CASE WHEN
                            (
                                peserta_didik.lintang IS NOT NULL 								-- nggak null
                                AND peserta_didik.lintang != 0 									-- nggak 0
                            ) THEN 1 ELSE 0 END ) +
                            ( CASE WHEN
                            (
                                peserta_didik.bujur IS NOT NULL 								-- nggak null
                                AND peserta_didik.bujur != 0 									-- nggak 0
                            ) THEN 1 ELSE 0 END )
                        ) 
                        / CAST ( 14 AS FLOAT ) * 100 
                        ) AS nilai_rapor,
                        getdate() AS create_date,
                        getdate() AS last_update,
                        0 AS soft_delete,
                        NULL AS updater_id 
                    FROM
                        peserta_didik -- LEFT
                        JOIN (
                            SELECT
                                ROW_NUMBER () OVER ( PARTITION BY anggota_rombel.peserta_didik_id ORDER BY rombongan_belajar.tingkat_pendidikan_id DESC ) AS urutan,
                                anggota_rombel.*,
                                rombongan_belajar.sekolah_id 
                            FROM
                                anggota_rombel
                                JOIN rombongan_belajar ON rombongan_belajar.rombongan_belajar_id = anggota_rombel.rombongan_belajar_id
                                JOIN ptk ON ptk.ptk_id = rombongan_belajar.ptk_id
                                JOIN sekolah ON sekolah.sekolah_id = rombongan_belajar.sekolah_id 
                            WHERE
                                anggota_rombel.Soft_delete = 0 
                            AND rombongan_belajar.Soft_delete = 0 
                            AND ptk.Soft_delete = 0 
                            AND sekolah.Soft_delete = 0 
                            AND rombongan_belajar.jenis_rombel IN ( 1, 8, 9, 10, 11, 12, 13 ) 
                            AND rombongan_belajar.semester_id = ".$semester_id." 
                            AND rombongan_belajar.sekolah_id = '".$sekolahs[$iSekolah]->{'sekolah_id'}."'
                        ) ar ON ar.peserta_didik_id = peserta_didik.peserta_didik_id AND ar.urutan = 1
                        JOIN (
                            SELECT
                                ROW_NUMBER () OVER ( PARTITION BY registrasi_peserta_didik.peserta_didik_id ORDER BY registrasi_peserta_didik.tanggal_masuk_sekolah DESC ) AS urutan,
                                * 
                            FROM
                                registrasi_peserta_didik 
                            WHERE
                                Soft_delete = 0 
                            AND jenis_keluar_id IS NULL 
                        ) rpd ON rpd.peserta_didik_id = peserta_didik.peserta_didik_id AND rpd.sekolah_id = ar.sekolah_id -- 	AND rpd.urutan = 1
                        LEFT JOIN peserta_didik_longitudinal pdl on pdl.peserta_didik_id = peserta_didik.peserta_didik_id and pdl.semester_id = ".$semester_id."
                    WHERE
                        peserta_didik.Soft_delete = 0
                        AND rpd.sekolah_id = '".$sekolahs[$iSekolah]->{'sekolah_id'}."'";

                    // echo $sql;die;

                    $records = DB::select(DB::raw($sql));
                    // order by peserta_didik_id asc"));

                    for ($iPd=0; $iPd < sizeof($records); $iPd++) { 

                        // echo json_encode($records[$i]);die;
                        $fetch_cek = DB::table('vld_peserta_didik')
                        ->where('peserta_didik_id','=', $records[$iPd]->{'peserta_didik_id'})
                        ->where('app_username','=','rapor_dapodik')
                        ->get();

                        try {
                            //code...
                            if(sizeof($fetch_cek) > 0){
                                //update
                                $label = 'UPDATE';
                                $exe = DB::connection('sqlsrv_rw')->table('vld_peserta_didik')
                                ->where('peserta_didik_id','=', $records[$iPd]->{'peserta_didik_id'})
                                ->where('app_username','=','rapor_dapodik')
                                ->update([
                                    'status_validasi' => ((float)$records[$iPd]->{'nilai_rapor'} < 100 ? 99 : 0),
                                    'field_error' => null,
                                    'error_message' => $records[$iPd]->{'keterangan'},
                                    'last_update' => DB::raw('getdate()'),
                                    'soft_delete' => ((float)$records[$iPd]->{'nilai_rapor'} < 100 ? 0 : 1)
                                ]);
    
                            }else{
                                //insert
                                $label = 'INSERT';
                                $exe = DB::connection('sqlsrv_rw')->table('vld_peserta_didik')
                                ->insert([
                                    'logid' => DB::raw('newid()'),
                                    'peserta_didik_id' => $records[$iPd]->{'peserta_didik_id'},
                                    'idtype' => 0,
                                    'status_validasi' => ((float)$records[$iPd]->{'nilai_rapor'} < 100 ? 99 : 0),
                                    'field_error' => null,
                                    'error_message' => substr(("Kualitas: ".round((float)$records[$iPd]->{'nilai_rapor'},2) ."% (". $records[$iPd]->{'keterangan'} .")"),0,140),
                                    'app_username' => 'rapor_dapodik',
                                    'create_date' => DB::raw('getdate()'),
                                    'last_update' => DB::raw('getdate()'),
                                    'soft_delete' => ((float)$records[$iPd]->{'nilai_rapor'} < 100 ? 0 : 1),
                                    'last_sync' => '1990-01-01 00:00:00',
                                    'updater_id' => $records[$iPd]->{'peserta_didik_id'}
                                ]);
    
                            }
    
                            if($exe){
                                echo "[INF] [BERHASIL] [".$label."] ".$records[$iPd]->{'nama'}." - ".substr(("Kualitas: ".round((float)$records[$iPd]->{'nilai_rapor'},2) ."% (". $records[$iPd]->{'keterangan'} .")"),0,140).PHP_EOL;
                            }else{
                                echo "[INF] [GAGAL] [".$label."] ".$records[$iPd]->{'nama'}." - ".substr(("Kualitas: ".round((float)$records[$iPd]->{'nilai_rapor'},2) ."% (". $records[$iPd]->{'keterangan'} .")"),0,140).PHP_EOL;
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            echo "[INF] [GAGAL] [".$label."] ".$records[$iPd]->{'nama'}." - kesalahan teknis".PHP_EOL;
                        }


                        
                        // // start of dikomen dlu sementara, karena sql server is such a piece of sh*t
                        // DB::connection('sqlsrv_3')->table('rapor_pd')
                        // ->insert([
                        //     'rapor_pd_id' => $records[$iPd]->{'rapor_pd_id'},
                        //     'semester_id' => $records[$iPd]->{'semester_id'}, 
                        //     'peserta_didik_id' => $records[$iPd]->{'peserta_didik_id'}, 
                        //     'sekolah_id' => $records[$iPd]->{'sekolah_id'}, 
                        //     'nama' => $records[$iPd]->{'nama'}, 
                        //     'nisn' => $records[$iPd]->{'nisn'}, 
                        //     'nilai_rapor' => $records[$iPd]->{'nilai_rapor'}, 
                        //     'create_date' => $records[$iPd]->{'create_date'}, 
                        //     'last_update' => $records[$iPd]->{'last_update'}, 
                        //     'soft_delete' => $records[$iPd]->{'soft_delete'}, 
                        //     'updater_id' => $records[$iPd]->{'updater_id'}
                        // ]);
                        // // end of dikomen dlu sementara, karena sql server is such a piece of sh*t

                        Redis::set( 'rapor_pd:'.$records[$iPd]->{'peserta_didik_id'}.':'.$records[$iPd]->{'semester_id'}, json_encode($records[$iPd]) );
                        Redis::lpush( 'rapor_pd:'.$records[$iPd]->{'sekolah_id'}.':'.$semester_id, $records[$iPd]->{'peserta_didik_id'}.':'.$records[$iPd]->{'semester_id'} );
                        Redis::lpush( 'rapor_pd:all:'.$semester_id, $records[$iPd]->{'peserta_didik_id'}.':'.$records[$iPd]->{'semester_id'} );
                    }

                    $nomor_global++;
                }
            }
        }
    }
}
