<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redis;

// use PhpOffice\PhpSpreadsheet\Spreadsheet;
// use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class RaporDapodikController extends Controller
{
    // public function tesExcel(){
    //     $spreadsheet = new Spreadsheet();
    //     $sheet = $spreadsheet->getActiveSheet();
    //     $sheet->setCellValue('A1', 'Hello World !');

    //     $writer = new Xlsx($spreadsheet);
    //     $writer->save('hello world.xlsx');
    // }

    public function getRaporDapodikExcel(Request $request){
        $return = self::getRaporDapodik($request);
        // return $return;die;
        return view('excel/UnduhExcelGeneral', ['return' => $return, 'judul' => 'Rapor Dapodik Wilayah', 'sub_judul' => $return['rows'][0]->induk]);
    }

    public function getRaporDapodikSekolahExcel(Request $request){
        $return = self::getRaporDapodikSekolah($request);
        // return $return;die;
        return view('excel/UnduhExcelGeneral', ['return' => $return, 'judul' => 'Rapor Dapodik Sekolah', 'sub_judul' => $return['rows'][0]->induk]);
    }

    public function getRefRaporDapodik(Request $request){
        $induk_rapor_dapodik_id =  $request->input('induk_rapor_dapodik_id') ? $request->input('induk_rapor_dapodik_id') : '01010000'; 

        $record = DB::connection('sqlsrv_3')
        ->table('ref.rapor_dapodik')
        ->where('induk_rapor_dapodik_id','=',$induk_rapor_dapodik_id)
        ->whereNull('expired_date')
        ->get();

        $return = array();
        $return['rows'] = $record;
        $return['total'] = sizeof($record);

        return $return;
    }

    public function getRaporDapodikIdentitas(Request $request){
        $sekolah_id = $request->input('sekolah_id') ? $request->input('sekolah_id') : '30D7A3F4-8B18-E111-85A3-011F3A87E1E4'; 
        $semester_id = $request->input('semester_id') ? $request->input('semester_id') : '20191'; 
        $start = $request->input('start') ? $request->input('start') : 0; 
        $limit = $request->input('limit') ? $request->input('limit') : 20; 
        $return = array();

        $sql = "SELECT
            sekolah.sekolah_id,
            sekolah.nama,
            ( CASE WHEN kepsek.nama IS NOT NULL AND kepsek.nama != '' AND kepsek.nama NOT LIKE '[!@#$%&*]' THEN 1 ELSE 0 END ) as '01010300', 
            ( CASE WHEN kepsek.email IS NOT NULL AND kepsek.email != '' THEN 1 ELSE 0 END ) as '01010500', 
            ( CASE WHEN kepsek.no_hp IS NOT NULL AND kepsek.no_hp != '' AND kepsek.no_hp LIKE '%[0-9]%' THEN 1 ELSE 0 END ) '01010400', 
            ( CASE WHEN sekolah.email IS NOT NULL AND sekolah.email != '' THEN 1 ELSE 0 END ) as '01010600', 
            ( CASE WHEN bp.bentuk_pendidikan_id IS NOT NULL AND bp.bentuk_pendidikan_id != '' THEN 1 ELSE 0 END ) '01010700', 
            ( CASE WHEN sekolah.status_sekolah IS NOT NULL AND sekolah.status_sekolah IN ( 1, 2 ) THEN 1 ELSE 0 END ) as '01010800', 
            ( CASE WHEN sekolah.nama IS NOT NULL AND sekolah.nama NOT LIKE '[!@#$%&*]' THEN 1 ELSE 0 END ) as '01010900', 
            ( CASE WHEN sekolah.status_sekolah = 1 THEN ( CASE WHEN seklong.partisipasi_bos = 1 THEN 1 ELSE 0 END ) ELSE 1 END ) as '01011000', 
            ( CASE WHEN seklong.akses_internet_id IS NOT NULL THEN 1 ELSE ( CASE WHEN seklong.akses_internet_2_id IS NOT NULL THEN 1 ELSE 0 END ) END ) as '01011100', 
            ( CASE WHEN seklong.sumber_listrik_id IS NOT NULL THEN 1 ELSE 0 END ) as '01011200', 
            ( CASE WHEN sekolah.status_sekolah = 1 THEN ( CASE WHEN sekolah.status_kepemilikan_id = 1 THEN 1 ELSE 0 END ) ELSE 1 END ) as '01011300', 
            ( CASE WHEN seklong.waktu_penyelenggaraan_id IS NOT NULL THEN 1 ELSE 0 END ) as '01011400', 
            ( CASE WHEN sekolah.sk_pendirian_sekolah IS NOT NULL THEN 1 ELSE 0 END ) as '01011500', 
            ( CASE WHEN sekolah.tanggal_sk_pendirian IS NOT NULL THEN 1 ELSE 0 END ) as '01011600', 
            ( CASE WHEN sekolah.sk_izin_operasional IS NOT NULL THEN 1 ELSE 0 END ) as '01011700', 
            ( CASE WHEN sekolah.tanggal_sk_izin_operasional IS NOT NULL THEN 1 ELSE 0 END ) as '01011800', 
            ( CASE WHEN seklong.sumber_listrik_id != 1 THEN ( CASE WHEN seklong.daya_listrik IS NOT NULL AND seklong.daya_listrik < 100000 THEN 1 ELSE 0 END ) ELSE 1 END ) as '01011900', 
            ( CASE WHEN seklong.sertifikasi_iso_id IS NOT NULL THEN 1 ELSE 0 END ) as '01012000'
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
                    AND ptk_terdaftar.tahun_ajaran_id = ".substr($semester_id,0,4)." 
                    AND tugas_tambahan.Soft_delete = 0 
                    AND sekolah.sekolah_id = '".$sekolah_id."'
            ) kepsek ON kepsek.sekolah_id = sekolah.sekolah_id AND kepsek.urutan = 1
            LEFT JOIN sekolah_longitudinal seklong with(nolock) ON seklong.sekolah_id = sekolah.sekolah_id AND seklong.semester_id = 20191
            JOIN ref.bentuk_pendidikan bp with(nolock) ON bp.bentuk_pendidikan_id = sekolah.bentuk_pendidikan_id
            JOIN ref.mst_wilayah kec with(nolock) on kec.kode_wilayah = LEFT(sekolah.kode_wilayah,6)
            JOIN ref.mst_wilayah kab with(nolock) on kab.kode_wilayah = kec.mst_kode_wilayah
            JOIN ref.mst_wilayah prop with(nolock) on prop.kode_wilayah = kab.mst_kode_wilayah
            JOIN ref.mst_wilayah negara with(nolock) on negara.kode_wilayah = prop.mst_kode_wilayah	
        WHERE
            sekolah.Soft_delete = 0
        AND sekolah.sekolah_id = '".$sekolah_id."'";

        $fetch = DB::connection('sqlsrv')
        ->select(DB::raw($sql));

        $return['rows'] = $fetch;
        $return['total'] = sizeof($fetch);

        return $return;
    }

    public function getRaporDapodikSarpras(Request $request){
        $sekolah_id = $request->input('sekolah_id') ? $request->input('sekolah_id') : '30D7A3F4-8B18-E111-85A3-011F3A87E1E4'; 
        $semester_id = $request->input('semester_id') ? $request->input('semester_id') : '20191'; 
        $start = $request->input('start') ? $request->input('start') : ( $request->input('start_sarpras') ? $request->input('start_sarpras') : 0 ); 
        $limit = $request->input('limit') ? $request->input('limit') : 20; 

        $sql_count = "SELECT
            SUM(1) as total
        FROM
            ruang prasarana WITH ( nolock )
            JOIN ruang_longitudinal prl WITH ( nolock ) ON prl.id_ruang = prasarana.id_ruang 
            AND prl.semester_id = ".$semester_id."
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
                AND bangunan.sekolah_id = '".$sekolah_id."' 
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
            AND sekolah.sekolah_id = '".$sekolah_id."'";

        $sql = "SELECT
            prasarana.sekolah_id,
            prasarana.id_ruang,
            prasarana.nm_ruang as nama,
            jenis.nama as jenis_prasarana,
            (cast(prasarana.panjang as varchar(10)) + 'm x ' + cast(prasarana.lebar as varchar(10)) + 'm') as panjang_lebar,
            (
                (
                ( CASE WHEN prasarana.panjang > 0 AND prasarana.panjang < 100 THEN 1 ELSE 0 END ) + ( CASE WHEN prasarana.lebar > 0 AND prasarana.lebar < 100 THEN 1 ELSE 0 END ) + ( CASE WHEN persen.persentase IS NOT NULL THEN 1 ELSE 0 END ) + ( CASE WHEN alat.jumlah > 0 THEN 1 ELSE 0 END ) + ( CASE WHEN prasarana.jenis_prasarana_id = 2 THEN ( CASE WHEN alat.jumlah_meja_siswa > 0 AND alat.jumlah_kursi_siswa > 0 THEN 1 ELSE 0 END ) ELSE 1 END ) 
            ) / 5 * 100 
            ) AS nilai_rapor 
        FROM
            ruang prasarana WITH ( nolock )
            JOIN ruang_longitudinal prl WITH ( nolock ) ON prl.id_ruang = prasarana.id_ruang 
            AND prl.semester_id = ".$semester_id."
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
                AND bangunan.sekolah_id = '".$sekolah_id."' 
            ) persen ON persen.id_ruang = prasarana.id_ruang
            JOIN sekolah ON sekolah.sekolah_id = bangunan.sekolah_id
            JOIN ref.mst_wilayah kec WITH ( nolock ) ON kec.kode_wilayah = LEFT ( sekolah.kode_wilayah, 6 )
            JOIN ref.mst_wilayah kab WITH ( nolock ) ON kab.kode_wilayah = kec.mst_kode_wilayah
            JOIN ref.mst_wilayah prop WITH ( nolock ) ON prop.kode_wilayah = kab.mst_kode_wilayah
            LEFT JOIN ref.jenis_prasarana jenis on jenis.jenis_prasarana_id = prasarana.jenis_prasarana_id
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
            AND sekolah.sekolah_id = '".$sekolah_id."'";
        
        $fetch = DB::connection('sqlsrv')
        ->select(DB::raw($sql_count));

        $return = array();
        $return['total'] = $fetch[0]->total;

        $sql .= " ORDER BY prasarana.nm_ruang OFFSET {$start} ROWS FETCH NEXT {$limit} ROWS ONLY";

        // return $sql;die;
        $fetch = DB::connection('sqlsrv')
        ->select(DB::raw($sql));

        $return['rows'] = $fetch;

        return $return;
    }

    public function getRaporDapodikRombel(Request $request){
        $sekolah_id = $request->input('sekolah_id') ? $request->input('sekolah_id') : '30D7A3F4-8B18-E111-85A3-011F3A87E1E4'; 
        $semester_id = $request->input('semester_id') ? $request->input('semester_id') : '20191'; 
        $start = $request->input('start') ? $request->input('start') : ( $request->input('start_rombel') ? $request->input('start_rombel') : 0 ); 
        $limit = $request->input('limit') ? $request->input('limit') : 20; 

        $sql_count = $sql = "SELECT
            SUM(1) as total
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
                anggota_rombel WITH ( nolock )
                JOIN rombongan_belajar WITH ( nolock ) ON rombongan_belajar.rombongan_belajar_id = anggota_rombel.rombongan_belajar_id
                JOIN sekolah WITH ( nolock ) ON sekolah.sekolah_id = rombongan_belajar.sekolah_id
                JOIN ref.mst_wilayah kec WITH ( nolock ) ON kec.kode_wilayah = LEFT ( sekolah.kode_wilayah, 6 )
                JOIN ref.mst_wilayah kab WITH ( nolock ) ON kab.kode_wilayah = kec.mst_kode_wilayah
                JOIN ref.mst_wilayah prop WITH ( nolock ) ON prop.kode_wilayah = kab.mst_kode_wilayah
                JOIN ref.mst_wilayah negara WITH ( nolock ) ON negara.kode_wilayah = prop.mst_kode_wilayah 
            WHERE
                anggota_rombel.Soft_delete = 0 
                AND rombongan_belajar.Soft_delete = 0 
                AND rombongan_belajar.semester_id = ".$semester_id." 
                AND rombongan_belajar.jenis_rombel IN ( 1, 8, 9, 10, 11, 12, 13 ) 
                AND sekolah.Soft_delete = 0 
                AND sekolah.sekolah_id = '".$sekolah_id."' 
            GROUP BY
                rombongan_belajar.rombongan_belajar_id 
            ) anggota ON anggota.rombongan_belajar_id = rombongan_belajar.rombongan_belajar_id 
            LEFT JOIN ptk on ptk.ptk_id = rombongan_belajar.ptk_id
        WHERE
            rombongan_belajar.jenis_rombel IN ( 1, 8, 9, 10, 11, 12, 13 ) 
            AND rombongan_belajar.Soft_delete = 0 
            AND sekolah.soft_delete = 0 
            AND rombongan_belajar.semester_id = ".$semester_id." 
            AND sekolah.sekolah_id = '".$sekolah_id."'";

        $sql = "SELECT
            rombongan_belajar.sekolah_id,
            rombongan_belajar.rombongan_belajar_id,
            rombongan_belajar.nama,
            rombongan_belajar.tingkat_pendidikan_id,
            ptk.nama as wali_kelas,
            ISNULL(
                (
                ( CASE WHEN rombongan_belajar.ptk_id IS NOT NULL THEN 1 ELSE 0 END ) + ( CASE WHEN rombongan_belajar.id_ruang IS NOT NULL THEN 1 ELSE 0 END ) + ( CASE WHEN rombongan_belajar.tingkat_pendidikan_id IS NOT NULL THEN 1 ELSE 0 END ) + ( CASE WHEN anggota.jumlah_anggota_rombel IS NOT NULL THEN 1 ELSE 0 END ) 
            ) / CAST ( 4 AS FLOAT ) * 100,
            0 
            ) AS nilai_rapor 
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
                anggota_rombel WITH ( nolock )
                JOIN rombongan_belajar WITH ( nolock ) ON rombongan_belajar.rombongan_belajar_id = anggota_rombel.rombongan_belajar_id
                JOIN sekolah WITH ( nolock ) ON sekolah.sekolah_id = rombongan_belajar.sekolah_id
                JOIN ref.mst_wilayah kec WITH ( nolock ) ON kec.kode_wilayah = LEFT ( sekolah.kode_wilayah, 6 )
                JOIN ref.mst_wilayah kab WITH ( nolock ) ON kab.kode_wilayah = kec.mst_kode_wilayah
                JOIN ref.mst_wilayah prop WITH ( nolock ) ON prop.kode_wilayah = kab.mst_kode_wilayah
                JOIN ref.mst_wilayah negara WITH ( nolock ) ON negara.kode_wilayah = prop.mst_kode_wilayah 
            WHERE
                anggota_rombel.Soft_delete = 0 
                AND rombongan_belajar.Soft_delete = 0 
                AND rombongan_belajar.semester_id = ".$semester_id." 
                AND rombongan_belajar.jenis_rombel IN ( 1, 8, 9, 10, 11, 12, 13 ) 
                AND sekolah.Soft_delete = 0 
                AND sekolah.sekolah_id = '".$sekolah_id."' 
            GROUP BY
                rombongan_belajar.rombongan_belajar_id 
            ) anggota ON anggota.rombongan_belajar_id = rombongan_belajar.rombongan_belajar_id 
            LEFT JOIN ptk on ptk.ptk_id = rombongan_belajar.ptk_id
        WHERE
            rombongan_belajar.jenis_rombel IN ( 1, 8, 9, 10, 11, 12, 13 ) 
            AND rombongan_belajar.Soft_delete = 0 
            AND sekolah.soft_delete = 0 
            AND rombongan_belajar.semester_id = ".$semester_id." 
            AND sekolah.sekolah_id = '".$sekolah_id."'";

        $fetch = DB::connection('sqlsrv')
        ->select(DB::raw($sql_count));

        $return = array();
        $return['total'] = $fetch[0]->total;

        $sql .= " ORDER BY rombongan_belajar.tingkat_pendidikan_id OFFSET {$start} ROWS FETCH NEXT {$limit} ROWS ONLY";

        // return $sql;die;
        $fetch = DB::connection('sqlsrv')
        ->select(DB::raw($sql));

        $return['rows'] = $fetch;

        return $return;
    }

    public function getRaporDapodikPTK(Request $request){
        $sekolah_id = $request->input('sekolah_id') ? $request->input('sekolah_id') : '30D7A3F4-8B18-E111-85A3-011F3A87E1E4'; 
        $semester_id = $request->input('semester_id') ? $request->input('semester_id') : '20191'; 
        $start = $request->input('start') ? $request->input('start') : ( $request->input('start_ptk') ? $request->input('start_ptk') : 0 ); 
        $limit = $request->input('limit') ? $request->input('limit') : 20; 

        $sql_count = "SELECT
            SUM(1) as total
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
            AND ptkd.tahun_ajaran_id = ".substr($semester_id,0,4)." 
            AND ptk.jenis_ptk_id IN ( 3, 4, 5, 6, 12, 13, 14 ) 
            AND ( ptkd.tgl_ptk_keluar > ta.tanggal_selesai OR ptkd.jenis_keluar_id IS NULL )
            AND sekolah.sekolah_id = '".$sekolah_id."'";

        $sql = "SELECT
            ptk.ptk_id,
            ptk.nama,
            ptk.nip,
            ptk.nuptk,
            bidang_sdm.bidang_studi,
            sekolah.nama as nama_sekolah,
            kec.nama as kecamatan,
            kab.nama as kabupaten,
            prop.nama as propinsi,
            ISNULL (
            ( cast ( ( 
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
                    ptk.nama IS NOT NULL 					-- nggak null
                    AND ptk.nama != ''						-- nggak string kosong
                    AND ptk.nama not like '%[0-9]%'			-- nggak mengandung angka
                ) THEN 1 ELSE 0 END ) +
                ( CASE WHEN 
                ( 
                    ptk.tanggal_lahir IS NOT NULL 			-- nggak null
                    AND ptk.tanggal_lahir != '' 			-- nggak string kosong
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
                    end 
                )
            ) as float(24) ) / 19 ) * 100,
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
            LEFT JOIN (
                SELECT
                    ROW_NUMBER () OVER ( PARTITION BY bidang_sdm.ptk_id ORDER BY bidang_sdm.urutan DESC ) AS urut,
                    bidang_sdm.* ,
                    bidang.bidang_studi AS bidang_studi 
                FROM
                    bidang_sdm
                    JOIN ptk ON ptk.ptk_id = bidang_sdm.ptk_id 
                    AND urutan = 1
                    JOIN ptk_terdaftar ptkd WITH ( nolock ) ON ptk.ptk_id = ptkd.ptk_id
                    JOIN sekolah WITH ( nolock ) ON sekolah.sekolah_id = ptkd.sekolah_id
                    JOIN ref.tahun_ajaran ta WITH ( nolock ) ON ta.tahun_ajaran_id = ptkd.tahun_ajaran_id
                    JOIN ref.bidang_studi bidang ON bidang.bidang_studi_id = bidang_sdm.bidang_studi_id 
                WHERE
                    bidang_sdm.Soft_delete = 0 
                    AND ptk.Soft_delete = 0 
                    AND ptk.Soft_delete = 0 
                    AND ptkd.Soft_delete = 0 
                    AND ptkd.ptk_induk = 1 
                    AND ptk.jenis_ptk_id IN ( 3, 4, 5, 6, 12, 13, 14 ) 
                    AND ( ptkd.tgl_ptk_keluar > ta.tanggal_selesai OR ptkd.jenis_keluar_id IS NULL ) 
                    AND ptkd.tahun_ajaran_id = ".substr($semester_id,0,4)." 
                    AND sekolah.sekolah_id = '".$sekolah_id."'
            ) bidang_sdm on bidang_sdm.ptk_id = ptk.ptk_id
        WHERE
            ptk.Soft_delete = 0 
            AND ptkd.Soft_delete = 0 
            AND ptkd.ptk_induk = 1 
            AND ptkd.tahun_ajaran_id = ".substr($semester_id,0,4)." 
            AND ptk.jenis_ptk_id IN ( 3, 4, 5, 6, 12, 13, 14 ) 
            AND ( ptkd.tgl_ptk_keluar > ta.tanggal_selesai OR ptkd.jenis_keluar_id IS NULL )
            AND sekolah.sekolah_id = '".$sekolah_id."'";

        // return $sql;die;

        $fetch = DB::connection('sqlsrv')
        ->select(DB::raw($sql_count));

        $return = array();
        $return['total'] = $fetch[0]->total;

        $sql .= " ORDER BY ptk.nama OFFSET {$start} ROWS FETCH NEXT {$limit} ROWS ONLY";

        // return $sql;die;
        $fetch = DB::connection('sqlsrv')
        ->select(DB::raw($sql));

        $return['rows'] = $fetch;

        return $return;
    }

    public function getRaporDapodikPD(Request $request){
        
        $sekolah_id = $request->input('sekolah_id') ? $request->input('sekolah_id') : '30D7A3F4-8B18-E111-85A3-011F3A87E1E4'; 
        $semester_id = $request->input('semester_id') ? $request->input('semester_id') : '20191'; 
        $start = $request->input('start') ? $request->input('start') : ( $request->input('start_pd') ? $request->input('start_pd') : 0 ); 
        $limit = $request->input('limit') ? $request->input('limit') : 20; 

        $sql_count = "SELECT
            SUM(1) as total
        FROM
            peserta_didik
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
            AND sekolah.sekolah_id = '".$sekolah_id."' 
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
            AND sekolah_id = '".$sekolah_id."'
        ) rpd ON rpd.peserta_didik_id = peserta_didik.peserta_didik_id AND rpd.sekolah_id = ar.sekolah_id -- 	AND rpd.urutan = 1
        LEFT JOIN peserta_didik_longitudinal pdl on pdl.peserta_didik_id = peserta_didik.peserta_didik_id and pdl.semester_id = ".$semester_id."
        WHERE
            peserta_didik.Soft_delete = 0
            AND rpd.sekolah_id = '".$sekolah_id."'";
        

        $sql = "SELECT
            newid() AS rapor_pd_id,
            ".$semester_id." AS semester_id,
            peserta_didik.peserta_didik_id,
            rpd.sekolah_id ,
            peserta_didik.nama,
            peserta_didik.nisn,
            ar.nama_sekolah,
            ar.nama_rombel,
            ar.tingkat_pendidikan_id,
            ar.kecamatan,
            ar.kabupaten,
            ar.propinsi,
            ( CASE WHEN 
                ( 
                    peserta_didik.nama IS NOT NULL 						            -- nggak null
                    AND peserta_didik.nama != ''									-- nggak string kosong
                    AND peserta_didik.nama not like '%[0-9]%'			            -- nggak mengandung angka
                ) THEN 1 ELSE 0 END 
            ) as nama_valid,
            ( CASE WHEN 
                ( 
                    peserta_didik.tanggal_lahir IS NOT NULL 			            -- nggak null
                    AND peserta_didik.tanggal_lahir != '' 				            -- nggak string kosong
                ) THEN 1 ELSE 0 END 
            ) as tanggal_lahir_valid,
            ( CASE WHEN 
                ( 
                    peserta_didik.nisn IS NOT NULL 								        -- nggak null
                    AND peserta_didik.nisn != '' 									-- nggak string kosong
                    AND LEN(peserta_didik.nisn) = 10							    -- panjang pas 10
                ) THEN 1 ELSE 0 END 
            ) as nisn_valid,
            ( CASE WHEN
                (
                    peserta_didik.nomor_telepon_seluler IS NOT NULL 				-- nggak null
                    AND peserta_didik.nomor_telepon_seluler != '' 					-- nggak string kosong
                ) THEN 1 ELSE 0 END 
            ) as no_telp_valid,
            ( CASE WHEN
                (
                    peserta_didik.email IS NOT NULL 								-- nggak null
                    AND peserta_didik.email != '' 									-- nggak string kosong
                ) THEN 1 ELSE 0 END 
            ) as email_valid,
            ( CASE WHEN
                (
                    peserta_didik.nama_ibu_kandung IS NOT NULL 						-- nggak null
                    AND peserta_didik.nama_ibu_kandung != '' 						-- nggak string kosong
                ) THEN 1 ELSE 0 END 
            ) as nama_ibu_kandung_valid,
            ( CASE WHEN
                (
                    peserta_didik.nama_ayah IS NOT NULL 							-- nggak null
                    AND peserta_didik.nama_ayah != '' 								-- nggak string kosong
                ) THEN 1 ELSE 0 END 
            ) as nama_ayah_valid,
            ( CASE WHEN
                (
                    peserta_didik.pekerjaan_id_ibu IS NOT NULL 						-- nggak null
                    AND peserta_didik.pekerjaan_id_ibu != '' 						-- nggak string kosong
                ) THEN 1 ELSE 0 END 
            ) as pekerjaan_ibu_valid,
            ( CASE WHEN
                (
                    peserta_didik.pekerjaan_id_ayah IS NOT NULL 					-- nggak null
                    AND peserta_didik.pekerjaan_id_ayah != '' 						-- nggak string kosong
                ) THEN 1 ELSE 0 END 
            ) as pekerjaan_ayah_valid,
            ( CASE WHEN
                (
                    pdl.tinggi_badan IS NOT NULL 								    -- nggak null
                    AND ISNUMERIC(pdl.tinggi_badan) = 1 				            -- nggak string kosong
                    AND pdl.tinggi_badan != 0										-- nggak 0
                ) THEN 1 ELSE 0 END 
            ) as tinggi_badan_valid,
            ( CASE WHEN
                (
                    pdl.berat_badan IS NOT NULL 								    -- nggak null
                    AND ISNUMERIC(pdl.berat_badan) = 1 			                    -- nggak string kosong
                    AND pdl.berat_badan != 0 										-- nggak 0
                ) THEN 1 ELSE 0 END 
            ) as berat_badan_valid,
            ( CASE WHEN
                (
                    peserta_didik.lintang IS NOT NULL 								-- nggak null
                    AND peserta_didik.lintang != 0 									-- nggak 0
                ) THEN 1 ELSE 0 END 
            ) as lintang_valid,
            ( CASE WHEN
                (
                    peserta_didik.bujur IS NOT NULL 								-- nggak null
                    AND peserta_didik.bujur != 0 									-- nggak 0
                ) THEN 1 ELSE 0 END 
            ) as bujur_valid,
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
                ) THEN 1 ELSE 0 END ) +
                ( CASE WHEN
                (
                    peserta_didik.nama_ayah IS NOT NULL 							-- nggak null
                    AND peserta_didik.nama_ayah != '' 								-- nggak string kosong
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
            peserta_didik
        JOIN (
            SELECT
                ROW_NUMBER () OVER ( PARTITION BY anggota_rombel.peserta_didik_id ORDER BY rombongan_belajar.tingkat_pendidikan_id DESC ) AS urutan,
                anggota_rombel.*,
                rombongan_belajar.sekolah_id,
                rombongan_belajar.nama as nama_rombel,
                rombongan_belajar.tingkat_pendidikan_id,
                sekolah.nama as nama_sekolah,
                kec.nama as kecamatan,
                kab.nama as kabupaten,
                prop.nama as propinsi 
            FROM
                anggota_rombel
                JOIN rombongan_belajar ON rombongan_belajar.rombongan_belajar_id = anggota_rombel.rombongan_belajar_id
                JOIN ptk ON ptk.ptk_id = rombongan_belajar.ptk_id
                JOIN sekolah ON sekolah.sekolah_id = rombongan_belajar.sekolah_id 
                JOIN ref.mst_wilayah kec ON kec.kode_wilayah = left(sekolah.kode_wilayah,6)
                JOIN ref.mst_wilayah kab ON kab.kode_wilayah = kec.mst_kode_wilayah 
                JOIN ref.mst_wilayah prop ON prop.kode_wilayah = kab.mst_kode_wilayah 
            WHERE
                anggota_rombel.Soft_delete = 0 
            AND rombongan_belajar.Soft_delete = 0 
            AND ptk.Soft_delete = 0 
            AND sekolah.Soft_delete = 0 
            AND rombongan_belajar.jenis_rombel IN ( 1, 8, 9, 10, 11, 12, 13 ) 
            AND rombongan_belajar.semester_id = ".$semester_id." 
            AND sekolah.sekolah_id = '".$sekolah_id."' 
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
            AND sekolah_id = '".$sekolah_id."' 
        ) rpd ON rpd.peserta_didik_id = peserta_didik.peserta_didik_id AND rpd.sekolah_id = ar.sekolah_id -- 	AND rpd.urutan = 1
        LEFT JOIN peserta_didik_longitudinal pdl on pdl.peserta_didik_id = peserta_didik.peserta_didik_id and pdl.semester_id = ".$semester_id."
        WHERE
            peserta_didik.Soft_delete = 0
            AND rpd.sekolah_id = '".$sekolah_id."'";

        $fetch = DB::connection('sqlsrv')
        ->select(DB::raw($sql_count));

        $return = array();
        $return['total'] = $fetch[0]->total;
        
        $sql .= " ORDER BY peserta_didik.nama OFFSET {$start} ROWS FETCH NEXT {$limit} ROWS ONLY";

        // return $sql;die;
        $fetch = DB::connection('sqlsrv')
        ->select(DB::raw($sql));

        $return['rows'] = $fetch;

        return $return;
    }

    // public function getRaporDapodikSekolah(Request $request){
    //     // rekap_rapor_dapodik_sekolah
    //     $kode_wilayah = $request->input('kode_wilayah') ? $request->input('kode_wilayah') : '010000'; 
    //     $semester_id = $request->input('semester_id') ? $request->input('semester_id') : '20191'; 
    //     $return = array();
    //     $rows = array();

    //     // return $request->input('start');die;

    //     // if($kode_wilayah){
    //     $sekolah = Redis::lrange('rekap_rapor_dapodik_sekolah:'.$kode_wilayah.":".$semester_id,$request->input('start'),($request->input('start')+($request->input('limit')-1)));
    //     $sekolahCount = Redis::llen('rekap_rapor_dapodik_sekolah:'.$kode_wilayah.":".$semester_id);

    //     // return sizeof($sekolah);die;

    //     for ($i=0; $i < sizeof($sekolah); $i++) { 
    //         $row = json_decode( Redis::get('rekap_rapor_dapodik_sekolah:'.$sekolah[$i]) );

    //         array_push($rows, $row);
    //     }
    //     // }

    //     $return['rows'] = $rows;
    //     $return['total'] = $sekolahCount;

    //     return $return;
    // }

    public function getRaporDapodikSekolah(Request $request){
        $sekolah_id = $request->input('sekolah_id') ? $request->input('sekolah_id') : null;
        $semester_id = $request->input('semester_id') ? $request->input('semester_id') : '20191';
        $mst_kode_wilayah = $request->input('mst_kode_wilayah') ? $request->input('mst_kode_wilayah') : null;
        $kode_wilayah = $request->input('kode_wilayah') ? $request->input('kode_wilayah') : null;
        $bentuk_pendidikan_id = $request->input('bentuk_pendidikan_id') ? $request->input('bentuk_pendidikan_id') : null;
        $keyword = $request->input('keyword') ? $request->input('keyword') : null;
        $start = $request->input('start') ? $request->input('start') : 0; 
        $limit = $request->input('limit') ? $request->input('limit') : 20; 
        $return = array();
        $rows = array();

        // return substr($mst_kode_wilayah,2,2);die;
        if($request->input('sekolah_id')){
            $param_sekolah_id = "AND rekap.rekap_rapor_dapodik_sekolah.sekolah_id = '".$request->input('sekolah_id')."'";
            $param_kode = "";
            $param_keyword = "";
            $param_bentuk = "";
            $param_status = "";
        }else{
            $param_sekolah_id = "";
            
            if($kode_wilayah == '000000'){
                //nasional
                $kolom_kode = "kode_wilayah_provinsi";
                $kolom_nama = "provinsi";
                $kolom_id_level = "id_level_wilayah_provinsi";
                $param_kode = "";
            }else{
                if(substr($kode_wilayah,2,2) == "00"){
                    //provinsi
                    $param_kode = "AND kode_wilayah_provinsi = '".$kode_wilayah."'";
                }else if(substr($kode_wilayah,4,2) == "00"){
                    //kabupaten
                    $param_kode = "AND kode_wilayah_kabupaten = '".$kode_wilayah."'";
                }else{
                    //kecamatan
                    $param_kode = "AND kode_wilayah_kecamatan = '".$kode_wilayah."'";
                }
            }
    
            if($keyword){
                $param_keyword = "AND (nama like '%".$keyword."%' OR npsn like '%".$keyword."%')";
            }else{
                $param_keyword = "";
            }
    
            if($request->input('bentuk_pendidikan_id')){
                $arrBentuk = explode("-", $request->input('bentuk_pendidikan_id'));
                $strBentuk = "(";
    
                for ($iBentuk=0; $iBentuk < sizeof($arrBentuk); $iBentuk++) { 
                    if($arrBentuk[$iBentuk] == '13'){
                        $strBentuk .= "13,55,";
                    }else if($arrBentuk[$iBentuk] == '5'){
                        $strBentuk .= "5,53,";
                    }else if($arrBentuk[$iBentuk] == '6'){
                        $strBentuk .= "6,54,";
                    }else{
                        $strBentuk .= $arrBentuk[$iBentuk].",";
                    }
                }
    
                $strBentuk = substr($strBentuk, 0, (strlen($strBentuk)-1));
                $strBentuk .= ")";
    
                // return $strBentuk;
                $param_bentuk = "AND rekap.rekap_rapor_dapodik_sekolah.bentuk_pendidikan_id IN ".$strBentuk;
    
                // return $param_bentuk;die;
            }else{
                $param_bentuk = "";
            }
    
            if($request->input('status_sekolah') && (int)$request->input('status_sekolah') != 99){
                $param_status = "AND rekap.rekap_rapor_dapodik_sekolah.status_sekolah = ".$request->input('status_sekolah');
            }else{
                $param_status = "AND rekap.rekap_rapor_dapodik_sekolah.status_sekolah IN (1,2)";
            }
        }
        

        $sql_count = "SELECT
                    SUM(1) as total
                FROM
                    rekap.rekap_rapor_dapodik_sekolah
                WHERE 
                    soft_delete = 0
                {$param_kode}
                {$param_keyword}
                {$param_bentuk}
                {$param_status}
                {$param_sekolah_id}
                AND semester_id = '".$semester_id."'";

        $sql = "SELECT
                    *,
                    null as induk,
                    -- round(cast(rapor_akhir as float),2) as rapor_akhir,
                    -- round(cast(rapor_sekolah as float),2) as rapor_sekolah,
                    -- round(cast(rapor_pd as float),2) as rapor_pd,
                    -- round(cast(rapor_ptk as float),2) as rapor_ptk,
                    -- round(cast(rapor_rombel as float),2) as rapor_rombel,
                    -- round(cast(rapor_sarpras as float),2) as rapor_sarpras,
                    ( (
                    rapor_berkelanjutan +
                    rapor_mutakhir +
                    rapor_akhir
                    ) / 3 ) as rapor_final,
                    last_update AS tanggal_rekap_terakhir 
                FROM
                    rekap.rekap_rapor_dapodik_sekolah
                WHERE 
                    soft_delete = 0
                {$param_kode}
                {$param_keyword}
                {$param_bentuk}
                {$param_status}
                {$param_sekolah_id}
                AND semester_id = '".$semester_id."'
                ORDER BY 
                    rapor_akhir DESC 
                OFFSET {$start} ROWS 
                FETCH NEXT {$limit} ROWS ONLY";
        
        // return $sql;die;
        
        $return['total'] = (int)DB::connection('sqlsrv_3')->select(DB::raw($sql_count))[0]->total;

        $fetch = DB::connection('sqlsrv_3')
        ->select(DB::raw($sql));

        $return['rows'] = $fetch;

        return $return;

    }

    public function getRaporDapodik(Request $request){
        $semester_id = $request->input('semester_id') ? $request->input('semester_id') : '20191';
        $mst_kode_wilayah = $request->input('mst_kode_wilayah') ? $request->input('mst_kode_wilayah') : '000000';
        $kode_wilayah = $request->input('kode_wilayah') ? $request->input('kode_wilayah') : null;
        $kode_wilayah = $request->input('kode_wilayah') ? $request->input('kode_wilayah') : null;
        $bentuk_pendidikan_id = $request->input('bentuk_pendidikan_id') ? $request->input('bentuk_pendidikan_id') : null;
        $keyword = $request->input('keyword') ? $request->input('keyword') : null;
        $limit = $request->input('limit') ? $request->input('limit') : 1000; 
        $start = $request->input('start') ? $request->input('start') : 0; 
        $output = $request->input('output') ? $request->input('output') : 'json'; 
        $override_wilayah = $request->input('override_wilayah') ? $request->input('override_wilayah') : 0; 
        $order_by = $request->input('order_by') ? $request->input('order_by') : 'rapor_final'; 
        $return = array();
        $rows = array();

        if($override_wilayah == 0){
         
            if(!$kode_wilayah){

                if($mst_kode_wilayah == '000000'){
                    //nasional
                    $kolom_kode = "kode_wilayah_provinsi";
                    $kolom_nama = "provinsi";
                    $kolom_id_level = "id_level_wilayah_provinsi";
                    $induk = "negara";
                    $param_kode = "";
                }else{
                    if(substr($mst_kode_wilayah,2,2) == "00"){
                        //provinsi
                        $kolom_kode = "kode_wilayah_kabupaten";
                        $kolom_nama = "kabupaten";
                        $kolom_id_level = "id_level_wilayah_kabupaten";
                        $induk = "provinsi";
                        $param_kode = "AND kode_wilayah_provinsi = '".$mst_kode_wilayah."'";
                    }else{
                        //kecamatan
                        $kolom_kode = "kode_wilayah_kecamatan";
                        $kolom_nama = "kecamatan";
                        $kolom_id_level = "id_level_wilayah_kecamatan";
                        $induk = "kabupaten";
                        $param_kode = "AND kode_wilayah_kabupaten = '".$mst_kode_wilayah."'";
                    }
                }

            }else{

                if($kode_wilayah == '000000'){
                    //nasional
                    $kolom_kode = "kode_wilayah_negara";
                    $kolom_nama = "negara";
                    $kolom_id_level = "id_level_wilayah_negara";
                    $induk = "null";
                    $param_kode = "AND kode_wilayah_negara = '".$kode_wilayah."'";
                }else{
                    if(substr($kode_wilayah,2,2) == "00"){
                        //provinsi
                        $kolom_kode = "kode_wilayah_provinsi";
                        $kolom_nama = "provinsi";
                        $kolom_id_level = "id_level_wilayah_provinsi";
                        $induk = "negara";
                        $param_kode = "AND kode_wilayah_provinsi = '".$kode_wilayah."'";
                    }else if(substr($kode_wilayah,4,2) == "00"){
                        //kabupaten
                        $kolom_kode = "kode_wilayah_kabupaten";
                        $kolom_nama = "kabupaten";
                        $kolom_id_level = "id_level_wilayah_kabupaten";
                        $induk = "provinsi";
                        $param_kode = "AND kode_wilayah_kabupaten = '".$kode_wilayah."'";
                    }else{
                        //kecamatan
                        $kolom_kode = "kode_wilayah_kecamatan";
                        $kolom_nama = "kecamatan";
                        $kolom_id_level = "id_level_wilayah_kecamatan";
                        $induk = "kabupaten";
                        $param_kode = "AND kode_wilayah_kecamatan = '".$kode_wilayah."'";
                    }
                }

            }

        }else{

            $kolom_kode = "kode_wilayah_kabupaten";
            $kolom_nama = "kabupaten";
            $kolom_id_level = "id_level_wilayah_kabupaten";
            $induk = "provinsi";
            $param_kode = " AND kode_wilayah_provinsi != '350000'";

        }

        if($keyword){
            $param_keyword = "AND {$kolom_nama} like '%".$keyword."%'";
        }else{
            $param_keyword = "";
        }

        if($request->input('bentuk_pendidikan_id')){
            $arrBentuk = explode("-", $request->input('bentuk_pendidikan_id'));
            $strBentuk = "(";

            for ($iBentuk=0; $iBentuk < sizeof($arrBentuk); $iBentuk++) { 
                if($arrBentuk[$iBentuk] == '13'){
                    $strBentuk .= "13,55,";
                }else if($arrBentuk[$iBentuk] == '5'){
                    $strBentuk .= "5,53,";
                }else if($arrBentuk[$iBentuk] == '6'){
                    $strBentuk .= "6,54,";
                }else{
                    $strBentuk .= $arrBentuk[$iBentuk].",";
                }
            }

            $strBentuk = substr($strBentuk, 0, (strlen($strBentuk)-1));
            $strBentuk .= ")";

            // return $strBentuk;
            $param_bentuk = "AND rekap.rekap_rapor_dapodik_sekolah.bentuk_pendidikan_id IN ".$strBentuk;

            // return $param_bentuk;die;
        }else{
            $param_bentuk = "";
        }

        if($request->input('status_sekolah') && (int)$request->input('status_sekolah') != 99){
            $param_status = "AND rekap.rekap_rapor_dapodik_sekolah.status_sekolah = ".$request->input('status_sekolah');
        }else{
            $param_status = "AND rekap.rekap_rapor_dapodik_sekolah.status_sekolah IN (1,2)";
        }

        switch ($order_by) {
            case 'rapor_akhir':
                $strOrder = "ISNULL( round( AVG ( round( rapor_akhir, 2 )), 2 ), 0 )";
                break;
            case 'rapor_mutakhir':
                $strOrder = "ISNULL( round( AVG ( round( rapor_mutakhir, 2 )), 2 ), 0 )";
                break;
            case 'rapor_berkelanjutan':
                $strOrder = "ISNULL( round( AVG ( round( rapor_berkelanjutan, 2 )), 2 ), 0 )";
                break;
            default:
                $strOrder = "( (
                    ISNULL( round( AVG ( round( rapor_berkelanjutan, 2 )), 2 ), 0 ) +
                    ISNULL( round( AVG ( round( rapor_mutakhir, 2 )), 2 ), 0 ) +
                    ISNULL( round( AVG ( round( rapor_akhir, 2 )), 2 ), 0 )
                    ) / 3 )";
                break;
        }

        $sql = "SELECT
                    RTRIM({$kolom_kode}) AS kode_wilayah,
                    {$kolom_nama} AS nama,
                    {$kolom_id_level} AS id_level_wilayah,
                    {$induk} as induk,
                    ISNULL( round( AVG ( round( rapor_sekolah, 2 )), 2 ), 0 ) AS rapor_sekolah,
                    ISNULL( round( AVG ( round( rapor_ptk, 2 )), 2 ), 0 ) AS rapor_ptk,
                    ISNULL( round( AVG ( round( rapor_pd, 2 )), 2 ), 0 ) AS rapor_pd,
                    ISNULL( round( AVG ( round( rapor_rombel, 2 )), 2 ), 0 ) AS rapor_rombel,
                    ISNULL( round( AVG ( round( rapor_sarpras, 2 )), 2 ), 0 ) AS rapor_sarpras,
                    ISNULL( round( AVG ( round( rapor_akhir, 2 )), 2 ), 0 ) AS rapor_akhir,
                    ISNULL( round( AVG ( round( rapor_berkelanjutan, 2 )), 2 ), 0 ) AS rapor_berkelanjutan,

                    ISNULL( round( AVG ( round( rapor_mutakhir_pd, 2 )), 2 ), 0 ) AS rapor_mutakhir_pd,
                    ISNULL( round( AVG ( round( rapor_mutakhir_ptk, 2 )), 2 ), 0 ) AS rapor_mutakhir_ptk,
                    ISNULL( round( AVG ( round( rapor_mutakhir_rombel, 2 )), 2 ), 0 ) AS rapor_mutakhir_rombel,
                    ISNULL( round( AVG ( round( rapor_mutakhir_sarpras, 2 )), 2 ), 0 ) AS rapor_mutakhir_sarpras,
                    ISNULL( round( AVG ( round( rapor_mutakhir, 2 )), 2 ), 0 ) AS rapor_mutakhir,

                    ( (
                    ISNULL( round( AVG ( round( rapor_berkelanjutan, 2 )), 2 ), 0 ) +
                    ISNULL( round( AVG ( round( rapor_mutakhir, 2 )), 2 ), 0 ) +
                    ISNULL( round( AVG ( round( rapor_akhir, 2 )), 2 ), 0 )
                    ) / 3 ) as rapor_final,

                    MAX ( last_update ) AS tanggal_rekap_terakhir 
                FROM
                    rekap.rekap_rapor_dapodik_sekolah
                WHERE 
                    soft_delete = 0
                {$param_kode}
                {$param_keyword}
                {$param_bentuk}
                {$param_status}
                AND semester_id = '".$semester_id."'
                GROUP BY
                    {$kolom_kode},
                    {$kolom_nama},
                    {$kolom_id_level}
                    ,{$induk}
                ORDER BY
                    {$strOrder} DESC";
        
        if($override_wilayah){
            $sql .= " OFFSET {$start} ROWS FETCH NEXT {$limit} ROWS ONLY";
        }

        // return $sql;die;

        $fetch = DB::connection('sqlsrv_3')
        ->select(DB::raw($sql));

        $return['total'] = sizeof($fetch);
        $return['rows'] = $fetch;
        $return['tanggal_rekap_terakhir'] = $fetch[0]->tanggal_rekap_terakhir;

        for ($iFetch=0; $iFetch < sizeof($fetch); $iFetch++) { 
            $return['tanggal_rekap_terakhir'] = ($fetch[$iFetch]->tanggal_rekap_terakhir > $return['tanggal_rekap_terakhir'] ? $fetch[$iFetch]->tanggal_rekap_terakhir : $return['tanggal_rekap_terakhir']);
        }
        // $return['tanggal_rekap_terakhir'] = $fetch;

        return $return;
        
    }

    // public function getRaporDapodik(Request $request){
    //     $semester_id = $request->input('semester_id') ? $request->input('semester_id') : '20191';
    //     $keyword = $request->input('keyword') ? $request->input('keyword') : null;
    //     $limit = $request->input('limit') ? $request->input('limit') : 1000; 
    //     $return = array();
    //     $rows = array();

    //     if($request->input('kode_wilayah') && !$request->input('id_level_wilayah')){
    //         array_push($rows, json_decode(Redis::get('wilayah:'.$request->input('kode_wilayah'))));
    //     }else{
    //         if($request->input('mst_kode_wilayah')){
    //             $wilayah = Redis::lrange('wilayah:mst_kode_wilayah:'.$request->input('mst_kode_wilayah'),0,-1);
    //         }else{
    //             $wilayah = Redis::lrange('wilayah:kode_wilayah:1',0,$limit);
    //         }
            
    //         for ($i=0; $i < sizeof($wilayah); $i++) { 
    //             $record = json_decode(Redis::get('rekap_rapor_dapodik_wilayah:'.$wilayah[$i].':'.$semester_id));
                
    //             if(trim($record->mst_kode_wilayah) != '000000'){
    //                 $induk = json_decode(Redis::get('wilayah:'.trim($record->mst_kode_wilayah)));
    //                 $record->induk = $induk->nama;
    //             }
                
    //             // foreach ($induk as $key => $value) {
    //             //     // echo "$key => $value\n";
    //             //     $record->$key = $value;
    //             // }
    //             // array_push($rows, $record->mst_kode_wilayah);
    //             array_push($rows, $record);
    //         }
    //     }

    //     usort($rows, function($a, $b) {return strcmp($b->rapor_akhir, $a->rapor_akhir);});

    //     $return['total'] = sizeof($rows);
    //     $return['rows'] = $rows;

    //     return $return;
    // }

    // public function getRaporDapodikPDIndividu(Request $request){
        
    //     $sekolah_id = $request->input('sekolah_id') ? $request->input('sekolah_id') : '30D7A3F4-8B18-E111-85A3-011F3A87E1E4'; 
    //     $sekolah_id = $request->input('sekolah_id') ? $request->input('sekolah_id') : '30D7A3F4-8B18-E111-85A3-011F3A87E1E4'; 
    //     $kode_wilayah = $request->input('kode_wilayah') ? $request->input('kode_wilayah') : null; 
    //     $id_level_wilayah = $request->input('id_level_wilayah') ? $request->input('id_level_wilayah') : null; 
    //     $start = $request->input('start') ? $request->input('start') : ( $request->input('start_pd') ? $request->input('start_pd') : 0 ); 
    //     $limit = $request->input('limit') ? $request->input('limit') : 20; 

    //     $sql_count = "SELECT
    //         SUM(1) as total
    //     FROM
    //         peserta_didik
    //     JOIN (
    //         SELECT
    //             ROW_NUMBER () OVER ( PARTITION BY anggota_rombel.peserta_didik_id ORDER BY rombongan_belajar.tingkat_pendidikan_id DESC ) AS urutan,
    //             anggota_rombel.*,
    //             rombongan_belajar.sekolah_id 
    //         FROM
    //             anggota_rombel
    //             JOIN rombongan_belajar ON rombongan_belajar.rombongan_belajar_id = anggota_rombel.rombongan_belajar_id
    //             JOIN ptk ON ptk.ptk_id = rombongan_belajar.ptk_id
    //             JOIN sekolah ON sekolah.sekolah_id = rombongan_belajar.sekolah_id 
    //         WHERE
    //             anggota_rombel.Soft_delete = 0 
    //         AND rombongan_belajar.Soft_delete = 0 
    //         AND ptk.Soft_delete = 0 
    //         AND sekolah.Soft_delete = 0 
    //         AND rombongan_belajar.jenis_rombel IN ( 1, 8, 9, 10, 11, 12, 13 ) 
    //         AND rombongan_belajar.semester_id = ".$semester_id." 
    //         AND sekolah.sekolah_id = '".$sekolah_id."' 
    //     ) ar ON ar.peserta_didik_id = peserta_didik.peserta_didik_id AND ar.urutan = 1
    //     JOIN (
    //         SELECT
    //             ROW_NUMBER () OVER ( PARTITION BY registrasi_peserta_didik.peserta_didik_id ORDER BY registrasi_peserta_didik.tanggal_masuk_sekolah DESC ) AS urutan,
    //             * 
    //         FROM
    //             registrasi_peserta_didik 
    //         WHERE
    //             Soft_delete = 0 
    //         AND jenis_keluar_id IS NULL 
    //         AND sekolah_id = '".$sekolah_id."'
    //     ) rpd ON rpd.peserta_didik_id = peserta_didik.peserta_didik_id AND rpd.sekolah_id = ar.sekolah_id -- 	AND rpd.urutan = 1
    //     LEFT JOIN peserta_didik_longitudinal pdl on pdl.peserta_didik_id = peserta_didik.peserta_didik_id and pdl.semester_id = ".$semester_id."
    //     WHERE
    //         peserta_didik.Soft_delete = 0
    //         AND rpd.sekolah_id = '".$sekolah_id."'";
        

    //     $sql = "SELECT
    //         newid() AS rapor_pd_id,
    //         ".$semester_id." AS semester_id,
    //         peserta_didik.peserta_didik_id,
    //         rpd.sekolah_id ,
    //         peserta_didik.nama,
    //         peserta_didik.nisn,
    //         ar.nama_sekolah,
    //         ar.nama_rombel,
    //         ar.tingkat_pendidikan_id,
    //         ar.kecamatan,
    //         ar.kabupaten,
    //         ar.propinsi,
    //         (
    //             (
    //             ( CASE WHEN 
    //             ( 
    //             peserta_didik.nama IS NOT NULL 						                -- nggak null
    //                 AND peserta_didik.nama != ''									-- nggak string kosong
    //                 AND peserta_didik.nama not like '%[0-9]%'			            -- nggak mengandung angka
    //             ) THEN 1 ELSE 0 END ) + 
    //             ( CASE WHEN 
    //             ( 
    //                 peserta_didik.tanggal_lahir IS NOT NULL 			            -- nggak null
    //                 AND peserta_didik.tanggal_lahir != '' 				            -- nggak string kosong
    //             ) THEN 1 ELSE 0 END ) + 
    //             ( CASE WHEN 
    //             ( 
    //             peserta_didik.nisn IS NOT NULL 								        -- nggak null
    //                 AND peserta_didik.nisn != '' 									-- nggak string kosong
    //                 AND LEN(peserta_didik.nisn) = 10							    -- panjang pas 10
    //             ) THEN 1 ELSE 0 END ) +
    //             ( CASE WHEN
    //             (
    //                 peserta_didik.nomor_telepon_seluler IS NOT NULL 				-- nggak null
    //                 AND peserta_didik.nomor_telepon_seluler != '' 					-- nggak string kosong
    //             ) THEN 1 ELSE 0 END ) +
    //             ( CASE WHEN
    //             (
    //                 peserta_didik.email IS NOT NULL 								-- nggak null
    //                 AND peserta_didik.email != '' 									-- nggak string kosong
    //             ) THEN 1 ELSE 0 END ) +
    //             ( CASE WHEN
    //             (
    //                 peserta_didik.nama_ibu_kandung IS NOT NULL 						-- nggak null
    //                 AND peserta_didik.nama_ibu_kandung != '' 						-- nggak string kosong
    //             ) THEN 1 ELSE 0 END ) +
    //             ( CASE WHEN
    //             (
    //                 peserta_didik.nama_ayah IS NOT NULL 							-- nggak null
    //                 AND peserta_didik.nama_ayah != '' 								-- nggak string kosong
    //             ) THEN 1 ELSE 0 END ) +
    //             ( CASE WHEN
    //             (
    //                 peserta_didik.pekerjaan_id_ibu IS NOT NULL 						-- nggak null
    //                 AND peserta_didik.pekerjaan_id_ibu != '' 						-- nggak string kosong
    //             ) THEN 1 ELSE 0 END ) +
    //             ( CASE WHEN
    //             (
    //                 peserta_didik.pekerjaan_id_ayah IS NOT NULL 					-- nggak null
    //                 AND peserta_didik.pekerjaan_id_ayah != '' 						-- nggak string kosong
    //             ) THEN 1 ELSE 0 END ) +
    //             (
    //                 CASE WHEN peserta_didik.penerima_KIP = 1 then ( case when peserta_didik.no_KIP IS NOT NULl then 1 else 0 end ) else 1 end
    //             ) +
    //             ( CASE WHEN
    //             (
    //                 pdl.tinggi_badan IS NOT NULL 								    -- nggak null
    //                 AND ISNUMERIC(pdl.tinggi_badan) = 1 				            -- nggak string kosong
    //                 AND pdl.tinggi_badan != 0										-- nggak 0
    //             ) THEN 1 ELSE 0 END ) +
    //             ( CASE WHEN
    //             (
    //                 pdl.berat_badan IS NOT NULL 								    -- nggak null
    //                 AND ISNUMERIC(pdl.berat_badan) = 1 			                    -- nggak string kosong
    //                 AND pdl.berat_badan != 0 										-- nggak 0
    //             ) THEN 1 ELSE 0 END ) +
    //             ( CASE WHEN
    //             (
    //                 peserta_didik.lintang IS NOT NULL 								-- nggak null
    //                 AND peserta_didik.lintang != 0 									-- nggak 0
    //             ) THEN 1 ELSE 0 END ) +
    //             ( CASE WHEN
    //             (
    //                 peserta_didik.bujur IS NOT NULL 								-- nggak null
    //                 AND peserta_didik.bujur != 0 									-- nggak 0
    //             ) THEN 1 ELSE 0 END )
    //         ) 
    //         / CAST ( 14 AS FLOAT ) * 100 
    //         ) AS nilai_rapor,
    //         getdate() AS create_date,
    //         getdate() AS last_update,
    //         0 AS soft_delete,
    //         NULL AS updater_id 
    //     FROM
    //         peserta_didik
    //     JOIN (
    //         SELECT
    //             ROW_NUMBER () OVER ( PARTITION BY anggota_rombel.peserta_didik_id ORDER BY rombongan_belajar.tingkat_pendidikan_id DESC ) AS urutan,
    //             anggota_rombel.*,
    //             rombongan_belajar.sekolah_id,
    //             rombongan_belajar.nama as nama_rombel,
    //             rombongan_belajar.tingkat_pendidikan_id,
    //             sekolah.nama as nama_sekolah,
    //             kec.nama as kecamatan,
    //             kab.nama as kabupaten,
    //             prop.nama as propinsi 
    //         FROM
    //             anggota_rombel
    //             JOIN rombongan_belajar ON rombongan_belajar.rombongan_belajar_id = anggota_rombel.rombongan_belajar_id
    //             JOIN ptk ON ptk.ptk_id = rombongan_belajar.ptk_id
    //             JOIN sekolah ON sekolah.sekolah_id = rombongan_belajar.sekolah_id 
    //             JOIN ref.mst_wilayah kec ON kec.kode_wilayah = left(sekolah.kode_wilayah,6)
    //             JOIN ref.mst_wilayah kab ON kab.kode_wilayah = kec.mst_kode_wilayah 
    //             JOIN ref.mst_wilayah prop ON prop.kode_wilayah = kab.mst_kode_wilayah 
    //         WHERE
    //             anggota_rombel.Soft_delete = 0 
    //         AND rombongan_belajar.Soft_delete = 0 
    //         AND ptk.Soft_delete = 0 
    //         AND sekolah.Soft_delete = 0 
    //         AND rombongan_belajar.jenis_rombel IN ( 1, 8, 9, 10, 11, 12, 13 ) 
    //         AND rombongan_belajar.semester_id = ".$semester_id." 
    //         AND sekolah.sekolah_id = '".$sekolah_id."' 
    //     ) ar ON ar.peserta_didik_id = peserta_didik.peserta_didik_id AND ar.urutan = 1
    //     JOIN (
    //         SELECT
    //             ROW_NUMBER () OVER ( PARTITION BY registrasi_peserta_didik.peserta_didik_id ORDER BY registrasi_peserta_didik.tanggal_masuk_sekolah DESC ) AS urutan,
    //             * 
    //         FROM
    //             registrasi_peserta_didik 
    //         WHERE
    //             Soft_delete = 0 
    //         AND jenis_keluar_id IS NULL 
    //         AND sekolah_id = '".$sekolah_id."' 
    //     ) rpd ON rpd.peserta_didik_id = peserta_didik.peserta_didik_id AND rpd.sekolah_id = ar.sekolah_id -- 	AND rpd.urutan = 1
    //     LEFT JOIN peserta_didik_longitudinal pdl on pdl.peserta_didik_id = peserta_didik.peserta_didik_id and pdl.semester_id = ".$semester_id."
    //     WHERE
    //         peserta_didik.Soft_delete = 0
    //         AND rpd.sekolah_id = '".$sekolah_id."'";

    //     $fetch = DB::connection('sqlsrv')
    //     ->select(DB::raw($sql_count));

    //     $return = array();
    //     $return['total'] = $fetch[0]->total;
        
    //     $sql .= " ORDER BY peserta_didik.nama OFFSET {$start} ROWS FETCH NEXT {$limit} ROWS ONLY";

    //     // return $sql;die;
    //     $fetch = DB::connection('sqlsrv')
    //     ->select(DB::raw($sql));

    //     $return['rows'] = $fetch;

    //     return $return;
    // }

    public function getRaporDapodikAkuratRadar(Request $request){
        $data = self::getRaporDapodik($request);

        $akurat_sekolah = 0;
        $akurat_pd = 0;
        $akurat_ptk = 0;
        $akurat_sarpras = 0;
        $akurat_rombel = 0;

        for ($i=0; $i < sizeof($data['rows']); $i++) { 
            $akurat_sekolah += (float)$data['rows'][$i]->{'rapor_sekolah'};
            $akurat_pd += (float)$data['rows'][$i]->{'rapor_pd'};
            $akurat_ptk += (float)$data['rows'][$i]->{'rapor_ptk'};
            $akurat_sarpras += (float)$data['rows'][$i]->{'rapor_sarpras'};
            $akurat_rombel += (float)$data['rows'][$i]->{'rapor_rombel'};
        }

        $return = array();
        $return['labels'] = ['Sekolah', 'PD', 'PTK', 'Sarpras', 'Rombel'];
        $return['datasets'] = [
            [
                "data" => [
                    round((float)$akurat_sekolah/sizeof($data['rows']),2), 
                    round((float)$akurat_pd/sizeof($data['rows']),2),
                    round((float)$akurat_ptk/sizeof($data['rows']),2),
                    round((float)$akurat_sarpras/sizeof($data['rows']),2),
                    round((float)$akurat_rombel/sizeof($data['rows']),2)
                ],
                "label" => "Nilai",
                "backgroundColor" => 'rgba(179,181,198,0.2)',
                "borderColor" => 'rgba(179,181,198,1)',
                // "pointBackgroundColor" => 'rgba(179,181,198,1)',
                "pointBorderColor" => '#fff',
                "pointHoverBackgroundColor" => '#fff',
                "pointHoverBorderColor" => 'rgba(179,181,198,1)',
                "pointBackgroundColor"  => [
                    '#558b2f',
                    '#039be5',
                    '#ff6f00',
                    '#ff6f00',
                    '#ff6f00',
                ]
            ]
        ];

        return $return;
    }

    public function getRaporDapodikMutakhirRadar(Request $request){
        $data = self::getRaporDapodik($request);

        $mutakhir_pd = 0;
        $mutakhir_ptk = 0;
        $mutakhir_sarpras = 0;
        $mutakhir_rombel = 0;

        for ($i=0; $i < sizeof($data['rows']); $i++) { 
            $mutakhir_pd += (float)$data['rows'][$i]->{'rapor_pd'};
            $mutakhir_ptk += (float)$data['rows'][$i]->{'rapor_ptk'};
            $mutakhir_sarpras += (float)$data['rows'][$i]->{'rapor_sarpras'};
            $mutakhir_rombel += (float)$data['rows'][$i]->{'rapor_rombel'};
        }

        $return = array();
        $return['labels'] = ['PD', 'PTK', 'Sarpras', 'Rombel'];
        $return['datasets'] = [
            [
                "data" => [
                    round((float)$mutakhir_pd/sizeof($data['rows']),2),
                    round((float)$mutakhir_ptk/sizeof($data['rows']),2),
                    round((float)$mutakhir_sarpras/sizeof($data['rows']),2),
                    round((float)$mutakhir_rombel/sizeof($data['rows']),2)
                ],
                "label" => "Nilai",
                "backgroundColor" => 'rgba(179,181,198,0.2)',
                "borderColor" => 'rgba(179,181,198,1)',
                // "pointBackgroundColor" => 'rgba(179,181,198,1)',
                "pointBorderColor" => '#fff',
                "pointHoverBackgroundColor" => '#fff',
                "pointHoverBorderColor" => 'rgba(179,181,198,1)',
                "pointBackgroundColor"  => [
                    '#558b2f',
                    '#039be5',
                    '#ff6f00',
                    '#ff6f00',
                ]
            ]
        ];

        return $return;
    }

    public function getRaporDapodikRadar(Request $request){
        
        $data = self::getRaporDapodik($request);

        $akurat = 0;
        $berkelanjutan = 0;
        $mutakhir = 0;

        for ($i=0; $i < sizeof($data['rows']); $i++) { 
            $akurat += (float)$data['rows'][$i]->{'rapor_akhir'};
            $berkelanjutan += (float)$data['rows'][$i]->{'rapor_berkelanjutan'};
            $mutakhir += (float)$data['rows'][$i]->{'rapor_mutakhir'};
        }

        $return = array();
        $return['labels'] = ['akurat', 'berkelanjutan', 'mutakhir'];
        $return['datasets'] = [
            [
                "data" => [
                    round((float)$akurat/sizeof($data['rows']),2), 
                    round((float)$berkelanjutan/sizeof($data['rows']),2),
                    round((float)$mutakhir/sizeof($data['rows']),2)
                ],
                "label" => "Nilai",
                "backgroundColor" => 'rgba(179,181,198,0.2)',
                "borderColor" => 'rgba(179,181,198,1)',
                // "pointBackgroundColor" => 'rgba(179,181,198,1)',
                "pointBorderColor" => '#fff',
                "pointHoverBackgroundColor" => '#fff',
                "pointHoverBorderColor" => 'rgba(179,181,198,1)',
                "pointBackgroundColor"  => [
                    '#558b2f',
                    '#039be5',
                    '#ff6f00',
                ],
                // "hoverBackgroundColor"  => [
                //     '#558b2f',
                //     '#039be5',
                //     '#ff6f00',
                // ]
            ]
        ];

        return $return;
                
    }
}

?>