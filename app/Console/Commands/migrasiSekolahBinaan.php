<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class migrasiSekolahBinaan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rekap:migrasi_sekolah_binaan';

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
    
        $sql_sekolah = "select top 100 * from sekolah";

        $fetch_sekolah = DB::connection('sqlsrv_pmp')->select(DB::raw($sql_sekolah));

        for ($i=0; $i < sizeof($fetch_sekolah); $i++) { 
            // echo json_encode($fetch_sekolah[$i]).PHP_EOL;
            try {
                $fetch_cek_sekolah = DB::connection('pgsql_diskuis')
                ->table('rekap.sekolah')
                ->where('sekolah_id','=',$fetch_sekolah[$i]->sekolah_id)
                ->get();
                
                if(sizeof($fetch_cek_sekolah) > 0){
                    // echo $fetch_sekolah[$i]->sekolah_id." - sudah ada".PHP_EOL;
    
                    $exe = DB::connection('pgsql_diskuis')
                    ->table('rekap.sekolah')
                    ->where('sekolah_id','=',$fetch_sekolah[$i]->sekolah_id)
                    ->update((array)$fetch_sekolah[$i]);
                    $pr = 'UPDATE';
    
                }else{
                    // echo $fetch_sekolah[$i]->sekolah_id." - belum ada".PHP_EOL;
    
                    $exe = DB::connection('pgsql_diskuis')
                    ->table('rekap.sekolah')
                    ->insert((array)$fetch_sekolah[$i]);
                    $pr = 'INSERT';
                }
    
                $label = $exe ? "[BERHASIL]" : "[GAGAL]";
    
                echo "[rekap:migrasi_sekolah_binaan] [".($i+1)."/".sizeof($fetch_sekolah)."] [sekolah] [".$pr."] ".$label." ".$fetch_sekolah[$i]->sekolah_id.PHP_EOL;
            } catch (\Throwable $th) {
                echo "[rekap:migrasi_sekolah_binaan] [".($i+1)."/".sizeof($fetch_sekolah)."] [sekolah] GAGAL EXCEPTION".PHP_EOL;
            }

        }


        // pengawas
        $sql_pengguna = "select top 100 * from pengguna where peran_id = 13";

        $fetch_pengguna = DB::connection('sqlsrv_pmp')->select(DB::raw($sql_pengguna));

        for ($i=0; $i < sizeof($fetch_pengguna); $i++) { 
            // echo json_encode($fetch_pengguna[$i]).PHP_EOL;

            try {
                $fetch_cek_pengguna = DB::connection('pgsql_diskuis')
                ->table('rekap.pengawas')
                ->where('pengguna_id','=',$fetch_pengguna[$i]->pengguna_id)
                ->get();
                
                if(sizeof($fetch_cek_pengguna) > 0){
                    // echo $fetch_pengguna[$i]->pengguna_id." - sudah ada".PHP_EOL;
    
                    $exe = DB::connection('pgsql_diskuis')
                    ->table('rekap.pengawas')
                    ->where('pengguna_id','=',$fetch_pengguna[$i]->pengguna_id)
                    ->update((array)$fetch_pengguna[$i]);
                    $pr = 'UPDATE';
    
                }else{
                    // echo $fetch_pengguna[$i]->pengguna_id." - belum ada".PHP_EOL;
    
                    $exe = DB::connection('pgsql_diskuis')
                    ->table('rekap.pengawas')
                    ->insert((array)$fetch_pengguna[$i]);
                    $pr = 'INSERT';
                }
    
                $label = $exe ? "[BERHASIL]" : "[GAGAL]";
    
                echo "[rekap:migrasi_sekolah_binaan] [".($i+1)."/".sizeof($fetch_pengguna)."] [pengguna] [".$pr."] ".$label." ".$fetch_pengguna[$i]->pengguna_id.PHP_EOL;
            } catch (\Throwable $th) {
                echo "[rekap:migrasi_sekolah_binaan] [".($i+1)."/".sizeof($fetch_pengguna)."] [pengguna] GAGAL EXCEPTION".PHP_EOL;
            }


        }
        
        // sekolah_binaan
        $sql_sekolah_binaan = "SELECT top 10
            sekolah.nama,
            sekolah.npsn,
            sekolah.bentuk_pendidikan_id,
            bp.nama as bentuk,
            sekolah.status_sekolah,
            (case when sekolah.status_sekolah = 1 then 'Negeri' else 'Swasta' end) as status,
            sekolah.alamat_jalan,
            kec.kode_wilayah as kode_wilayah_kecamatan,
            kab.kode_wilayah as kode_wilayah_kabupaten,
            prov.kode_wilayah as kode_wilayah_provinsi,
            kec.nama as kecamatan,
            kab.nama as kabupaten,
            prov.nama as provinsi,
            sekolah_binaan.* 
        FROM
            sekolah_binaan
            LEFT JOIN sekolah ON sekolah.sekolah_id = sekolah_binaan.sekolah_id 
            LEFT JOIN ref.mst_wilayah kec on kec.kode_wilayah = LEFT(sekolah.kode_wilayah,6)
            LEfT JOIN ref.mst_wilayah kab on kab.kode_wilayah = kec.mst_kode_wilayah
            LEfT JOIN ref.mst_wilayah prov on prov.kode_wilayah = kab.mst_kode_wilayah
            LEFT JOIN ref.bentuk_pendidikan bp on bp.bentuk_pendidikan_id = sekolah.bentuk_pendidikan_id
        WHERE
            sekolah_binaan.soft_delete = 0 
            AND sekolah.soft_delete = 0";

        $fetch_sekolah_binaan = DB::connection('sqlsrv_pmp')->select(DB::raw($sql_sekolah_binaan));

        for ($i=0; $i < sizeof($fetch_sekolah_binaan); $i++) { 
            // echo json_encode($fetch_sekolah_binaan[$i]).PHP_EOL;
            try {
                
                $fetch_cek_sekolah_binaan = DB::connection('pgsql_diskuis')
                ->table('rekap.sekolah_binaan')
                ->where('pengguna_id','=',$fetch_sekolah_binaan[$i]->pengguna_id)
                ->where('sekolah_id','=',$fetch_sekolah_binaan[$i]->sekolah_id)
                ->get();
                
                if(sizeof($fetch_cek_sekolah_binaan) > 0){
                    // echo $fetch_sekolah_binaan[$i]->pengguna_id." - sudah ada".PHP_EOL;
    
                    $exe = DB::connection('pgsql_diskuis')
                    ->table('rekap.sekolah_binaan')
                    ->where('pengguna_id','=',$fetch_sekolah_binaan[$i]->pengguna_id)
                    ->where('sekolah_id','=',$fetch_sekolah_binaan[$i]->sekolah_id)
                    ->update((array)$fetch_sekolah_binaan[$i]);
                    $pr = 'UPDATE';
    
                }else{
                    // echo $fetch_sekolah_binaan[$i]->pengguna_id." - belum ada".PHP_EOL;
    
                    $exe = DB::connection('pgsql_diskuis')
                    ->table('rekap.sekolah_binaan')
                    ->insert((array)$fetch_sekolah_binaan[$i]);
                    $pr = 'INSERT';
                }
    
                $label = $exe ? "[BERHASIL]" : "[GAGAL]";
    
                echo "[rekap:migrasi_sekolah_binaan] [".($i+1)."/".sizeof($fetch_sekolah_binaan)."] [sekolah_binaan] [".$pr."] ".$label." ".$fetch_sekolah_binaan[$i]->pengguna_id.PHP_EOL;
            } catch (\Throwable $th) {
                echo "[rekap:migrasi_sekolah_binaan] [".($i+1)."/".sizeof($fetch_sekolah_binaan)."] [sekolah_binaan] GAGAL EXCEPTION".PHP_EOL;//throw $th;
            }

        }

    }
}
