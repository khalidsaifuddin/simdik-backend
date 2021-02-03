<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Http\Controllers\Preface;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redis;

class RekapPesertaDidikController extends Controller
{
    public function getRekapPesertaDidikRingkasanExcel(Request $request){
        $return = self::getRekapPesertaDidikRingkasan($request);
        return view('excel/UnduhExcelGeneral', ['return' => $return, 'judul' => 'Rekap Ringkasan Peserta Didik', 'sub_judul' => "Tanggal Rekap Terakhir: ".$return['rows'][0]->tanggal_rekap_terakhir]);
    }
    
    public function getRekapPesertaDidikRingkasanSpExcel(Request $request){
        $return = self::getRekapPesertaDidikRingkasanSp($request);
        return view('excel/UnduhExcelGeneral', ['return' => $return, 'judul' => 'Rekap Ringkasan Peserta Didik Per Sekolah', 'sub_judul' => "Tanggal Rekap Terakhir: ".$return['rows'][0]->tanggal_rekap_terakhir]);
    }
    
    public function getRekapPesertaDidikNISNExcel(Request $request){
        $return = self::getRekapPesertaDidikNISN($request);
        return view('excel/UnduhExcelGeneral', ['return' => $return, 'judul' => 'Rekap NISN Peserta Didik', 'sub_judul' => "Tanggal Rekap Terakhir: ".$return['rows'][0]->tanggal_rekap_terakhir]);
    }
    
    public function getRekapPesertaDidikNISNSpExcel(Request $request){
        $return = self::getRekapPesertaDidikNISNSp($request);
        return view('excel/UnduhExcelGeneral', ['return' => $return, 'judul' => 'Rekap NISN Peserta Didik Per Sekolah', 'sub_judul' => "Tanggal Rekap Terakhir: ".$return['rows'][0]->tanggal_rekap_terakhir]);
    }

    public function getRekapPesertaDidikAgamaExcel(Request $request){
        $return = self::getRekapPesertaDidikAgama($request);
        return view('excel/UnduhExcelGeneral', ['return' => $return, 'judul' => 'Rekap Agama Peserta Didik', 'sub_judul' => "Tanggal Rekap Terakhir: ".$return['rows'][0]->tanggal_rekap_terakhir]);
    }
    
    public function getRekapPesertaDidikAgamaSpExcel(Request $request){
        $return = self::getRekapPesertaDidikAgamaSp($request);
        return view('excel/UnduhExcelGeneral', ['return' => $return, 'judul' => 'Rekap Agama Peserta Didik Per Sekolah', 'sub_judul' => "Tanggal Rekap Terakhir: ".$return['rows'][0]->tanggal_rekap_terakhir]);
    }

    public function getRekapPesertaDidikNISNSp(Request $request){
        switch ($request->input('id_level_wilayah')) {
			case "0":
				$param_kode_wilayah = "";
				break;
			case "1":
				$param_kode_wilayah = "AND kode_wilayah_propinsi = '".$request->input('kode_wilayah')."'";
				break;
			case "2":
				$param_kode_wilayah = "AND kode_wilayah_kabupaten = '".$request->input('kode_wilayah')."'";
				# code...
				break;
			case "3":
				$param_kode_wilayah = "AND kode_wilayah_kecamatan = '".$request->input('kode_wilayah')."'";
				# code...
				break;
			default:
				$param_kode_wilayah = "";
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

            $param_bentuk = "AND rekap_sekolah.bentuk_pendidikan_id IN ".$strBentuk;

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
            SUM(1) as total
        from
            rekap_sekolah
        where soft_delete = 0
        and semester_id = ".$request->input('semester_id')."
        {$param_kode_wilayah}
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
            ISNULL( pd_kelas_1_ada_nisn, 0 ) as pd_kelas_1_ada_nisn,
            ISNULL( pd_kelas_1_tidak_ada_nisn, 0 ) as pd_kelas_1_tidak_ada_nisn,
            ISNULL( pd_kelas_2_ada_nisn, 0 ) as pd_kelas_2_ada_nisn,
            ISNULL( pd_kelas_2_tidak_ada_nisn, 0 ) as pd_kelas_2_tidak_ada_nisn,
            ISNULL( pd_kelas_3_ada_nisn, 0 ) as pd_kelas_3_ada_nisn,
            ISNULL( pd_kelas_3_tidak_ada_nisn, 0 ) as pd_kelas_3_tidak_ada_nisn,
            ISNULL( pd_kelas_4_ada_nisn, 0 ) as pd_kelas_4_ada_nisn,
            ISNULL( pd_kelas_4_tidak_ada_nisn, 0 ) as pd_kelas_4_tidak_ada_nisn,
            ISNULL( pd_kelas_5_ada_nisn, 0 ) as pd_kelas_5_ada_nisn,
            ISNULL( pd_kelas_5_tidak_ada_nisn, 0 ) as pd_kelas_5_tidak_ada_nisn,
            ISNULL( pd_kelas_6_ada_nisn, 0 ) as pd_kelas_6_ada_nisn,
            ISNULL( pd_kelas_6_tidak_ada_nisn, 0 ) as pd_kelas_6_tidak_ada_nisn,
            ISNULL( pd_kelas_7_ada_nisn, 0 ) as pd_kelas_7_ada_nisn,
            ISNULL( pd_kelas_7_tidak_ada_nisn, 0 ) as pd_kelas_7_tidak_ada_nisn,
            ISNULL( pd_kelas_8_ada_nisn, 0 ) as pd_kelas_8_ada_nisn,
            ISNULL( pd_kelas_8_tidak_ada_nisn, 0 ) as pd_kelas_8_tidak_ada_nisn,
            ISNULL( pd_kelas_9_ada_nisn, 0 ) as pd_kelas_9_ada_nisn,
            ISNULL( pd_kelas_9_tidak_ada_nisn, 0 ) as pd_kelas_9_tidak_ada_nisn,
            ISNULL( pd_kelas_10_ada_nisn, 0 ) as pd_kelas_10_ada_nisn,
            ISNULL( pd_kelas_10_tidak_ada_nisn, 0 ) as pd_kelas_10_tidak_ada_nisn,
            ISNULL( pd_kelas_11_ada_nisn, 0 ) as pd_kelas_11_ada_nisn,
            ISNULL( pd_kelas_11_tidak_ada_nisn, 0 ) as pd_kelas_11_tidak_ada_nisn,
            ISNULL( pd_kelas_12_ada_nisn, 0 ) as pd_kelas_12_ada_nisn,
            ISNULL( pd_kelas_12_tidak_ada_nisn, 0 ) as pd_kelas_12_tidak_ada_nisn,
            ISNULL( pd_kelas_13_ada_nisn, 0 ) as pd_kelas_13_ada_nisn,
            ISNULL( pd_kelas_13_tidak_ada_nisn, 0 ) as pd_kelas_13_tidak_ada_nisn,
            tanggal as tanggal_rekap_terakhir
        from
            rekap_sekolah
        where soft_delete = 0
        and semester_id = ".$request->input('semester_id')."
        {$param_kode_wilayah}
        {$param_bentuk}
        {$param_status}
        {$param_keyword}
        ";

        $fetch = DB::connection('sqlsrv_2')
        ->select(DB::raw($sql_count));

        $return = array();
        $return['total'] = $fetch[0]->total;
        
        $sql .= " ORDER BY nama OFFSET ".($request->input('start')?$request->input('start'):0)." ROWS FETCH NEXT ".($request->input('limit')?$request->input('limit'):0)." ROWS ONLY";

        // return $sql;die;
        $fetch = DB::connection('sqlsrv_2')
        ->select(DB::raw($sql));

        $return['rows'] = $fetch;

        return $return;
    }

    public function getRekapPesertaDidikRingkasanSp(Request $request){
        switch ($request->input('id_level_wilayah')) {
			case "0":
				//nothing to do
				$wilayah = 'mst_kode_wilayah_propinsi';
				$params_wilayah = 'mst_kode_wilayah_propinsi';
				$params_mst_wilayah = 'mst_kode_wilayah_propinsi';
				$params_id_level = 'id_level_wilayah_propinsi';
				break;
			case "1":
				$wilayah = 'propinsi';
				$params_wilayah = 'kode_wilayah_propinsi';
				$params_mst_wilayah = 'mst_kode_wilayah_propinsi';
				$params_id_level = 'id_level_wilayah_propinsi';
				break;
			case "2":
				$wilayah = 'kabupaten';
				$params_wilayah = 'kode_wilayah_kabupaten';
				$params_mst_wilayah = 'mst_kode_wilayah_kabupaten';
				$params_id_level = 'id_level_wilayah_kabupaten';
				break;
			case "3":
				$wilayah = 'kecamatan';
				$params_wilayah = 'kode_wilayah_kecamatan';
				$params_mst_wilayah = 'mst_kode_wilayah_kecamatan';
				$params_id_level = 'id_level_wilayah_kecamatan';
				break;
			default:
				$wilayah = null;
				$params_wilayah = 'mst_kode_wilayah_propinsi';
				$params_mst_wilayah = 'mst_kode_wilayah_propinsi';
				$params_id_level = 'id_level_wilayah_propinsi';
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

            $param_bentuk = "AND rekap_sekolah.bentuk_pendidikan_id IN ".$strBentuk;

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
            {$wilayah},
            {$params_wilayah} AS kode_wilayah,
            {$params_mst_wilayah} AS mst_kode_wilayah,
            {$params_id_level} AS id_level_wilayah,
            kecamatan,
            kabupaten,
            propinsi,
            ISNULL(pd,0) AS pd,
            ISNULL((pd_kelas_1_laki + pd_kelas_1_perempuan),0)  as pd_kelas_1,
            ISNULL((pd_kelas_2_laki + pd_kelas_2_perempuan),0)  as pd_kelas_2,
            ISNULL((pd_kelas_3_laki + pd_kelas_3_perempuan),0)  as pd_kelas_3,
            ISNULL((pd_kelas_4_laki + pd_kelas_4_perempuan),0)  as pd_kelas_4,
            ISNULL((pd_kelas_5_laki + pd_kelas_5_perempuan),0)  as pd_kelas_5,
            ISNULL((pd_kelas_6_laki + pd_kelas_6_perempuan),0)  as pd_kelas_6,
            ISNULL((pd_kelas_7_laki + pd_kelas_7_perempuan),0)  as pd_kelas_7,
            ISNULL((pd_kelas_8_laki + pd_kelas_8_perempuan),0)  as pd_kelas_8,
            ISNULL((pd_kelas_9_laki + pd_kelas_9_perempuan),0)  as pd_kelas_9,
            ISNULL((pd_kelas_10_laki + pd_kelas_10_perempuan),0)  as pd_kelas_10,
            ISNULL((pd_kelas_11_laki + pd_kelas_11_perempuan),0)  as pd_kelas_11,
            ISNULL((pd_kelas_12_laki + pd_kelas_12_perempuan),0)  as pd_kelas_12,
            ISNULL((pd_kelas_13_laki + pd_kelas_13_perempuan),0)  as pd_kelas_13,
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
        
        $fetch = DB::connection('sqlsrv_2')
        ->select(DB::raw($sql_count));

        $return = array();
        $return['total'] = $fetch[0]->total;
        
        $sql .= " ORDER BY nama OFFSET ".($request->input('start')?$request->input('start'):0)." ROWS FETCH NEXT ".($request->input('limit')?$request->input('limit'):20)." ROWS ONLY";

        // return $sql;die;
        $fetch = DB::connection('sqlsrv_2')
        ->select(DB::raw($sql));

        $return['rows'] = $fetch;

        return $return;
    }

    public function getRekapPesertaDidikNISN(Request $request){
        switch ($request->input('id_level_wilayah')) {
            case "0":
                $params = 'propinsi';
                $mst_kode_wilayah_induk = '';
                $mst_kode_wilayah_induk_group = '';
                $param_kode_wilayah = '';

                $add_induk_propinsi = "null as induk_propinsi,";
                $add_kode_wilayah_induk_propinsi = "null as kode_wilayah_induk_propinsi,";
                $add_induk_kabupaten = "null as induk_kabupaten,";
                $add_kode_wilayah_induk_kabupaten = "null as kode_wilayah_induk_kabupaten,";

                $add_group_induk_propinsi = "propinsi,";
                $add_group_kode_wilayah_induk_propinsi = "kode_wilayah_propinsi";
                $add_group_induk_kabupaten = "";
                $add_group_kode_wilayah_induk_kabupaten = "";
                break;
            case "1":
                $params = 'kabupaten';
                $mst_kode_wilayah_induk = 'mst_kode_wilayah_propinsi as mst_kode_wilayah_induk,';
                $mst_kode_wilayah_induk_group = 'mst_kode_wilayah_propinsi,';
                $param_kode_wilayah = "AND kode_wilayah_propinsi = '".$request->input('kode_wilayah')."'";

                $add_induk_propinsi = "propinsi as induk_propinsi,";
                $add_kode_wilayah_induk_propinsi = "kode_wilayah_propinsi as kode_wilayah_induk_propinsi,";
                $add_induk_kabupaten = "null as induk_kabupaten,";
                $add_kode_wilayah_induk_kabupaten = "null as kode_wilayah_induk_kabupaten,";

                $add_group_induk_propinsi = "propinsi,";
                $add_group_kode_wilayah_induk_propinsi = "kode_wilayah_propinsi";
                $add_group_induk_kabupaten = "";
                $add_group_kode_wilayah_induk_kabupaten = "";
                break;
            case "2":
                $params = 'kecamatan';
                $mst_kode_wilayah_induk = 'mst_kode_wilayah_kabupaten as mst_kode_wilayah_induk,';
                $mst_kode_wilayah_induk_group = 'mst_kode_wilayah_kabupaten,';
                $param_kode_wilayah = "AND kode_wilayah_kabupaten = '".$request->input('kode_wilayah')."'";

                $add_induk_propinsi = "propinsi as induk_propinsi,";
                $add_kode_wilayah_induk_propinsi = "kode_wilayah_propinsi as kode_wilayah_induk_propinsi,";
                $add_induk_kabupaten = "kabupaten as induk_kabupaten,";
                $add_kode_wilayah_induk_kabupaten = "kode_wilayah_kabupaten as kode_wilayah_induk_kabupaten,";

                $add_group_induk_propinsi = "propinsi,";
                $add_group_kode_wilayah_induk_propinsi = "kode_wilayah_propinsi,";
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

            $param_bentuk = "AND rekap_sekolah.bentuk_pendidikan_id IN ".$strBentuk;

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
            {$add_induk_propinsi}
            {$add_kode_wilayah_induk_propinsi}
            {$add_induk_kabupaten}
            {$add_kode_wilayah_induk_kabupaten}
            ISNULL( SUM( pd_kelas_1_ada_nisn ), 0 ) as pd_kelas_1_ada_nisn,
            ISNULL( SUM( pd_kelas_1_tidak_ada_nisn ), 0 ) as pd_kelas_1_tidak_ada_nisn,
            ISNULL( SUM( (pd_kelas_1_ada_nisn + pd_kelas_1_tidak_ada_nisn) ), 0 ) as pd_kelas_1_total,
            ISNULL( SUM( pd_kelas_2_ada_nisn ), 0 ) as pd_kelas_2_ada_nisn,
            ISNULL( SUM( pd_kelas_2_tidak_ada_nisn ), 0 ) as pd_kelas_2_tidak_ada_nisn,
            ISNULL( SUM( (pd_kelas_2_ada_nisn + pd_kelas_2_tidak_ada_nisn) ), 0 ) as pd_kelas_2_total,
            ISNULL( SUM( pd_kelas_3_ada_nisn ), 0 ) as pd_kelas_3_ada_nisn,
            ISNULL( SUM( pd_kelas_3_tidak_ada_nisn ), 0 ) as pd_kelas_3_tidak_ada_nisn,
            ISNULL( SUM( (pd_kelas_3_ada_nisn + pd_kelas_3_tidak_ada_nisn) ), 0 ) as pd_kelas_3_total,
            ISNULL( SUM( pd_kelas_4_ada_nisn ), 0 ) as pd_kelas_4_ada_nisn,
            ISNULL( SUM( pd_kelas_4_tidak_ada_nisn ), 0 ) as pd_kelas_4_tidak_ada_nisn,
            ISNULL( SUM( (pd_kelas_4_ada_nisn + pd_kelas_4_tidak_ada_nisn) ), 0 ) as pd_kelas_4_total,
            ISNULL( SUM( pd_kelas_5_ada_nisn ), 0 ) as pd_kelas_5_ada_nisn,
            ISNULL( SUM( pd_kelas_5_tidak_ada_nisn ), 0 ) as pd_kelas_5_tidak_ada_nisn,
            ISNULL( SUM( (pd_kelas_5_ada_nisn + pd_kelas_5_tidak_ada_nisn) ), 0 ) as pd_kelas_5_total,
            ISNULL( SUM( pd_kelas_6_ada_nisn ), 0 ) as pd_kelas_6_ada_nisn,
            ISNULL( SUM( pd_kelas_6_tidak_ada_nisn ), 0 ) as pd_kelas_6_tidak_ada_nisn,
            ISNULL( SUM( (pd_kelas_6_ada_nisn + pd_kelas_6_tidak_ada_nisn) ), 0 ) as pd_kelas_6_total,
            ISNULL( SUM( pd_kelas_7_ada_nisn ), 0 ) as pd_kelas_7_ada_nisn,
            ISNULL( SUM( pd_kelas_7_tidak_ada_nisn ), 0 ) as pd_kelas_7_tidak_ada_nisn,
            ISNULL( SUM( (pd_kelas_7_ada_nisn + pd_kelas_7_tidak_ada_nisn) ), 0 ) as pd_kelas_7_total,
            ISNULL( SUM( pd_kelas_8_ada_nisn ), 0 ) as pd_kelas_8_ada_nisn,
            ISNULL( SUM( pd_kelas_8_tidak_ada_nisn ), 0 ) as pd_kelas_8_tidak_ada_nisn,
            ISNULL( SUM( (pd_kelas_8_ada_nisn + pd_kelas_8_tidak_ada_nisn) ), 0 ) as pd_kelas_8_total,
            ISNULL( SUM( pd_kelas_9_ada_nisn ), 0 ) as pd_kelas_9_ada_nisn,
            ISNULL( SUM( pd_kelas_9_tidak_ada_nisn ), 0 ) as pd_kelas_9_tidak_ada_nisn,
            ISNULL( SUM( (pd_kelas_9_ada_nisn + pd_kelas_9_tidak_ada_nisn) ), 0 ) as pd_kelas_9_total,
            ISNULL( SUM( pd_kelas_10_ada_nisn ), 0 ) as pd_kelas_10_ada_nisn,
            ISNULL( SUM( pd_kelas_10_tidak_ada_nisn ), 0 ) as pd_kelas_10_tidak_ada_nisn,
            ISNULL( SUM( (pd_kelas_10_ada_nisn + pd_kelas_10_tidak_ada_nisn) ), 0 ) as pd_kelas_10_total,
            ISNULL( SUM( pd_kelas_11_ada_nisn ), 0 ) as pd_kelas_11_ada_nisn,
            ISNULL( SUM( pd_kelas_11_tidak_ada_nisn ), 0 ) as pd_kelas_11_tidak_ada_nisn,
            ISNULL( SUM( (pd_kelas_11_ada_nisn + pd_kelas_11_tidak_ada_nisn) ), 0 ) as pd_kelas_11_total,
            ISNULL( SUM( pd_kelas_12_ada_nisn ), 0 ) as pd_kelas_12_ada_nisn,
            ISNULL( SUM( pd_kelas_12_tidak_ada_nisn ), 0 ) as pd_kelas_12_tidak_ada_nisn,
            ISNULL( SUM( (pd_kelas_12_ada_nisn + pd_kelas_12_tidak_ada_nisn) ), 0 ) as pd_kelas_12_total,
            ISNULL( SUM( pd_kelas_13_ada_nisn ), 0 ) as pd_kelas_13_ada_nisn,
            ISNULL( SUM( pd_kelas_13_tidak_ada_nisn ), 0 ) as pd_kelas_13_tidak_ada_nisn,
            ISNULL( SUM( (pd_kelas_13_ada_nisn + pd_kelas_13_tidak_ada_nisn) ), 0 ) as pd_kelas_13_total,
            SUM(1) as jumlah_sekolah,
            MIN(tanggal) as tanggal_rekap_terakhir
        FROM
            rekap_sekolah WITH (NOLOCK)
        WHERE
            semester_id = '".$request->input('semester_id')."'
        AND tahun_ajaran_id = '".$request->input('tahun_ajaran_id')."'
        AND soft_delete = 0
        {$param_kode_wilayah}
        {$param_bentuk}
        {$param_status}
        {$param_keyword}
        GROUP BY
            {$params},
            kode_wilayah_{$params},
            mst_kode_wilayah_{$params},
            id_level_wilayah_{$params},
            {$add_group_induk_propinsi}
            {$mst_kode_wilayah_induk_group}
            {$add_group_kode_wilayah_induk_propinsi}
            {$add_group_induk_kabupaten}
            {$add_group_kode_wilayah_induk_kabupaten}
        ";

        $fetch = DB::connection('sqlsrv_2')
        ->select(DB::raw($sql));

        $return = array();
        $return['rows'] = $fetch;
        $return['total'] = sizeof($fetch);

        return $return;
    }

    public function getRekapPesertaDidikRingkasan(Request $request){
        switch ($request->input('id_level_wilayah')) {
			case "0":
				$params = 'propinsi';
				$mst_kode_wilayah_induk = '';
				$mst_kode_wilayah_induk_group = '';
				$param_kode_wilayah = '';

				$add_induk_propinsi = "null as induk_propinsi,";
				$add_kode_wilayah_induk_propinsi = "null as kode_wilayah_induk_propinsi,";
				$add_induk_kabupaten = "null as induk_kabupaten,";
				$add_kode_wilayah_induk_kabupaten = "null as kode_wilayah_induk_kabupaten,";

				$add_group_induk_propinsi = "propinsi,";
				$add_group_kode_wilayah_induk_propinsi = "kode_wilayah_propinsi";
				$add_group_induk_kabupaten = "";
				$add_group_kode_wilayah_induk_kabupaten = "";
				break;
			case "1":
				$params = 'kabupaten';
				$mst_kode_wilayah_induk = 'mst_kode_wilayah_propinsi as mst_kode_wilayah_induk,';
				$mst_kode_wilayah_induk_group = 'mst_kode_wilayah_propinsi,';
				$param_kode_wilayah = "AND kode_wilayah_propinsi = '".$request->input('kode_wilayah')."'";

				$add_induk_propinsi = "propinsi as induk_propinsi,";
				$add_kode_wilayah_induk_propinsi = "kode_wilayah_propinsi as kode_wilayah_induk_propinsi,";
				$add_induk_kabupaten = "null as induk_kabupaten,";
				$add_kode_wilayah_induk_kabupaten = "null as kode_wilayah_induk_kabupaten,";

				$add_group_induk_propinsi = "propinsi,";
				$add_group_kode_wilayah_induk_propinsi = "kode_wilayah_propinsi";
				$add_group_induk_kabupaten = "";
				$add_group_kode_wilayah_induk_kabupaten = "";
				break;
			case "2":
				$params = 'kecamatan';
				$mst_kode_wilayah_induk = 'mst_kode_wilayah_kabupaten as mst_kode_wilayah_induk,';
				$mst_kode_wilayah_induk_group = 'mst_kode_wilayah_kabupaten,';
				$param_kode_wilayah = "AND kode_wilayah_kabupaten = '".$request->input('kode_wilayah')."'";

				$add_induk_propinsi = "propinsi as induk_propinsi,";
				$add_kode_wilayah_induk_propinsi = "kode_wilayah_propinsi as kode_wilayah_induk_propinsi,";
				$add_induk_kabupaten = "kabupaten as induk_kabupaten,";
				$add_kode_wilayah_induk_kabupaten = "kode_wilayah_kabupaten as kode_wilayah_induk_kabupaten,";

				$add_group_induk_propinsi = "propinsi,";
				$add_group_kode_wilayah_induk_propinsi = "kode_wilayah_propinsi,";
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
            {$add_induk_propinsi}
            {$add_kode_wilayah_induk_propinsi}
            {$add_induk_kabupaten}
            {$add_kode_wilayah_induk_kabupaten}
            SUM( CASE WHEN status_sekolah = 1 then pd else 0 end ) as pd_negeri,
            SUM( CASE WHEN status_sekolah = 2 then pd else 0 end ) as pd_swasta,
            SUM( CASE WHEN status_sekolah IN  (1,2) then pd else 0 end ) as pd_total,
            SUM( CASE WHEN status_sekolah = 1 then pd_laki else 0 end ) as pd_negeri_laki,
            SUM( CASE WHEN status_sekolah = 1 then pd_perempuan else 0 end ) as pd_negeri_perempuan,
            SUM( CASE WHEN status_sekolah = 2 then pd_laki else 0 end ) as pd_swasta_laki,
            SUM( CASE WHEN status_sekolah = 2 then pd_perempuan else 0 end ) as pd_swasta_perempuan,
            SUM( CASE WHEN status_sekolah IN  (1,2) then pd_laki else 0 end ) as pd_total_laki,
            SUM( CASE WHEN status_sekolah IN  (1,2) then pd_perempuan else 0 end ) as pd_total_perempuan,
            SUM( pd_kelas_1_laki + pd_kelas_1_perempuan ) as pd_kelas_1,
            SUM( pd_kelas_1_laki ) as pd_kelas_1_laki,
            SUM( pd_kelas_1_perempuan ) as pd_kelas_1_perempuan,
            SUM( pd_kelas_2_laki + pd_kelas_2_perempuan ) as pd_kelas_2,
            SUM( pd_kelas_2_laki ) as pd_kelas_2_laki,
            SUM( pd_kelas_2_perempuan ) as pd_kelas_2_perempuan,
            SUM( pd_kelas_3_laki + pd_kelas_3_perempuan ) as pd_kelas_3,
            SUM( pd_kelas_3_laki ) as pd_kelas_3_laki,
            SUM( pd_kelas_3_perempuan ) as pd_kelas_3_perempuan,
            SUM( pd_kelas_4_laki + pd_kelas_4_perempuan ) as pd_kelas_4,
            SUM( pd_kelas_4_laki ) as pd_kelas_4_laki,
            SUM( pd_kelas_4_perempuan ) as pd_kelas_4_perempuan,
            SUM( pd_kelas_5_laki + pd_kelas_5_perempuan ) as pd_kelas_5,
            SUM( pd_kelas_5_laki ) as pd_kelas_5_laki,
            SUM( pd_kelas_5_perempuan ) as pd_kelas_5_perempuan,
            SUM( pd_kelas_6_laki + pd_kelas_6_perempuan ) as pd_kelas_6,
            SUM( pd_kelas_6_laki ) as pd_kelas_6_laki,
            SUM( pd_kelas_6_perempuan ) as pd_kelas_6_perempuan,
            SUM( pd_kelas_7_laki + pd_kelas_7_perempuan ) as pd_kelas_7,
            SUM( pd_kelas_7_laki ) as pd_kelas_7_laki,
            SUM( pd_kelas_7_perempuan ) as pd_kelas_7_perempuan,
            SUM( pd_kelas_8_laki + pd_kelas_8_perempuan ) as pd_kelas_8,
            SUM( pd_kelas_8_laki ) as pd_kelas_8_laki,
            SUM( pd_kelas_8_perempuan ) as pd_kelas_8_perempuan,
            SUM( pd_kelas_9_laki + pd_kelas_9_perempuan ) as pd_kelas_9,
            SUM( pd_kelas_9_laki ) as pd_kelas_9_laki,
            SUM( pd_kelas_9_perempuan ) as pd_kelas_9_perempuan,
            SUM( pd_kelas_10_laki + pd_kelas_10_perempuan ) as pd_kelas_10,
            SUM( pd_kelas_10_laki ) as pd_kelas_10_laki,
            SUM( pd_kelas_10_perempuan ) as pd_kelas_10_perempuan,
            SUM( pd_kelas_11_laki + pd_kelas_11_perempuan ) as pd_kelas_11,
            SUM( pd_kelas_11_laki ) as pd_kelas_11_laki,
            SUM( pd_kelas_11_perempuan ) as pd_kelas_11_perempuan,
            SUM( pd_kelas_12_laki + pd_kelas_12_perempuan ) as pd_kelas_12,
            SUM( pd_kelas_12_laki ) as pd_kelas_12_laki,
            SUM( pd_kelas_12_perempuan ) as pd_kelas_12_perempuan,
            SUM( pd_kelas_13_laki + pd_kelas_13_perempuan ) as pd_kelas_13,
            SUM( pd_kelas_13_laki ) as pd_kelas_13_laki,
            SUM( pd_kelas_13_perempuan ) as pd_kelas_13_perempuan,
            SUM (
                ( pd_kelas_1_laki + pd_kelas_1_perempuan ) +
                ( pd_kelas_2_laki + pd_kelas_2_perempuan ) +
                ( pd_kelas_3_laki + pd_kelas_3_perempuan ) +
                ( pd_kelas_4_laki + pd_kelas_4_perempuan ) +
                ( pd_kelas_5_laki + pd_kelas_5_perempuan ) +
                ( pd_kelas_6_laki + pd_kelas_6_perempuan ) +
                ( pd_kelas_7_laki + pd_kelas_7_perempuan ) +
                ( pd_kelas_8_laki + pd_kelas_8_perempuan ) +
                ( pd_kelas_9_laki + pd_kelas_9_perempuan )
            ) as total_dikdas,
            SUM (
                ( pd_kelas_10_laki + pd_kelas_10_perempuan ) +
                ( pd_kelas_11_laki + pd_kelas_11_perempuan ) +
                ( pd_kelas_12_laki + pd_kelas_12_perempuan ) +
                ( pd_kelas_13_laki + pd_kelas_13_perempuan )
            ) as total_dikmen,
            SUM (
                ( pd_kelas_1_laki + pd_kelas_1_perempuan ) +
                ( pd_kelas_2_laki + pd_kelas_2_perempuan ) +
                ( pd_kelas_3_laki + pd_kelas_3_perempuan ) +
                ( pd_kelas_4_laki + pd_kelas_4_perempuan ) +
                ( pd_kelas_5_laki + pd_kelas_5_perempuan ) +
                ( pd_kelas_6_laki + pd_kelas_6_perempuan ) +
                ( pd_kelas_7_laki + pd_kelas_7_perempuan ) +
                ( pd_kelas_8_laki + pd_kelas_8_perempuan ) +
                ( pd_kelas_9_laki + pd_kelas_9_perempuan ) +
                ( pd_kelas_10_laki + pd_kelas_10_perempuan ) +
                ( pd_kelas_11_laki + pd_kelas_11_perempuan ) +
                ( pd_kelas_12_laki + pd_kelas_12_perempuan ) +
                ( pd_kelas_13_laki + pd_kelas_13_perempuan )
            ) as total_dikdasmen,
            SUM(1) as jumlah_sekolah,
            min(tanggal) as tanggal_rekap_terakhir
        FROM
            rekap_sekolah WITH (NOLOCK)
        WHERE
            semester_id = '".$request->input('semester_id')."'
        AND tahun_ajaran_id = '".$request->input('tahun_ajaran_id')."'
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
            {$add_group_induk_propinsi}
            {$mst_kode_wilayah_induk_group}
            {$add_group_kode_wilayah_induk_propinsi}
            {$add_group_induk_kabupaten}
            {$add_group_kode_wilayah_induk_kabupaten}";

        $fetch = DB::connection('sqlsrv_2')
        ->select(DB::raw($sql));

        $return = array();
        $return['rows'] = $fetch;
        $return['total'] = sizeof($fetch);

        return $return;
    }

    public function getRekapPesertaDidikAgama(Request $request){

        switch ($request->input('id_level_wilayah')) {
			case "0":
				$params = 'propinsi';
				$mst_kode_wilayah_induk = '';
				$mst_kode_wilayah_induk_group = '';
				$param_kode_wilayah = '';

				$add_induk_propinsi = "null as induk_propinsi,";
				$add_kode_wilayah_induk_propinsi = "null as kode_wilayah_induk_propinsi,";
				$add_induk_kabupaten = "null as induk_kabupaten,";
				$add_kode_wilayah_induk_kabupaten = "null as kode_wilayah_induk_kabupaten,";

				$add_group_induk_propinsi = "propinsi,";
				$add_group_kode_wilayah_induk_propinsi = "kode_wilayah_propinsi";
				$add_group_induk_kabupaten = "";
				$add_group_kode_wilayah_induk_kabupaten = "";
				break;
			case "1":
				$params = 'kabupaten';
				$mst_kode_wilayah_induk = 'mst_kode_wilayah_propinsi as mst_kode_wilayah_induk,';
				$mst_kode_wilayah_induk_group = 'mst_kode_wilayah_propinsi,';
				$param_kode_wilayah = "AND kode_wilayah_propinsi = '".$request->input('kode_wilayah')."'";

				$add_induk_propinsi = "propinsi as induk_propinsi,";
				$add_kode_wilayah_induk_propinsi = "kode_wilayah_propinsi as kode_wilayah_induk_propinsi,";
				$add_induk_kabupaten = "null as induk_kabupaten,";
				$add_kode_wilayah_induk_kabupaten = "null as kode_wilayah_induk_kabupaten,";

				$add_group_induk_propinsi = "propinsi,";
				$add_group_kode_wilayah_induk_propinsi = "kode_wilayah_propinsi";
				$add_group_induk_kabupaten = "";
				$add_group_kode_wilayah_induk_kabupaten = "";
				break;
			case "2":
				$params = 'kecamatan';
				$mst_kode_wilayah_induk = 'mst_kode_wilayah_kabupaten as mst_kode_wilayah_induk,';
				$mst_kode_wilayah_induk_group = 'mst_kode_wilayah_kabupaten,';
				$param_kode_wilayah = "AND kode_wilayah_kabupaten = '".$request->input('kode_wilayah')."'";

				$add_induk_propinsi = "propinsi as induk_propinsi,";
				$add_kode_wilayah_induk_propinsi = "kode_wilayah_propinsi as kode_wilayah_induk_propinsi,";
				$add_induk_kabupaten = "kabupaten as induk_kabupaten,";
				$add_kode_wilayah_induk_kabupaten = "kode_wilayah_kabupaten as kode_wilayah_induk_kabupaten,";

				$add_group_induk_propinsi = "propinsi,";
				$add_group_kode_wilayah_induk_propinsi = "kode_wilayah_propinsi,";
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
            {$add_induk_propinsi}
            {$add_kode_wilayah_induk_propinsi}
            {$add_induk_kabupaten}
            {$add_kode_wilayah_induk_kabupaten}
            ISNULL( SUM( pd_laki_islam ), 0 ) as pd_laki_islam,
            ISNULL( SUM( pd_perempuan_islam ), 0 ) as pd_perempuan_islam,
            ISNULL( SUM( (pd_laki_islam+pd_perempuan_islam) ), 0 ) as pd_islam,
            ISNULL( SUM( pd_laki_kristen ), 0 ) as pd_laki_kristen,
            ISNULL( SUM( pd_perempuan_kristen ), 0 ) as pd_perempuan_kristen,
            ISNULL( SUM( (pd_laki_kristen+pd_perempuan_kristen) ), 0 ) as pd_kristen,
            ISNULL( SUM( pd_laki_katholik ), 0 ) as pd_laki_katholik,
            ISNULL( SUM( pd_perempuan_katholik ), 0 ) as pd_perempuan_katholik,
            ISNULL( SUM( (pd_laki_katholik+pd_perempuan_katholik) ), 0 ) as pd_katholik,
            ISNULL( SUM( pd_laki_hindu ), 0 ) as pd_laki_hindu,
            ISNULL( SUM( pd_perempuan_hindu ), 0 ) as pd_perempuan_hindu,
            ISNULL( SUM( (pd_laki_hindu+pd_perempuan_hindu) ), 0 ) as pd_hindu,
            ISNULL( SUM( pd_laki_budha ), 0 ) as pd_laki_budha,
            ISNULL( SUM( pd_perempuan_budha ), 0 ) as pd_perempuan_budha,
            ISNULL( SUM( (pd_laki_budha+pd_perempuan_budha) ), 0 ) as pd_budha,
            ISNULL( SUM( pd_laki_konghucu ), 0 ) as pd_laki_konghucu,
            ISNULL( SUM( pd_perempuan_konghucu ), 0 ) as pd_perempuan_konghucu,
            ISNULL( SUM( (pd_laki_konghucu+pd_perempuan_konghucu) ), 0 ) as pd_konghucu,
            ISNULL( SUM( pd_laki_kepercayaan ), 0 ) as pd_laki_kepercayaan,
            ISNULL( SUM( pd_perempuan_kepercayaan ), 0 ) as pd_perempuan_kepercayaan,
            ISNULL( SUM( (pd_laki_kepercayaan+pd_perempuan_kepercayaan) ), 0 ) as pd_kepercayaan,
            ISNULL( SUM( pd_laki_agama_tidak_diisi ), 0 ) as pd_laki_agama_tidak_diisi,
            ISNULL( SUM( pd_perempuan_agama_tidak_diisi ), 0 ) as pd_perempuan_agama_tidak_diisi,
            ISNULL( SUM( (pd_laki_agama_tidak_diisi+pd_perempuan_agama_tidak_diisi) ), 0 ) as pd_agama_tidak_diisi,
            ISNULL( SUM( pd_laki ), 0 ) as pd_laki,
            ISNULL( SUM( pd_perempuan ), 0 ) as pd_perempuan,
            ISNULL( SUM( (pd_laki+pd_perempuan) ), 0 ) as pd,
            SUM(1) as jumlah_sekolah,
            min(tanggal) as tanggal_rekap_terakhir
        FROM
            rekap_sekolah WITH (NOLOCK)
        WHERE
            semester_id = '".$request->input('semester_id')."'
        AND tahun_ajaran_id = '".$request->input('tahun_ajaran_id')."'
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
            {$add_group_induk_propinsi}
            {$mst_kode_wilayah_induk_group}
            {$add_group_kode_wilayah_induk_propinsi}
            {$add_group_induk_kabupaten}
            {$add_group_kode_wilayah_induk_kabupaten}";

        $fetch = DB::connection('sqlsrv_2')
        ->select(DB::raw($sql));

        $return = array();
        $return['rows'] = $fetch;
        $return['total'] = sizeof($fetch);

        return $return;
    }

    public function getRekapPesertaDidikAgamaSp(Request $request){
        switch ($request->input('id_level_wilayah')) {
			case "0":
				//nothing to do
				$wilayah = 'mst_kode_wilayah_propinsi';
				$params_wilayah = 'mst_kode_wilayah_propinsi';
				$params_mst_wilayah = 'mst_kode_wilayah_propinsi';
				$params_id_level = 'id_level_wilayah_propinsi';
				break;
			case "1":
				$wilayah = 'propinsi';
				$params_wilayah = 'kode_wilayah_propinsi';
				$params_mst_wilayah = 'mst_kode_wilayah_propinsi';
				$params_id_level = 'id_level_wilayah_propinsi';
				break;
			case "2":
				$wilayah = 'kabupaten';
				$params_wilayah = 'kode_wilayah_kabupaten';
				$params_mst_wilayah = 'mst_kode_wilayah_kabupaten';
				$params_id_level = 'id_level_wilayah_kabupaten';
				break;
			case "3":
				$wilayah = 'kecamatan';
				$params_wilayah = 'kode_wilayah_kecamatan';
				$params_mst_wilayah = 'mst_kode_wilayah_kecamatan';
				$params_id_level = 'id_level_wilayah_kecamatan';
				break;
			default:
				$wilayah = null;
				$params_wilayah = 'mst_kode_wilayah_propinsi';
				$params_mst_wilayah = 'mst_kode_wilayah_propinsi';
				$params_id_level = 'id_level_wilayah_propinsi';
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

            $param_bentuk = "AND rekap_sekolah.bentuk_pendidikan_id IN ".$strBentuk;

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
            {$wilayah},
            {$params_wilayah} AS kode_wilayah,
            {$params_mst_wilayah} AS mst_kode_wilayah,
            {$params_id_level} AS id_level_wilayah,
            kecamatan,
            kabupaten,
            propinsi,
            ISNULL( pd_laki_islam , 0 ) as pd_laki_islam,
            ISNULL( pd_perempuan_islam , 0 ) as pd_perempuan_islam,
            ISNULL( (pd_laki_islam+pd_perempuan_islam) , 0 ) as pd_islam,
            ISNULL( pd_laki_kristen , 0 ) as pd_laki_kristen,
            ISNULL( pd_perempuan_kristen , 0 ) as pd_perempuan_kristen,
            ISNULL( (pd_laki_kristen+pd_perempuan_kristen) , 0 ) as pd_kristen,
            ISNULL( pd_laki_katholik , 0 ) as pd_laki_katholik,
            ISNULL( pd_perempuan_katholik , 0 ) as pd_perempuan_katholik,
            ISNULL( (pd_laki_katholik+pd_perempuan_katholik) , 0 ) as pd_katholik,
            ISNULL( pd_laki_hindu , 0 ) as pd_laki_hindu,
            ISNULL( pd_perempuan_hindu , 0 ) as pd_perempuan_hindu,
            ISNULL( (pd_laki_hindu+pd_perempuan_hindu) , 0 ) as pd_hindu,
            ISNULL( pd_laki_budha , 0 ) as pd_laki_budha,
            ISNULL( pd_perempuan_budha , 0 ) as pd_perempuan_budha,
            ISNULL( (pd_laki_budha+pd_perempuan_budha) , 0 ) as pd_budha,
            ISNULL( pd_laki_konghucu , 0 ) as pd_laki_konghucu,
            ISNULL( pd_perempuan_konghucu , 0 ) as pd_perempuan_konghucu,
            ISNULL( (pd_laki_konghucu+pd_perempuan_konghucu) , 0 ) as pd_konghucu,
            ISNULL( pd_laki_kepercayaan , 0 ) as pd_laki_kepercayaan,
            ISNULL( pd_perempuan_kepercayaan , 0 ) as pd_perempuan_kepercayaan,
            ISNULL( (pd_laki_kepercayaan+pd_perempuan_kepercayaan) , 0 ) as pd_kepercayaan,
            ISNULL( pd_laki_agama_tidak_diisi , 0 ) as pd_laki_agama_tidak_diisi,
            ISNULL( pd_perempuan_agama_tidak_diisi , 0 ) as pd_perempuan_agama_tidak_diisi,
            ISNULL( (pd_laki_agama_tidak_diisi+pd_perempuan_agama_tidak_diisi) , 0 ) as pd_agama_tidak_diisi,
            ISNULL( pd_laki , 0 ) as pd_laki,
            ISNULL( pd_perempuan , 0 ) as pd_perempuan,
            ISNULL( (pd_laki+pd_perempuan) , 0 ) as pd,
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
        
        $fetch = DB::connection('sqlsrv_2')
        ->select(DB::raw($sql_count));

        $return = array();
        $return['total'] = $fetch[0]->total;
        
        $sql .= " ORDER BY nama OFFSET ".($request->input('start')?$request->input('start'):0)." ROWS FETCH NEXT ".($request->input('limit')?$request->input('limit'):20)." ROWS ONLY";

        // return $sql;die;
        $fetch = DB::connection('sqlsrv_2')
        ->select(DB::raw($sql));

        $return['rows'] = $fetch;

        return $return;
    }
}