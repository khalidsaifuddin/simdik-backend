<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Http\Controllers\Preface;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redis;

class RekapSekolahController extends Controller
{
    public function getRekapSekolahRingkasanExcel(Request $request){
        $return = self::getRekapSekolahRingkasan($request);
        return view('excel/UnduhExcelGeneral', ['return' => $return, 'judul' => 'Rekap Ringkasan Sekolah', 'sub_judul' => "Tanggal Rekap Terakhir: ".$return['rows'][0]->tanggal_rekap_terakhir]);
    }
    
    public function getRekapSekolahRingkasanSpExcel(Request $request){
        $return = self::getRekapSekolahRingkasanSp($request);
        return view('excel/UnduhExcelGeneral', ['return' => $return, 'judul' => 'Rekap Ringkasan Per Sekolah', 'sub_judul' => "Tanggal Rekap Terakhir: ".$return['rows'][0]->tanggal_rekap_terakhir]);
    }
    
    public function getRekapSekolahWaktuPenyelenggaraanExcel(Request $request){
        $return = self::getRekapSekolahWaktuPenyelenggaraan($request);
        return view('excel/UnduhExcelGeneral', ['return' => $return, 'judul' => 'Rekap Waktu Penyelenggaraan Sekolah', 'sub_judul' => "Tanggal Rekap Terakhir: ".$return['rows'][0]->tanggal_rekap_terakhir]);
    }
    
    public function getRekapSekolahWaktuPenyelenggaraanSpExcel(Request $request){
        $return = self::getRekapSekolahWaktuPenyelenggaraanSp($request);
        return view('excel/UnduhExcelGeneral', ['return' => $return, 'judul' => 'Rekap Waktu Penyelenggaraan Per Sekolah', 'sub_judul' => "Tanggal Rekap Terakhir: ".$return['rows'][0]->tanggal_rekap_terakhir]);
    }

    public function getRekapSekolahTotal(Request $request){
        switch ($request->input('id_level_wilayah')) {
            case "0":
                $params = 'propinsi';
                $mst_kode_wilayah_induk = '';
                $mst_kode_wilayah_induk_group = '';
                $param_kode_wilayah = '';

                $add_induk_provinsi = "null as induk_propinsi,";
                $add_kode_wilayah_induk_provinsi = "null as kode_wilayah_induk_propinsi,";
                $add_induk_kabupaten = "null as induk_kabupaten,";
                $add_kode_wilayah_induk_kabupaten = "null as kode_wilayah_induk_kabupaten,";

                $add_group_induk_provinsi = "propinsi,";
                $add_group_kode_wilayah_induk_provinsi = "kode_wilayah_propinsi";
                $add_group_induk_kabupaten = "";
                $add_group_kode_wilayah_induk_kabupaten = "";
                break;
            case "1":
                $params = 'kabupaten';
                $mst_kode_wilayah_induk = 'mst_kode_wilayah_propinsi as mst_kode_wilayah_induk,';
                $mst_kode_wilayah_induk_group = 'mst_kode_wilayah_propinsi,';
                $param_kode_wilayah = "AND kode_wilayah_propinsi = '".$request->input('kode_wilayah')."'";

                $add_induk_provinsi = "propinsi as induk_propinsi,";
                $add_kode_wilayah_induk_provinsi = "kode_wilayah_propinsi as kode_wilayah_induk_propinsi,";
                $add_induk_kabupaten = "null as induk_kabupaten,";
                $add_kode_wilayah_induk_kabupaten = "null as kode_wilayah_induk_kabupaten,";

                $add_group_induk_provinsi = "propinsi,";
                $add_group_kode_wilayah_induk_provinsi = "kode_wilayah_propinsi";
                $add_group_induk_kabupaten = "";
                $add_group_kode_wilayah_induk_kabupaten = "";
                break;
            case "2":
                $params = 'kecamatan';
                $mst_kode_wilayah_induk = 'mst_kode_wilayah_kabupaten as mst_kode_wilayah_induk,';
                $mst_kode_wilayah_induk_group = 'mst_kode_wilayah_kabupaten,';
                $param_kode_wilayah = "AND kode_wilayah_kabupaten = '".$request->input('kode_wilayah')."'";

                $add_induk_provinsi = "propinsi as induk_propinsi,";
                $add_kode_wilayah_induk_provinsi = "kode_wilayah_propinsi as kode_wilayah_induk_propinsi,";
                $add_induk_kabupaten = "kabupaten as induk_kabupaten,";
                $add_kode_wilayah_induk_kabupaten = "kode_wilayah_kabupaten as kode_wilayah_induk_kabupaten,";

                $add_group_induk_provinsi = "propinsi,";
                $add_group_kode_wilayah_induk_provinsi = "kode_wilayah_propinsi,";
                $add_group_induk_kabupaten = "kabupaten,";
                $add_group_kode_wilayah_induk_kabupaten = "kode_wilayah_kabupaten";
                break;
            default:
                break;
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
            $param_bentuk = "AND rekap_sekolah.bentuk_pendidikan_id IN ".$strBentuk;

            // return $param_bentuk;die;
        }else{
            $param_bentuk = "";
        }

        if($request->input('status_sekolah') && (int)$request->input('status_sekolah') != 99){
            $param_status = "AND rekap_sekolah.status_sekolah = ".$request->input('status_sekolah');
        }else{
            $param_status = "AND rekap_sekolah.status_sekolah IN (1,2)";
        }

        if($request->input('keyword')){
            $param_keyword = "AND ".$params." LIKE '%".$request->input('keyword')."%'";
        }else{
            $param_keyword = "";
        }

        $sql = "SELECT
            ROW_NUMBER() OVER (ORDER BY bentuk_pendidikan.bentuk_pendidikan_id) as 'no',
            bentuk_pendidikan.bentuk_pendidikan_id,
            bentuk_pendidikan.nama as nama,
            sum( case when status_sekolah = 1 then 1 else 0 end ) as negeri,
            sum( case when status_sekolah = 2 then 1 else 0 end ) as swasta,
            sum( case when status_sekolah IN (1,2) then 1 else 0 end ) as total,
            sum( case when status_sekolah = 1 then pd else 0 end ) as pd_negeri,
            sum( case when status_sekolah = 2 then pd else 0 end ) as pd_swasta,
            sum( case when status_sekolah IN (1,2) then pd else 0 end ) as pd_total,
            sum( case when status_sekolah = 1 then guru else 0 end ) as guru_negeri,
            sum( case when status_sekolah = 2 then guru else 0 end ) as guru_swasta,
            sum( case when status_sekolah IN (1,2) then guru else 0 end ) as guru_total,
            sum( case when status_sekolah = 1 then pegawai else 0 end ) as pegawai_negeri,
            sum( case when status_sekolah = 2 then pegawai else 0 end ) as pegawai_swasta,
            sum( case when status_sekolah IN (1,2) then pegawai else 0 end ) as pegawai_total,
            sum( case when status_sekolah = 1 then rombel else 0 end ) as rombel_negeri,
            sum( case when status_sekolah = 2 then rombel else 0 end ) as rombel_swasta,
            sum( case when status_sekolah IN (1,2) then rombel else 0 end ) as rombel_total,
            max(tanggal) as tanggal_rekap_terakhir
        FROM
            rekap_sekolah
        JOIN ref.bentuk_pendidikan bentuk_pendidikan on bentuk_pendidikan.bentuk_pendidikan_id = rekap_sekolah.bentuk_pendidikan_id
        WHERE
            semester_id = '".$request->input('semester_id')."'
        AND tahun_ajaran_id = '".($request->input('tahun_ajaran_id') ? $request->input('tahun_ajaran_id') : substr($request->input('semester_id'),0,4))."'
        {$param_kode_wilayah}
        {$param_bentuk}
        {$param_status}
        {$param_keyword}
        AND soft_delete = 0
        GROUP BY
            bentuk_pendidikan.bentuk_pendidikan_id,
            bentuk_pendidikan.nama
        ORDER BY
            bentuk_pendidikan_id";

        // return $sql;die;

        $fetch = DB::connection('sqlsrv_2')
        ->select(DB::raw($sql));

        $return = array();
        $return['rows'] = $fetch;
        $return['total'] = sizeof($fetch);

        return $return;
    }

    public function getRekapSekolahWaktuPenyelenggaraanSp(Request $request){
        switch ($request->input('id_level_wilayah')) {
            case 0:
                $col_wilayah = "w3.nama";
                $col_kode = "w3.kode_wilayah";
                $col_id_level = "w3.id_level_wilayah";
                $col_mst_kode = "w3.mst_kode_wilayah";
                $col_mst_kode_induk = '';
                $col_mst_kode_induk_group = '';
                $params_wilayah = " and w3.mst_kode_wilayah = '".$request->input('kode_wilayah')."'";
                break;
            case 1:
                $col_wilayah = "w2.nama";
                $col_kode = "w2.kode_wilayah";
                $col_id_level = "w2.id_level_wilayah";
                $col_mst_kode = "w2.mst_kode_wilayah";
                $col_mst_kode_induk = 'w3.mst_kode_wilayah as mst_kode_wilayah_induk,';
                $col_mst_kode_induk_group = 'w3.mst_kode_wilayah,';
                $params_wilayah = " and w2.mst_kode_wilayah = '".$request->input('kode_wilayah')."'";
                break;
            case 2:
                $col_wilayah = "w1.nama";
                $col_kode = "w1.kode_wilayah";
                $col_id_level = "w1.id_level_wilayah";
                $col_mst_kode = "w1.mst_kode_wilayah";
                $col_mst_kode_induk = 'w2.mst_kode_wilayah as mst_kode_wilayah_induk,';
                $col_mst_kode_induk_group = 'w2.mst_kode_wilayah,';
                $params_wilayah = " and w1.mst_kode_wilayah = '".$request->input('kode_wilayah')."'";
                break;
            case 3:
                $col_wilayah = "w1.nama";
                $col_kode = "w1.kode_wilayah";
                $col_id_level = "w1.id_level_wilayah";
                $col_mst_kode = "w1.mst_kode_wilayah";
                $col_mst_kode_induk = 'w2.mst_kode_wilayah as mst_kode_wilayah_induk,';
                $col_mst_kode_induk_group = 'w2.mst_kode_wilayah,';
                $params_wilayah = " and w1.kode_wilayah = '".$request->input('kode_wilayah')."'";
                break;
            default:
                $col_wilayah = "w3.nama";
                $col_kode = "w3.kode_wilayah";
                $col_id_level = "w3.id_level_wilayah";
                $col_mst_kode = "w3.mst_kode_wilayah";
                $col_mst_kode_induk = '';
                $col_mst_kode_induk_group = '';
                $params_wilayah = " and w3.mst_kode_wilayah = '".$request->input('kode_wilayah')."'";
                break;
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
            $param_bentuk = "AND sekolah.bentuk_pendidikan_id IN ".$strBentuk;

            // return $param_bentuk;die;
        }else{
            $param_bentuk = "";
        }

        if($request->input('status_sekolah') && (int)$request->input('status_sekolah') != 99){
            $param_status = "AND sekolah.status_sekolah = ".$request->input('status_sekolah');
        }else{
            $param_status = "AND sekolah.status_sekolah IN (1,2)";
        }

        if($request->input('keyword')){
            $param_keyword = "AND (sekolah.nama LIKE '%".$request->input('keyword')."%' OR sekolah.npsn LIKE '%".$request->input('keyword')."%')";
        }else{
            $param_keyword = "";
        }

        $sql_count = "SELECT
            SUM(1) as total
        FROM
            sekolah
        INNER JOIN ref.mst_wilayah w1 WITH (nolock) ON LEFT (sekolah.kode_wilayah, 6) = w1.kode_wilayah
        INNER JOIN ref.mst_wilayah w2 WITH (nolock) ON w1.mst_kode_wilayah = w2.kode_wilayah
        INNER JOIN ref.mst_wilayah w3 WITH (nolock) ON w2.mst_kode_wilayah = w3.kode_wilayah
        INNER JOIN ref.bentuk_pendidikan bp WITH (nolock) ON bp.bentuk_pendidikan_id = sekolah.bentuk_pendidikan_id
        WHERE
            sekolah.soft_delete = 0
        {$param_bentuk}
        {$param_status}
        {$param_keyword}
        {$params_wilayah}";

        $sql = "SELECT
            ROW_NUMBER() OVER (ORDER BY sekolah.nama) as 'no',
            sekolah.sekolah_id,
            sekolah.nama,
            sekolah.npsn,
            sekolah.status_sekolah,
            CASE sekolah.status_sekolah
                WHEN 1 THEN 'Negeri'
                WHEN 2 THEN 'Swasta'
            ELSE '-' END AS status,
            bp.nama as bentuk_pendidikan_id,
            bp.nama as bentuk,
            w1.nama as kecamatan,
            w2.nama as kabupaten,
            w3.nama as propinsi,
            getdate() as tanggal_rekap_terakhir,
            isnull((
                SELECT
                    wp.nama
                FROM
                    sekolah_longitudinal
                LEFT OUTER JOIN ref.waktu_penyelenggaraan wp on wp.waktu_penyelenggaraan_id = sekolah_longitudinal.waktu_penyelenggaraan_id
                WHERE
                    soft_delete = 0
                AND semester_id = ".$request->input('semester_id')."
                AND sekolah_id = sekolah.sekolah_id
            ),'Tidak diisi') as waktu_penyelenggaraan_id_str
        FROM
            sekolah
        INNER JOIN ref.mst_wilayah w1 WITH (nolock) ON LEFT (sekolah.kode_wilayah, 6) = w1.kode_wilayah
        INNER JOIN ref.mst_wilayah w2 WITH (nolock) ON w1.mst_kode_wilayah = w2.kode_wilayah
        INNER JOIN ref.mst_wilayah w3 WITH (nolock) ON w2.mst_kode_wilayah = w3.kode_wilayah
        INNER JOIN ref.bentuk_pendidikan bp WITH (nolock) ON bp.bentuk_pendidikan_id = sekolah.bentuk_pendidikan_id
        WHERE
            sekolah.soft_delete = 0
        {$param_bentuk}
        {$param_status}
        {$param_keyword}
        {$params_wilayah}";

        $fetch = DB::connection('sqlsrv')
        ->select(DB::raw($sql_count));

        $return = array();
        $return['total'] = $fetch[0]->total;
        
        $sql .= " ORDER BY sekolah.nama OFFSET ".($request->input('start')?$request->input('start'):0)." ROWS FETCH NEXT ".($request->input('limit')?$request->input('limit'):0)." ROWS ONLY";

        $fetch = DB::connection('sqlsrv')
        ->select(DB::raw($sql));

        $return['rows'] = $fetch;

        return $return;
    }

    public function getRekapSekolahWaktuPenyelenggaraan(Request $request){
        switch ($request->input('id_level_wilayah')) {
            case 0:
                $col_wilayah = "w3.nama";
                $col_kode = "w3.kode_wilayah";
                $col_id_level = "w3.id_level_wilayah";
                $col_mst_kode = "w3.mst_kode_wilayah";
                $col_mst_kode_induk = '';
                $col_mst_kode_induk_group = '';
                $params_wilayah = " and w3.mst_kode_wilayah = '".$request->input('kode_wilayah')."'";
                break;
            case 1:
                $col_wilayah = "w2.nama";
                $col_kode = "w2.kode_wilayah";
                $col_id_level = "w2.id_level_wilayah";
                $col_mst_kode = "w2.mst_kode_wilayah";
                $col_mst_kode_induk = 'w3.mst_kode_wilayah as mst_kode_wilayah_induk,';
                $col_mst_kode_induk_group = 'w3.mst_kode_wilayah,';
                $params_wilayah = " and w2.mst_kode_wilayah = '".$request->input('kode_wilayah')."'";
                break;
            case 2:
                $col_wilayah = "w1.nama";
                $col_kode = "w1.kode_wilayah";
                $col_id_level = "w1.id_level_wilayah";
                $col_mst_kode = "w1.mst_kode_wilayah";
                $col_mst_kode_induk = 'w2.mst_kode_wilayah as mst_kode_wilayah_induk,';
                $col_mst_kode_induk_group = 'w2.mst_kode_wilayah,';
                $params_wilayah = " and w1.mst_kode_wilayah = '".$request->input('kode_wilayah')."'";
                break;
            default:
                $col_wilayah = "w3.nama";
                $col_kode = "w3.kode_wilayah";
                $col_id_level = "w3.id_level_wilayah";
                $col_mst_kode = "w3.mst_kode_wilayah";
                $col_mst_kode_induk = '';
                $col_mst_kode_induk_group = '';
                $params_wilayah = " and w3.mst_kode_wilayah = '".$request->input('kode_wilayah')."'";
                break;
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

            $param_bentuk = "AND sekolah.bentuk_pendidikan_id IN ".$strBentuk;

        }else{
            $param_bentuk = "";
        }

        if($request->input('status_sekolah') && (int)$request->input('status_sekolah') != 99){
            $param_status = "AND sekolah.status_sekolah = ".$request->input('status_sekolah');
        }else{
            $param_status = "AND sekolah.status_sekolah IN (1,2)";
        }

        if($request->input('keyword')){
            $param_keyword = "AND ".$col_wilayah." LIKE '%".$request->input('keyword')."%'";
        }else{
            $param_keyword = "";
        }

        $sql = "SELECT
            ROW_NUMBER() OVER (ORDER BY {$col_wilayah}) as 'no',
            {$col_wilayah} as nama,
            {$col_kode} as kode_wilayah,
            {$col_id_level} as id_level_wilayah,
            {$col_mst_kode} as mst_kode_wilayah,
            {$col_mst_kode_induk}
            sum(case when slong.waktu_penyelenggaraan_id = 1 and status_sekolah = 1 then 1 else 0 end) as negeri_pagi,
            sum(case when slong.waktu_penyelenggaraan_id = 1 and status_sekolah = 2 then 1 else 0 end) as swasta_pagi,
            sum(case when slong.waktu_penyelenggaraan_id = 1 and status_sekolah IN (1,2) then 1 else 0 end) as pagi,
            sum(case when slong.waktu_penyelenggaraan_id = 2 and status_sekolah = 1 then 1 else 0 end) as negeri_siang,
            sum(case when slong.waktu_penyelenggaraan_id = 2 and status_sekolah = 2 then 1 else 0 end) as swasta_siang,
            sum(case when slong.waktu_penyelenggaraan_id = 2 and status_sekolah IN (1,2) then 1 else 0 end) as siang,
            sum(case when slong.waktu_penyelenggaraan_id = 4 and status_sekolah = 1 then 1 else 0 end) as negeri_sore,
            sum(case when slong.waktu_penyelenggaraan_id = 4 and status_sekolah = 2 then 1 else 0 end) as swasta_sore,
            sum(case when slong.waktu_penyelenggaraan_id = 4 and status_sekolah IN (1,2) then 1 else 0 end) as sore,
            sum(case when slong.waktu_penyelenggaraan_id = 5 and status_sekolah = 1 then 1 else 0 end) as negeri_malam,
            sum(case when slong.waktu_penyelenggaraan_id = 5 and status_sekolah = 2 then 1 else 0 end) as swasta_malam,
            sum(case when slong.waktu_penyelenggaraan_id = 5 and status_sekolah IN (1,2) then 1 else 0 end) as malam,
            sum(case when slong.waktu_penyelenggaraan_id = 3 and status_sekolah = 1 then 1 else 0 end) as negeri_kombinasi,
            sum(case when slong.waktu_penyelenggaraan_id = 3 and status_sekolah = 2 then 1 else 0 end) as swasta_kombinasi,
            sum(case when slong.waktu_penyelenggaraan_id = 3 and status_sekolah IN (1,2) then 1 else 0 end) as kombinasi,
            sum(case when slong.waktu_penyelenggaraan_id = 6 and status_sekolah = 1 then 1 else 0 end) as negeri_sehari_penuh_5,
            sum(case when slong.waktu_penyelenggaraan_id = 6 and status_sekolah = 2 then 1 else 0 end) as swasta_sehari_penuh_5,
            sum(case when slong.waktu_penyelenggaraan_id = 6 and status_sekolah IN (1,2) then 1 else 0 end) as sehari_penuh_5,
            sum(case when slong.waktu_penyelenggaraan_id = 7 and status_sekolah = 1 then 1 else 0 end) as negeri_sehari_penuh_6,
            sum(case when slong.waktu_penyelenggaraan_id = 7 and status_sekolah = 2 then 1 else 0 end) as swasta_sehari_penuh_6,
            sum(case when slong.waktu_penyelenggaraan_id = 7 and status_sekolah IN (1,2) then 1 else 0 end) as sehari_penuh_6,
            sum(case when slong.waktu_penyelenggaraan_id = 9 and status_sekolah = 1 then 1 else 0 end) as negeri_lainnya,
            sum(case when slong.waktu_penyelenggaraan_id = 9 and status_sekolah = 2 then 1 else 0 end) as swasta_lainnya,
            sum(case when slong.waktu_penyelenggaraan_id = 9 and status_sekolah IN (1,2) then 1 else 0 end) as lainnya,
            sum(case when slong.waktu_penyelenggaraan_id is null and status_sekolah = 1 then 1 else 0 end) as negeri_belum_isi,
            sum(case when slong.waktu_penyelenggaraan_id is null and status_sekolah = 2 then 1 else 0 end) as swasta_belum_isi,
            sum(case when slong.waktu_penyelenggaraan_id is null and status_sekolah IN (1,2) then 1 else 0 end) as belum_isi,
            sum(1) as jumlah_sekolah,
            getdate() as tanggal_rekap_terakhir
        FROM
            sekolah WITH (nolock)
        LEFT OUTER JOIN sekolah_longitudinal slong WITH (nolock) on sekolah.sekolah_id = slong.sekolah_id
        INNER JOIN ref.mst_wilayah w1 WITH (nolock) ON LEFT (sekolah.kode_wilayah, 6) = w1.kode_wilayah
        INNER JOIN ref.mst_wilayah w2 WITH (nolock) ON w1.mst_kode_wilayah = w2.kode_wilayah
        INNER JOIN ref.mst_wilayah w3 WITH (nolock) ON w2.mst_kode_wilayah = w3.kode_wilayah
        WHERE
            slong.soft_delete = 0
        AND sekolah.soft_delete = 0
        AND slong.semester_id = {$request->input('semester_id')}
        {$params_wilayah}
        {$param_bentuk}
        {$param_status}
        {$param_keyword}
        GROUP BY
            {$col_wilayah},
            {$col_kode},
            {$col_id_level},
            {$col_mst_kode_induk_group}
            {$col_mst_kode}
        ORDER BY
            {$col_wilayah} ASC";

        $fetch = DB::connection('sqlsrv')
        ->select(DB::raw($sql));

        $return = array();
        $return['rows'] = $fetch;
        $return['total'] = sizeof($fetch);

        return $return;
    }

    public function getRekapSekolahRingkasan(Request $request){
        switch ($request->input('id_level_wilayah')) {
            case "0":
                $params = 'propinsi';
                $mst_kode_wilayah_induk = '';
                $mst_kode_wilayah_induk_group = '';
                $param_kode_wilayah = '';

                $add_induk_provinsi = "null as induk_propinsi,";
                $add_kode_wilayah_induk_provinsi = "null as kode_wilayah_induk_propinsi,";
                $add_induk_kabupaten = "null as induk_kabupaten,";
                $add_kode_wilayah_induk_kabupaten = "null as kode_wilayah_induk_kabupaten,";

                $add_group_induk_provinsi = "propinsi,";
                $add_group_kode_wilayah_induk_provinsi = "kode_wilayah_propinsi";
                $add_group_induk_kabupaten = "";
                $add_group_kode_wilayah_induk_kabupaten = "";
                break;
            case "1":
                $params = 'kabupaten';
                $mst_kode_wilayah_induk = 'mst_kode_wilayah_propinsi as mst_kode_wilayah_induk,';
                $mst_kode_wilayah_induk_group = 'mst_kode_wilayah_propinsi,';
                $param_kode_wilayah = "AND kode_wilayah_propinsi = '".$request->input('kode_wilayah')."'";

                $add_induk_provinsi = "propinsi as induk_propinsi,";
                $add_kode_wilayah_induk_provinsi = "kode_wilayah_propinsi as kode_wilayah_induk_propinsi,";
                $add_induk_kabupaten = "null as induk_kabupaten,";
                $add_kode_wilayah_induk_kabupaten = "null as kode_wilayah_induk_kabupaten,";

                $add_group_induk_provinsi = "propinsi,";
                $add_group_kode_wilayah_induk_provinsi = "kode_wilayah_propinsi";
                $add_group_induk_kabupaten = "";
                $add_group_kode_wilayah_induk_kabupaten = "";
                break;
            case "2":
                $params = 'kecamatan';
                $mst_kode_wilayah_induk = 'mst_kode_wilayah_kabupaten as mst_kode_wilayah_induk,';
                $mst_kode_wilayah_induk_group = 'mst_kode_wilayah_kabupaten,';
                $param_kode_wilayah = "AND kode_wilayah_kabupaten = '".$request->input('kode_wilayah')."'";

                $add_induk_provinsi = "propinsi as induk_propinsi,";
                $add_kode_wilayah_induk_provinsi = "kode_wilayah_propinsi as kode_wilayah_induk_propinsi,";
                $add_induk_kabupaten = "kabupaten as induk_kabupaten,";
                $add_kode_wilayah_induk_kabupaten = "kode_wilayah_kabupaten as kode_wilayah_induk_kabupaten,";

                $add_group_induk_provinsi = "propinsi,";
                $add_group_kode_wilayah_induk_provinsi = "kode_wilayah_propinsi,";
                $add_group_induk_kabupaten = "kabupaten,";
                $add_group_kode_wilayah_induk_kabupaten = "kode_wilayah_kabupaten";
                break;
            default:
                break;
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
            $param_bentuk = "AND rekap_sekolah.bentuk_pendidikan_id IN ".$strBentuk;

            // return $param_bentuk;die;
        }else{
            $param_bentuk = "";
        }

        if($request->input('status_sekolah') && (int)$request->input('status_sekolah') != 99){
            $param_status = "AND rekap_sekolah.status_sekolah = ".$request->input('status_sekolah');
        }else{
            $param_status = "AND rekap_sekolah.status_sekolah IN (1,2)";
        }

        if($request->input('keyword')){
            $param_keyword = "AND ".$params." LIKE '%".$request->input('keyword')."%'";
        }else{
            $param_keyword = "";
        }

        $sql = "SELECT
            ROW_NUMBER() OVER (ORDER BY {$params}) as 'no',
            {$params} as nama,
            kode_wilayah_{$params} as kode_wilayah,
            mst_kode_wilayah_{$params} as mst_kode_wilayah,
            {$mst_kode_wilayah_induk}
            id_level_wilayah_{$params} as id_level_wilayah,
            {$add_induk_provinsi}
            {$add_kode_wilayah_induk_provinsi}
            {$add_induk_kabupaten}
            {$add_kode_wilayah_induk_kabupaten}
            sum( case when status_sekolah = 1 then 1 else 0 end ) as negeri,
            sum( case when status_sekolah = 2 then 1 else 0 end ) as swasta,
            sum( case when status_sekolah IN (1,2) then 1 else 0 end ) as total,
            sum( case when status_sekolah = 1 then pd else 0 end ) as pd_negeri,
            sum( case when status_sekolah = 2 then pd else 0 end ) as pd_swasta,
            sum( case when status_sekolah IN (1,2) then pd else 0 end ) as pd_total,
            sum( case when status_sekolah = 1 then guru else 0 end ) as guru_negeri,
            sum( case when status_sekolah = 2 then guru else 0 end ) as guru_swasta,
            sum( case when status_sekolah IN (1,2) then guru else 0 end ) as guru_total,
            sum( case when status_sekolah = 1 then pegawai else 0 end ) as pegawai_negeri,
            sum( case when status_sekolah = 2 then pegawai else 0 end ) as pegawai_swasta,
            sum( case when status_sekolah IN (1,2) then pegawai else 0 end ) as pegawai_total,
            sum( case when status_sekolah = 1 then rombel else 0 end ) as rombel_negeri,
            sum( case when status_sekolah = 2 then rombel else 0 end ) as rombel_swasta,
            sum( case when status_sekolah IN (1,2) then rombel else 0 end ) as rombel_total,
            max(tanggal) as tanggal_rekap_terakhir
        FROM
            rekap_sekolah
        WHERE
            semester_id = '".$request->input('semester_id')."'
        AND tahun_ajaran_id = '".($request->input('tahun_ajaran_id') ? $request->input('tahun_ajaran_id') : substr($request->input('semester_id'),0,4))."'
        {$param_kode_wilayah}
        {$param_bentuk}
        {$param_status}
        {$param_keyword}
        AND soft_delete = 0
        GROUP BY
            {$params},
            kode_wilayah_{$params},
            mst_kode_wilayah_{$params},
            id_level_wilayah_{$params},
            {$add_group_induk_provinsi}
            {$mst_kode_wilayah_induk_group}
            {$add_group_kode_wilayah_induk_provinsi}
            {$add_group_induk_kabupaten}
            {$add_group_kode_wilayah_induk_kabupaten}
        ORDER BY
            {$params}";

        // return $sql;die;

        $fetch = DB::connection('sqlsrv_2')
        ->select(DB::raw($sql));

        $return = array();
        $return['rows'] = $fetch;
        $return['total'] = sizeof($fetch);

        return $return;
    }

    public function getRekapSekolahRingkasanSp(Request $request){
        switch ($request->input('id_level_wilayah')) {
            case "0":
                $params_wilayah = 'mst_kode_wilayah_propinsi';
                break;
            case "1":
                $params_wilayah = 'kode_wilayah_propinsi';
                break;
            case "2":
                $params_wilayah = 'kode_wilayah_kabupaten';
                break;
            case "3":
                $params_wilayah = 'kode_wilayah_kecamatan';
                break;
            default:
                break;
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
            $param_bentuk = "AND rekap_sekolah.bentuk_pendidikan_id IN ".$strBentuk;

            // return $param_bentuk;die;
        }else{
            $param_bentuk = "";
        }

        if($request->input('status_sekolah') && (int)$request->input('status_sekolah') != 99){
            $param_status = "AND rekap_sekolah.status_sekolah = ".$request->input('status_sekolah');
        }else{
            $param_status = "AND rekap_sekolah.status_sekolah IN (1,2)";
        }

        if($request->input('keyword')){
            $param_keyword = "AND (nama LIKE '%".$request->input('keyword')."%' OR npsn LIKE '%".$request->input('keyword')."%')";
        }else{
            $param_keyword = "";
        }

        $sql_count = "SELECT
            sum(1) as total
        FROM
            rekap_sekolah WITH(NOLOCK)
        WHERE
            semester_id = '".$request->input('semester_id')."'
            AND tahun_ajaran_id = '".$request->input('tahun_ajaran_id')."'
            AND {$params_wilayah} = '".$request->input('kode_wilayah')."'
            AND soft_delete = 0
            {$param_bentuk}
            {$param_status}
            {$param_keyword}";

        $sql = "SELECT
            ROW_NUMBER() OVER (ORDER BY nama) as 'no',
            sekolah_id,
            nama,
            npsn,
            status_sekolah,
            CASE status_sekolah
                WHEN 1 THEN 'Negeri'
                WHEN 2 THEN 'Swasta'
            ELSE '-' END AS status,
            bentuk_pendidikan_id,
            CASE 
                WHEN bentuk_pendidikan_id = 1 THEN 'TK'
                WHEN bentuk_pendidikan_id = 2 THEN 'KB'
                WHEN bentuk_pendidikan_id = 3 THEN 'TPA'
                WHEN bentuk_pendidikan_id = 4 THEN 'SPS'
                WHEN bentuk_pendidikan_id = 5 THEN 'SD'
                WHEN bentuk_pendidikan_id = 6 THEN 'SMP'
                WHEN bentuk_pendidikan_id IN (7, 8, 14, 29) THEN 'SLB'
                WHEN bentuk_pendidikan_id = 13 THEN 'SMA'
                WHEN bentuk_pendidikan_id = 15 THEN 'SMK'
            ELSE '-' END AS bentuk,
            kecamatan,
            kabupaten,
            propinsi,
            kode_wilayah_kecamatan,
            mst_kode_wilayah_kecamatan,
            id_level_wilayah_kecamatan,
            kode_wilayah_kabupaten,
            mst_kode_wilayah_kabupaten,
            id_level_wilayah_kabupaten,
            kode_wilayah_propinsi,
            mst_kode_wilayah_propinsi,
            id_level_wilayah_propinsi,
            ISNULL((case when status_sekolah = 1 or status_sekolah = 2 then pd else 0 end ), 0) as pd,
            ISNULL((case when status_sekolah = 1 or status_sekolah = 2 then guru else 0 end ), 0) as guru,
            ISNULL((case when status_sekolah = 1 or status_sekolah = 2 then pegawai else 0 end ), 0) as pegawai,
            ISNULL((case when status_sekolah = 1 or status_sekolah = 2 then rombel else 0 end ), 0) as rombel,
            tanggal as tanggal_rekap_terakhir
        FROM
            rekap_sekolah WITH(NOLOCK)
        WHERE
            semester_id = '".$request->input('semester_id')."'
            AND tahun_ajaran_id = '".$request->input('tahun_ajaran_id')."'
            AND {$params_wilayah} = '".$request->input('kode_wilayah')."'
            AND soft_delete = 0
            {$param_bentuk}
            {$param_status}
            {$param_keyword}";

        // return $sql;die;

        $fetch = DB::connection('sqlsrv_2')
        ->select(DB::raw($sql_count));

        $return = array();
        $return['total'] = $fetch[0]->total;
        
        $sql .= " ORDER BY nama OFFSET ".($request->input('start')?$request->input('start'):0)." ROWS FETCH NEXT ".($request->input('limit')?$request->input('limit'):20)." ROWS ONLY";

        $fetch = DB::connection('sqlsrv_2')
        ->select(DB::raw($sql));

        $return['rows'] = $fetch;

        return $return;
    }

    public function getRekapSekolahKurikulum(Request $request){
        switch ($request->input('id_level_wilayah')) {
            case "0":
                $params = 'propinsi';
                $mst_kode_wilayah_induk = '';
                $mst_kode_wilayah_induk_group = '';
                $param_kode_wilayah = '';

                $add_induk_provinsi = "null as induk_propinsi,";
                $add_kode_wilayah_induk_provinsi = "null as kode_wilayah_induk_propinsi,";
                $add_induk_kabupaten = "null as induk_kabupaten,";
                $add_kode_wilayah_induk_kabupaten = "null as kode_wilayah_induk_kabupaten,";

                $add_group_induk_provinsi = "propinsi,";
                $add_group_kode_wilayah_induk_provinsi = "kode_wilayah_propinsi";
                $add_group_induk_kabupaten = "";
                $add_group_kode_wilayah_induk_kabupaten = "";
                break;
            case "1":
                $params = 'kabupaten';
                $mst_kode_wilayah_induk = 'mst_kode_wilayah_propinsi as mst_kode_wilayah_induk,';
                $mst_kode_wilayah_induk_group = 'mst_kode_wilayah_propinsi,';
                $param_kode_wilayah = "AND kode_wilayah_propinsi = '".$request->input('kode_wilayah')."'";

                $add_induk_provinsi = "propinsi as induk_propinsi,";
                $add_kode_wilayah_induk_provinsi = "kode_wilayah_propinsi as kode_wilayah_induk_propinsi,";
                $add_induk_kabupaten = "null as induk_kabupaten,";
                $add_kode_wilayah_induk_kabupaten = "null as kode_wilayah_induk_kabupaten,";

                $add_group_induk_provinsi = "propinsi,";
                $add_group_kode_wilayah_induk_provinsi = "kode_wilayah_propinsi";
                $add_group_induk_kabupaten = "";
                $add_group_kode_wilayah_induk_kabupaten = "";
                break;
            case "2":
                $params = 'kecamatan';
                $mst_kode_wilayah_induk = 'mst_kode_wilayah_kabupaten as mst_kode_wilayah_induk,';
                $mst_kode_wilayah_induk_group = 'mst_kode_wilayah_kabupaten,';
                $param_kode_wilayah = "AND kode_wilayah_kabupaten = '".$request->input('kode_wilayah')."'";

                $add_induk_provinsi = "propinsi as induk_propinsi,";
                $add_kode_wilayah_induk_provinsi = "kode_wilayah_propinsi as kode_wilayah_induk_propinsi,";
                $add_induk_kabupaten = "kabupaten as induk_kabupaten,";
                $add_kode_wilayah_induk_kabupaten = "kode_wilayah_kabupaten as kode_wilayah_induk_kabupaten,";

                $add_group_induk_provinsi = "propinsi,";
                $add_group_kode_wilayah_induk_provinsi = "kode_wilayah_propinsi,";
                $add_group_induk_kabupaten = "kabupaten,";
                $add_group_kode_wilayah_induk_kabupaten = "kode_wilayah_kabupaten";
                break;
            default:
                break;
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
            $param_bentuk = "AND rekap_sekolah.bentuk_pendidikan_id IN ".$strBentuk;

            // return $param_bentuk;die;
        }else{
            $param_bentuk = "";
        }

        if($request->input('status_sekolah') && (int)$request->input('status_sekolah') != 99){
            $param_status = "AND rekap_sekolah.status_sekolah = ".$request->input('status_sekolah');
        }else{
            $param_status = "AND rekap_sekolah.status_sekolah IN (1,2)";
        }

        if($request->input('keyword')){
            $param_keyword = "AND ".$params." LIKE '%".$request->input('keyword')."%'";
        }else{
            $param_keyword = "";
        }

        $sql = "SELECT
            ROW_NUMBER() OVER (ORDER BY {$params}) as 'no',
            {$params} as nama,
            kode_wilayah_{$params} as kode_wilayah,
            mst_kode_wilayah_{$params} as mst_kode_wilayah,
            {$mst_kode_wilayah_induk}
            id_level_wilayah_{$params} as id_level_wilayah,
            {$add_induk_provinsi}
            {$add_kode_wilayah_induk_provinsi}
            {$add_induk_kabupaten}
            {$add_kode_wilayah_induk_kabupaten}
            sum( case when status_sekolah = 1 and kurikulum = 'K13' then 1 else 0 end ) as k13_negeri,
            sum( case when status_sekolah = 2 and kurikulum = 'K13' then 1 else 0 end ) as k13_swasta,
            sum( case when status_sekolah in (1,2) and kurikulum = 'K13' then 1 else 0 end ) as k13_total,
            sum( case when status_sekolah = 1 and kurikulum = 'KTSP' then 1 else 0 end ) as ktsp_negeri,
            sum( case when status_sekolah = 2 and kurikulum = 'KTSP' then 1 else 0 end ) as ktsp_swasta,
            sum( case when status_sekolah in (1,2) and kurikulum = 'KTSP' then 1 else 0 end ) as ktsp_total,
            max(tanggal) as tanggal_rekap_terakhir
        FROM
            rekap_sekolah
        WHERE
            semester_id = '".$request->input('semester_id')."'
        AND tahun_ajaran_id = '".$request->input('tahun_ajaran_id')."'
        {$param_kode_wilayah}
        {$param_bentuk}
        AND soft_delete = 0
        GROUP BY
            {$params},
            kode_wilayah_{$params},
            mst_kode_wilayah_{$params},
            id_level_wilayah_{$params},
            {$add_group_induk_provinsi}
            {$mst_kode_wilayah_induk_group}
            {$add_group_kode_wilayah_induk_provinsi}
            {$add_group_induk_kabupaten}
            {$add_group_kode_wilayah_induk_kabupaten}
        ORDER BY
            {$params}";

        // return $sql;die;

        $fetch = DB::connection('sqlsrv_2')
        ->select(DB::raw($sql));

        $return = array();
        $return['rows'] = $fetch;
        $return['total'] = sizeof($fetch);

        return $return;
    }

    public function getRekapSekolahKurikulumSp(Request $request){
        switch ($request->input('id_level_wilayah')) {
            case "0":
                $params_wilayah = 'mst_kode_wilayah_propinsi';
                break;
            case "1":
                $params_wilayah = 'kode_wilayah_propinsi';
                break;
            case "2":
                $params_wilayah = 'kode_wilayah_kabupaten';
                break;
            case "3":
                $params_wilayah = 'kode_wilayah_kecamatan';
                break;
            default:
                break;
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
            $param_bentuk = "AND rekap_sekolah.bentuk_pendidikan_id IN ".$strBentuk;

            // return $param_bentuk;die;
        }else{
            $param_bentuk = "";
        }

        if($request->input('status_sekolah') && (int)$request->input('status_sekolah') != 99){
            $param_status = "AND rekap_sekolah.status_sekolah = ".$request->input('status_sekolah');
        }else{
            $param_status = "AND rekap_sekolah.status_sekolah IN (1,2)";
        }

        if($request->input('keyword')){
            $param_keyword = "AND (nama LIKE '%".$request->input('keyword')."%' OR npsn LIKE '%".$request->input('keyword')."%')";
        }else{
            $param_keyword = "";
        }

        $sql_count = "SELECT
            sum(1) as total
        FROM
            rekap_sekolah WITH(NOLOCK)
        WHERE
            semester_id = '".$request->input('semester_id')."'
            AND tahun_ajaran_id = '".$request->input('tahun_ajaran_id')."'
            AND {$params_wilayah} = '".$request->input('kode_wilayah')."'
            AND soft_delete = 0
            {$param_bentuk}
            {$param_status}
            {$param_keyword}";

        $sql = "SELECT
            ROW_NUMBER() OVER (ORDER BY nama) as 'no',
            sekolah_id,
            nama,
            npsn,
            status_sekolah,
            CASE status_sekolah
                WHEN 1 THEN 'Negeri'
                WHEN 2 THEN 'Swasta'
            ELSE '-' END AS status,
            bentuk_pendidikan_id,
            CASE 
                WHEN bentuk_pendidikan_id = 1 THEN 'TK'
                WHEN bentuk_pendidikan_id = 2 THEN 'KB'
                WHEN bentuk_pendidikan_id = 3 THEN 'TPA'
                WHEN bentuk_pendidikan_id = 4 THEN 'SPS'
                WHEN bentuk_pendidikan_id = 5 THEN 'SD'
                WHEN bentuk_pendidikan_id = 6 THEN 'SMP'
                WHEN bentuk_pendidikan_id IN (7, 8, 14, 29) THEN 'SLB'
                WHEN bentuk_pendidikan_id = 13 THEN 'SMA'
                WHEN bentuk_pendidikan_id = 15 THEN 'SMK'
            ELSE '-' END AS bentuk,
            kecamatan,
            kabupaten,
            propinsi,
            kode_wilayah_kecamatan,
            mst_kode_wilayah_kecamatan,
            id_level_wilayah_kecamatan,
            kode_wilayah_kabupaten,
            mst_kode_wilayah_kabupaten,
            id_level_wilayah_kabupaten,
            kode_wilayah_propinsi,
            mst_kode_wilayah_propinsi,
            id_level_wilayah_propinsi,
            kurikulum,
            -- ISNULL((case when status_sekolah = 1 and kurikulum = 'K13' then 1 else 0 end ), 0) as k13_negeri,
            -- ISNULL((case when status_sekolah = 2 and kurikulum = 'K13' then 1 else 0 end ), 0) as k13_swasta,
            -- ISNULL((case when status_sekolah in (1,2) and kurikulum = 'K13' then 1 else 0 end ), 0) as k13_total,
            -- ISNULL((case when status_sekolah = 1 and kurikulum = 'KTSP' then 1 else 0 end ), 0) as ktsp_negeri,
            -- ISNULL((case when status_sekolah = 2 and kurikulum = 'KTSP' then 1 else 0 end ), 0) as ktsp_swasta,
            -- ISNULL((case when status_sekolah in (1,2) and kurikulum = 'KTSP' then 1 else 0 end ), 0) as ktsp_total,
            -- ISNULL((case when status_sekolah = 1 or status_sekolah = 2 then pd else 0 end ), 0) as pd,
            -- ISNULL((case when status_sekolah = 1 or status_sekolah = 2 then guru else 0 end ), 0) as guru,
            -- ISNULL((case when status_sekolah = 1 or status_sekolah = 2 then pegawai else 0 end ), 0) as pegawai,
            -- ISNULL((case when status_sekolah = 1 or status_sekolah = 2 then rombel else 0 end ), 0) as rombel,
            tanggal as tanggal_rekap_terakhir
        FROM
            rekap_sekolah WITH(NOLOCK)
        WHERE
            semester_id = '".$request->input('semester_id')."'
            AND tahun_ajaran_id = '".$request->input('tahun_ajaran_id')."'
            AND {$params_wilayah} = '".$request->input('kode_wilayah')."'
            AND soft_delete = 0
            {$param_bentuk}
            {$param_status}
            {$param_keyword}";

        // return $sql;die;

        $fetch = DB::connection('sqlsrv_2')
        ->select(DB::raw($sql_count));

        $return = array();
        $return['total'] = $fetch[0]->total;
        
        $sql .= " ORDER BY nama OFFSET ".($request->input('start')?$request->input('start'):0)." ROWS FETCH NEXT ".($request->input('limit')?$request->input('limit'):20)." ROWS ONLY";

        $fetch = DB::connection('sqlsrv_2')
        ->select(DB::raw($sql));

        $return['rows'] = $fetch;

        return $return;
    }

    public function getRekapSekolahAkreditasi(Request $request){
        switch ($request->input('id_level_wilayah')) {
            case "0":
                $params = 'propinsi';
                $mst_kode_wilayah_induk = '';
                $mst_kode_wilayah_induk_group = '';
                $param_kode_wilayah = '';

                $add_induk_provinsi = "null as induk_propinsi,";
                $add_kode_wilayah_induk_provinsi = "null as kode_wilayah_induk_propinsi,";
                $add_induk_kabupaten = "null as induk_kabupaten,";
                $add_kode_wilayah_induk_kabupaten = "null as kode_wilayah_induk_kabupaten,";

                $add_group_induk_provinsi = "propinsi,";
                $add_group_kode_wilayah_induk_provinsi = "kode_wilayah_propinsi";
                $add_group_induk_kabupaten = "";
                $add_group_kode_wilayah_induk_kabupaten = "";
                break;
            case "1":
                $params = 'kabupaten';
                $mst_kode_wilayah_induk = 'mst_kode_wilayah_propinsi as mst_kode_wilayah_induk,';
                $mst_kode_wilayah_induk_group = 'mst_kode_wilayah_propinsi,';
                $param_kode_wilayah = "AND kode_wilayah_propinsi = '".$request->input('kode_wilayah')."'";

                $add_induk_provinsi = "propinsi as induk_propinsi,";
                $add_kode_wilayah_induk_provinsi = "kode_wilayah_propinsi as kode_wilayah_induk_propinsi,";
                $add_induk_kabupaten = "null as induk_kabupaten,";
                $add_kode_wilayah_induk_kabupaten = "null as kode_wilayah_induk_kabupaten,";

                $add_group_induk_provinsi = "propinsi,";
                $add_group_kode_wilayah_induk_provinsi = "kode_wilayah_propinsi";
                $add_group_induk_kabupaten = "";
                $add_group_kode_wilayah_induk_kabupaten = "";
                break;
            case "2":
                $params = 'kecamatan';
                $mst_kode_wilayah_induk = 'mst_kode_wilayah_kabupaten as mst_kode_wilayah_induk,';
                $mst_kode_wilayah_induk_group = 'mst_kode_wilayah_kabupaten,';
                $param_kode_wilayah = "AND kode_wilayah_kabupaten = '".$request->input('kode_wilayah')."'";

                $add_induk_provinsi = "propinsi as induk_propinsi,";
                $add_kode_wilayah_induk_provinsi = "kode_wilayah_propinsi as kode_wilayah_induk_propinsi,";
                $add_induk_kabupaten = "kabupaten as induk_kabupaten,";
                $add_kode_wilayah_induk_kabupaten = "kode_wilayah_kabupaten as kode_wilayah_induk_kabupaten,";

                $add_group_induk_provinsi = "propinsi,";
                $add_group_kode_wilayah_induk_provinsi = "kode_wilayah_propinsi,";
                $add_group_induk_kabupaten = "kabupaten,";
                $add_group_kode_wilayah_induk_kabupaten = "kode_wilayah_kabupaten";
                break;
            default:
                break;
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
            $param_bentuk = "AND rekap_sekolah.bentuk_pendidikan_id IN ".$strBentuk;

            // return $param_bentuk;die;
        }else{
            $param_bentuk = "";
        }

        if($request->input('status_sekolah') && (int)$request->input('status_sekolah') != 99){
            $param_status = "AND rekap_sekolah.status_sekolah = ".$request->input('status_sekolah');
        }else{
            $param_status = "AND rekap_sekolah.status_sekolah IN (1,2)";
        }

        if($request->input('keyword')){
            $param_keyword = "AND ".$params." LIKE '%".$request->input('keyword')."%'";
        }else{
            $param_keyword = "";
        }

        $sql = "SELECT
            ROW_NUMBER() OVER (ORDER BY {$params}) as 'no',
            {$params} as nama,
            kode_wilayah_{$params} as kode_wilayah,
            mst_kode_wilayah_{$params} as mst_kode_wilayah,
            {$mst_kode_wilayah_induk}
            id_level_wilayah_{$params} as id_level_wilayah,
            {$add_induk_provinsi}
            {$add_kode_wilayah_induk_provinsi}
            {$add_induk_kabupaten}
            {$add_kode_wilayah_induk_kabupaten}
            ISNULL(SUM(case when status_sekolah = 1 and akreditasi_id = '1' then 1 else 0 end ), 0) as a_negeri,
            ISNULL(SUM(case when status_sekolah = 2 and akreditasi_id = '1' then 1 else 0 end ), 0) as a_swasta,
            ISNULL(SUM(case when status_sekolah = 1 and akreditasi_id = '2' then 1 else 0 end ), 0) as b_negeri,
            ISNULL(SUM(case when status_sekolah = 2 and akreditasi_id = '2' then 1 else 0 end ), 0) as b_swasta,
            ISNULL(SUM(case when status_sekolah = 1 and akreditasi_id = '3' then 1 else 0 end ), 0) as c_negeri,
            ISNULL(SUM(case when status_sekolah = 2 and akreditasi_id = '3' then 1 else 0 end ), 0) as c_swasta,
            ISNULL(SUM(case when status_sekolah = 1 and akreditasi_id = '9' then 1 else 0 end ), 0) as belum_terakreditasi_negeri,
            ISNULL(SUM(case when status_sekolah = 2 and akreditasi_id = '9' then 1 else 0 end ), 0) as belum_terakreditasi_swasta,
            ISNULL(SUM(case when status_sekolah = 1 and akreditasi_id = '8' then 1 else 0 end ), 0) as tidak_terakreditasi_negeri,
            ISNULL(SUM(case when status_sekolah = 2 and akreditasi_id = '8' then 1 else 0 end ), 0) as tidak_terakreditasi_swasta,
            ISNULL(SUM(case when status_sekolah = 1 and akreditasi_id NOT IN ('1','2','3','8','9') then 1 else 0 end ), 0) as lainnya_negeri,
            ISNULL(SUM(case when status_sekolah = 2 and akreditasi_id NOT IN ('1','2','3','8','9') then 1 else 0 end ), 0) as lainnya_swasta,
            max(tanggal) as tanggal_rekap_terakhir
        FROM
            rekap_sekolah
        WHERE
            semester_id = '".$request->input('semester_id')."'
        AND tahun_ajaran_id = '".$request->input('tahun_ajaran_id')."'
        {$param_kode_wilayah}
        {$param_bentuk}
        AND soft_delete = 0
        GROUP BY
            {$params},
            kode_wilayah_{$params},
            mst_kode_wilayah_{$params},
            id_level_wilayah_{$params},
            {$add_group_induk_provinsi}
            {$mst_kode_wilayah_induk_group}
            {$add_group_kode_wilayah_induk_provinsi}
            {$add_group_induk_kabupaten}
            {$add_group_kode_wilayah_induk_kabupaten}
        ORDER BY
            {$params}";

        // return $sql;die;

        $fetch = DB::connection('sqlsrv_2')
        ->select(DB::raw($sql));

        $return = array();
        $return['rows'] = $fetch;
        $return['total'] = sizeof($fetch);

        return $return;
    }

    public function getRekapSekolahAkreditasiSp(Request $request){
        switch ($request->input('id_level_wilayah')) {
            case "0":
                $params_wilayah = 'mst_kode_wilayah_propinsi';
                break;
            case "1":
                $params_wilayah = 'kode_wilayah_propinsi';
                break;
            case "2":
                $params_wilayah = 'kode_wilayah_kabupaten';
                break;
            case "3":
                $params_wilayah = 'kode_wilayah_kecamatan';
                break;
            default:
                break;
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
            $param_bentuk = "AND rekap_sekolah.bentuk_pendidikan_id IN ".$strBentuk;

            // return $param_bentuk;die;
        }else{
            $param_bentuk = "";
        }

        if($request->input('status_sekolah') && (int)$request->input('status_sekolah') != 99){
            $param_status = "AND rekap_sekolah.status_sekolah = ".$request->input('status_sekolah');
        }else{
            $param_status = "AND rekap_sekolah.status_sekolah IN (1,2)";
        }

        if($request->input('keyword')){
            $param_keyword = "AND (nama LIKE '%".$request->input('keyword')."%' OR npsn LIKE '%".$request->input('keyword')."%')";
        }else{
            $param_keyword = "";
        }

        $sql_count = "SELECT
            sum(1) as total
        FROM
            rekap_sekolah WITH(NOLOCK)
        WHERE
            semester_id = '".$request->input('semester_id')."'
            AND tahun_ajaran_id = '".$request->input('tahun_ajaran_id')."'
            AND {$params_wilayah} = '".$request->input('kode_wilayah')."'
            AND soft_delete = 0
            {$param_bentuk}
            {$param_status}
            {$param_keyword}";

        $sql = "SELECT
            ROW_NUMBER() OVER (ORDER BY nama) as 'no',
            sekolah_id,
            nama,
            npsn,
            status_sekolah,
            CASE status_sekolah
                WHEN 1 THEN 'Negeri'
                WHEN 2 THEN 'Swasta'
            ELSE '-' END AS status,
            bentuk_pendidikan_id,
            CASE 
                WHEN bentuk_pendidikan_id = 1 THEN 'TK'
                WHEN bentuk_pendidikan_id = 2 THEN 'KB'
                WHEN bentuk_pendidikan_id = 3 THEN 'TPA'
                WHEN bentuk_pendidikan_id = 4 THEN 'SPS'
                WHEN bentuk_pendidikan_id = 5 THEN 'SD'
                WHEN bentuk_pendidikan_id = 6 THEN 'SMP'
                WHEN bentuk_pendidikan_id IN (7, 8, 14, 29) THEN 'SLB'
                WHEN bentuk_pendidikan_id = 13 THEN 'SMA'
                WHEN bentuk_pendidikan_id = 15 THEN 'SMK'
            ELSE '-' END AS bentuk,
            kecamatan,
            kabupaten,
            propinsi,
            kode_wilayah_kecamatan,
            mst_kode_wilayah_kecamatan,
            id_level_wilayah_kecamatan,
            kode_wilayah_kabupaten,
            mst_kode_wilayah_kabupaten,
            id_level_wilayah_kabupaten,
            kode_wilayah_propinsi,
            mst_kode_wilayah_propinsi,
            id_level_wilayah_propinsi,
            akreditasi_id_str,
            tanggal as tanggal_rekap_terakhir
        FROM
            rekap_sekolah WITH(NOLOCK)
        WHERE
            semester_id = '".$request->input('semester_id')."'
            AND tahun_ajaran_id = '".$request->input('tahun_ajaran_id')."'
            AND {$params_wilayah} = '".$request->input('kode_wilayah')."'
            AND soft_delete = 0
            {$param_bentuk}
            {$param_status}
            {$param_keyword}";

        // return $sql;die;

        $fetch = DB::connection('sqlsrv_2')
        ->select(DB::raw($sql_count));

        $return = array();
        $return['total'] = $fetch[0]->total;
        
        $sql .= " ORDER BY nama OFFSET ".($request->input('start')?$request->input('start'):0)." ROWS FETCH NEXT ".($request->input('limit')?$request->input('limit'):20)." ROWS ONLY";

        $fetch = DB::connection('sqlsrv_2')
        ->select(DB::raw($sql));

        $return['rows'] = $fetch;

        return $return;
    }
}

?>