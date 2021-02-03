<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class rekapRaporPTK extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rekap:rapor_ptk {--semester_id=20191}';

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
        $limit = 10000;
        $nomor_global = 1;
        // $semester_id = '20191';
        $semester_id = $this->option('semester_id');

        $sekolah_total = DB::select(DB::raw("select sum(1) as total from sekolah where soft_delete = 0"))[0]->{'total'};

        for ($i=0; $i < $sekolah_total; $i++) { 
            if($i % $limit == 0){
                echo "[INF] Mengambil data sekolah dari ".($i+1)." sampai ".($i+$limit).PHP_EOL;

                $sekolahs = DB::select(DB::raw("select * from sekolah where soft_delete = 0 order by sekolah_id OFFSET ".$i." ROWS FETCH NEXT ".($i+$limit)." ROWS ONLY"));

                for ($iSekolah=0; $iSekolah < sizeof($sekolahs); $iSekolah++) { 
                    echo "[INF] ".$nomor_global.". Mengambil data PTK ".$sekolahs[$iSekolah]->{'nama'}." - ".$sekolahs[$iSekolah]->{'kode_wilayah'}.PHP_EOL;
                    
                    DB::connection('sqlsrv_3')->table('rapor_ptk')
                    ->where('sekolah_id', '=', $sekolahs[$iSekolah]->{'sekolah_id'})
                    ->where('semester_id', '=', $semester_id)
                    ->delete();

                    $listRaporPtkSekolah = Redis::lrange( 'rapor_ptk:'.$sekolahs[$iSekolah]->{'sekolah_id'}.':'.$semester_id, 0, -1 );

                    for ($iRaporPtk=0; $iRaporPtk < sizeof($listRaporPtkSekolah); $iRaporPtk++) { 
                        Redis::lrem( 'rapor_ptk:all:'.$semester_id, 0, $listRaporPtkSekolah[$iRaporPtk] );
                    }
                    Redis::del( 'rapor_ptk:'.$sekolahs[$iSekolah]->{'sekolah_id'}.':'.$semester_id );

                    $sql = "
                    SELECT
                        newid() AS rapor_ptk_id,
                        ".$semester_id." AS semester_id,
                        ptk.ptk_id,
                        ptkd.sekolah_id,
                        ptk.nama,
                        ( ( cast ( ( 
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
                                ptk.nama IS NOT NULL
                                AND ptk.nama != ''
                                AND ptk.nama not like '%[0-9]%'
                            ) THEN 1 ELSE 0 END ) +
                            ( CASE WHEN 
                            ( 
                                ptk.tanggal_lahir IS NOT NULL
                                AND ptk.tanggal_lahir != ''
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
                        ) as float(24) ) / 19 ) * 100 ) as nilai_rapor,
                        getdate() AS create_date,
                        getdate() AS last_update,
                        0 AS soft_delete,
                        NULL AS updater_id,
                        (
                            ( case when ptk.status_kepegawaian_id in ( 1, 2, 3, 10 ) then
                            ( case when ptk.sk_cpns is null then 'SK CPNS kosong, ' else '' end )
                            else
                            ''
                            end ) +
                        ( case when ptk.status_kepegawaian_id in ( 1, 2, 3, 10 ) then
                            ( case when ptk.tgl_cpns is null then 'Tanggal CPNS kosong, ' else '' end )
                            else
                            ''
                            end ) +
                        ( case when ptk.status_kepegawaian_id in ( 1, 2, 3, 10 ) then
                            ( case when ptk.tmt_pns is null then 'TMT PNS kosong, ' else '' end )
                            else
                            ''
                            end ) +
                        ( case when ptk.status_kepegawaian_id in ( 1, 2, 3, 10 ) then
                            ( case when ptk.pangkat_golongan_id is null then 'Pangkat Golongan kosong, ' else '' end )
                            else
                            ''
                            end ) +
                        ( case when ptk.status_perkawinan = 1 then
                            ( case when ptk.nama_suami_istri is null then 'Nama suami/istri kosong, ' else '' end )
                            else
                            ''
                            end ) +
                        ( case when ptk.status_perkawinan = 1 then
                            ( case when ptk.pekerjaan_suami_istri is null then 'Pekerjaan Suami/Istri kosong, ' else '' end )
                            else
                            ''
                            end ) +
                        ( case when ptk.sk_pengangkatan is null or ptk.sk_pengangkatan = '-' then 'SK Pengangkatan kosong, ' else '' end ) +
                        ( case when ptk.tmt_pengangkatan is null then 'TMT Pengangkatan, ' else '' end ) +
                        ( case when ptk.nama_ibu_kandung is null or ptk.nama_ibu_kandung = '-' then 'Nama Ibu kandung tidak wajar, ' else '' end ) +
                        ( case when ptk.nik is null or ptk.nik = '-' then 'NIK kosong, ' else '' end ) +
                        ( case when ptk.nuptk is null or ptk.nuptk = '-' then 'NUPTK kosong, ' else '' end ) +
                        ( case when ptk.desa_kelurahan is null or ptk.desa_kelurahan = '-' then 'Alamat desa/kelurahan kosong, ' else '' end ) +
                        ( case when ptk.no_hp is null or ptk.no_hp = '-' then 'No HP kosong, ' else '' end ) +
                        ( case when ptk.email is null or ptk.email = '-' then 'Email kosong, ' else '' end ) +
                        ( case when ptk.npwp is null or ptk.npwp = '-' then 'NPWP kosong, ' else '' end ) +
                        ( CASE WHEN
                        (
                            ptk.nama IS NOT NULL
                            AND ptk.nama != ''
                            AND ptk.nama not like '%[0-9]%'
                        ) THEN '' ELSE 'Nama tidak wajar, ' END ) +
                        ( CASE WHEN
                        (
                            ptk.tanggal_lahir IS NOT NULL
                            AND ptk.tanggal_lahir != ''
                        ) THEN '' ELSE 'Tanggal Lahir tidak wajar, ' END ) +
                        (
                        CASE
                        WHEN ptk.no_telepon_rumah IS NULL THEN ''
                        WHEN ( ptk.no_telepon_rumah IS NOT NULL AND ptk.no_telepon_rumah like '%[0-9]%' ) THEN ''
                        ELSE 'Telepon rumah mengantung karakter selain angka, ' END
                        ) +
                        ( case when ptk.status_kepegawaian_id in ( 1, 2, 3, 10 ) then
                            ( case when ptk.nip is null then 'NIP kosong, ' else '' end )
                            else
                            ''
                            end )
                        ) as keterangan
                    FROM
                        ptk ptk WITH ( nolock )
                        JOIN ptk_terdaftar ptkd WITH ( nolock ) ON ptk.ptk_id = ptkd.ptk_id
                        JOIN ref.tahun_ajaran ta WITH ( nolock ) ON ta.tahun_ajaran_id = ptkd.tahun_ajaran_id 
                    WHERE
                        ptk.Soft_delete = 0 
                    AND ptkd.Soft_delete = 0 
                    AND ptkd.ptk_induk = 1 
                    AND ptkd.tahun_ajaran_id = '".substr($semester_id,0,4)."' 
                    AND ptk.jenis_ptk_id IN ( 3, 4, 5, 6, 12, 13, 14 ) 
                    AND ptkd.sekolah_id = '".$sekolahs[$iSekolah]->{'sekolah_id'}."'
                    AND ( ptkd.tgl_ptk_keluar > ta.tanggal_selesai OR ptkd.jenis_keluar_id IS NULL )";

                    // echo $sql;die;

                    $records = DB::select(DB::raw($sql));

                    for ($iPtk=0; $iPtk < sizeof($records); $iPtk++) { 

                        $fetch_cek = DB::table('vld_ptk')
                        ->where('ptk_id','=', $records[$iPtk]->{'ptk_id'})
                        ->where('app_username','=','rapor_dapodik')
                        ->get();

                        try {
                            
                            if(sizeof($fetch_cek) > 0){
                                //update
                                $label = 'UPDATE';
                                $exe = DB::connection('sqlsrv_rw')->table('vld_ptk')
                                ->where('ptk_id','=', $records[$iPtk]->{'ptk_id'})
                                ->where('app_username','=','rapor_dapodik')
                                ->update([
                                    'status_validasi' => ((float)$records[$iPtk]->{'nilai_rapor'} < 100 ? 99 : 0),
                                    'field_error' => null,
                                    'error_message' => $records[$iPtk]->{'keterangan'},
                                    'last_update' => DB::raw('getdate()'),
                                    'soft_delete' => ((float)$records[$iPtk]->{'nilai_rapor'} < 100 ? 0 : 1)
                                ]);
    
                            }else{
                                //insert
                                $label = 'INSERT';
                                $exe = DB::connection('sqlsrv_rw')->table('vld_ptk')
                                ->insert([
                                    'logid' => DB::raw('newid()'),
                                    'ptk_id' => $records[$iPtk]->{'ptk_id'},
                                    'idtype' => 0,
                                    'status_validasi' => ((float)$records[$iPtk]->{'nilai_rapor'} < 100 ? 99 : 0),
                                    'field_error' => null,
                                    'error_message' => substr(("Kualitas: ".round((float)$records[$iPtk]->{'nilai_rapor'},2) ."% (". $records[$iPtk]->{'keterangan'} .")"),0,140),
                                    'app_username' => 'rapor_dapodik',
                                    'create_date' => DB::raw('getdate()'),
                                    'last_update' => DB::raw('getdate()'),
                                    'soft_delete' => ((float)$records[$iPtk]->{'nilai_rapor'} < 100 ? 0 : 1),
                                    'last_sync' => '1990-01-01 00:00:00',
                                    'updater_id' => $records[$iPtk]->{'ptk_id'}
                                ]);
    
                            }
    
                            if($exe){
                                echo "[INF] [BERHASIL] [".$label."] ".$records[$iPtk]->{'nama'}." - ".substr(("Kualitas: ".round((float)$records[$iPtk]->{'nilai_rapor'},2) ."% (". $records[$iPtk]->{'keterangan'} .")"),0,140).PHP_EOL;
                            }else{
                                echo "[INF] [GAGAL] [".$label."] ".$records[$iPtk]->{'nama'}." - ".substr(("Kualitas: ".round((float)$records[$iPtk]->{'nilai_rapor'},2) ."% (". $records[$iPtk]->{'keterangan'} .")"),0,140).PHP_EOL;
                            }
                        } catch (\Throwable $th) {
                            //throw $th;
                            echo "[INF] [GAGAL] [".$label."] ".$records[$iPtk]->{'nama'}." - kesalahan teknis".PHP_EOL;
                        }

                        Redis::set( 'rapor_ptk:'.$records[$iPtk]->{'ptk_id'}.':'.$records[$iPtk]->{'semester_id'}, json_encode($records[$iPtk]) );
                        Redis::lpush( 'rapor_ptk:'.$records[$iPtk]->{'sekolah_id'}.':'.$semester_id, $records[$iPtk]->{'ptk_id'}.':'.$records[$iPtk]->{'semester_id'} );
                        Redis::lpush( 'rapor_ptk:all:'.$semester_id, $records[$iPtk]->{'ptk_id'}.':'.$records[$iPtk]->{'semester_id'} );
                    }

                    $nomor_global++;
                }
            }
        }
    }
}
