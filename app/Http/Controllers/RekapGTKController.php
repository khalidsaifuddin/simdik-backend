<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Http\Controllers\Preface;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redis;

class RekapGTKController extends Controller
{
    public function getRekapGTKRingkasanExcel(Request $request){
        $return = self::getRekapGTKRingkasan($request);
        return view('excel/UnduhExcelGeneral', ['return' => $return, 'judul' => 'Rekap Ringkasan GTK', 'sub_judul' => "Tanggal Rekap Terakhir: ".$return['rows'][0]->tanggal_rekap_terakhir]);
    }
    
    public function getRekapGTKRingkasanSpExcel(Request $request){
        $return = self::getRekapGTKRingkasanSp($request);
        return view('excel/UnduhExcelGeneral', ['return' => $return, 'judul' => 'Rekap Ringkasan Per GTK', 'sub_judul' => "Tanggal Rekap Terakhir: ".$return['rows'][0]->tanggal_rekap_terakhir]);
    }

    public function getRekapGTKRingkasan(Request $request){
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
            sum( case when status_sekolah = 1 then guru else 0 end ) as guru_negeri,
            sum( case when status_sekolah = 2 then guru else 0 end ) as guru_swasta,
            sum( case when status_sekolah = 1 then pegawai else 0 end ) as tendik_negeri,
            sum( case when status_sekolah = 2 then pegawai else 0 end ) as tendik_swasta,
            sum( case when status_sekolah = 1 then (guru+pegawai) else 0 end ) as gtk_negeri,
            sum( case when status_sekolah = 2 then (guru+pegawai) else 0 end ) as gtk_swasta,
            min(tanggal) as tanggal_rekap_terakhir
        FROM
            rekap_sekolah
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

    public function getRekapGTKRingkasanSp(Request $request){
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
            ISNULL((case when status_sekolah = 1 or status_sekolah = 2 then guru_laki else 0 end ), 0) as guru_laki,
            ISNULL((case when status_sekolah = 1 or status_sekolah = 2 then guru_perempuan else 0 end ), 0) as guru_perempuan,
            ISNULL((case when status_sekolah = 1 or status_sekolah = 2 then pegawai_laki else 0 end ), 0) as tendik_laki,
            ISNULL((case when status_sekolah = 1 or status_sekolah = 2 then pegawai_perempuan else 0 end ), 0) as tendik_perempuan,
            ISNULL((case when status_sekolah = 1 or status_sekolah = 2 then (guru_laki+pegawai_laki) else 0 end ), 0) as gtk_laki,
            ISNULL((case when status_sekolah = 1 or status_sekolah = 2 then (guru_perempuan+pegawai_perempuan) else 0 end ), 0) as gtk_perempuan,
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

    public function getRekapGTKAgama(Request $request){
        switch ($request->input('id_level_wilayah')) {
			case 0:
				$col_wilayah = "w3.nama";
				$col_kode = "w3.kode_wilayah";
				$col_id_level = "w3.id_level_wilayah";
				$col_mst_kode = "w3.mst_kode_wilayah";
				$col_mst_kode_induk = '';
				$col_mst_kode_induk_group = '';
				$params_wilayah = "";
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

            // return $strBentuk;
            $param_bentuk = "AND s.bentuk_pendidikan_id IN ".$strBentuk;

            // return $param_bentuk;die;
        }else{
            $param_bentuk = "";
        }

        if($request->input('status_sekolah') && (int)$request->input('status_sekolah') != 99){
            $param_status = "AND s.status_sekolah = ".$request->input('status_sekolah');
        }else{
            $param_status = "AND s.status_sekolah IN (1,2)";
        }

        if($request->input('keyword')){
            $param_keyword = "AND ".$params." LIKE '%".$request->input('keyword')."%'";
        }else{
            $param_keyword = "";
        }

        $params_status = '';

		if($request->get('status_sekolah')){
			if($request->get('status_sekolah') != 99){
				$params_status = 'AND s.status_sekolah = '.$request->get('status_sekolah');
			}
		}

		if($request->get('jenis_ptk_id')){
			if($request->get('jenis_ptk_id') == 1){
				$params_jptk = "AND ptk.jenis_ptk_id IN (3, 4, 5, 6, 12, 13, 14)";
			}else if($request->get('jenis_ptk_id') == 2){
				$params_jptk = "AND ptk.jenis_ptk_id IN (11, 99, 30, 40)";
			}else{
				$params_jptk = "AND ptk.jenis_ptk_id IN (3, 4, 5, 6, 12, 13, 14, 11, 99, 30, 40)";
			}
		}else{
			$params_jptk = "AND ptk.jenis_ptk_id IN (3, 4, 5, 6, 12, 13, 14, 11, 99, 30, 40)";
		}

        $sql = "SELECT
            ROW_NUMBER() OVER (ORDER BY {$col_wilayah}) as 'no',
            {$col_wilayah} as nama,
            {$col_kode} as kode_wilayah,
            {$col_id_level} as id_level_wilayah,
            {$col_mst_kode} as mst_kode_wilayah,
            {$col_mst_kode_induk}
            -- count(1) as total,
            SUM(CASE WHEN ptk.agama_id in (1,2,3,4,5,6,99) THEN 1 ELSE 0 END) as gtk_total,
            SUM(CASE WHEN ptk.agama_id = 1 THEN 1 ELSE 0 END) as gtk_islam,
            SUM(CASE WHEN ptk.agama_id = 2 THEN 1 ELSE 0 END) as gtk_kristen,
            SUM(CASE WHEN ptk.agama_id = 3 THEN 1 ELSE 0 END) as gtk_katholik,
            SUM(CASE WHEN ptk.agama_id = 4 THEN 1 ELSE 0 END) as gtk_hindu,
            SUM(CASE WHEN ptk.agama_id = 5 THEN 1 ELSE 0 END) as gtk_budha,
            SUM(CASE WHEN ptk.agama_id = 6 THEN 1 ELSE 0 END) as gtk_konghucu,
            SUM(CASE WHEN ptk.agama_id = 7 THEN 1 ELSE 0 END) as gtk_kepercayaan,
            SUM(CASE WHEN ptk.agama_id = 99 THEN 1 ELSE 0 END) as gtk_lainnya,

            SUM(CASE WHEN ptk.agama_id in (1,2,3,4,5,6,99) AND ptk.jenis_kelamin = 'L' THEN 1 ELSE 0 END) as total_laki,
            SUM(CASE WHEN ptk.agama_id in (1,2,3,4,5,6,99) AND ptk.jenis_kelamin = 'P' THEN 1 ELSE 0 END) as total_perempuan,
            SUM(CASE WHEN ptk.agama_id = 1 AND ptk.jenis_kelamin = 'L' THEN 1 ELSE 0 END) as islam_laki,
            SUM(CASE WHEN ptk.agama_id = 1 AND ptk.jenis_kelamin = 'P' THEN 1 ELSE 0 END) as islam_perempuan,
            SUM(CASE WHEN ptk.agama_id = 2 AND ptk.jenis_kelamin = 'L' THEN 1 ELSE 0 END) as kristen_laki,
            SUM(CASE WHEN ptk.agama_id = 2 AND ptk.jenis_kelamin = 'P' THEN 1 ELSE 0 END) as kristen_perempuan,
            SUM(CASE WHEN ptk.agama_id = 3 AND ptk.jenis_kelamin = 'L' THEN 1 ELSE 0 END) as katholik_laki,
            SUM(CASE WHEN ptk.agama_id = 3 AND ptk.jenis_kelamin = 'P' THEN 1 ELSE 0 END) as katholik_perempuan,
            SUM(CASE WHEN ptk.agama_id = 4 AND ptk.jenis_kelamin = 'L' THEN 1 ELSE 0 END) as hindu_laki,
            SUM(CASE WHEN ptk.agama_id = 4 AND ptk.jenis_kelamin = 'P' THEN 1 ELSE 0 END) as hindu_perempuan,
            SUM(CASE WHEN ptk.agama_id = 5 AND ptk.jenis_kelamin = 'L' THEN 1 ELSE 0 END) as budha_laki,
            SUM(CASE WHEN ptk.agama_id = 5 AND ptk.jenis_kelamin = 'P' THEN 1 ELSE 0 END) as budha_perempuan,
            SUM(CASE WHEN ptk.agama_id = 6 AND ptk.jenis_kelamin = 'L' THEN 1 ELSE 0 END) as konghucu_laki,
            SUM(CASE WHEN ptk.agama_id = 6 AND ptk.jenis_kelamin = 'P' THEN 1 ELSE 0 END) as konghucu_perempuan,
            SUM(CASE WHEN ptk.agama_id = 7 AND ptk.jenis_kelamin = 'L' THEN 1 ELSE 0 END) as kepercayaan_laki,
            SUM(CASE WHEN ptk.agama_id = 7 AND ptk.jenis_kelamin = 'P' THEN 1 ELSE 0 END) as kepercayaan_perempuan,
            SUM(CASE WHEN ptk.agama_id = 99 AND ptk.jenis_kelamin = 'L' THEN 1 ELSE 0 END) as lainnya_laki,
            SUM(CASE WHEN ptk.agama_id = 99 AND ptk.jenis_kelamin = 'P' THEN 1 ELSE 0 END) as lainnya_perempuan,

            getdate() as tanggal_rekap_terakhir
        FROM
            ptk ptk with(nolock)
        JOIN ptk_terdaftar ptkd with(nolock) ON ptk.ptk_id = ptkd.ptk_id
        JOIN ref.tahun_ajaran ta with(nolock) ON ta.tahun_ajaran_id = ptkd.tahun_ajaran_id
        JOIN sekolah s with(nolock) ON ptkd.sekolah_id = s.sekolah_id
        JOIN ref.mst_wilayah w1 with(nolock) ON LEFT (s.kode_wilayah, 6) = w1.kode_wilayah
        JOIN ref.mst_wilayah w2 with(nolock) ON w1.mst_kode_wilayah = w2.kode_wilayah
        JOIN ref.mst_wilayah w3 with(nolock) ON w2.mst_kode_wilayah = w3.kode_wilayah
        WHERE
            ptk.Soft_delete = 0
        AND ptkd.Soft_delete = 0
        AND ptkd.ptk_induk = 1
        AND ptkd.tahun_ajaran_id = ".$request->input('tahun_ajaran_id')."
        {$params_jptk}
        {$params_status}
        AND (
            ptkd.tgl_ptk_keluar > ta.tanggal_selesai
            OR ptkd.jenis_keluar_id IS NULL
        )
        {$params_wilayah}
        {$param_bentuk}
        GROUP BY
            {$col_wilayah},
            {$col_kode},
            {$col_id_level},
            {$col_mst_kode_induk_group}
            {$col_mst_kode}
        ORDER BY
            {$col_wilayah} ASC";

        // return $sql;die;

        $fetch = DB::connection('sqlsrv')
        ->select(DB::raw($sql));

        $return = array();
        $return['rows'] = $fetch;
        $return['total'] = sizeof($fetch);

        return $return;
    }

    public function getRekapGTKAgamaSp(Request $request){
        switch ($request->input('id_level_wilayah')) {
            case 0:
                $col_wilayah = "w3.nama";
                $col_kode = "w3.kode_wilayah";
                $col_id_level = "w3.id_level_wilayah";
                $col_mst_kode = "w3.mst_kode_wilayah";
                $col_mst_kode_induk = '';
                $col_mst_kode_induk_group = '';
                $params_wilayah = "";
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
            $param_bentuk = "AND s.bentuk_pendidikan_id IN ".$strBentuk;

            // return $param_bentuk;die;
        }else{
            $param_bentuk = "";
        }

        if($request->input('status_sekolah') && (int)$request->input('status_sekolah') != 99){
            $param_status = "AND s.status_sekolah = ".$request->input('status_sekolah');
        }else{
            $param_status = "AND s.status_sekolah IN (1,2)";
        }

        if($request->input('keyword')){
            $param_keyword = "AND (nama LIKE '%".$request->input('keyword')."%' OR npsn LIKE '%".$request->input('keyword')."%')";
        }else{
            $param_keyword = "";
        }


        if($request->get('jenis_ptk_id')){
            if($request->get('jenis_ptk_id') == 1){
                $params_jptk = "AND ptk.jenis_ptk_id IN (3, 4, 5, 6, 12, 13, 14)";
            }else if($request->get('jenis_ptk_id') == 2){
                $params_jptk = "AND ptk.jenis_ptk_id IN (11, 99, 30, 40)";
            }else{
                $params_jptk = "AND ptk.jenis_ptk_id IN (3, 4, 5, 6, 12, 13, 14, 11, 99, 30, 40)";
            }
        }else{
            $params_jptk = "AND ptk.jenis_ptk_id IN (3, 4, 5, 6, 12, 13, 14, 11, 99, 30, 40)";
        }

        $sql_count = "SELECT
					count(1) as total
				FROM
					ptk ptk
				LEFT OUTER JOIN ptk_terdaftar ptkd ON ptk.ptk_id = ptkd.ptk_id
				LEFT OUTER JOIN ref.tahun_ajaran ta ON ta.tahun_ajaran_id = ptkd.tahun_ajaran_id
				LEFT OUTER JOIN sekolah s ON ptkd.sekolah_id = s.sekolah_id
				LEFT OUTER JOIN ref.mst_wilayah w1 ON LEFT (s.kode_wilayah, 6) = w1.kode_wilayah
				LEFT OUTER JOIN ref.mst_wilayah w2 ON w1.mst_kode_wilayah = w2.kode_wilayah
				LEFT OUTER JOIN ref.mst_wilayah w3 ON w2.mst_kode_wilayah = w3.kode_wilayah
				LEFT OUTER JOIN ref.bentuk_pendidikan bp on bp.bentuk_pendidikan_id = s.bentuk_pendidikan_id
				WHERE
					ptk.Soft_delete = 0
				AND ptkd.Soft_delete = 0
				AND ptkd.ptk_induk = 1
				AND ptkd.tahun_ajaran_id = ".$request->input('tahun_ajaran_id')."
				{$params_jptk}
				AND (
					ptkd.tgl_ptk_keluar > ta.tanggal_selesai
					OR ptkd.jenis_keluar_id IS NULL
				)
				{$params_wilayah}
				{$param_bentuk}
				{$param_status}
				GROUP BY
					s.sekolah_id,
					s.nama,
					s.npsn,
					s.bentuk_pendidikan_id,
					bp.nama,
					s.status_sekolah,
					w1.nama,
					w2.nama,
					w3.nama
				ORDER BY
					s.nama ASC";

        $sql = "SELECT
					ROW_NUMBER() OVER (ORDER BY s.nama) as 'no',
					s.sekolah_id as sekolah_id,
					s.nama,
					s.npsn,
					s.bentuk_pendidikan_id,
					bp.nama as bentuk,
					s.status_sekolah,
					CASE s.status_sekolah
                        WHEN 1 THEN 'Negeri'
                        WHEN 2 THEN 'Swasta'
                    ELSE '-' END AS status,
					w1.nama as kecamatan,
					w2.nama as kabupaten,
					w3.nama as propinsi,
					SUM(CASE WHEN ptk.agama_id = 1 THEN 1 ELSE 0 END) as gtk_islam,
					SUM(CASE WHEN ptk.agama_id = 2 THEN 1 ELSE 0 END) as gtk_kristen,
					SUM(CASE WHEN ptk.agama_id = 3 THEN 1 ELSE 0 END) as gtk_katholik,
					SUM(CASE WHEN ptk.agama_id = 4 THEN 1 ELSE 0 END) as gtk_hindu,
					SUM(CASE WHEN ptk.agama_id = 5 THEN 1 ELSE 0 END) as gtk_budha,
					SUM(CASE WHEN ptk.agama_id = 6 THEN 1 ELSE 0 END) as gtk_konghucu,
					SUM(CASE WHEN ptk.agama_id = 7 THEN 1 ELSE 0 END) as gtk_kepercayaan,
					SUM(CASE WHEN ptk.agama_id = 99 THEN 1 ELSE 0 END) as gtk_lainnya,
                    SUM(1) as gtk_total
				FROM
					ptk ptk
				LEFT OUTER JOIN ptk_terdaftar ptkd ON ptk.ptk_id = ptkd.ptk_id
				LEFT OUTER JOIN ref.tahun_ajaran ta ON ta.tahun_ajaran_id = ptkd.tahun_ajaran_id
				LEFT OUTER JOIN sekolah s ON ptkd.sekolah_id = s.sekolah_id
				LEFT OUTER JOIN ref.mst_wilayah w1 ON LEFT (s.kode_wilayah, 6) = w1.kode_wilayah
				LEFT OUTER JOIN ref.mst_wilayah w2 ON w1.mst_kode_wilayah = w2.kode_wilayah
				LEFT OUTER JOIN ref.mst_wilayah w3 ON w2.mst_kode_wilayah = w3.kode_wilayah
				LEFT OUTER JOIN ref.bentuk_pendidikan bp on bp.bentuk_pendidikan_id = s.bentuk_pendidikan_id
				WHERE
					ptk.Soft_delete = 0
				AND ptkd.Soft_delete = 0
				AND ptkd.ptk_induk = 1
				AND ptkd.tahun_ajaran_id = ".$request->input('tahun_ajaran_id')."
				{$params_jptk}
				AND (
					ptkd.tgl_ptk_keluar > ta.tanggal_selesai
					OR ptkd.jenis_keluar_id IS NULL
				)
				{$params_wilayah}
				{$param_bentuk}
				{$param_status}
				GROUP BY
					s.sekolah_id,
					s.nama,
					s.npsn,
					s.bentuk_pendidikan_id,
					bp.nama,
					s.status_sekolah,
					w1.nama,
					w2.nama,
					w3.nama
				ORDER BY
					s.nama ASC";

        // return $sql;die;
        
        $fetch = DB::connection('sqlsrv')
        ->select(DB::raw($sql_count));

        $return = array();
        $return['total'] = $fetch[0]->total;
        
        $sql .= " OFFSET ".($request->input('start')?$request->input('start'):0)." ROWS FETCH NEXT ".($request->input('limit')?$request->input('limit'):20)." ROWS ONLY";

        $fetch = DB::connection('sqlsrv')
        ->select(DB::raw($sql));

        $return['rows'] = $fetch;

        return $return;
    }
}