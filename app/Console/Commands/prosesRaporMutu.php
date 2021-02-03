<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class prosesRaporMutu extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rapor:proses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Memroses Rapor Mutu dari jawaban responden';

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
        // ambil acuan data instrumen
        $fetch_instrumen = DB::connection('sqlsrv_pmp')
        ->table(DB::raw('instrumen as instrumen with(nolock)'))
        ->where('instrumen.tingkat_instrumen_id', '=', DB::raw("6"))
        ->where('instrumen.soft_delete', '=', DB::raw("0"))
        ->orderBy('kode', 'ASC')
        ->get();
        ;

        echo "[INF] Mengambil acuan data instrumen: ".sizeof($fetch_instrumen)." record".PHP_EOL;
        // die;

        // $delete_jawaban_tmp = DB::connection('sqlsrv_pmp')
        // ->statement('delete from tmp.jawaban_tmp')
        // // ->delete()
        // ;

        // if($delete_jawaban_tmp){
        //     echo "[INF] Hapus jawaban tmp: BERHASIL".PHP_EOL;
        // }else{
        //     echo "[INF] Hapus jawaban tmp: GAGAL".PHP_EOL;
        // }
        // die;

        // ambil data sekolah yang sudah selesai rapor mutunya
        $fetch_sekolah = DB::connection('sqlsrv_pmp')
        ->table(DB::raw('timeline as timeline with(nolock)'))
        ->join(DB::raw('sekolah as sekolah with(nolock)'), 'sekolah.sekolah_id', '=', 'timeline.sekolah_id')
        ->join(DB::raw('ref.wilayah as kec with(nolock)'), 'kec.kode_wilayah', '=', DB::raw('LEFT(sekolah.kode_wilayah,6)'))
        ->join(DB::raw('ref.wilayah as kab with(nolock)'), 'kab.kode_wilayah', '=', 'kec.induk_kode_wilayah')
        ->join(DB::raw('(SELECT
            master_pmp.sekolah_id,
            SUM ( 1 ) AS jumlah 
        FROM
            master_pmp
            JOIN timeline ON timeline.sekolah_id = master_pmp.sekolah_id 
            AND timeline.jenis_timeline_id = 2 
        WHERE
            master_pmp.last_update < DATEADD( week,- 1, GETDATE() ) 
        GROUP BY
            master_pmp.sekolah_id) as master'), 'master.sekolah_id','=','sekolah.sekolah_id'    )
        ->where('timeline.jenis_timeline_id','=',DB::raw("2"))
        ->where('sekolah.soft_delete','=',DB::raw("0"))
        ->where('timeline.soft_delete','=',DB::raw("0"))
        // ->where('timeline.sekolah_id','=',DB::raw("'A2ABF706-DAC3-4308-A3F9-83B1B225DEBA'"))
        ->select(
            'timeline.*',
            'sekolah.nama',
            'sekolah.npsn',
            'sekolah.bentuk_pendidikan_id',
            'kab.kode_wilayah as kode_wilayah'
        )
        ->get();
        ;

        for ($iSekolah=0; $iSekolah < sizeof($fetch_sekolah); $iSekolah++) { 
            $delete_jawaban_tmp = DB::connection('sqlsrv_pmp')
            ->statement('delete from tmp.jawaban_tmp')
            // ->delete()
            ;

            if($delete_jawaban_tmp){
                echo "[INF] Hapus jawaban tmp: BERHASIL".PHP_EOL;
            }else{
                echo "[INF] Hapus jawaban tmp: GAGAL".PHP_EOL;
            }

            $record = $fetch_sekolah[$iSekolah];
            // echo $record->kode_wilayah.PHP_EOL;continue;

            echo "[INF] [".($iSekolah+1)."/".sizeof($fetch_sekolah)."] Mengambil data ".$record->nama.PHP_EOL;

            // ambil data jawaban_utama sekolah tsb
            $fetch_jawaban_utama = DB::connection('sqlsrv_pmp')
            ->table(DB::raw('[jawaban].['.$record->kode_wilayah.'] as ju with(nolock)'))
            ->join(DB::raw('pengguna as pengguna with(nolock)'), 'pengguna.pengguna_id', '=', 'ju.pengguna_id')
            ->where('pengguna.sekolah_id','=',DB::raw("'".$record->sekolah_id."'"))
            ->select(
                'ju.*',
                'pengguna.nama'
            )
            // ->skip(0)->take(1)
            ->orderBy('pengguna_id', 'DESC')
            ->get();
            ;
            
            for ($iJu=0; $iJu < sizeof($fetch_jawaban_utama); $iJu++) { 
                $record_ju = $fetch_jawaban_utama[$iJu];

                
                echo "[INF] [".($iSekolah+1)."/".sizeof($fetch_sekolah)."] |_ [".($iJu+1)."/".sizeof($fetch_jawaban_utama)."] Mengambil data responden ".$record_ju->nama.PHP_EOL;

                try {
                
                    $isian = json_decode($record_ju->json_jawaban)->isian;
                    $nilai = json_decode($record_ju->json_jawaban)->nilai;

                    for ($iInstrumen=0; $iInstrumen < sizeof($fetch_instrumen); $iInstrumen++) { 
                        $record_instrumen = $fetch_instrumen[$iInstrumen];

                        echo "[INF] [".($iSekolah+1)."/".sizeof($fetch_sekolah)."] |_ [".($iJu+1)."/".sizeof($fetch_jawaban_utama)."] [".($iInstrumen+1)."/".sizeof($fetch_instrumen)."] Ekstrak data jawaban instrumen ".$record_instrumen->kode.PHP_EOL;

                        // echo json_encode($record_instrumen).PHP_EOL;
                        $arrJawabanTmp = array();
                        $arrJawabanTmp['jawaban_id'] = '43c02951-c5a8-4164-9de7-bcfd365d4e96';
                        $arrJawabanTmp['pengguna_id'] = $record_ju->pengguna_id;
                        $arrJawabanTmp['instrumen_id'] = '4B0483FE-9834-47E2-82BD-8015727C7FF4';
                        $arrJawabanTmp['sekolah_id'] = $record->sekolah_id;
                        $arrJawabanTmp['isian'] = ($isian[$iInstrumen] != null ? $isian[$iInstrumen] : '');
                        $arrJawabanTmp['nilai'] = ($nilai[$iInstrumen] != null ? $nilai[$iInstrumen] : '0');
                        $arrJawabanTmp['create_date'] = '1990-01-01 00:00:00';
                        $arrJawabanTmp['last_update'] = '1990-01-01 00:00:00';
                        $arrJawabanTmp['soft_delete'] = '0';
                        $arrJawabanTmp['updater_id'] = '43c02951-c5a8-4164-9de7-bcfd365d4e96';
                        $arrJawabanTmp['last_sync'] = '1990-01-01 00:00:00';

                        // ekstrak jawaban_utama ke tabel jawaban_tmp
                        $exeInsertJawabanTmp = DB::connection('sqlsrv_pmp')
                        ->table('tmp.jawaban_tmp')
                        ->insert($arrJawabanTmp);

                        if($exeInsertJawabanTmp){
                            echo "[INF] insert jawaban tmp: BERHASIL".PHP_EOL;
                        }else{
                            echo "[INF] insert jawaban tmp: GAGAL".PHP_EOL;
                        }

                        // if($nilai[$iInstrumen] != null){
                        //     echo json_encode($arrJawabanTmp).PHP_EOL;
                        // }
                    }
                } catch (\Throwable $th) {

                    for ($iInstrumen=0; $iInstrumen < sizeof($fetch_instrumen); $iInstrumen++) { 
                        $record_instrumen = $fetch_instrumen[$iInstrumen];

                        try {
                            
                            $rows = json_decode($record_ju->json_jawaban)->rows;
    
                            echo "[INF] [".($iSekolah+1)."/".sizeof($fetch_sekolah)."] |_ [".($iJu+1)."/".sizeof($fetch_jawaban_utama)."] [".($iInstrumen+1)."/".sizeof($fetch_instrumen)."] Ekstrak data jawaban instrumen ".$record_instrumen->kode.PHP_EOL;
    
                            // echo json_encode($record_instrumen).PHP_EOL;
                            $arrJawabanTmp = array();
                            $arrJawabanTmp['jawaban_id'] = '43c02951-c5a8-4164-9de7-bcfd365d4e96';
                            $arrJawabanTmp['pengguna_id'] = $record_ju->pengguna_id;
                            $arrJawabanTmp['instrumen_id'] = '4B0483FE-9834-47E2-82BD-8015727C7FF4';
                            $arrJawabanTmp['sekolah_id'] = $record->sekolah_id;
                            $arrJawabanTmp['isian'] = ($rows[$iInstrumen]->isian != null ? $rows[$iInstrumen]->isian : '');
                            $arrJawabanTmp['nilai'] = ($rows[$iInstrumen]->nilai != null ? $rows[$iInstrumen]->nilai : '0');
                            $arrJawabanTmp['create_date'] = '1990-01-01 00:00:00';
                            $arrJawabanTmp['last_update'] = '1990-01-01 00:00:00';
                            $arrJawabanTmp['soft_delete'] = '0';
                            $arrJawabanTmp['updater_id'] = '43c02951-c5a8-4164-9de7-bcfd365d4e96';
                            $arrJawabanTmp['last_sync'] = '1990-01-01 00:00:00';
    
                            // ekstrak jawaban_utama ke tabel jawaban_tmp
                            $exeInsertJawabanTmp = DB::connection('sqlsrv_pmp')
                            ->table('tmp.jawaban_tmp')
                            ->insert($arrJawabanTmp);
    
                            if($exeInsertJawabanTmp){
                                echo "[INF] insert jawaban tmp: BERHASIL".PHP_EOL;
                            }else{
                                echo "[INF] insert jawaban tmp: GAGAL".PHP_EOL;
                            }
    
                            // if($nilai[$iInstrumen] != null){
                            //     echo json_encode($arrJawabanTmp).PHP_EOL;
                            // }

                        } catch (\Throwable $th) {
                            //throw $th;
                        }
                    }
                    
                }
                // echo json_encode(json_decode($record_ju->json_jawaban)->nilai).PHP_EOL;
                // die;
            }

            // hitung rapor mutunya
            $sql = "SELECT
                COALESCE(sekolah.sekolah_id,'".$record->sekolah_id."') as sekolah_id,
                COALESCE(sekolah.npsn,'".$record->npsn."') as npsn,
                COALESCE(sekolah.bentuk_pendidikan_id,".$record->bentuk_pendidikan_id.") as bentuk_pendidikan_id,
                snp.id,
                snp.level,
                snp.urut,
                snp.nomor,
                snp.uraian,
                COALESCE ( sub_indikator.final_skor, 0 ) AS r19 
            FROM
                REF.snp snp
                LEFT JOIN (
                SELECT
                    ketiga.sekolah_id,
                    ketiga.subind_id,
                    ( SUM ( ketiga.bobot_peran * ketiga.nilai_dua ) * 7 ) AS final_skor 
                FROM
                    (
                    SELECT
                        kedua.sekolah_id,
                        kedua.peran_id,
                        kedua.subind_id,
                    CASE
                            
                            WHEN kedua.peran_id = 10 THEN
                            bbt.bobot_kepsek 
                            WHEN kedua.peran_id = 53 THEN
                            bbt.bobot_guru 
                            WHEN kedua.peran_id = 90 THEN
                            bbt.bobot_siswa 
                            WHEN kedua.peran_id = 14 THEN
                            bbt.bobot_komite 
                        END AS bobot_peran,
                        AVG ( kedua.nilai_satu ) AS nilai_dua 
                    FROM
                        (
                        SELECT
                            awal.sekolah_id,
                            awal.pengguna_id,
                            awal.peran_id,
                            awal.subind_id,
                            SUM ( awal.bobot_nilai ) AS nilai_satu 
                        FROM
                            (
                            SELECT
                                sekolah.sekolah_id,
                                pengguna.pengguna_id,
                                pengguna.peran_id,
                                konversi.subind_id,
                            CASE
                                    
                                    WHEN sekolah.bentuk_pendidikan_id = 5 THEN
                                CASE
                                        
                                        WHEN pengguna.peran_id = 10 THEN
                                        konversi.SD_Kepsek 
                                        WHEN pengguna.peran_id = 53 THEN
                                        konversi.SD_Guru 
                                        WHEN pengguna.peran_id = 90 THEN
                                        konversi.SD_Siswa 
                                        WHEN pengguna.peran_id = 14 THEN
                                        konversi.SD_Komite 
                                    END 
                                        WHEN sekolah.bentuk_pendidikan_id = 6 THEN
                                    CASE
                                            
                                            WHEN pengguna.peran_id = 10 THEN
                                            konversi.SMP_Kepsek 
                                            WHEN pengguna.peran_id = 53 THEN
                                            konversi.SMP_Guru 
                                            WHEN pengguna.peran_id = 90 THEN
                                            konversi.SMP_Siswa 
                                            WHEN pengguna.peran_id = 14 THEN
                                            konversi.SMP_Komite 
                                        END 
                                            WHEN sekolah.bentuk_pendidikan_id = 13 THEN
                                        CASE
                                                
                                                WHEN pengguna.peran_id = 10 THEN
                                                konversi.SMA_Kepsek 
                                                WHEN pengguna.peran_id = 53 THEN
                                                konversi.SMA_Guru 
                                                WHEN pengguna.peran_id = 90 THEN
                                                konversi.SMA_Siswa 
                                                WHEN pengguna.peran_id = 14 THEN
                                                konversi.SMA_Komite 
                                            END 
                                                WHEN sekolah.bentuk_pendidikan_id = 15 THEN
                                            CASE
                                                    
                                                    WHEN pengguna.peran_id = 10 THEN
                                                    konversi.SMK_Kepsek 
                                                    WHEN pengguna.peran_id = 53 THEN
                                                    konversi.SMK_Guru 
                                                    WHEN pengguna.peran_id = 90 THEN
                                                    konversi.SMK_Siswa 
                                                    WHEN pengguna.peran_id = 14 THEN
                                                    konversi.SMK_Komite 
                                                END 
                                                END AS bobot_nilai 
                                            FROM
                                                jawaban AS jawab
                                                INNER JOIN pengguna AS pengguna ON jawab.pengguna_id = pengguna.pengguna_id
                                                INNER JOIN sekolah AS sekolah ON pengguna.sekolah_id = sekolah.sekolah_id
                                                INNER JOIN REF.konversi AS konversi ON jawab.instrumen_id = konversi.instrumen_id 
                                            WHERE
                                                jawab.nilai = 1 
                                                AND konversi.subind_id IS NOT NULL 
                                                AND sekolah.sekolah_id = 'a2abf706-dac3-4308-a3f9-83b1b225deba' 
                                            ) awal 
                                        GROUP BY
                                            awal.sekolah_id,
                                            awal.pengguna_id,
                                            awal.peran_id,
                                            awal.subind_id 
                                        ) kedua
                                        INNER JOIN REF.bobot AS bbt ON kedua.subind_id = bbt.kode_ind 
                                    GROUP BY
                                        kedua.sekolah_id,
                                        kedua.peran_id,
                                        kedua.subind_id,
                                        bbt.bobot_kepsek,
                                        bbt.bobot_siswa,
                                        bbt.bobot_guru,
                                        bbt.bobot_komite 
                                    ) ketiga 
                                GROUP BY
                                    ketiga.sekolah_id,
                                    ketiga.subind_id 
                                ) sub_indikator ON snp.nomor = sub_indikator.subind_id
                                LEFT JOIN sekolah ON sekolah.sekolah_id = sub_indikator.sekolah_id 
                            WHERE
                                snp.LEVEL = 'grandchild' UNION
                            SELECT
                                COALESCE(sekolah.sekolah_id,'".$record->sekolah_id."') as sekolah_id,
                                COALESCE(sekolah.npsn,'".$record->npsn."') as npsn,
                                COALESCE(sekolah.bentuk_pendidikan_id,".$record->bentuk_pendidikan_id.") as bentuk_pendidikan_id,
                                snp.ID,
                                snp.LEVEL,
                                snp.urut,
                                snp.nomor,
                                snp.uraian,
                                COALESCE ( indikator.final_skor, 0 ) AS r19 
                            FROM
                                REF.snp snp
                                LEFT JOIN (
                                SELECT
                                    CONCAT( dbo.FN_SPLIT_PART ( subind_id, '.', 1 ) , '.' , dbo.FN_SPLIT_PART ( subind_id, '.', 2 ) , '.' ) AS indikator,
                                    sekolah.npsn,
                                    sekolah.sekolah_id,
                                    AVG ( final_skor ) AS final_skor 
                                FROM
                                    (
                                    SELECT
                                        ketiga.sekolah_id,
                                        ketiga.subind_id,
                                        ( SUM ( ketiga.bobot_peran * ketiga.nilai_dua ) * 7 ) AS final_skor 
                                    FROM
                                        (
                                        SELECT
                                            kedua.sekolah_id,
                                            kedua.peran_id,
                                            kedua.subind_id,
                                        CASE
                                                
                                                WHEN kedua.peran_id = 10 THEN
                                                bbt.bobot_kepsek 
                                                WHEN kedua.peran_id = 53 THEN
                                                bbt.bobot_guru 
                                                WHEN kedua.peran_id = 90 THEN
                                                bbt.bobot_siswa 
                                                WHEN kedua.peran_id = 14 THEN
                                                bbt.bobot_komite 
                                            END AS bobot_peran,
                                            AVG ( kedua.nilai_satu ) AS nilai_dua 
                                        FROM
                                            (
                                            SELECT
                                                awal.sekolah_id,
                                                awal.pengguna_id,
                                                awal.peran_id,
                                                awal.subind_id,
                                                SUM ( awal.bobot_nilai ) AS nilai_satu 
                                            FROM
                                                (
                                                SELECT
                                                    sekolah.sekolah_id,
                                                    pengguna.pengguna_id,
                                                    pengguna.peran_id,
                                                    konversi.subind_id,
                                                CASE
                                                        
                                                        WHEN sekolah.bentuk_pendidikan_id = 5 THEN
                                                    CASE
                                                            
                                                            WHEN pengguna.peran_id = 10 THEN
                                                            konversi.SD_Kepsek 
                                                            WHEN pengguna.peran_id = 53 THEN
                                                            konversi.SD_Guru 
                                                            WHEN pengguna.peran_id = 90 THEN
                                                            konversi.SD_Siswa 
                                                            WHEN pengguna.peran_id = 14 THEN
                                                            konversi.SD_Komite 
                                                        END 
                                                            WHEN sekolah.bentuk_pendidikan_id = 6 THEN
                                                        CASE
                                                                
                                                                WHEN pengguna.peran_id = 10 THEN
                                                                konversi.SMP_Kepsek 
                                                                WHEN pengguna.peran_id = 53 THEN
                                                                konversi.SMP_Guru 
                                                                WHEN pengguna.peran_id = 90 THEN
                                                                konversi.SMP_Siswa 
                                                                WHEN pengguna.peran_id = 14 THEN
                                                                konversi.SMP_Komite 
                                                            END 
                                                                WHEN sekolah.bentuk_pendidikan_id = 13 THEN
                                                            CASE
                                                                    
                                                                    WHEN pengguna.peran_id = 10 THEN
                                                                    konversi.SMA_Kepsek 
                                                                    WHEN pengguna.peran_id = 53 THEN
                                                                    konversi.SMA_Guru 
                                                                    WHEN pengguna.peran_id = 90 THEN
                                                                    konversi.SMA_Siswa 
                                                                    WHEN pengguna.peran_id = 14 THEN
                                                                    konversi.SMA_Komite 
                                                                END 
                                                                    WHEN sekolah.bentuk_pendidikan_id = 15 THEN
                                                                CASE
                                                                        
                                                                        WHEN pengguna.peran_id = 10 THEN
                                                                        konversi.SMK_Kepsek 
                                                                        WHEN pengguna.peran_id = 53 THEN
                                                                        konversi.SMK_Guru 
                                                                        WHEN pengguna.peran_id = 90 THEN
                                                                        konversi.SMK_Siswa 
                                                                        WHEN pengguna.peran_id = 14 THEN
                                                                        konversi.SMK_Komite 
                                                                    END 
                                                                    END AS bobot_nilai 
                                                                FROM
                                                                    jawaban AS jawab
                                                                    INNER JOIN pengguna AS pengguna ON jawab.pengguna_id = pengguna.pengguna_id
                                                                    INNER JOIN sekolah AS sekolah ON pengguna.sekolah_id = sekolah.sekolah_id
                                                                    INNER JOIN REF.konversi AS konversi ON jawab.instrumen_id = konversi.instrumen_id 
                                                                WHERE
                                                                    jawab.nilai = 1 
                                                                    AND konversi.subind_id IS NOT NULL 
                                                                    AND sekolah.sekolah_id = 'a2abf706-dac3-4308-a3f9-83b1b225deba' 
                                                                ) awal 
                                                            GROUP BY
                                                                awal.sekolah_id,
                                                                awal.pengguna_id,
                                                                awal.peran_id,
                                                                awal.subind_id 
                                                            ) kedua
                                                            INNER JOIN REF.bobot AS bbt ON kedua.subind_id = bbt.kode_ind 
                                                        GROUP BY
                                                            kedua.sekolah_id,
                                                            kedua.peran_id,
                                                            kedua.subind_id,
                                                            bbt.bobot_kepsek,
                                                            bbt.bobot_siswa,
                                                            bbt.bobot_guru,
                                                            bbt.bobot_komite 
                                                        ) ketiga 
                                                    GROUP BY
                                                        ketiga.sekolah_id,
                                                        ketiga.subind_id 
                                                    ) sub_indikator
                                                    JOIN sekolah ON sekolah.sekolah_id = sub_indikator.sekolah_id 
                                                GROUP BY
                                                    CONCAT( dbo.FN_SPLIT_PART ( subind_id, '.', 1 ) , '.' , dbo.FN_SPLIT_PART ( subind_id, '.', 2 ) , '.' ),
                                                    sekolah.npsn,
                                                    sekolah.sekolah_id 
                                                ) indikator ON snp.nomor = indikator.indikator
                                                LEFT JOIN sekolah ON sekolah.sekolah_id = indikator.sekolah_id 
                                            WHERE
                                                snp.LEVEL = 'child' UNION
                                            SELECT
                                                COALESCE(sekolah.sekolah_id,'".$record->sekolah_id."') as sekolah_id,
                                                COALESCE(sekolah.npsn,'".$record->npsn."') as npsn,
                                                COALESCE(sekolah.bentuk_pendidikan_id,".$record->bentuk_pendidikan_id.") as bentuk_pendidikan_id,
                                                snp.id,
                                                snp.level,
                                                snp.urut,
                                                snp.nomor,
                                                snp.uraian,
                                                COALESCE ( standar.final_skor, 0 ) AS r19 
                                            FROM
                                                REF.snp snp
                                                LEFT JOIN (
                                                SELECT
                                                    CONCAT( dbo.FN_SPLIT_PART ( subind_id, '.', 1 ) , '.' ) AS standar,
                                                    sekolah.npsn,
                                                    sekolah.sekolah_id,
                                                    AVG ( final_skor ) AS final_skor 
                                                FROM
                                                    (
                                                    SELECT
                                                        ketiga.sekolah_id,
                                                        ketiga.subind_id,
                                                        ( SUM ( ketiga.bobot_peran * ketiga.nilai_dua ) * 7 ) AS final_skor 
                                                    FROM
                                                        (
                                                        SELECT
                                                            kedua.sekolah_id,
                                                            kedua.peran_id,
                                                            kedua.subind_id,
                                                        CASE
                                                                
                                                                WHEN kedua.peran_id = 10 THEN
                                                                bbt.bobot_kepsek 
                                                                WHEN kedua.peran_id = 53 THEN
                                                                bbt.bobot_guru 
                                                                WHEN kedua.peran_id = 90 THEN
                                                                bbt.bobot_siswa 
                                                                WHEN kedua.peran_id = 14 THEN
                                                                bbt.bobot_komite 
                                                            END AS bobot_peran,
                                                            AVG ( kedua.nilai_satu ) AS nilai_dua 
                                                        FROM
                                                            (
                                                            SELECT
                                                                awal.sekolah_id,
                                                                awal.pengguna_id,
                                                                awal.peran_id,
                                                                awal.subind_id,
                                                                SUM ( awal.bobot_nilai ) AS nilai_satu 
                                                            FROM
                                                                (
                                                                SELECT
                                                                    sekolah.sekolah_id,
                                                                    pengguna.pengguna_id,
                                                                    pengguna.peran_id,
                                                                    konversi.subind_id,
                                                                CASE
                                                                        
                                                                        WHEN sekolah.bentuk_pendidikan_id = 5 THEN
                                                                    CASE
                                                                            
                                                                            WHEN pengguna.peran_id = 10 THEN
                                                                            konversi.SD_Kepsek 
                                                                            WHEN pengguna.peran_id = 53 THEN
                                                                            konversi.SD_Guru 
                                                                            WHEN pengguna.peran_id = 90 THEN
                                                                            konversi.SD_Siswa 
                                                                            WHEN pengguna.peran_id = 14 THEN
                                                                            konversi.SD_Komite 
                                                                        END 
                                                                            WHEN sekolah.bentuk_pendidikan_id = 6 THEN
                                                                        CASE
                                                                                
                                                                                WHEN pengguna.peran_id = 10 THEN
                                                                                konversi.SMP_Kepsek 
                                                                                WHEN pengguna.peran_id = 53 THEN
                                                                                konversi.SMP_Guru 
                                                                                WHEN pengguna.peran_id = 90 THEN
                                                                                konversi.SMP_Siswa 
                                                                                WHEN pengguna.peran_id = 14 THEN
                                                                                konversi.SMP_Komite 
                                                                            END 
                                                                                WHEN sekolah.bentuk_pendidikan_id = 13 THEN
                                                                            CASE
                                                                                    
                                                                                    WHEN pengguna.peran_id = 10 THEN
                                                                                    konversi.SMA_Kepsek 
                                                                                    WHEN pengguna.peran_id = 53 THEN
                                                                                    konversi.SMA_Guru 
                                                                                    WHEN pengguna.peran_id = 90 THEN
                                                                                    konversi.SMA_Siswa 
                                                                                    WHEN pengguna.peran_id = 14 THEN
                                                                                    konversi.SMA_Komite 
                                                                                END 
                                                                                    WHEN sekolah.bentuk_pendidikan_id = 15 THEN
                                                                                CASE
                                                                                        
                                                                                        WHEN pengguna.peran_id = 10 THEN
                                                                                        konversi.SMK_Kepsek 
                                                                                        WHEN pengguna.peran_id = 53 THEN
                                                                                        konversi.SMK_Guru 
                                                                                        WHEN pengguna.peran_id = 90 THEN
                                                                                        konversi.SMK_Siswa 
                                                                                        WHEN pengguna.peran_id = 14 THEN
                                                                                        konversi.SMK_Komite 
                                                                                    END 
                                                                                    END AS bobot_nilai 
                                                                                FROM
                                                                                    jawaban AS jawab
                                                                                    INNER JOIN pengguna AS pengguna ON jawab.pengguna_id = pengguna.pengguna_id
                                                                                    INNER JOIN sekolah AS sekolah ON pengguna.sekolah_id = sekolah.sekolah_id
                                                                                    INNER JOIN REF.konversi AS konversi ON jawab.instrumen_id = konversi.instrumen_id 
                                                                                WHERE
                                                                                    jawab.nilai = 1 
                                                                                    AND konversi.subind_id IS NOT NULL 
                                                                                    AND sekolah.sekolah_id = 'a2abf706-dac3-4308-a3f9-83b1b225deba' 
                                                                                ) awal 
                                                                            GROUP BY
                                                                                awal.sekolah_id,
                                                                                awal.pengguna_id,
                                                                                awal.peran_id,
                                                                                awal.subind_id 
                                                                            ) kedua
                                                                            INNER JOIN REF.bobot AS bbt ON kedua.subind_id = bbt.kode_ind 
                                                                        GROUP BY
                                                                            kedua.sekolah_id,
                                                                            kedua.peran_id,
                                                                            kedua.subind_id,
                                                                            bbt.bobot_kepsek,
                                                                            bbt.bobot_siswa,
                                                                            bbt.bobot_guru,
                                                                            bbt.bobot_komite 
                                                                        ) ketiga 
                                                                    GROUP BY
                                                                        ketiga.sekolah_id,
                                                                        ketiga.subind_id 
                                                                    ) sub_indikator
                                                                    JOIN sekolah ON sekolah.sekolah_id = sub_indikator.sekolah_id 
                                                                GROUP BY
                                                                    CONCAT( dbo.FN_SPLIT_PART ( subind_id, '.', 1 ) , '.' ),
                                                                    sekolah.npsn,
                                                                    sekolah.sekolah_id 
                                                                ) standar ON snp.nomor = standar.standar
                                                                LEFT JOIN sekolah ON sekolah.sekolah_id = standar.sekolah_id 
                                                        WHERE
                snp.level = 'parent'";

                $fetch_rapor = DB::connection('sqlsrv_pmp')->select($sql);

                for ($iRapor=0; $iRapor < sizeof($fetch_rapor); $iRapor++) { 
                    // echo json_encode($fetch_rapor[$iRapor]).PHP_EOL;
                    $record_rapor = $fetch_rapor[$iRapor];

                    $arrRaporMutu = array();
                    $arrRaporMutu['sekolah_id'] = $record_rapor->sekolah_id;
                    $arrRaporMutu['npsn'] = $record_rapor->npsn;
                    $arrRaporMutu['bentuk_pendidikan_id'] = $record_rapor->bentuk_pendidikan_id;
                    $arrRaporMutu['id'] = $record_rapor->id;
                    $arrRaporMutu['level'] = $record_rapor->level;
                    $arrRaporMutu['urut'] = $record_rapor->urut;
                    $arrRaporMutu['nomor'] = $record_rapor->nomor;
                    $arrRaporMutu['uraian'] = $record_rapor->uraian;
                    $arrRaporMutu['n16'] = null;
                    $arrRaporMutu['r16'] = null;
                    $arrRaporMutu['r17'] = null;
                    $arrRaporMutu['r18'] = null;
                    $arrRaporMutu['soft_delete'] = 0;
                    $arrRaporMutu['jenis_rapor_id'] = null;
                    $arrRaporMutu['r19'] = $record_rapor->r19;
                    $arrRaporMutu['tahun'] = null;
                    $arrRaporMutu['create_date'] = date('Y-m-d H:i:s');
                    $arrRaporMutu['last_update'] = date('Y-m-d H:i:s');
                    $arrRaporMutu['updater_id'] = null;
                    $arrRaporMutu['last_sync'] = '1990-01-01 00:00:00';

                    // echo json_encode($arrRaporMutu).PHP_EOL;

                    // masukkan ke tabel master_pmp
                    $cek_rapor = DB::connection('sqlsrv_pmp')
                    ->table('master_pmp')
                    ->where('sekolah_id','=',DB::raw("'".$record_rapor->sekolah_id."'"))
                    ->where('nomor','=',DB::raw("'".$record_rapor->nomor."'"))
                    ->count();

                    if($cek_rapor < 1){
                        //belum ada recordnya
                        $exe = DB::connection('sqlsrv_pmp')
                        ->table('master_pmp')
                        ->insert($arrRaporMutu);
                        $label= "INSERT";

                    }else{
                        //sudah ada recordnya
                        $exe = DB::connection('sqlsrv_pmp')
                        ->table('master_pmp')
                        ->where('sekolah_id','=',DB::raw("'".$record_rapor->sekolah_id."'"))
                        ->where('nomor','=',DB::raw("'".$record_rapor->nomor."'"))
                        ->update([
                            'r19' => $record_rapor->r19,
                            'last_update' => date('Y-m-d H:i:s')
                        ]);

                        $label= "UPDATE";
                    }

                    // echo $cek_rapor.PHP_EOL;

                    if($exe){
                        echo "[INF] [".($iSekolah+1)."/".sizeof($fetch_sekolah)."] |_ [".($iRapor+1)."/".sizeof($fetch_rapor)."] [".$label."] RAPOR MUTU: BERHASIL: ".$record_rapor->nomor.PHP_EOL;
                    }else{
                        echo "[INF] [".($iSekolah+1)."/".sizeof($fetch_sekolah)."] |_ [".($iRapor+1)."/".sizeof($fetch_rapor)."] [".$label."] RAPOR MUTU: GAGAL: ".$record_rapor->nomor.PHP_EOL;
                    }


                }

            // hapus lagi data di tabel jawaban_tmp
            // selesai
            // echo $fetch_sekolah->get();
            
            $delete_jawaban_tmp = DB::connection('sqlsrv_pmp')
            ->statement('delete from tmp.jawaban_tmp')
            // ->delete()
            ;
    
            if($delete_jawaban_tmp){
                echo "[INF] Hapus jawaban tmp: BERHASIL".PHP_EOL;
            }else{
                echo "[INF] Hapus jawaban tmp: GAGAL".PHP_EOL;
            }
            
        }

    }
}
