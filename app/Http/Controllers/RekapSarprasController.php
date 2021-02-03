<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Http\Controllers\Preface;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redis;

class RekapSarprasController extends Controller
{
    public function getRekapSarprasRingkasanExcel(Request $request){
        $return = self::getRekapSarprasRingkasan($request);
        return view('excel/UnduhExcelGeneral', ['return' => $return, 'judul' => 'Rekap Ringkasan Prasarana', 'sub_judul' => "Tanggal Rekap Terakhir: ".$return['rows'][0]->tanggal_rekap_terakhir]);
    }
    
    public function getRekapSarprasRingkasanSpExcel(Request $request){
        $return = self::getRekapSarprasRingkasanSp($request);
        return view('excel/UnduhExcelGeneral', ['return' => $return, 'judul' => 'Rekap Ringkasan Prasarana Per Sekolah', 'sub_judul' => "Tanggal Rekap Terakhir: ".$return['rows'][0]->tanggal_rekap_terakhir]);
    }

    public function getRekapSarprasTingkatKerusakanExcel(Request $request){
        $return = (array)json_decode(self::getRekapSarprasTingkatKerusakan($request));
        // return $return;die;
        return view('excel/UnduhExcelGeneral', ['return' => $return, 'judul' => 'Rekap Tingkat Kerusakan Prasarana', 'sub_judul' => "Tanggal Rekap Terakhir: ".$return['rows'][0]->tanggal_rekap_terakhir]);
    }
    
    public function getRekapSarprasTingkatKerusakanSpExcel(Request $request){
        $return = (array)json_decode(self::getRekapSarprasTingkatKerusakanSp($request));
        return view('excel/UnduhExcelGeneral', ['return' => $return, 'judul' => 'Rekap Tingkat Kerusakan Prasarana Per Sekolah', 'sub_judul' => "Tanggal Rekap Terakhir: ".$return['rows'][0]->tanggal_rekap_terakhir]);
    }

    public function getRekapSarprasTingkatKerusakanSp(Request $request){
        $keyStr = 'RekapSarprasTingkatKerusakanSp';
        $keyStr .= ':'.($request->input('id_level_wilayah') ? $request->input('id_level_wilayah') : "0");
        $keyStr .= ':'.($request->input('kode_wilayah') ? $request->input('kode_wilayah') : '000000');
        $keyStr .= ':'.($request->input('keyword') ? $request->input('keyword') : '');
        $keyStr .= ':'.($request->input('status_sekolah') ? $request->input('status_sekolah') : '999');
        $keyStr .= ':'.($request->input('bentuk_pendidikan_id') ? $request->input('bentuk_pendidikan_id') : '');
        $keyStr .= ':'.($request->input('start') ? $request->input('start') : '0');
        $keyStr .= ':'.($request->input('limit') ? $request->input('limit') : '20');

        if(!Redis::exists($keyStr)){

            switch ($request->input('id_level_wilayah')) {
                case 0:
                    $params_wilayah = "";
                    break;
                case 1:
                    $params_wilayah = "AND w3.kode_wilayah = '{$request->input('kode_wilayah')}'";
                    break;
                case 2:
                    $params_wilayah = "AND w2.kode_wilayah = '{$request->input('kode_wilayah')}'";
                    break;
                case 3:
                    $params_wilayah = "AND w1.kode_wilayah = '{$request->input('kode_wilayah')}'";
                    break;
                default:
                    $params_wilayah = "";
                    break;
            }
            
            if($request->input('keyword')){
                $param_keyword = " AND (s.nama like '%".$request->input('keyword')."%' OR s.npsn like '%".$request->input('keyword')."%')";
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

                $param_bentuk = "AND s.bentuk_pendidikan_id IN ".$strBentuk;

            }else{
                $param_bentuk = "";
            }

            $rumus_kerusakan = "round( (
                            (rusak_pondasi * 0.12) +
                            (rusak_sloop_kolom_balok * 0.25) +
                            (rusak_plester_struktur * 0.02) +
                            (rusak_kudakuda_atap * 0.05) +
                            (rusak_kaso_atap * 0.032) +
                            (rusak_reng_atap * 0.015) +
                            ((rusak_tutup_atap) * 0.025) +
                            (rusak_rangka_plafon * 0.03) +
                            (rusak_tutup_plafon * 0.04) +
                            (rusak_bata_dinding * 0.07) +
                            (rusak_plester_dinding * 0.022) +
                            (rusak_daun_jendela * 0.016) +
                            (rusak_daun_pintu * 0.0125) +
                            (rusak_kusen * 0.026) +
                            (rusak_tutup_lantai * 0.12) +
                            (rusak_inst_listrik * 0.0475) +
                            (rusak_inst_air * 0.0245) +
                            (rusak_drainase * 0.015) +
                            (rusak_finish_struktur * 0.01) +
                            (rusak_finish_plafon * 0.012) +
                            (rusak_finish_dinding * 0.025) +
                            (rusak_finish_kpj * 0.0175)
                        ) , 2)";

            $sql_count = "SELECT 
                            SUM ( 1 ) AS total 
                        FROM
                            sekolah s
                            JOIN ref.mst_wilayah w1 ON LEFT ( s.kode_wilayah, 6 ) = w1.kode_wilayah
                            JOIN ref.mst_wilayah w2 ON w1.mst_kode_wilayah = w2.kode_wilayah
                            JOIN ref.mst_wilayah w3 ON w2.mst_kode_wilayah = w3.kode_wilayah 
                        WHERE
                            s.Soft_delete = 0
                        {$params_wilayah}
                        {$param_keyword}
                        {$param_bentuk}";

            $sql = "SELECT
                        ROW_NUMBER() OVER (ORDER BY s.nama) as 'no',
                        s.sekolah_id,
                        s.nama,
                        s.npsn,
                        s.status_sekolah,
                        CASE s.status_sekolah
                            WHEN 1 THEN 'Negeri'
                            WHEN 2 THEN 'Swasta'
                            ELSE '-' 
                        END AS status,
                        s.bentuk_pendidikan_id,
                        CASE 
                            WHEN s.bentuk_pendidikan_id = 1 THEN 'TK'
                            WHEN s.bentuk_pendidikan_id = 2 THEN 'KB'
                            WHEN s.bentuk_pendidikan_id = 3 THEN 'TPA'
                            WHEN s.bentuk_pendidikan_id = 4 THEN 'SPS'
                            WHEN s.bentuk_pendidikan_id = 5 THEN 'SD'
                            WHEN s.bentuk_pendidikan_id = 6 THEN 'SMP'
                            WHEN s.bentuk_pendidikan_id IN (7, 8, 29) THEN 'SLB'
                            WHEN s.bentuk_pendidikan_id = 13 THEN 'SMA'
                            WHEN s.bentuk_pendidikan_id = 15 THEN 'SMK'
                            ELSE '-' 
                        END AS bentuk,
                        w1.nama as kecamatan,
                        w2.nama as kabupaten,
                        w3.nama as propinsi,

                        SUM(CASE WHEN pr.jenis_prasarana_id = 1 and {$rumus_kerusakan} = 0 then 1 else 0 end) as r_kelas_baik,
                        SUM(CASE WHEN pr.jenis_prasarana_id = 1 and {$rumus_kerusakan} > 0 AND {$rumus_kerusakan} < 30 then 1 else 0 end) as r_kelas_rusak_ringan,
                        SUM(CASE WHEN pr.jenis_prasarana_id = 1 and {$rumus_kerusakan} >= 30 AND {$rumus_kerusakan} <= 45 then 1 else 0 end) as r_kelas_rusak_sedang,
                        SUM(CASE WHEN pr.jenis_prasarana_id = 1 and {$rumus_kerusakan} > 45 then 1 else 0 end) as r_kelas_rusak_berat,

                        SUM(CASE WHEN pr.jenis_prasarana_id = 16 and {$rumus_kerusakan} = 0 then 1 else 0 end) as bengkel_baik,
                        SUM(CASE WHEN pr.jenis_prasarana_id = 16 and {$rumus_kerusakan} > 0 AND {$rumus_kerusakan} < 30 then 1 else 0 end) as bengkel_rusak_ringan,
                        SUM(CASE WHEN pr.jenis_prasarana_id = 16 and {$rumus_kerusakan} >= 30 AND {$rumus_kerusakan} <= 45 then 1 else 0 end) as bengkel_rusak_sedang,
                        SUM(CASE WHEN pr.jenis_prasarana_id = 16 and {$rumus_kerusakan} > 45 then 1 else 0 end) as bengkel_rusak_berat,

                        SUM(CASE WHEN pr.jenis_prasarana_id = 10 and {$rumus_kerusakan} = 0 then 1 else 0 end) as perpustakaan_baik,
                        SUM(CASE WHEN pr.jenis_prasarana_id = 10 and {$rumus_kerusakan} > 0 AND {$rumus_kerusakan} < 30 then 1 else 0 end) as perpustakaan_rusak_ringan,
                        SUM(CASE WHEN pr.jenis_prasarana_id = 10 and {$rumus_kerusakan} >= 30 AND {$rumus_kerusakan} <= 45 then 1 else 0 end) as perpustakaan_rusak_sedang,
                        SUM(CASE WHEN pr.jenis_prasarana_id = 10 and {$rumus_kerusakan} > 45 then 1 else 0 end) as perpustakaan_rusak_berat,

                        SUM(CASE WHEN pr.jenis_prasarana_id = 23 and {$rumus_kerusakan} = 0 then 1 else 0 end) as r_guru_baik,
                        SUM(CASE WHEN pr.jenis_prasarana_id = 23 and {$rumus_kerusakan} > 0 AND {$rumus_kerusakan} < 30 then 1 else 0 end) as r_guru_rusak_ringan,
                        SUM(CASE WHEN pr.jenis_prasarana_id = 23 and {$rumus_kerusakan} >= 30 AND {$rumus_kerusakan} <= 45 then 1 else 0 end) as r_guru_rusak_sedang,
                        SUM(CASE WHEN pr.jenis_prasarana_id = 23 and {$rumus_kerusakan} > 45 then 1 else 0 end) as r_guru_rusak_berat,
                    
                        SUM(CASE WHEN pr.jenis_prasarana_id = 24 and {$rumus_kerusakan} = 0 then 1 else 0 end) as r_kepsek_baik,
                        SUM(CASE WHEN pr.jenis_prasarana_id = 24 and {$rumus_kerusakan} > 0 AND {$rumus_kerusakan} < 30 then 1 else 0 end) as r_kepsek_rusak_ringan,
                        SUM(CASE WHEN pr.jenis_prasarana_id = 24 and {$rumus_kerusakan} >= 30 AND {$rumus_kerusakan} <= 45 then 1 else 0 end) as r_kepsek_rusak_sedang,
                        SUM(CASE WHEN pr.jenis_prasarana_id = 24 and {$rumus_kerusakan} > 45 then 1 else 0 end) as r_kepsek_rusak_berat,
                    
                        SUM(CASE WHEN pr.jenis_prasarana_id = 3 and {$rumus_kerusakan} = 0 then 1 else 0 end) as lab_kimia_baik,
                        SUM(CASE WHEN pr.jenis_prasarana_id = 3 and {$rumus_kerusakan} > 0 AND {$rumus_kerusakan} < 30 then 1 else 0 end) as lab_kimia_rusak_ringan,
                        SUM(CASE WHEN pr.jenis_prasarana_id = 3 and {$rumus_kerusakan} >= 30 AND {$rumus_kerusakan} <= 45 then 1 else 0 end) as lab_kimia_rusak_sedang,
                        SUM(CASE WHEN pr.jenis_prasarana_id = 3 and {$rumus_kerusakan} > 45 then 1 else 0 end) as lab_kimia_rusak_berat,
                    
                        SUM(CASE WHEN pr.jenis_prasarana_id = 4 and {$rumus_kerusakan} = 0 then 1 else 0 end) as lab_fisika_baik,
                        SUM(CASE WHEN pr.jenis_prasarana_id = 4 and {$rumus_kerusakan} > 0 AND {$rumus_kerusakan} < 30 then 1 else 0 end) as lab_fisika_rusak_ringan,
                        SUM(CASE WHEN pr.jenis_prasarana_id = 4 and {$rumus_kerusakan} >= 30 AND {$rumus_kerusakan} <= 45 then 1 else 0 end) as lab_fisika_rusak_sedang,
                        SUM(CASE WHEN pr.jenis_prasarana_id = 4 and {$rumus_kerusakan} > 45 then 1 else 0 end) as lab_fisika_rusak_berat,
                    
                        SUM(CASE WHEN pr.jenis_prasarana_id = 5 and {$rumus_kerusakan} = 0 then 1 else 0 end) as lab_biologi_baik,
                        SUM(CASE WHEN pr.jenis_prasarana_id = 5 and {$rumus_kerusakan} > 0 AND {$rumus_kerusakan} < 30 then 1 else 0 end) as lab_biologi_rusak_ringan,
                        SUM(CASE WHEN pr.jenis_prasarana_id = 5 and {$rumus_kerusakan} >= 30 AND {$rumus_kerusakan} <= 45 then 1 else 0 end) as lab_biologi_rusak_sedang,
                        SUM(CASE WHEN pr.jenis_prasarana_id = 5 and {$rumus_kerusakan} > 45 then 1 else 0 end) as lab_biologi_rusak_berat,

                        getdate() as tanggal_rekap_terakhir
                    FROM
                    sekolah s with(nolock)
                    LEFT JOIN ruang pr with(nolock) ON pr.sekolah_id = s.sekolah_id
                    LEFT JOIN ruang_longitudinal prl with(nolock) ON prl.id_ruang = pr.id_ruang
                    LEFT JOIN bangunan with(nolock) ON pr.id_bangunan = bangunan.id_bangunan
                    LEFT JOIN bangunan_longitudinal with(nolock) ON bangunan.id_bangunan = bangunan_longitudinal.id_bangunan
                    JOIN ref.mst_wilayah w1 with(nolock) ON LEFT (s.kode_wilayah, 6) = w1.kode_wilayah
                    JOIN ref.mst_wilayah w2 with(nolock) ON w1.mst_kode_wilayah = w2.kode_wilayah
                    JOIN ref.mst_wilayah w3 with(nolock) ON w2.mst_kode_wilayah = w3.kode_wilayah
                    WHERE
                        pr.Soft_delete = 0
                    AND prl.Soft_delete = 0
                    AND s.Soft_delete = 0
                    AND prl.semester_id = '".($request->input('semester_id') ? $request->input('semester_id') : 20191)."'
                    AND prl.berfungsi = 1
                    AND bangunan_longitudinal.semester_id = '".($request->input('semester_id') ? $request->input('semester_id') : 20191)."'
                    AND bangunan.id_hapus_buku is null
                    AND bangunan_longitudinal.Soft_delete = 0
                    AND bangunan.Soft_delete = 0
                    {$params_wilayah}
                    {$param_keyword}
                    {$param_bentuk}
                    GROUP BY
                        s.sekolah_id,
                        s.nama, 
                        s.npsn,
                        s.status_sekolah,
                        s.bentuk_pendidikan_id,
                        w1.nama,
                        w2.nama,
                        w3.nama
                    ORDER BY
                        s.nama ASC";

            // return $sql;die;
            
            $sql .= " OFFSET ".($request->input('start') ? $request->input('start') : 0)." ROWS FETCH NEXT ".($request->input('limit') ? $request->input('limit') : 20)." ROWS ONLY";

            // return $sql;die;

            $fetch = DB::connection('sqlsrv')
            ->select(DB::raw($sql_count));

            $return = array();
            $return['total'] = $fetch[0]->total;

            $fetch = DB::connection('sqlsrv')
            ->select(DB::raw($sql));

            $return['rows'] = $fetch;

            // return $return;
            Redis::set($keyStr, json_encode($return));
            Redis::expire( $keyStr, 86400);

            return Redis::get($keyStr);
        
        }else{
            return Redis::get($keyStr);
        }

    }

    public function getRekapSarprasRingkasanSp(Request $request){
        $jenis_sarpras_jumlah = $request->input('jenis_sarpras_jumlah') ? $request->input('jenis_sarpras_jumlah') : 'umum';

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

        if($jenis_sarpras_jumlah == 'lab'){
			$kolom_rekap = 'isnull(lab_ipa,0) as lab_ipa,
							isnull(lab_kimia,0) as lab_kimia,
							isnull(lab_fisika,0) as lab_fisika,
							isnull(lab_biologi,0) as lab_biologi,
							isnull(lab_ips,0) as lab_ips,
							isnull(lab_bahasa,0) as lab_bahasa,
							isnull(lab_komputer,0) as lab_komputer,
							isnull(lab_multimedia,0) as lab_multimedia,';
		}else if($jenis_sarpras_jumlah == 'umum'){
			$kolom_rekap = 'isnull(r_kelas,0) as r_kelas,
							isnull(r_guru,0) as r_guru,
							isnull(r_kepsek,0) as r_kepsek,
							isnull(perpustakaan,0) as perpustakaan,
							isnull(perpustakaan_multimedia,0) as perpustakaan_multimedia,
							isnull(r_keterampilan,0) as r_keterampilan,
							isnull(aula,0) as aula,
							';
		}else if($jenis_sarpras_jumlah == 'lab_komputer'){
			$kolom_rekap = 'isnull(lab_komputer,0) as jumlah_lab_komputer,
							isnull(sarana.jumlah_komputer,0) as jumlah_komputer,
							isnull(pd,0) as jumlah_pd,
							';
		}else if($jenis_sarpras_jumlah == 'km'){
			$kolom_rekap = 'isnull(km_guru_laki,0) as km_guru_laki,
							isnull(km_guru_perempuan,0) as km_guru_perempuan,
							isnull(km_siswa_laki,0) as km_siswa_laki,
							isnull(km_siswa_perempuan,0) as km_siswa_perempuan,
							';
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
            rekap_sekolah.sekolah_id,
            rekap_sekolah.nama,
            rekap_sekolah.npsn,
            rekap_sekolah.status_sekolah,
            CASE rekap_sekolah.status_sekolah
                WHEN 1 THEN 'Negeri'
                WHEN 2 THEN 'Swasta'
            ELSE '-' END AS status,
            rekap_sekolah.bentuk_pendidikan_id,
            CASE 
                WHEN rekap_sekolah.bentuk_pendidikan_id = 1 THEN 'TK'
                WHEN rekap_sekolah.bentuk_pendidikan_id = 2 THEN 'KB'
                WHEN rekap_sekolah.bentuk_pendidikan_id = 3 THEN 'TPA'
                WHEN rekap_sekolah.bentuk_pendidikan_id = 4 THEN 'SPS'
                WHEN rekap_sekolah.bentuk_pendidikan_id = 5 THEN 'SD'
                WHEN rekap_sekolah.bentuk_pendidikan_id = 6 THEN 'SMP'
                WHEN rekap_sekolah.bentuk_pendidikan_id IN (7, 8, 14, 29) THEN 'SLB'
                WHEN rekap_sekolah.bentuk_pendidikan_id = 13 THEN 'SMA'
                WHEN rekap_sekolah.bentuk_pendidikan_id = 15 THEN 'SMK'
            ELSE '-' END AS bentuk,
            {$wilayah},
            {$params_wilayah} AS kode_wilayah,
            {$params_mst_wilayah} AS mst_kode_wilayah,
            {$params_id_level} AS id_level_wilayah,
            kecamatan,
            kabupaten,
            propinsi,
            {$kolom_rekap}
            tanggal as tanggal_rekap_terakhir
        FROM
            rekap_sekolah WITH(NOLOCK)
        LEFT OUTER JOIN rekap_sekolah_sarpras rekap_sarpras on rekap_sarpras.sekolah_id = rekap_sekolah.sekolah_id and rekap_sarpras.semester_id = '".$request->input('semester_id')."'
        LEFT OUTER JOIN (SELECT
            sekolah_id,
            SUM (jumlah_komputer) AS jumlah_komputer
        FROM
            rekap_sekolah_sarana
        WHERE
            jenis_prasarana_id = 8
        AND semester_id = '".($request->input('semester_id') ? $request->input('semester_id') : 20191)."'
        GROUP BY
            sekolah_id) sarana on sarana.sekolah_id = rekap_sekolah.sekolah_id
        WHERE
            rekap_sekolah.semester_id = '".($request->input('semester_id') ? $request->input('semester_id') : 20191)."'
        AND rekap_sekolah.tahun_ajaran_id = '".(substr($request->input('semester_id'),0,4) ? substr($request->input('semester_id'),0,4) : 2019)."'
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

    public function getRekapSarprasTingkatKerusakan(Request $request){

        $keyStr = 'RekapSarprasTingkatKerusakan';
        $keyStr .= ':'.($request->input('id_level_wilayah') ? $request->input('id_level_wilayah') : "0");
        $keyStr .= ':'.($request->input('kode_wilayah') ? $request->input('kode_wilayah') : '000000');
        $keyStr .= ':'.($request->input('keyword') ? $request->input('keyword') : '');
        $keyStr .= ':'.($request->input('status_sekolah') ? $request->input('status_sekolah') : '999');
        $keyStr .= ':'.($request->input('bentuk_pendidikan_id') ? $request->input('bentuk_pendidikan_id') : '');

        // return $keyStr;die;

        if(!Redis::exists($keyStr)){

            $induk = '';
            $induk_group = '';

            switch ($request->input('id_level_wilayah')) {
                case 0:
                    $col_wilayah = "w3.nama";
                    $col_kode = "w3.kode_wilayah";
                    $col_id_level = "w3.id_level_wilayah";
                    $col_mst_kode = "w3.mst_kode_wilayah";
                    $col_mst_kode_induk = '';
                    $col_mst_kode_induk_group = '';
                    $params_wilayah = " and w3.mst_kode_wilayah = '".($request->input('kode_wilayah') ? $request->input('kode_wilayah') : '000000')."'";
                    break;
                case 1:
                    $col_wilayah = "w2.nama";
                    $col_kode = "w2.kode_wilayah";
                    $col_id_level = "w2.id_level_wilayah";
                    $col_mst_kode = "w2.mst_kode_wilayah";
                    $col_mst_kode_induk = 'w3.mst_kode_wilayah as mst_kode_wilayah_induk,';
                    $col_mst_kode_induk_group = 'w3.mst_kode_wilayah,';
                    $params_wilayah = " and w2.mst_kode_wilayah = '".$request->input('kode_wilayah')."'";
                    $induk = 'w3.nama as induk_propinsi,';
                    $induk_group = 'w3.nama,';
                    break;
                case 2:
                    $col_wilayah = "w1.nama";
                    $col_kode = "w1.kode_wilayah";
                    $col_id_level = "w1.id_level_wilayah";
                    $col_mst_kode = "w1.mst_kode_wilayah";
                    $col_mst_kode_induk = 'w2.mst_kode_wilayah as mst_kode_wilayah_induk,';
                    $col_mst_kode_induk_group = 'w2.mst_kode_wilayah,';
                    $params_wilayah = " and w1.mst_kode_wilayah = '".$request->input('kode_wilayah')."'";
                    $induk = 'w2.nama as induk_kabupaten,';
                    $induk_group = 'w2.nama,';
                    break;
                default:
                    $col_wilayah = "w3.nama";
                    $col_kode = "w3.kode_wilayah";
                    $col_id_level = "w3.id_level_wilayah";
                    $col_mst_kode = "w3.mst_kode_wilayah";
                    $col_mst_kode_induk = '';
                    $col_mst_kode_induk_group = '';
                    $params_wilayah = " and w3.mst_kode_wilayah = '".($request->input('kode_wilayah') ? $request->input('kode_wilayah') : '000000')."'";
                    break;
            }

            if($request->input('keyword')){
                $param_keyword = " AND {$col_wilayah} like '%".$request->input('keyword')."%'";
            }else{
                $param_keyword = '';
            }

            $params_status = '';
            if($request->get('status_sekolah')){
                if($request->get('status_sekolah') != 99){
                    $params_status = " AND s.status_sekolah = ".$request->get('status_sekolah');
                }
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

            $rumus_kerusakan = "round( (
                            (rusak_pondasi * 0.12) +
                            (rusak_sloop_kolom_balok * 0.25) +
                            (rusak_plester_struktur * 0.02) +
                            (rusak_kudakuda_atap * 0.05) +
                            (rusak_kaso_atap * 0.032) +
                            (rusak_reng_atap * 0.015) +
                            ((rusak_tutup_atap) * 0.025) +
                            (rusak_rangka_plafon * 0.03) +
                            (rusak_tutup_plafon * 0.04) +
                            (rusak_bata_dinding * 0.07) +
                            (rusak_plester_dinding * 0.022) +
                            (rusak_daun_jendela * 0.016) +
                            (rusak_daun_pintu * 0.0125) +
                            (rusak_kusen * 0.026) +
                            (rusak_tutup_lantai * 0.12) +
                            (rusak_inst_listrik * 0.0475) +
                            (rusak_inst_air * 0.0245) +
                            (rusak_drainase * 0.015) +
                            (rusak_finish_struktur * 0.01) +
                            (rusak_finish_plafon * 0.012) +
                            (rusak_finish_dinding * 0.025) +
                            (rusak_finish_kpj * 0.0175)
                        ) , 2)";
        
            switch ($request->get('tipe')) {
                case 'r_kelas':
                    $kolom_kerusakan = "SUM(CASE WHEN pr.jenis_prasarana_id = 1 and {$rumus_kerusakan} = 0 then 1 else 0 end) as r_kelas_baik,
                                        SUM(CASE WHEN pr.jenis_prasarana_id = 1 and {$rumus_kerusakan} > 0 AND {$rumus_kerusakan} < 30 then 1 else 0 end) as r_kelas_rusak_ringan,
                                        SUM(CASE WHEN pr.jenis_prasarana_id = 1 and {$rumus_kerusakan} >= 30 AND {$rumus_kerusakan} <= 45 then 1 else 0 end) as r_kelas_rusak_sedang,
                                        SUM(CASE WHEN pr.jenis_prasarana_id = 1 and {$rumus_kerusakan} > 45 then 1 else 0 end) as r_kelas_rusak_berat,";
                    break;
                case 'perpustakaan':
                    $kolom_kerusakan = "SUM(CASE WHEN pr.jenis_prasarana_id = 10 and {$rumus_kerusakan} = 0 then 1 else 0 end) as perpustakaan_baik,
                                        SUM(CASE WHEN pr.jenis_prasarana_id = 10 and {$rumus_kerusakan} > 0 AND {$rumus_kerusakan} < 30 then 1 else 0 end) as perpustakaan_rusak_ringan,
                                        SUM(CASE WHEN pr.jenis_prasarana_id = 10 and {$rumus_kerusakan} >= 30 AND {$rumus_kerusakan} <= 45 then 1 else 0 end) as perpustakaan_rusak_sedang,
                                        SUM(CASE WHEN pr.jenis_prasarana_id = 10 and {$rumus_kerusakan} > 45 then 1 else 0 end) as perpustakaan_rusak_berat,";
                    break;
                
                case 'labor':
                    $kolom_kerusakan = "SUM(CASE WHEN pr.jenis_prasarana_id = 16 and {$rumus_kerusakan} = 0 then 1 else 0 end) as bengkel_baik,
                                        SUM(CASE WHEN pr.jenis_prasarana_id = 16 and {$rumus_kerusakan} > 0 AND {$rumus_kerusakan} < 30 then 1 else 0 end) as bengkel_rusak_ringan,
                                        SUM(CASE WHEN pr.jenis_prasarana_id = 16 and {$rumus_kerusakan} >= 30 AND {$rumus_kerusakan} <= 45 then 1 else 0 end) as bengkel_rusak_sedang,
                                        SUM(CASE WHEN pr.jenis_prasarana_id = 16 and {$rumus_kerusakan} > 45 then 1 else 0 end) as bengkel_rusak_berat,";
                    break;
                
                case 'r_guru':
                    $kolom_kerusakan = "SUM(CASE WHEN pr.jenis_prasarana_id = 23 and {$rumus_kerusakan} = 0 then 1 else 0 end) as r_guru_baik,
                                        SUM(CASE WHEN pr.jenis_prasarana_id = 23 and {$rumus_kerusakan} > 0 AND {$rumus_kerusakan} < 30 then 1 else 0 end) as r_guru_rusak_ringan,
                                        SUM(CASE WHEN pr.jenis_prasarana_id = 23 and {$rumus_kerusakan} >= 30 AND {$rumus_kerusakan} <= 45 then 1 else 0 end) as r_guru_rusak_sedang,
                                        SUM(CASE WHEN pr.jenis_prasarana_id = 23 and {$rumus_kerusakan} > 45 then 1 else 0 end) as r_guru_rusak_berat,";
                    break;
                
                case 'r_kepsek':
                    $kolom_kerusakan = "SUM(CASE WHEN pr.jenis_prasarana_id = 24 and {$rumus_kerusakan} = 0 then 1 else 0 end) as r_kepsek_baik,
                                        SUM(CASE WHEN pr.jenis_prasarana_id = 24 and {$rumus_kerusakan} > 0 AND {$rumus_kerusakan} < 30 then 1 else 0 end) as r_kepsek_rusak_ringan,
                                        SUM(CASE WHEN pr.jenis_prasarana_id = 24 and {$rumus_kerusakan} >= 30 AND {$rumus_kerusakan} <= 45 then 1 else 0 end) as r_kepsek_rusak_sedang,
                                        SUM(CASE WHEN pr.jenis_prasarana_id = 24 and {$rumus_kerusakan} > 45 then 1 else 0 end) as r_kepsek_rusak_berat,";
                    break;
                
                case 'lab_kimia':
                    $kolom_kerusakan = "SUM(CASE WHEN pr.jenis_prasarana_id = 3 and {$rumus_kerusakan} = 0 then 1 else 0 end) as lab_kimia_baik,
                                        SUM(CASE WHEN pr.jenis_prasarana_id = 3 and {$rumus_kerusakan} > 0 AND {$rumus_kerusakan} < 30 then 1 else 0 end) as lab_kimia_rusak_ringan,
                                        SUM(CASE WHEN pr.jenis_prasarana_id = 3 and {$rumus_kerusakan} >= 30 AND {$rumus_kerusakan} <= 45 then 1 else 0 end) as lab_kimia_rusak_sedang,
                                        SUM(CASE WHEN pr.jenis_prasarana_id = 3 and {$rumus_kerusakan} > 45 then 1 else 0 end) as lab_kimia_rusak_berat,";
                    break;
                
                case 'lab_fisika':
                    $kolom_kerusakan = "SUM(CASE WHEN pr.jenis_prasarana_id = 4 and {$rumus_kerusakan} = 0 then 1 else 0 end) as lab_fisika_baik,
                                        SUM(CASE WHEN pr.jenis_prasarana_id = 4 and {$rumus_kerusakan} > 0 AND {$rumus_kerusakan} < 30 then 1 else 0 end) as lab_fisika_rusak_ringan,
                                        SUM(CASE WHEN pr.jenis_prasarana_id = 4 and {$rumus_kerusakan} >= 30 AND {$rumus_kerusakan} <= 45 then 1 else 0 end) as lab_fisika_rusak_sedang,
                                        SUM(CASE WHEN pr.jenis_prasarana_id = 4 and {$rumus_kerusakan} > 45 then 1 else 0 end) as lab_fisika_rusak_berat,";
                    break;
                
                case 'lab_biologi':
                    $kolom_kerusakan = "SUM(CASE WHEN pr.jenis_prasarana_id = 5 and {$rumus_kerusakan} = 0 then 1 else 0 end) as lab_biologi_baik,
                                        SUM(CASE WHEN pr.jenis_prasarana_id = 5 and {$rumus_kerusakan} > 0 AND {$rumus_kerusakan} < 30 then 1 else 0 end) as lab_biologi_rusak_ringan,
                                        SUM(CASE WHEN pr.jenis_prasarana_id = 5 and {$rumus_kerusakan} >= 30 AND {$rumus_kerusakan} <= 45 then 1 else 0 end) as lab_biologi_rusak_sedang,
                                        SUM(CASE WHEN pr.jenis_prasarana_id = 5 and {$rumus_kerusakan} > 45 then 1 else 0 end) as lab_biologi_rusak_berat,";
                    break;
                
                default:
                    $kolom_kerusakan = "SUM(CASE WHEN pr.jenis_prasarana_id = 1 and {$rumus_kerusakan} = 0 then 1 else 0 end) as r_kelas_baik,
                                        SUM(CASE WHEN pr.jenis_prasarana_id = 1 and {$rumus_kerusakan} > 0 AND {$rumus_kerusakan} < 30 then 1 else 0 end) as r_kelas_rusak_ringan,
                                        SUM(CASE WHEN pr.jenis_prasarana_id = 1 and {$rumus_kerusakan} >= 30 AND {$rumus_kerusakan} <= 45 then 1 else 0 end) as r_kelas_rusak_sedang,
                                        SUM(CASE WHEN pr.jenis_prasarana_id = 1 and {$rumus_kerusakan} > 45 then 1 else 0 end) as r_kelas_rusak_berat,
                                        
                                        SUM(CASE WHEN pr.jenis_prasarana_id = 16 and {$rumus_kerusakan} = 0 then 1 else 0 end) as bengkel_baik,
                                        SUM(CASE WHEN pr.jenis_prasarana_id = 16 and {$rumus_kerusakan} > 0 AND {$rumus_kerusakan} < 30 then 1 else 0 end) as bengkel_rusak_ringan,
                                        SUM(CASE WHEN pr.jenis_prasarana_id = 16 and {$rumus_kerusakan} >= 30 AND {$rumus_kerusakan} <= 45 then 1 else 0 end) as bengkel_rusak_sedang,
                                        SUM(CASE WHEN pr.jenis_prasarana_id = 16 and {$rumus_kerusakan} > 45 then 1 else 0 end) as bengkel_rusak_berat,
                                        
                                        SUM(CASE WHEN pr.jenis_prasarana_id = 10 and {$rumus_kerusakan} = 0 then 1 else 0 end) as perpustakaan_baik,
                                        SUM(CASE WHEN pr.jenis_prasarana_id = 10 and {$rumus_kerusakan} > 0 AND {$rumus_kerusakan} < 30 then 1 else 0 end) as perpustakaan_rusak_ringan,
                                        SUM(CASE WHEN pr.jenis_prasarana_id = 10 and {$rumus_kerusakan} >= 30 AND {$rumus_kerusakan} <= 45 then 1 else 0 end) as perpustakaan_rusak_sedang,
                                        SUM(CASE WHEN pr.jenis_prasarana_id = 10 and {$rumus_kerusakan} > 45 then 1 else 0 end) as perpustakaan_rusak_berat,
                                    
                                        SUM(CASE WHEN pr.jenis_prasarana_id = 23 and {$rumus_kerusakan} = 0 then 1 else 0 end) as r_guru_baik,
                                        SUM(CASE WHEN pr.jenis_prasarana_id = 23 and {$rumus_kerusakan} > 0 AND {$rumus_kerusakan} < 30 then 1 else 0 end) as r_guru_rusak_ringan,
                                        SUM(CASE WHEN pr.jenis_prasarana_id = 23 and {$rumus_kerusakan} >= 30 AND {$rumus_kerusakan} <= 45 then 1 else 0 end) as r_guru_rusak_sedang,
                                        SUM(CASE WHEN pr.jenis_prasarana_id = 23 and {$rumus_kerusakan} > 45 then 1 else 0 end) as r_guru_rusak_berat,
                                    
                                        SUM(CASE WHEN pr.jenis_prasarana_id = 24 and {$rumus_kerusakan} = 0 then 1 else 0 end) as r_kepsek_baik,
                                        SUM(CASE WHEN pr.jenis_prasarana_id = 24 and {$rumus_kerusakan} > 0 AND {$rumus_kerusakan} < 30 then 1 else 0 end) as r_kepsek_rusak_ringan,
                                        SUM(CASE WHEN pr.jenis_prasarana_id = 24 and {$rumus_kerusakan} >= 30 AND {$rumus_kerusakan} <= 45 then 1 else 0 end) as r_kepsek_rusak_sedang,
                                        SUM(CASE WHEN pr.jenis_prasarana_id = 24 and {$rumus_kerusakan} > 45 then 1 else 0 end) as r_kepsek_rusak_berat,
                                    
                                        SUM(CASE WHEN pr.jenis_prasarana_id = 3 and {$rumus_kerusakan} = 0 then 1 else 0 end) as lab_kimia_baik,
                                        SUM(CASE WHEN pr.jenis_prasarana_id = 3 and {$rumus_kerusakan} > 0 AND {$rumus_kerusakan} < 30 then 1 else 0 end) as lab_kimia_rusak_ringan,
                                        SUM(CASE WHEN pr.jenis_prasarana_id = 3 and {$rumus_kerusakan} >= 30 AND {$rumus_kerusakan} <= 45 then 1 else 0 end) as lab_kimia_rusak_sedang,
                                        SUM(CASE WHEN pr.jenis_prasarana_id = 3 and {$rumus_kerusakan} > 45 then 1 else 0 end) as lab_kimia_rusak_berat,
                                    
                                        SUM(CASE WHEN pr.jenis_prasarana_id = 4 and {$rumus_kerusakan} = 0 then 1 else 0 end) as lab_fisika_baik,
                                        SUM(CASE WHEN pr.jenis_prasarana_id = 4 and {$rumus_kerusakan} > 0 AND {$rumus_kerusakan} < 30 then 1 else 0 end) as lab_fisika_rusak_ringan,
                                        SUM(CASE WHEN pr.jenis_prasarana_id = 4 and {$rumus_kerusakan} >= 30 AND {$rumus_kerusakan} <= 45 then 1 else 0 end) as lab_fisika_rusak_sedang,
                                        SUM(CASE WHEN pr.jenis_prasarana_id = 4 and {$rumus_kerusakan} > 45 then 1 else 0 end) as lab_fisika_rusak_berat,
                                    
                                        SUM(CASE WHEN pr.jenis_prasarana_id = 5 and {$rumus_kerusakan} = 0 then 1 else 0 end) as lab_biologi_baik,
                                        SUM(CASE WHEN pr.jenis_prasarana_id = 5 and {$rumus_kerusakan} > 0 AND {$rumus_kerusakan} < 30 then 1 else 0 end) as lab_biologi_rusak_ringan,
                                        SUM(CASE WHEN pr.jenis_prasarana_id = 5 and {$rumus_kerusakan} >= 30 AND {$rumus_kerusakan} <= 45 then 1 else 0 end) as lab_biologi_rusak_sedang,
                                        SUM(CASE WHEN pr.jenis_prasarana_id = 5 and {$rumus_kerusakan} > 45 then 1 else 0 end) as lab_biologi_rusak_berat,";
                    break;
            }

            $sql = "SELECT
                        ROW_NUMBER() OVER (ORDER BY {$col_wilayah}) as 'no',
                        {$col_wilayah} as nama,
                        {$col_kode} as kode_wilayah,
                        {$col_id_level} as id_level_wilayah,
                        {$col_mst_kode} as mst_kode_wilayah,
                        {$col_mst_kode_induk}
                        {$induk}
                        {$kolom_kerusakan}
                        getdate() as tanggal_rekap_terakhir
                    FROM
                        ruang pr with(nolock)
                    JOIN ruang_longitudinal prl with(nolock) ON prl.id_ruang = pr.id_ruang
                    JOIN bangunan with(nolock) on pr.id_bangunan = bangunan.id_bangunan
                    JOIN bangunan_longitudinal with(nolock) on bangunan.id_bangunan = bangunan_longitudinal.id_bangunan
                    JOIN sekolah s with(nolock) ON pr.sekolah_id = s.sekolah_id
                    JOIN ref.mst_wilayah w1 with(nolock) ON LEFT (s.kode_wilayah, 6) = w1.kode_wilayah
                    JOIN ref.mst_wilayah w2 with(nolock) ON w1.mst_kode_wilayah = w2.kode_wilayah
                    JOIN ref.mst_wilayah w3 with(nolock) ON w2.mst_kode_wilayah = w3.kode_wilayah
                    WHERE
                        pr.Soft_delete = 0
                    AND prl.Soft_delete = 0
                    AND s.Soft_delete = 0
                    AND prl.semester_id = ".($request->input('semester_id') ? $request->input('semester_id') : 20191)."     
                    AND prl.berfungsi = 1
                    AND bangunan_longitudinal.semester_id = ".($request->input('semester_id') ? $request->input('semester_id') : 20191)."       
                    AND bangunan.id_hapus_buku is null
                    AND bangunan_longitudinal.Soft_delete = 0
                    AND bangunan.Soft_delete = 0
                    {$params_wilayah}
                    {$params_status}
                    {$param_keyword}
                    {$param_bentuk}
                    GROUP BY
                        {$col_wilayah},
                        {$col_kode},
                        {$col_id_level},
                        {$col_mst_kode_induk_group}
                        {$induk_group}
                        {$col_mst_kode}
                    ORDER BY
                        {$col_wilayah} ASC";
            
            // return $sql;die;
            $fetch = DB::connection('sqlsrv')
            ->select(DB::raw($sql));

            $return = array();
            $return['rows'] = $fetch;
            $return['total'] = sizeof($fetch);

            Redis::set($keyStr, json_encode($return));
            Redis::expire( $keyStr, 86400);

            return Redis::get($keyStr);
        
        }else{

            return Redis::get($keyStr);
        }
    }

    public function getRekapSarprasRingkasan(Request $request){
        $jenis_sarpras_jumlah = $request->input('jenis_sarpras_jumlah') ? $request->input('jenis_sarpras_jumlah') : 'umum';
        
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

        if($jenis_sarpras_jumlah == 'lab'){
			$kolom_rekap = 'isnull(sum(lab_ipa),0) as lab_ipa,
							isnull(sum(lab_kimia),0) as lab_kimia,
							isnull(sum(lab_fisika),0) as lab_fisika,
							isnull(sum(lab_biologi),0) as lab_biologi,
							isnull(sum(lab_ips),0) as lab_ips,
							isnull(sum(lab_bahasa),0) as lab_bahasa,
							isnull(sum(lab_komputer),0) as lab_komputer,
							isnull(sum(lab_multimedia),0) as lab_multimedia,';
		}else if($jenis_sarpras_jumlah == 'umum'){
			$kolom_rekap = 'isnull(sum(r_kelas),0) as r_kelas,
							isnull(sum(r_guru),0) as r_guru,
							isnull(sum(r_kepsek),0) as r_kepsek,
							isnull(sum(perpustakaan),0) as perpustakaan,
							isnull(sum(perpustakaan_multimedia),0) as perpustakaan_multimedia,
							isnull(sum(r_keterampilan),0) as r_keterampilan,
							isnull(sum(aula),0) as aula,
							';
		}else if($jenis_sarpras_jumlah == 'lab_komputer'){
			$kolom_rekap = 'isnull(sum(lab_komputer),0) as jumlah_lab_komputer,
							isnull(sum(sarana.jumlah_komputer),0) as jumlah_komputer,
							isnull(sum(pd),0) as jumlah_pd,
							';
		}else if($jenis_sarpras_jumlah == 'km'){
			$kolom_rekap = 'isnull(sum(km_guru_laki),0) as km_guru_laki,
							isnull(sum(km_guru_perempuan),0) as km_guru_perempuan,
							isnull(sum(km_siswa_laki),0) as km_siswa_laki,
							isnull(sum(km_siswa_perempuan),0) as km_siswa_perempuan,
							';
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
            {$kolom_rekap}
            min(tanggal) as tanggal_rekap_terakhir
        FROM
            rekap_sekolah WITH (NOLOCK)
        LEFT OUTER JOIN rekap_sekolah_sarpras rekap_sarpras on rekap_sarpras.sekolah_id = rekap_sekolah.sekolah_id and rekap_sarpras.semester_id = '".$request->input('semester_id')."'
        LEFT OUTER JOIN (SELECT
            sekolah_id,
            SUM (jumlah_komputer) AS jumlah_komputer
        FROM
            rekap_sekolah_sarana
        WHERE
            jenis_prasarana_id = 8
        AND semester_id = '".$request->input('semester_id')."'
        GROUP BY
            sekolah_id) sarana on sarana.sekolah_id = rekap_sekolah.sekolah_id
        WHERE
            rekap_sekolah.semester_id = '".$request->input('semester_id')."'
        AND rekap_sekolah.tahun_ajaran_id = '".$request->input('tahun_ajaran_id')."'
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

        $fetch = DB::connection('sqlsrv_2')
        ->select(DB::raw($sql));

        $return = array();
        $return['rows'] = $fetch;
        $return['total'] = sizeof($fetch);

        return $return;
    }
}