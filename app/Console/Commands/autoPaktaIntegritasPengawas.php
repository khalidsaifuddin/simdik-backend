<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

use App\Console\Commands\Log\guidGenerator;
use App\Console\Commands\Log\logAutoVerval;

class autoPaktaIntegritasPengawas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rapor:auto_pakta_integritas_pengawas {--kode_wilayah=000000} {--sekolah_id=0} {--npsn=0}';

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
        $kode_wilayah = $this->option('kode_wilayah');
        $op_sekolah_id = $this->option('sekolah_id');
        $op_npsn = $this->option('npsn');

        $provinsi = DB::table(DB::raw('ref.mst_wilayah wilayah with(nolock)'))
        ->where('wilayah.id_level_wilayah','=',1)
        ->whereNull('wilayah.expired_date');

        if($kode_wilayah != '000000'){
            $provinsi = $provinsi->where('kode_wilayah','=',$kode_wilayah);
        }

        $provinsi = $provinsi->get();

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

                // $gage = substr($semester_id,4,1);

                for ($iKecamatan=0; $iKecamatan < sizeof($kecamatan); $iKecamatan++) { 


                    echo "[INF] [".($iProvinsi+1)."/".sizeof($provinsi)."] [".($iKabupaten+1)."/".sizeof($kabupaten)."] [".($iKecamatan+1)."/".sizeof($kecamatan)."] ".$provinsi[$iProvinsi]->{'nama'}." - ".$kabupaten[$iKabupaten]->{'nama'}." - ".$kecamatan[$iKecamatan]->{'nama'}.PHP_EOL;
                    
                    if($op_sekolah_id != '0'){
                        $params_sekolah_id = " AND sekolah.sekolah_id = '".$op_sekolah_id."'";
                    }else{
                        $params_sekolah_id  = "";
                    }

                    if($op_npsn != '0'){
                        $params_npsn = " AND sekolah.npsn = '".$op_npsn."'";
                    }else{
                        $params_npsn = "";
                    }

                    $sql = "SELECT
                        timeline.sekolah_id,
                        max(sekolah.nama) as nama_sekolah,
                        max(sekolah.npsn) as npsn,
                        SUM ( CASE WHEN jenis_timeline_id = 1 THEN 1 ELSE 0 END ) AS timeline_1,
                        SUM ( CASE WHEN jenis_timeline_id = 2 THEN 1 ELSE 0 END ) AS timeline_2,
                        SUM ( CASE WHEN jenis_timeline_id = 3 THEN 1 ELSE 0 END ) AS timeline_3,
                        SUM ( CASE WHEN jenis_timeline_id = 4 THEN 1 ELSE 0 END ) AS timeline_4,
                        SUM ( CASE WHEN jenis_timeline_id = 5 THEN 1 ELSE 0 END ) AS timeline_5,
                        MAX ( timeline.create_date ) AS tanggal,
                        DATEDIFF(
                            week,
                            MAX ( timeline.create_date ),
                        getdate()) AS interval_minggu,
                        ( CASE WHEN SUM ( CASE WHEN validasi_rapor.jumlah > 0 THEN 1 ELSE 0 END ) > 0 THEN 'Y' ELSE 'N' END ) AS rapor_valid,
                        MAX ( pengawas.pengguna_id ) AS pengguna_id,
                        MAX ( pengawas.nama ) AS nama_pengawas 
                    FROM
                        timeline with(nolock)
                        LEFT JOIN ( SELECT sekolah_id, SUM ( 1 ) AS jumlah FROM master_pmp with(nolock) WHERE r19 IS NOT NULL AND LEVEL = 'parent' GROUP BY sekolah_id ) validasi_rapor ON validasi_rapor.sekolah_id = timeline.sekolah_id
                        LEFT JOIN (
                        SELECT
                            ROW_NUMBER () OVER ( PARTITION BY sekolah_binaan.sekolah_id ORDER BY sekolah_binaan.last_update DESC ) AS urutan,
                            pengguna.pengguna_id,
                            sekolah_binaan.sekolah_id,
                            pengguna.nama AS nama 
                        FROM
                            sekolah_binaan with(nolock)
                            JOIN pengguna with(nolock) ON pengguna.pengguna_id = sekolah_binaan.pengguna_id 
                        WHERE
                            sekolah_binaan.soft_delete = 0 
                            AND pengguna.soft_delete = 0 
                        ) pengawas ON pengawas.sekolah_id = timeline.sekolah_id 
                        AND pengawas.urutan = 1 
                        JOIN sekolah with(nolock) on sekolah.sekolah_id = timeline.sekolah_id
                        JOIN ref.wilayah kec with(nolock) ON kec.kode_wilayah = LEFT ( sekolah.kode_wilayah, 6 )
                        JOIN ref.wilayah kab with(nolock) ON kab.kode_wilayah = kec.induk_kode_wilayah
                        JOIN ref.wilayah prop with(nolock) ON prop.kode_wilayah = kab.induk_kode_wilayah 
                    WHERE
                        timeline.soft_delete = 0 
                        and sekolah.soft_delete = 0
                        AND timeline.sekolah_id IS NOT NULL 
                        AND kec.kode_wilayah = '".$kecamatan[$iKecamatan]->{'kode_wilayah'}."'
                        {$params_sekolah_id}
                        {$params_npsn}
                    GROUP BY
                        timeline.sekolah_id 
                    HAVING
                        SUM ( CASE WHEN jenis_timeline_id = 2 THEN 1 ELSE 0 END ) > 0 
                        AND SUM ( CASE WHEN jenis_timeline_id = 3 THEN 1 ELSE 0 END ) IN ( 0, 1 ) 
                        AND SUM ( CASE WHEN jenis_timeline_id = 4 THEN 1 ELSE 0 END ) = 0 
                        AND DATEDIFF(
                            week,
                        MAX ( timeline.create_date ),
                        getdate()) >= 2
                        AND MAX ( pengawas.pengguna_id ) IS NOT NULL
                        AND ( CASE WHEN SUM ( CASE WHEN validasi_rapor.jumlah > 0 THEN 1 ELSE 0 END ) > 0 THEN 'Y' ELSE 'N' END ) = 'Y'";

                    $fetch = DB::connection('sqlsrv_pmp')->select(DB::raw($sql));

                    for ($iSekolah=0; $iSekolah < sizeof($fetch); $iSekolah++) { 
                        
                        $record = $fetch[$iSekolah];

                        //log
                        $auto_verval_id = guidGenerator::index();
                        $verval_total = 0;
                        $verval_insert = 0;
                        $verval_update = 0;
                        $verval_gagal = 0;
                        
                        $waktu_mulai = logAutoVerval::index($auto_verval_id, $record->sekolah_id, $record->pengguna_id, $record->interval_minggu);
                        //log

                        if($record->timeline_3 == 0){

                            $sql_verval = "SELECT
                                newid() AS verifikasi_pengawas_id,
                                '".$record->sekolah_id."' as sekolah_id,
                                '2019' as tahun,
                                '".$record->pengguna_id."' as pengguna_id,
                                urut AS kode_sub_indikator ,
                                getdate() as create_date,
                                getdate() as last_update,
                                0 as soft_delete,
                                'BAE5B460-5D2A-11E4-A97A-6594CE45B02E' as updater_id,
                                '2000-01-01 00:00:00' as last_sync,
                                '1' as nilai,
                                concat('verval otomatis oleh sistem pada tanggal ', getdate()) as keterangan
                            FROM
                                master_pmp with(nolock)
                            WHERE
                                LEVEL = 'grandchild' 
                                AND sekolah_id = '".$record->sekolah_id."' 
                            ORDER BY
                                urut";
    
                            $fetch_verval = DB::connection('sqlsrv_pmp')->select(DB::raw($sql_verval));
    
                            $n_verval = 0;
                            $verval_total = sizeof($fetch_verval);
    
                            for ($iVerval=0; $iVerval < sizeof($fetch_verval); $iVerval++) { 
    
                                $sql_cek_verval = "SELECT * FROM verifikasi_pengawas WHERE sekolah_id = '".$fetch_verval[$iVerval]->sekolah_id."' AND tahun = '".$fetch_verval[$iVerval]->tahun."' AND kode_sub_indikator = '".$fetch_verval[$iVerval]->kode_sub_indikator."'";
    
                                $fetch_cek_verval = DB::connection('sqlsrv_pmp')->select(DB::raw($sql_cek_verval));
    
                                if(sizeof($fetch_cek_verval) > 0){
                                    //update
                                    $sql_exe = "UPDATE verifikasi_pengawas set
                                        pengguna_id = '".$fetch_verval[$iVerval]->pengguna_id."',
                                        last_update = '".$fetch_verval[$iVerval]->last_update."',
                                        soft_delete = '".$fetch_verval[$iVerval]->soft_delete."',
                                        updater_id = '".$fetch_verval[$iVerval]->updater_id."',
                                        nilai = '".$fetch_verval[$iVerval]->nilai."',
                                        keterangan = '".$fetch_verval[$iVerval]->keterangan."'
                                    where
                                        sekolah_id = '".$fetch_verval[$iVerval]->sekolah_id."' 
                                    AND tahun = '".$fetch_verval[$iVerval]->tahun."'
                                    AND kode_sub_indikator = '".$fetch_verval[$iVerval]->kode_sub_indikator."'
                                    ";
    
                                    try {
                                        $exe = DB::connection('sqlsrv_pmp')->statement(DB::raw($sql_exe));
                                        $n_verval++;
                                        $verval_update++;
                                        echo "[INF] ".$fetch_verval[$iVerval]->sekolah_id." - verval: ".$fetch_verval[$iVerval]->kode_sub_indikator." [UPDATE BERHASIL]".PHP_EOL;
                                    } catch (\Throwable $th) {
                                        echo "[INF] ".$fetch_verval[$iVerval]->sekolah_id." - verval: ".$fetch_verval[$iVerval]->kode_sub_indikator." [UPDATE GAGAL]".PHP_EOL;
                                    }
    
                                }else{
                                    //insert
                                    $sql_exe = "INSERT INTO verifikasi_pengawas values(
                                        '".$fetch_verval[$iVerval]->verifikasi_pengawas_id."',
                                        '".$fetch_verval[$iVerval]->sekolah_id."',
                                        '".$fetch_verval[$iVerval]->tahun."',
                                        '".$fetch_verval[$iVerval]->pengguna_id."',
                                        '".$fetch_verval[$iVerval]->kode_sub_indikator."',
                                        '".$fetch_verval[$iVerval]->create_date."',
                                        '".$fetch_verval[$iVerval]->last_update."',
                                        '".$fetch_verval[$iVerval]->soft_delete."',
                                        '".$fetch_verval[$iVerval]->updater_id."',
                                        '".$fetch_verval[$iVerval]->last_sync."',
                                        '".$fetch_verval[$iVerval]->nilai."',
                                        '".$fetch_verval[$iVerval]->keterangan."'
                                    )";
    
                                    try {
                                        $exe = DB::connection('sqlsrv_pmp')->statement(DB::raw($sql_exe));
                                        $n_verval++;
                                        $verval_insert++;
                                        echo "[INF] ".$fetch_verval[$iVerval]->sekolah_id." - verval: ".$fetch_verval[$iVerval]->kode_sub_indikator." [INSERT BERHASIL]".PHP_EOL;
                                    } catch (\Throwable $th) {
                                        echo "[INF] ".$fetch_verval[$iVerval]->sekolah_id." - verval: ".$fetch_verval[$iVerval]->kode_sub_indikator." [INSERT GAGAL]".PHP_EOL;
                                    }
    
                                    // echo $sql_exe;die;
    
                                }
    
                            }

                            if($n_verval == sizeof($fetch_verval)){
                                $sql_cek_timeline = "select * from timeline where sekolah_id = '".$record->sekolah_id."' and jenis_timeline_id = '3'";
                                $fetch_cek_timeline = DB::connection('sqlsrv_pmp')->select(DB::raw($sql_cek_timeline));
    
                                if(sizeof($fetch_cek_timeline) > 0){
                                    //update
                                    $sql_exe_timeline = "UPDATE timeline set
                                        soft_delete = 0,
                                        last_update = getdate()
                                    where
                                        sekolah_id = '".$record->sekolah_id."'
                                    AND jenis_timeline_id = '3'
                                    ";
    
                                    $label_3 = "UPDATE";
                                }else{
                                    //insert
                                    $sql_exe_timeline = "INSERT INTO timeline values(
                                        newid(),
                                        '".$record->sekolah_id."',
                                        '2019',
                                        null,
                                        3,
                                        getdate(),
                                        getdate(),
                                        0,
                                        'BAE5B460-5D2A-11E4-A97A-6594CE45B02E',
                                        '2000-01-01 00:00:00'
                                    )";
    
                                    $label_3 = "INSERT";
                                }
    
                                try {
                                    $exe_timeline = DB::connection('sqlsrv_pmp')->statement(DB::raw($sql_exe_timeline));
                                    echo "[INF] ".$label_3." TIMELINE 3 [BERHASIL]".PHP_EOL;
                                    $timeline_3 = $label_3;
                                    
                                    $sql_cek_timeline = "select * from timeline where sekolah_id = '".$record->sekolah_id."' and jenis_timeline_id = '4'";
                                    $fetch_cek_timeline = DB::connection('sqlsrv_pmp')->select(DB::raw($sql_cek_timeline));
                                    
                                    if(sizeof($fetch_cek_timeline) > 0){
                                        //update
                                        $sql_exe_timeline = "UPDATE timeline set
                                            soft_delete = 0,
                                            last_update = getdate()
                                        where
                                            sekolah_id = '".$record->sekolah_id."'
                                        AND jenis_timeline_id = '4'
                                        ";
    
                                        $label = "UPDATE";    
                                    }else{
                                        //insert
                                        $sql_exe_timeline = "INSERT INTO timeline values(
                                            newid(),
                                            '".$record->sekolah_id."',
                                            '2019',
                                            null,
                                            4,
                                            getdate(),
                                            getdate(),
                                            0,
                                            'BAE5B460-5D2A-11E4-A97A-6594CE45B02E',
                                            '2000-01-01 00:00:00'
                                        )";
    
                                        $label = "INSERT";
                                    }
    
                                    try {
                                        $exe_timeline = DB::connection('sqlsrv_pmp')->statement(DB::raw($sql_exe_timeline));
                                        echo "[INF] ".$label." TIMELINE 4 [BERHASIL]".PHP_EOL;
                                        $timeline_4 = $label;
                                    } catch (\Throwable $th) {
                                        //throw $th;
                                        $timeline_4 = 'GAGAL';
                                        echo "[INF] ".$label." TIMELINE 4 [GAGAL]".PHP_EOL;
                                    }
    
                                } catch (\Throwable $th) {
                                    //throw $th;
                                    $timeline_3 = 'GAGAL';
                                    echo "[INF] ".$label_3." TIMELINE 3 [GAGAL]".PHP_EOL;
                                }
    
                                
                            }
                        }else{
                            $timeline_3 = 'SKIPPED';
                            $sql_cek_timeline = "select * from timeline where sekolah_id = '".$record->sekolah_id."' and jenis_timeline_id = '4'";
                            $fetch_cek_timeline = DB::connection('sqlsrv_pmp')->select(DB::raw($sql_cek_timeline));
                            
                            if(sizeof($fetch_cek_timeline) > 0){
                                //update
                                $sql_exe_timeline = "UPDATE timeline set
                                    soft_delete = 0,
                                    last_update = getdate()
                                where
                                    sekolah_id = '".$record->sekolah_id."'
                                AND jenis_timeline_id = '4'
                                ";

                                $label = "UPDATE";    
                            }else{
                                //insert
                                $sql_exe_timeline = "INSERT INTO timeline values(
                                    newid(),
                                    '".$record->sekolah_id."',
                                    '2019',
                                    null,
                                    4,
                                    getdate(),
                                    getdate(),
                                    0,
                                    'BAE5B460-5D2A-11E4-A97A-6594CE45B02E',
                                    '2000-01-01 00:00:00'
                                )";

                                $label = "INSERT";
                            }

                            try {
                                $exe_timeline = DB::connection('sqlsrv_pmp')->statement(DB::raw($sql_exe_timeline));
                                echo "[INF] ".$label." TIMELINE 4 [BERHASIL]".PHP_EOL;
                                $timeline_4 = $label;
                            } catch (\Throwable $th) {
                                //throw $th;
                                $timeline_4 = 'GAGAL';
                                echo "[INF] ".$label." TIMELINE 4 [GAGAL]".PHP_EOL;
                            }
                        }



                        try {
                            // DB::connection('sqlsrv_pmp')->statement($sql_update);

                            echo "[INF] [".($iSekolah+1)."/".sizeof($fetch)."] ".$provinsi[$iProvinsi]->{'nama'}." - ".$kabupaten[$iKabupaten]->{'nama'}." - ".$kecamatan[$iKecamatan]->{'nama'}." - Interval ".$fetch[$iSekolah]->interval_minggu." minggu - AUTO PAKTA INTEGRITAS PENGAWAS ".$fetch[$iSekolah]->nama_sekolah." [BERHASIL]".PHP_EOL;
                        } catch (\Throwable $th) {
                            echo "[INF] [".($iSekolah+1)."/".sizeof($fetch)."] ".$provinsi[$iProvinsi]->{'nama'}." - ".$kabupaten[$iKabupaten]->{'nama'}." - ".$kecamatan[$iKecamatan]->{'nama'}." - Interval ".$fetch[$iSekolah]->interval_minggu." minggu - AUTO PAKTA INTEGRITAS PENGAWAS ".$fetch[$iSekolah]->nama_sekolah." [GAGAL]".PHP_EOL;
                        }

                        //log
                        logAutoVerval::update($auto_verval_id, $waktu_mulai, $verval_total, $verval_insert, $verval_update, $verval_gagal, ($timeline_3 ? $timeline_3 : 'SKIPPED'), $timeline_4);
                        //log
                    }
                }
            }
        }
    }
}
