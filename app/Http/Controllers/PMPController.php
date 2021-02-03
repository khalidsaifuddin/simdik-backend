<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class PMPController extends Controller
{

    public function getRekapPMP(Request $request){
        switch ($request->input('id_level_wilayah') ? $request->input('id_level_wilayah') : '0') {
            case "0":
                $params = 'mst_kode_wilayah_propinsi';
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
                $params = 'propinsi';
                $mst_kode_wilayah_induk = 'rekap_sekolah.mst_kode_wilayah_propinsi as mst_kode_wilayah_induk,';
                $mst_kode_wilayah_induk_group = 'rekap_sekolah.mst_kode_wilayah_propinsi,';
                $param_kode_wilayah = "AND rekap_sekolah.kode_wilayah_propinsi = '".$request->input('kode_wilayah')."'";

                $add_induk_provinsi = "rekap_sekolah.propinsi as induk_propinsi,";
                $add_kode_wilayah_induk_provinsi = "rekap_sekolah.kode_wilayah_propinsi as kode_wilayah_induk_propinsi,";
                $add_induk_kabupaten = "null as induk_kabupaten,";
                $add_kode_wilayah_induk_kabupaten = "null as kode_wilayah_induk_kabupaten,";

                $add_group_induk_provinsi = "rekap_sekolah.propinsi,";
                $add_group_kode_wilayah_induk_provinsi = "rekap_sekolah.kode_wilayah_propinsi";
                $add_group_induk_kabupaten = "";
                $add_group_kode_wilayah_induk_kabupaten = "";
                break;
            case "2":
                $params = 'kabupaten';
                $mst_kode_wilayah_induk = 'mst_kode_wilayah_kabupaten as mst_kode_wilayah_induk,';
                $mst_kode_wilayah_induk_group = 'mst_kode_wilayah_kabupaten,';
                $param_kode_wilayah = "AND rekap_sekolah.kode_wilayah_kabupaten = '".$request->input('kode_wilayah')."'";

                $add_induk_provinsi = "rekap_sekolah.propinsi as induk_propinsi,";
                $add_kode_wilayah_induk_provinsi = "rekap_sekolah.kode_wilayah_propinsi as kode_wilayah_induk_propinsi,";
                $add_induk_kabupaten = "rekap_sekolah.kabupaten as induk_kabupaten,";
                $add_kode_wilayah_induk_kabupaten = "rekap_sekolah.kode_wilayah_kabupaten as kode_wilayah_induk_kabupaten,";

                $add_group_induk_provinsi = "rekap_sekolah.propinsi,";
                $add_group_kode_wilayah_induk_provinsi = "rekap_sekolah.kode_wilayah_propinsi,";
                $add_group_induk_kabupaten = "rekap_sekolah.kabupaten,";
                $add_group_kode_wilayah_induk_kabupaten = "rekap_sekolah.kode_wilayah_kabupaten";
                break;
            default:
                break;
        }

        if($request->input('bentuk_pendidikan_id')){
            $arrBentuk = explode("-", $request->input('bentuk_pendidikan_id'));
            $strBentuk = "(";

            for ($iBentuk=0; $iBentuk < sizeof($arrBentuk); $iBentuk++) { 
                if($arrBentuk[$iBentuk] == '13'){
                    $strBentuk .= "13,";
                }else if($arrBentuk[$iBentuk] == '5'){
                    $strBentuk .= "5,";
                }else if($arrBentuk[$iBentuk] == '6'){
                    $strBentuk .= "6,";
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
            
            sum(1) as sekolah_total,
            sum(case when pmp.hitung_rapor_mutu > 0 and bentuk_pendidikan_id in (5,6,13,15) then 1 else 0 end) as hitung_rapor_total,
            (sum(case when bentuk_pendidikan_id in (5,6,13,15) then 1 else 0 end) - sum(case when pmp.hitung_rapor_mutu > 0 and bentuk_pendidikan_id in (5,6,13,15) then 1 else 0 end)) as sisa_hitung_rapor_total,
            (case when sum(case when bentuk_pendidikan_id in (5,6,13,15) then 1 else 0 end) > 0 then (sum(case when pmp.hitung_rapor_mutu > 0 and bentuk_pendidikan_id in (5,6,13,15) then 1 else 0 end) / cast(sum(case when bentuk_pendidikan_id in (5,6,13,15) then 1 else 0 end) as float) * 100) else 1 end) as persen_hitung_rapor_total,
            sum(case when pmp.jumlah_kirim > 0 and bentuk_pendidikan_id in (5,6,13,15) then 1 else 0 end) as kirim_total,
            (sum(case when bentuk_pendidikan_id in (5,6,13,15) then 1 else 0 end) - sum(case when pmp.jumlah_kirim > 0 and bentuk_pendidikan_id in (5,6,13,15) then 1 else 0 end)) as sisa_kirim_total,
            (case when sum(case when bentuk_pendidikan_id in (5,6,13,15) then 1 else 0 end) > 0 then (sum(case when pmp.jumlah_kirim > 0 and bentuk_pendidikan_id in (5,6,13,15) then 1 else 0 end) / cast(sum(case when bentuk_pendidikan_id in (5,6,13,15) then 1 else 0 end) as float) * 100) else 1 end) as persen_kirim_total,

            sum(case when bentuk_pendidikan_id IN (5) then 1 else 0 end) as sekolah_sd,
            sum(case when pmp.hitung_rapor_mutu > 0 and bentuk_pendidikan_id IN (5) then 1 else 0 end) as hitung_rapor_sd,
            (sum(case when bentuk_pendidikan_id IN (5) then 1 else 0 end) - sum(case when pmp.hitung_rapor_mutu > 0 and bentuk_pendidikan_id IN (5) then 1 else 0 end)) as sisa_hitung_rapor_sd,
            (case when sum(case when bentuk_pendidikan_id IN (5) then 1 else 0 end) > 0 then (sum(case when pmp.hitung_rapor_mutu > 0 and bentuk_pendidikan_id IN (5) then 1 else 0 end) / cast(sum(case when bentuk_pendidikan_id = 5 then 1 else 0 end) as float) * 100) else 0 end) as persen_hitung_rapor_sd,
            -- sum(case when bentuk_pendidikan_id IN (5) then 1 else 0 end) as sekolah_sd,
            sum(case when pmp.jumlah_kirim > 0 and bentuk_pendidikan_id IN (5) then 1 else 0 end) as kirim_sd,
            (sum(case when bentuk_pendidikan_id IN (5) then 1 else 0 end) - sum(case when pmp.jumlah_kirim > 0 and bentuk_pendidikan_id IN (5) then 1 else 0 end)) as sisa_kirim_sd,
            (case when sum(case when bentuk_pendidikan_id IN (5) then 1 else 0 end) > 0 then (sum(case when pmp.jumlah_kirim > 0 and bentuk_pendidikan_id IN (5) then 1 else 0 end) / cast(sum(case when bentuk_pendidikan_id = 5 then 1 else 0 end) as float) * 100) else 0 end) as persen_kirim_sd,
            
            sum(case when bentuk_pendidikan_id IN (6) then 1 else 0 end) as sekolah_smp,
            sum(case when pmp.hitung_rapor_mutu > 0 and bentuk_pendidikan_id IN (6) then 1 else 0 end) as hitung_rapor_smp,
            (sum(case when bentuk_pendidikan_id IN (6) then 1 else 0 end) - sum(case when pmp.hitung_rapor_mutu > 0 and bentuk_pendidikan_id IN (6) then 1 else 0 end)) as sisa_hitung_rapor_smp,
            (case when sum(case when bentuk_pendidikan_id IN (6) then 1 else 0 end) > 0 then (sum(case when pmp.hitung_rapor_mutu > 0 and bentuk_pendidikan_id IN (6) then 1 else 0 end) / cast(sum(case when bentuk_pendidikan_id = 6 then 1 else 0 end) as float) * 100) else 0 end) as persen_hitung_rapor_smp,
            -- sum(case when bentuk_pendidikan_id IN (6) then 1 else 0 end) as sekolah_smp,
            sum(case when pmp.jumlah_kirim > 0 and bentuk_pendidikan_id IN (6) then 1 else 0 end) as kirim_smp,
            (sum(case when bentuk_pendidikan_id IN (6) then 1 else 0 end) - sum(case when pmp.jumlah_kirim > 0 and bentuk_pendidikan_id IN (6) then 1 else 0 end)) as sisa_kirim_smp,
            (case when sum(case when bentuk_pendidikan_id IN (6) then 1 else 0 end) > 0 then (sum(case when pmp.jumlah_kirim > 0 and bentuk_pendidikan_id IN (6) then 1 else 0 end) / cast(sum(case when bentuk_pendidikan_id = 6 then 1 else 0 end) as float) * 100) else 0 end) as persen_kirim_smp,
            
            sum(case when bentuk_pendidikan_id IN (13) then 1 else 0 end) as sekolah_sma,
            sum(case when pmp.hitung_rapor_mutu > 0 and bentuk_pendidikan_id IN (13) then 1 else 0 end) as hitung_rapor_sma,
            (sum(case when bentuk_pendidikan_id IN (13) then 1 else 0 end) - sum(case when pmp.hitung_rapor_mutu > 0 and bentuk_pendidikan_id IN (13) then 1 else 0 end)) as sisa_hitung_rapor_sma,
            (case when sum(case when bentuk_pendidikan_id IN (13) then 1 else 0 end) > 0 then (sum(case when pmp.hitung_rapor_mutu > 0 and bentuk_pendidikan_id IN (13) then 1 else 0 end) / cast(sum(case when bentuk_pendidikan_id = 13 then 1 else 0 end) as float) * 100) else 0 end) as persen_hitung_rapor_sma,
            -- sum(case when bentuk_pendidikan_id IN (13) then 1 else 0 end) as sekolah_sma,
            sum(case when pmp.jumlah_kirim > 0 and bentuk_pendidikan_id IN (13) then 1 else 0 end) as kirim_sma,
            (sum(case when bentuk_pendidikan_id IN (13) then 1 else 0 end) - sum(case when pmp.jumlah_kirim > 0 and bentuk_pendidikan_id IN (13) then 1 else 0 end)) as sisa_kirim_sma,
            (case when sum(case when bentuk_pendidikan_id IN (13) then 1 else 0 end) > 0 then (sum(case when pmp.jumlah_kirim > 0 and bentuk_pendidikan_id IN (13) then 1 else 0 end) / cast(sum(case when bentuk_pendidikan_id = 13 then 1 else 0 end) as float) * 100) else 0 end) as persen_kirim_sma,
            
            sum(case when bentuk_pendidikan_id = 15 then 1 else 0 end) as sekolah_smk,
            sum(case when pmp.hitung_rapor_mutu > 0 and bentuk_pendidikan_id = 15 then 1 else 0 end) as hitung_rapor_smk,
            (sum(case when bentuk_pendidikan_id = 15 then 1 else 0 end) - sum(case when pmp.hitung_rapor_mutu > 0 and bentuk_pendidikan_id = 15 then 1 else 0 end)) as sisa_hitung_rapor_smk,
            (case when sum(case when bentuk_pendidikan_id = 15 then 1 else 0 end) > 0 then (sum(case when pmp.hitung_rapor_mutu > 0 and bentuk_pendidikan_id = 15 then 1 else 0 end) / cast(sum(case when bentuk_pendidikan_id = 15 then 1 else 0 end) as float) * 100) else 0 end)   as persen_hitung_rapor_smk,
            -- sum(case when bentuk_pendidikan_id = 15 then 1 else 0 end) as sekolah_smk,
            sum(case when pmp.jumlah_kirim > 0 and bentuk_pendidikan_id = 15 then 1 else 0 end) as kirim_smk,
            (sum(case when bentuk_pendidikan_id = 15 then 1 else 0 end) - sum(case when pmp.jumlah_kirim > 0 and bentuk_pendidikan_id = 15 then 1 else 0 end)) as sisa_kirim_smk,
            (case when sum(case when bentuk_pendidikan_id = 15 then 1 else 0 end) > 0 then (sum(case when pmp.jumlah_kirim > 0 and bentuk_pendidikan_id = 15 then 1 else 0 end) / cast(sum(case when bentuk_pendidikan_id = 15 then 1 else 0 end) as float) * 100) else 0 end)   as persen_kirim_smk,

            max(pmp.sync_terakhir) as tanggal_rekap_terakhir
        FROM
            rekap_sekolah
        JOIN rekap_pengiriman_pmp pmp on pmp.sekolah_id = rekap_sekolah.sekolah_id and pmp.soft_delete_pmp = 0 and pmp.tahun_ajaran_id = '".($request->input('tahun_ajaran_id') ? $request->input('tahun_ajaran_id') : substr(($request->input('semester_id') ? $request->input('semester_id') : '20201'),0,4))."'
        WHERE
            semester_id = '".($request->input('semester_id') ? $request->input('semester_id') : '20201')."'
        AND rekap_sekolah.tahun_ajaran_id = '".($request->input('tahun_ajaran_id') ? $request->input('tahun_ajaran_id') : substr(($request->input('semester_id') ? $request->input('semester_id') : '20201'),0,4))."'
        {$param_kode_wilayah}
        {$param_bentuk}
        {$param_status}
        {$param_keyword}
        AND rekap_sekolah.bentuk_pendidikan_id != 29
        AND soft_delete = 0
        GROUP BY
            {$params}";

        // return $sql;die;

        $fetch = DB::connection('sqlsrv_2')
        ->select(DB::raw($sql));

        $return = array();
        $return['rows'] = $fetch;
        $return['total'] = sizeof($fetch);

        return $return;
    }

    public function getRekapProgresRaporMutu(Request $request){
        // return "oke";
        switch ($request->input('id_level_wilayah') ? $request->input('id_level_wilayah') : '0') {
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
                $mst_kode_wilayah_induk = 'rekap_sekolah.mst_kode_wilayah_propinsi as mst_kode_wilayah_induk,';
                $mst_kode_wilayah_induk_group = 'rekap_sekolah.mst_kode_wilayah_propinsi,';
                $param_kode_wilayah = "AND rekap_sekolah.kode_wilayah_propinsi = '".$request->input('kode_wilayah')."'";

                $add_induk_provinsi = "rekap_sekolah.propinsi as induk_propinsi,";
                $add_kode_wilayah_induk_provinsi = "rekap_sekolah.kode_wilayah_propinsi as kode_wilayah_induk_propinsi,";
                $add_induk_kabupaten = "null as induk_kabupaten,";
                $add_kode_wilayah_induk_kabupaten = "null as kode_wilayah_induk_kabupaten,";

                $add_group_induk_provinsi = "rekap_sekolah.propinsi,";
                $add_group_kode_wilayah_induk_provinsi = "rekap_sekolah.kode_wilayah_propinsi";
                $add_group_induk_kabupaten = "";
                $add_group_kode_wilayah_induk_kabupaten = "";
                break;
            case "2":
                $params = 'kecamatan';
                $mst_kode_wilayah_induk = 'mst_kode_wilayah_kabupaten as mst_kode_wilayah_induk,';
                $mst_kode_wilayah_induk_group = 'mst_kode_wilayah_kabupaten,';
                $param_kode_wilayah = "AND rekap_sekolah.kode_wilayah_kabupaten = '".$request->input('kode_wilayah')."'";

                $add_induk_provinsi = "rekap_sekolah.propinsi as induk_propinsi,";
                $add_kode_wilayah_induk_provinsi = "rekap_sekolah.kode_wilayah_propinsi as kode_wilayah_induk_propinsi,";
                $add_induk_kabupaten = "rekap_sekolah.kabupaten as induk_kabupaten,";
                $add_kode_wilayah_induk_kabupaten = "rekap_sekolah.kode_wilayah_kabupaten as kode_wilayah_induk_kabupaten,";

                $add_group_induk_provinsi = "rekap_sekolah.propinsi,";
                $add_group_kode_wilayah_induk_provinsi = "rekap_sekolah.kode_wilayah_propinsi,";
                $add_group_induk_kabupaten = "rekap_sekolah.kabupaten,";
                $add_group_kode_wilayah_induk_kabupaten = "rekap_sekolah.kode_wilayah_kabupaten";
                break;
            default:
                break;
        }

        if($request->input('bentuk_pendidikan_id')){
            $arrBentuk = explode("-", $request->input('bentuk_pendidikan_id'));
            $strBentuk = "(";

            for ($iBentuk=0; $iBentuk < sizeof($arrBentuk); $iBentuk++) { 
                if($arrBentuk[$iBentuk] == '13'){
                    $strBentuk .= "13,";
                }else if($arrBentuk[$iBentuk] == '5'){
                    $strBentuk .= "5,";
                }else if($arrBentuk[$iBentuk] == '6'){
                    $strBentuk .= "6,";
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
            rekap_sekolah.kode_wilayah_{$params} as kode_wilayah,
            rekap_sekolah.mst_kode_wilayah_{$params} as mst_kode_wilayah,
            {$mst_kode_wilayah_induk}
            rekap_sekolah.id_level_wilayah_{$params} as id_level_wilayah,
            {$add_induk_provinsi}
            {$add_kode_wilayah_induk_provinsi}
            {$add_induk_kabupaten}
            {$add_kode_wilayah_induk_kabupaten}
            
            sum(1) as sekolah_total,
            sum(case when pmp.hitung_rapor_mutu > 0 and bentuk_pendidikan_id in (5,6,13,15) then 1 else 0 end) as hitung_rapor_total,
            (sum(case when bentuk_pendidikan_id in (5,6,13,15) then 1 else 0 end) - sum(case when pmp.hitung_rapor_mutu > 0 and bentuk_pendidikan_id in (5,6,13,15) then 1 else 0 end)) as sisa_total,
            (case when sum(case when bentuk_pendidikan_id in (5,6,13,15) then 1 else 0 end) > 0 then (sum(case when pmp.hitung_rapor_mutu > 0 and bentuk_pendidikan_id in (5,6,13,15) then 1 else 0 end) / cast(sum(case when bentuk_pendidikan_id in (5,6,13,15) then 1 else 0 end) as float) * 100) else 1 end) as persen_total,

            sum(case when bentuk_pendidikan_id IN (5) then 1 else 0 end) as sekolah_sd,
            sum(case when pmp.hitung_rapor_mutu > 0 and bentuk_pendidikan_id IN (5) then 1 else 0 end) as hitung_rapor_sd,
            (sum(case when bentuk_pendidikan_id IN (5) then 1 else 0 end) - sum(case when pmp.hitung_rapor_mutu > 0 and bentuk_pendidikan_id IN (5) then 1 else 0 end)) as sisa_sd,
            (case when sum(case when bentuk_pendidikan_id IN (5) then 1 else 0 end) > 0 then (sum(case when pmp.hitung_rapor_mutu > 0 and bentuk_pendidikan_id IN (5) then 1 else 0 end) / cast(sum(case when bentuk_pendidikan_id = 5 then 1 else 0 end) as float) * 100) else 0 end) as persen_sd,
            
            sum(case when bentuk_pendidikan_id IN (6) then 1 else 0 end) as sekolah_smp,
            sum(case when pmp.hitung_rapor_mutu > 0 and bentuk_pendidikan_id IN (6) then 1 else 0 end) as hitung_rapor_smp,
            (sum(case when bentuk_pendidikan_id IN (6) then 1 else 0 end) - sum(case when pmp.hitung_rapor_mutu > 0 and bentuk_pendidikan_id IN (6) then 1 else 0 end)) as sisa_smp,
            (case when sum(case when bentuk_pendidikan_id IN (6) then 1 else 0 end) > 0 then (sum(case when pmp.hitung_rapor_mutu > 0 and bentuk_pendidikan_id IN (6) then 1 else 0 end) / cast(sum(case when bentuk_pendidikan_id = 6 then 1 else 0 end) as float) * 100) else 0 end) as persen_smp,
            
            sum(case when bentuk_pendidikan_id IN (13) then 1 else 0 end) as sekolah_sma,
            sum(case when pmp.hitung_rapor_mutu > 0 and bentuk_pendidikan_id IN (13) then 1 else 0 end) as hitung_rapor_sma,
            (sum(case when bentuk_pendidikan_id IN (13) then 1 else 0 end) - sum(case when pmp.hitung_rapor_mutu > 0 and bentuk_pendidikan_id IN (13) then 1 else 0 end)) as sisa_sma,
            (case when sum(case when bentuk_pendidikan_id IN (13) then 1 else 0 end) > 0 then (sum(case when pmp.hitung_rapor_mutu > 0 and bentuk_pendidikan_id IN (13) then 1 else 0 end) / cast(sum(case when bentuk_pendidikan_id = 13 then 1 else 0 end) as float) * 100) else 0 end) as persen_sma,
            
            sum(case when bentuk_pendidikan_id = 15 then 1 else 0 end) as sekolah_smk,
            sum(case when pmp.hitung_rapor_mutu > 0 and bentuk_pendidikan_id = 15 then 1 else 0 end) as hitung_rapor_smk,
            (sum(case when bentuk_pendidikan_id = 15 then 1 else 0 end) - sum(case when pmp.hitung_rapor_mutu > 0 and bentuk_pendidikan_id = 15 then 1 else 0 end)) as sisa_smk,
            (case when sum(case when bentuk_pendidikan_id = 15 then 1 else 0 end) > 0 then (sum(case when pmp.hitung_rapor_mutu > 0 and bentuk_pendidikan_id = 15 then 1 else 0 end) / cast(sum(case when bentuk_pendidikan_id = 15 then 1 else 0 end) as float) * 100) else 0 end)   as persen_smk,
            
            max(pmp.sync_terakhir) as tanggal_rekap_terakhir
        FROM
            rekap_sekolah
        JOIN rekap_pengiriman_pmp pmp on pmp.sekolah_id = rekap_sekolah.sekolah_id and pmp.soft_delete_pmp = 0 and pmp.tahun_ajaran_id = '".($request->input('tahun_ajaran_id') ? $request->input('tahun_ajaran_id') : substr(($request->input('semester_id') ? $request->input('semester_id') : '20201'),0,4))."'
        WHERE
            semester_id = '".($request->input('semester_id') ? $request->input('semester_id') : '20201')."'
        AND rekap_sekolah.tahun_ajaran_id = '".($request->input('tahun_ajaran_id') ? $request->input('tahun_ajaran_id') : substr(($request->input('semester_id') ? $request->input('semester_id') : '20201'),0,4))."'
        {$param_kode_wilayah}
        {$param_bentuk}
        {$param_status}
        {$param_keyword}
        AND rekap_sekolah.bentuk_pendidikan_id != 29
        AND soft_delete = 0
        GROUP BY
            {$params},
            rekap_sekolah.kode_wilayah_{$params},
            rekap_sekolah.mst_kode_wilayah_{$params},
            rekap_sekolah.id_level_wilayah_{$params},
            {$add_group_induk_provinsi}
            {$mst_kode_wilayah_induk_group}
            {$add_group_kode_wilayah_induk_provinsi}
            {$add_group_induk_kabupaten}
            {$add_group_kode_wilayah_induk_kabupaten}
        ORDER BY
            persen_total desc";

        // return $sql;die;

        $fetch = DB::connection('sqlsrv_2')
        ->select(DB::raw($sql));

        $return = array();
        $return['rows'] = $fetch;
        $return['total'] = sizeof($fetch);

        return $return;
    }

    public function getRaporSNP(Request $request){
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
        }

        if($request->input('bentuk_pendidikan_id')){
            $arrBentuk = explode("-", $request->input('bentuk_pendidikan_id'));
            $strBentuk = "(";

            for ($iBentuk=0; $iBentuk < sizeof($arrBentuk); $iBentuk++) { 
                if($arrBentuk[$iBentuk] == '13'){
                    $strBentuk .= "13,";
                }else if($arrBentuk[$iBentuk] == '5'){
                    $strBentuk .= "5,";
                }else if($arrBentuk[$iBentuk] == '6'){
                    $strBentuk .= "6,";
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
            AVG(ISNULL(snp.rapor_snp,0)) as rapor_snp,
            AVG(ISNULL(snp.standar_1,0)) as standar_1,
            AVG(ISNULL(snp.standar_2,0)) as standar_2,
            AVG(ISNULL(snp.standar_3,0)) as standar_3,
            AVG(ISNULL(snp.standar_4,0)) as standar_4,
            AVG(ISNULL(snp.standar_5,0)) as standar_5,
            AVG(ISNULL(snp.standar_6,0)) as standar_6,
            AVG(ISNULL(snp.standar_7,0)) as standar_7,
            AVG(ISNULL(snp.standar_8,0)) as standar_8,
            max(snp.tanggal) as tanggal_rekap_terakhir
        FROM
            rekap_sekolah
        LEFT JOIN rekap_rapor_snp snp on snp.sekolah_id = rekap_sekolah.sekolah_id and snp.semester_id = rekap_sekolah.semester_id
        WHERE
            rekap_sekolah.semester_id = '".$request->input('semester_id')."'
        AND rekap_sekolah.tahun_ajaran_id = '".($request->input('tahun_ajaran_id') ? $request->input('tahun_ajaran_id') : substr($request->input('semester_id'),0,4))."'
        {$param_kode_wilayah}
        {$param_bentuk}
        {$param_status}
        {$param_keyword}
        AND rekap_sekolah.soft_delete = 0
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
            AVG(ISNULL(snp.rapor_snp,0)) desc";

        // return $sql;die;

        $fetch = DB::connection('sqlsrv_2')
        ->select(DB::raw($sql));

        $return = array();
        $return['rows'] = $fetch;
        $return['total'] = sizeof($fetch);

        return $return;
    }

    public function ubahTimeline(Request $request){
        $sekolah_id = $request->input('sekolah_id') ? $request->input('sekolah_id') : null;
        $jenis_timeline_id = $request->input('jenis_timeline_id') ? $request->input('jenis_timeline_id') : null;

        $return = array();

        if($jenis_timeline_id){
            
            $exe = DB::connection('sqlsrv_pmp')->table('timeline')
            ->where('timeline.sekolah_id','=', $sekolah_id)
            ->where('timeline.jenis_timeline_id','>', $jenis_timeline_id)
            ->update([
                'soft_delete' => DB::raw("1"),
                'last_update' => DB::raw("DATEADD(mi, 30, getdate())")
                // 'last_update' => DB::raw("DATEADD(mi, 30, last_sync)")
                // 'last_update' => date('Y-m-d H:i:s')
            ]);     
            
            if($exe){
                $return['status'] = true;
                $return['pesan'] = 'Berhasil mengubah data';
                $return['rows'] = DB::connection('sqlsrv_pmp')->table('timeline')
                                    ->join('ref.jenis_timeline as jenis','jenis.jenis_timeline_id','=','timeline.jenis_timeline_id')
                                    ->where('timeline.sekolah_id','=', $sekolah_id)
                                    ->where('timeline.soft_delete','=', 0)
                                    ->orderBy('timeline.last_update', 'DESC')
                                    // ->take(1)
                                    // ->skip(0)
                                    ->select(
                                        'timeline.*',
                                        'jenis.nama as jenis_timeline'
                                    )->get();
            }else{
                $return['status'] = false;
                $return['pesan'] = 'Gagal mengubah data';
            }

        }else{
            $return['status'] = false;
            $return['pesan'] = 'Parameter jenis_timeline_id tidak ditemukan';
        }

        return $return;
    }

    public function getTimeline(Request $request){
        $sekolah_id = $request->input('sekolah_id') ? $request->input('sekolah_id') : null;

        $fetch = DB::connection('sqlsrv_pmp')->table('timeline')
        ->join('ref.jenis_timeline as jenis','jenis.jenis_timeline_id','=','timeline.jenis_timeline_id')
        ->where('timeline.sekolah_id','=', $sekolah_id)
        ->where('timeline.soft_delete','=', 0)
        ->orderBy('timeline.jenis_timeline_id', 'DESC')
        // ->take(1)
        // ->skip(0)
        ->select(
            'timeline.*',
            'jenis.nama as jenis_timeline'
        );

        return $fetch->get();
    }

    public function getRekapPengirimanPMP(Request $request){
        // return "oke";
        switch ($request->input('id_level_wilayah') ? $request->input('id_level_wilayah') : '0') {
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
                $mst_kode_wilayah_induk = 'rekap_sekolah.mst_kode_wilayah_propinsi as mst_kode_wilayah_induk,';
                $mst_kode_wilayah_induk_group = 'rekap_sekolah.mst_kode_wilayah_propinsi,';
                $param_kode_wilayah = "AND rekap_sekolah.kode_wilayah_propinsi = '".$request->input('kode_wilayah')."'";

                $add_induk_provinsi = "rekap_sekolah.propinsi as induk_propinsi,";
                $add_kode_wilayah_induk_provinsi = "rekap_sekolah.kode_wilayah_propinsi as kode_wilayah_induk_propinsi,";
                $add_induk_kabupaten = "null as induk_kabupaten,";
                $add_kode_wilayah_induk_kabupaten = "null as kode_wilayah_induk_kabupaten,";

                $add_group_induk_provinsi = "rekap_sekolah.propinsi,";
                $add_group_kode_wilayah_induk_provinsi = "rekap_sekolah.kode_wilayah_propinsi";
                $add_group_induk_kabupaten = "";
                $add_group_kode_wilayah_induk_kabupaten = "";
                break;
            case "2":
                $params = 'kecamatan';
                $mst_kode_wilayah_induk = 'mst_kode_wilayah_kabupaten as mst_kode_wilayah_induk,';
                $mst_kode_wilayah_induk_group = 'mst_kode_wilayah_kabupaten,';
                $param_kode_wilayah = "AND rekap_sekolah.kode_wilayah_kabupaten = '".$request->input('kode_wilayah')."'";

                $add_induk_provinsi = "rekap_sekolah.propinsi as induk_propinsi,";
                $add_kode_wilayah_induk_provinsi = "rekap_sekolah.kode_wilayah_propinsi as kode_wilayah_induk_propinsi,";
                $add_induk_kabupaten = "rekap_sekolah.kabupaten as induk_kabupaten,";
                $add_kode_wilayah_induk_kabupaten = "rekap_sekolah.kode_wilayah_kabupaten as kode_wilayah_induk_kabupaten,";

                $add_group_induk_provinsi = "rekap_sekolah.propinsi,";
                $add_group_kode_wilayah_induk_provinsi = "rekap_sekolah.kode_wilayah_propinsi,";
                $add_group_induk_kabupaten = "rekap_sekolah.kabupaten,";
                $add_group_kode_wilayah_induk_kabupaten = "rekap_sekolah.kode_wilayah_kabupaten";
                break;
            default:
                break;
        }

        if($request->input('bentuk_pendidikan_id')){
            $arrBentuk = explode("-", $request->input('bentuk_pendidikan_id'));
            $strBentuk = "(";

            for ($iBentuk=0; $iBentuk < sizeof($arrBentuk); $iBentuk++) { 
                if($arrBentuk[$iBentuk] == '13'){
                    $strBentuk .= "13,";
                }else if($arrBentuk[$iBentuk] == '5'){
                    $strBentuk .= "5,";
                }else if($arrBentuk[$iBentuk] == '6'){
                    $strBentuk .= "6,";
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
            rekap_sekolah.kode_wilayah_{$params} as kode_wilayah,
            rekap_sekolah.mst_kode_wilayah_{$params} as mst_kode_wilayah,
            {$mst_kode_wilayah_induk}
            rekap_sekolah.id_level_wilayah_{$params} as id_level_wilayah,
            {$add_induk_provinsi}
            {$add_kode_wilayah_induk_provinsi}
            {$add_induk_kabupaten}
            {$add_kode_wilayah_induk_kabupaten}
            
            sum(1) as sekolah_total,
            sum(case when pmp.jumlah_kirim > 0 and bentuk_pendidikan_id in (5,6,13,15) then 1 else 0 end) as kirim_total,
            (sum(case when bentuk_pendidikan_id in (5,6,13,15) then 1 else 0 end) - sum(case when pmp.jumlah_kirim > 0 and bentuk_pendidikan_id in (5,6,13,15) then 1 else 0 end)) as sisa_total,
            (case when sum(case when bentuk_pendidikan_id in (5,6,13,15) then 1 else 0 end) > 0 then (sum(case when pmp.jumlah_kirim > 0 and bentuk_pendidikan_id in (5,6,13,15) then 1 else 0 end) / cast(sum(case when bentuk_pendidikan_id in (5,6,13,15) then 1 else 0 end) as float) * 100) else 1 end) as persen_total,

            sum(case when bentuk_pendidikan_id IN (5) then 1 else 0 end) as sekolah_sd,
            sum(case when pmp.jumlah_kirim > 0 and bentuk_pendidikan_id IN (5) then 1 else 0 end) as kirim_sd,
            (sum(case when bentuk_pendidikan_id IN (5) then 1 else 0 end) - sum(case when pmp.jumlah_kirim > 0 and bentuk_pendidikan_id IN (5) then 1 else 0 end)) as sisa_sd,
            (case when sum(case when bentuk_pendidikan_id IN (5) then 1 else 0 end) > 0 then (sum(case when pmp.jumlah_kirim > 0 and bentuk_pendidikan_id IN (5) then 1 else 0 end) / cast(sum(case when bentuk_pendidikan_id = 5 then 1 else 0 end) as float) * 100) else 0 end) as persen_sd,
            
            sum(case when bentuk_pendidikan_id IN (6) then 1 else 0 end) as sekolah_smp,
            sum(case when pmp.jumlah_kirim > 0 and bentuk_pendidikan_id IN (6) then 1 else 0 end) as kirim_smp,
            (sum(case when bentuk_pendidikan_id IN (6) then 1 else 0 end) - sum(case when pmp.jumlah_kirim > 0 and bentuk_pendidikan_id IN (6) then 1 else 0 end)) as sisa_smp,
            (case when sum(case when bentuk_pendidikan_id IN (6) then 1 else 0 end) > 0 then (sum(case when pmp.jumlah_kirim > 0 and bentuk_pendidikan_id IN (6) then 1 else 0 end) / cast(sum(case when bentuk_pendidikan_id = 6 then 1 else 0 end) as float) * 100) else 0 end) as persen_smp,
            
            sum(case when bentuk_pendidikan_id IN (13) then 1 else 0 end) as sekolah_sma,
            sum(case when pmp.jumlah_kirim > 0 and bentuk_pendidikan_id IN (13) then 1 else 0 end) as kirim_sma,
            (sum(case when bentuk_pendidikan_id IN (13) then 1 else 0 end) - sum(case when pmp.jumlah_kirim > 0 and bentuk_pendidikan_id IN (13) then 1 else 0 end)) as sisa_sma,
            (case when sum(case when bentuk_pendidikan_id IN (13) then 1 else 0 end) > 0 then (sum(case when pmp.jumlah_kirim > 0 and bentuk_pendidikan_id IN (13) then 1 else 0 end) / cast(sum(case when bentuk_pendidikan_id = 13 then 1 else 0 end) as float) * 100) else 0 end) as persen_sma,
            
            sum(case when bentuk_pendidikan_id = 15 then 1 else 0 end) as sekolah_smk,
            sum(case when pmp.jumlah_kirim > 0 and bentuk_pendidikan_id = 15 then 1 else 0 end) as kirim_smk,
            (sum(case when bentuk_pendidikan_id = 15 then 1 else 0 end) - sum(case when pmp.jumlah_kirim > 0 and bentuk_pendidikan_id = 15 then 1 else 0 end)) as sisa_smk,
            (case when sum(case when bentuk_pendidikan_id = 15 then 1 else 0 end) > 0 then (sum(case when pmp.jumlah_kirim > 0 and bentuk_pendidikan_id = 15 then 1 else 0 end) / cast(sum(case when bentuk_pendidikan_id = 15 then 1 else 0 end) as float) * 100) else 0 end)   as persen_smk,
            
            max(pmp.sync_terakhir) as tanggal_rekap_terakhir
        FROM
            rekap_sekolah
        JOIN rekap_pengiriman_pmp pmp on pmp.sekolah_id = rekap_sekolah.sekolah_id and pmp.soft_delete_pmp = 0 and pmp.tahun_ajaran_id = '".($request->input('tahun_ajaran_id') ? $request->input('tahun_ajaran_id') : substr(($request->input('semester_id') ? $request->input('semester_id') : '20201'),0,4))."'
        WHERE
            semester_id = '".($request->input('semester_id') ? $request->input('semester_id') : '20201')."'
        AND rekap_sekolah.tahun_ajaran_id = '".($request->input('tahun_ajaran_id') ? $request->input('tahun_ajaran_id') : substr(($request->input('semester_id') ? $request->input('semester_id') : '20201'),0,4))."'
        {$param_kode_wilayah}
        {$param_bentuk}
        {$param_status}
        {$param_keyword}
        AND rekap_sekolah.bentuk_pendidikan_id != 29
        AND soft_delete = 0
        GROUP BY
            {$params},
            rekap_sekolah.kode_wilayah_{$params},
            rekap_sekolah.mst_kode_wilayah_{$params},
            rekap_sekolah.id_level_wilayah_{$params},
            {$add_group_induk_provinsi}
            {$mst_kode_wilayah_induk_group}
            {$add_group_kode_wilayah_induk_provinsi}
            {$add_group_induk_kabupaten}
            {$add_group_kode_wilayah_induk_kabupaten}
        ORDER BY
            persen_total desc";

        // return $sql;die;

        $fetch = DB::connection('sqlsrv_2')
        ->select(DB::raw($sql));

        $return = array();
        $return['rows'] = $fetch;
        $return['total'] = sizeof($fetch);

        return $return;
    }

    public function getRekapPengirimanPMPSp(Request $request){
        $sudah_kirim_saja = $request->input('sudah_kirim_saja') ? $request->input('sudah_kirim_saja') : null;
        $belum_kirim_saja = $request->input('belum_kirim_saja') ? $request->input('belum_kirim_saja') : null;

        switch ($request->input('id_level_wilayah') ? $request->input('id_level_wilayah') : "0") {
            case "0":
                $params_wilayah = 'rekap_sekolah.mst_kode_wilayah_propinsi';
                break;
            case "1":
                $params_wilayah = 'rekap_sekolah.kode_wilayah_propinsi';
                break;
            case "2":
                $params_wilayah = 'rekap_sekolah.kode_wilayah_kabupaten';
                break;
            case "3":
                $params_wilayah = 'rekap_sekolah.kode_wilayah_kecamatan';
                break;
            default:
                break;
        }

        if($request->input('bentuk_pendidikan_id')){
            $arrBentuk = explode("-", $request->input('bentuk_pendidikan_id'));
            $strBentuk = "(";

            for ($iBentuk=0; $iBentuk < sizeof($arrBentuk); $iBentuk++) { 
                if($arrBentuk[$iBentuk] == '13'){
                    $strBentuk .= "13,";
                }else if($arrBentuk[$iBentuk] == '5'){
                    $strBentuk .= "5,";
                }else if($arrBentuk[$iBentuk] == '6'){
                    $strBentuk .= "6,";
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

        if($sudah_kirim_saja == 'Y'){
            $param_sudah_kirim_saja = "AND pmp.jumlah_kirim > 0";
        }else{
            $param_sudah_kirim_saja = "";
        }

        if($belum_kirim_saja == 'Y'){
            $param_belum_kirim_saja = "AND pmp.jumlah_kirim < 1";
        }else{
            $param_belum_kirim_saja = "";
        }

        $sql_count = "SELECT
            sum(1) as total
        FROM
            rekap_sekolah WITH(NOLOCK)
        WHERE
            semester_id = '".($request->input('semester_id') ? $request->input('semester_id') : '20201')."'
        AND rekap_sekolah.tahun_ajaran_id = '".($request->input('tahun_ajaran_id') ? $request->input('tahun_ajaran_id') : substr(($request->input('semester_id') ? $request->input('semester_id') : '20201'),0,4))."'
        AND {$params_wilayah} = '".($request->input('kode_wilayah') ? $request->input('kode_wilayah') : '000000')."'
        AND soft_delete = 0
        AND rekap_sekolah.bentuk_pendidikan_id != 29
        {$param_bentuk}
        {$param_status}
        {$param_keyword}
        AND rekap_sekolah.bentuk_pendidikan_id != 29";

        $sql = "SELECT
            ROW_NUMBER() OVER (ORDER BY nama) as 'no',
            rekap_sekolah.sekolah_id,
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
                WHEN bentuk_pendidikan_id = 53 THEN 'SPK SD'
                WHEN bentuk_pendidikan_id = 54 THEN 'SPK SMP'
                WHEN bentuk_pendidikan_id = 55 THEN 'SPK SMA'
            ELSE '-' END AS bentuk,
            rekap_sekolah.kecamatan,
            rekap_sekolah.kabupaten,
            rekap_sekolah.propinsi,
            rekap_sekolah.kode_wilayah_kecamatan,
            rekap_sekolah.mst_kode_wilayah_kecamatan,
            rekap_sekolah.id_level_wilayah_kecamatan,
            rekap_sekolah.kode_wilayah_kabupaten,
            rekap_sekolah.mst_kode_wilayah_kabupaten,
            rekap_sekolah.id_level_wilayah_kabupaten,
            rekap_sekolah.kode_wilayah_propinsi,
            rekap_sekolah.mst_kode_wilayah_propinsi,
            rekap_sekolah.id_level_wilayah_propinsi,
            ISNULL(pmp.jumlah_kirim,0) as jumlah_kirim,
            ISNULL(pmp.jumlah_pengguna,0) as jumlah_pengguna,
            ISNULL(pmp.jumlah_pengguna_mengerjakan,0) as jumlah_pengguna_mengerjakan,
            ISNULL(pmp.hitung_rapor_mutu,0) as hitung_rapor_mutu,
            ISNULL(pmp.kepsek_total,0) as kepsek_total,
            ISNULL(pmp.kepsek_mengerjakan,0) as kepsek_mengerjakan,
            ISNULL(pmp.kepsek_persen,0) as kepsek_persen,
            ISNULL(pmp.pd_total,0) as pd_total,
            ISNULL(pmp.pd_mengerjakan,0) as pd_mengerjakan,
            ISNULL(pmp.pd_persen,0) as pd_persen,
            ISNULL(pmp.ptk_total,0) as ptk_total,
            ISNULL(pmp.ptk_mengerjakan,0) as ptk_mengerjakan,
            ISNULL(pmp.ptk_persen,0) as ptk_persen,
            ISNULL(pmp.komite_total,0) as komite_total,
            ISNULL(pmp.komite_mengerjakan,0) as komite_mengerjakan,
            ISNULL(pmp.komite_persen,0) as komite_persen,
            ISNULL(pmp.verifikasi_pengawas,0) as verifikasi_pengawas,
            pmp.sync_terakhir as sync_terakhir,
            rekap_sekolah.tanggal as tanggal_rekap_terakhir
        FROM
            rekap_sekolah WITH(NOLOCK)
        JOIN rekap_pengiriman_pmp pmp on pmp.sekolah_id = rekap_sekolah.sekolah_id and pmp.soft_delete_pmp = 0 and pmp.tahun_ajaran_id = '".($request->input('tahun_ajaran_id') ? $request->input('tahun_ajaran_id') : substr(($request->input('semester_id') ? $request->input('semester_id') : '20201'),0,4))."'
        WHERE
            semester_id = '".($request->input('semester_id') ? $request->input('semester_id') : '20201')."'
        AND rekap_sekolah.tahun_ajaran_id = '".($request->input('tahun_ajaran_id') ? $request->input('tahun_ajaran_id') : substr(($request->input('semester_id') ? $request->input('semester_id') : '20201'),0,4))."'
        AND {$params_wilayah} = '".($request->input('kode_wilayah') ? $request->input('kode_wilayah') : '000000')."'
        AND soft_delete = 0
        {$param_bentuk}
        {$param_status}
        {$param_keyword}
        {$param_sudah_kirim_saja}
        {$param_belum_kirim_saja}
        ";

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

    public function getRekapProgresRaporMutuSp(Request $request){
        $sudah_hitung_saja = $request->input('sudah_hitung_saja') ? $request->input('sudah_hitung_saja') : null;
        $belum_hitung_saja = $request->input('belum_hitung_saja') ? $request->input('belum_hitung_saja') : null;

        switch ($request->input('id_level_wilayah') ? $request->input('id_level_wilayah') : "0") {
            case "0":
                $params_wilayah = 'rekap_sekolah.mst_kode_wilayah_propinsi';
                break;
            case "1":
                $params_wilayah = 'rekap_sekolah.kode_wilayah_propinsi';
                break;
            case "2":
                $params_wilayah = 'rekap_sekolah.kode_wilayah_kabupaten';
                break;
            case "3":
                $params_wilayah = 'rekap_sekolah.kode_wilayah_kecamatan';
                break;
            default:
                break;
        }

        if($request->input('bentuk_pendidikan_id')){
            $arrBentuk = explode("-", $request->input('bentuk_pendidikan_id'));
            $strBentuk = "(";

            for ($iBentuk=0; $iBentuk < sizeof($arrBentuk); $iBentuk++) { 
                if($arrBentuk[$iBentuk] == '13'){
                    $strBentuk .= "13,";
                }else if($arrBentuk[$iBentuk] == '5'){
                    $strBentuk .= "5,";
                }else if($arrBentuk[$iBentuk] == '6'){
                    $strBentuk .= "6,";
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
        
        if($sudah_hitung_saja == 'Y'){
            $param_sudah_hitung_saja = "AND pmp.hitung_rapor_mutu > 0";
        }else{
            $param_sudah_hitung_saja = "";
        }

        if($belum_hitung_saja == 'Y'){
            $param_belum_hitung_saja = "AND pmp.hitung_rapor_mutu < 1";
        }else{
            $param_belum_hitung_saja = "";
        }

        $sql_count = "SELECT
            sum(1) as total
        FROM
            rekap_sekolah WITH(NOLOCK)
        WHERE
            semester_id = '".($request->input('semester_id') ? $request->input('semester_id') : '20201')."'
        AND rekap_sekolah.tahun_ajaran_id = '".($request->input('tahun_ajaran_id') ? $request->input('tahun_ajaran_id') : substr(($request->input('semester_id') ? $request->input('semester_id') : '20201'),0,4))."'
        AND {$params_wilayah} = '".($request->input('kode_wilayah') ? $request->input('kode_wilayah') : '000000')."'
        AND soft_delete = 0
        AND rekap_sekolah.bentuk_pendidikan_id != 29
        {$param_bentuk}
        {$param_status}
        {$param_keyword}
        AND rekap_sekolah.bentuk_pendidikan_id != 29";

        $sql = "SELECT
            ROW_NUMBER() OVER (ORDER BY nama) as 'no',
            rekap_sekolah.sekolah_id,
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
                WHEN bentuk_pendidikan_id = 53 THEN 'SPK SD'
                WHEN bentuk_pendidikan_id = 54 THEN 'SPK SMP'
                WHEN bentuk_pendidikan_id = 55 THEN 'SPK SMA'
            ELSE '-' END AS bentuk,
            rekap_sekolah.kecamatan,
            rekap_sekolah.kabupaten,
            rekap_sekolah.propinsi,
            rekap_sekolah.kode_wilayah_kecamatan,
            rekap_sekolah.mst_kode_wilayah_kecamatan,
            rekap_sekolah.id_level_wilayah_kecamatan,
            rekap_sekolah.kode_wilayah_kabupaten,
            rekap_sekolah.mst_kode_wilayah_kabupaten,
            rekap_sekolah.id_level_wilayah_kabupaten,
            rekap_sekolah.kode_wilayah_propinsi,
            rekap_sekolah.mst_kode_wilayah_propinsi,
            rekap_sekolah.id_level_wilayah_propinsi,
            -- ISNULL(pmp.jumlah_kirim,0) as jumlah_kirim,
            ISNULL(pmp.hitung_rapor_mutu,0) as hitung_rapor_mutu,
            ISNULL(pmp.jumlah_pengguna,0) as jumlah_pengguna,
            ISNULL(pmp.jumlah_pengguna_mengerjakan,0) as jumlah_pengguna_mengerjakan,
            ISNULL(pmp.kepsek_total,0) as kepsek_total,
            ISNULL(pmp.kepsek_mengerjakan,0) as kepsek_mengerjakan,
            ISNULL(pmp.kepsek_persen,0) as kepsek_persen,
            ISNULL(pmp.pd_total,0) as pd_total,
            ISNULL(pmp.pd_mengerjakan,0) as pd_mengerjakan,
            ISNULL(pmp.pd_persen,0) as pd_persen,
            ISNULL(pmp.ptk_total,0) as ptk_total,
            ISNULL(pmp.ptk_mengerjakan,0) as ptk_mengerjakan,
            ISNULL(pmp.ptk_persen,0) as ptk_persen,
            ISNULL(pmp.komite_total,0) as komite_total,
            ISNULL(pmp.komite_mengerjakan,0) as komite_mengerjakan,
            ISNULL(pmp.komite_persen,0) as komite_persen,
            ISNULL(pmp.verifikasi_pengawas,0) as verifikasi_pengawas,
            pmp.sync_terakhir as sync_terakhir,
            rekap_sekolah.tanggal as tanggal_rekap_terakhir
        FROM
            rekap_sekolah WITH(NOLOCK)
        JOIN rekap_pengiriman_pmp pmp on pmp.sekolah_id = rekap_sekolah.sekolah_id and pmp.soft_delete_pmp = 0 and pmp.tahun_ajaran_id = '".($request->input('tahun_ajaran_id') ? $request->input('tahun_ajaran_id') : substr(($request->input('semester_id') ? $request->input('semester_id') : '20201'),0,4))."'
        WHERE
            semester_id = '".($request->input('semester_id') ? $request->input('semester_id') : '20201')."'
        AND rekap_sekolah.tahun_ajaran_id = '".($request->input('tahun_ajaran_id') ? $request->input('tahun_ajaran_id') : substr(($request->input('semester_id') ? $request->input('semester_id') : '20201'),0,4))."'
        AND {$params_wilayah} = '".($request->input('kode_wilayah') ? $request->input('kode_wilayah') : '000000')."'
        AND soft_delete = 0
        {$param_bentuk}
        {$param_status}
        {$param_keyword}
        {$param_sudah_hitung_saja}
        {$param_belum_hitung_saja}
        ";

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