<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Http\Controllers\Preface;
use App\Http\Controllers\SekolahController;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redis;

class SPMController extends Controller
{
    static function simpanIndexPendidikan(Request $request){
        $kode_wilayah = $request->kode_wilayah ? $request->kode_wilayah : null;
        $tahun = $request->tahun ? $request->tahun : null;
        $nilai_ipm = $request->nilai_ipm ? $request->nilai_ipm : null;
        $nilai_hls = $request->nilai_hls ? $request->nilai_hls : null; 
        $nilai_rls = $request->nilai_rls ? $request->nilai_rls : null;
        $nilai_amh = $request->nilai_amh ? $request->nilai_amh : null;
        $nilai_apk = $request->nilai_apk ? $request->nilai_apk : null;
        $nilai_apm = $request->nilai_apm ? $request->nilai_apm : null;

        $cek = DB::connection('sqlsrv_2')->table('spm.index_pendidikan')
        ->where('kode_wilayah','=',$kode_wilayah)
        ->where('tahun','=',$tahun)
        ->get()
        ;

        if(sizeof($cek) > 0){
            //update
            $exe = DB::connection('sqlsrv_2')->table('spm.index_pendidikan')
            ->where('kode_wilayah','=',$kode_wilayah)
            ->where('tahun','=',$tahun)
            ;

            if($nilai_ipm){
                $exe->update([
                    'nilai_ipm' => $nilai_ipm
                ]);
            }
            
            if($nilai_hls){
                $exe->update([
                    'nilai_hls' => $nilai_hls
                ]);
            }
            
            if($nilai_rls){
                $exe->update([
                    'nilai_rls' => $nilai_rls
                ]);
            }
            
            if($nilai_amh){
                $exe->update([
                    'nilai_amh' => $nilai_amh
                ]);
            }
            
            if($nilai_apk){
                $exe->update([
                    'nilai_apk' => $nilai_apk
                ]);
            }
            
            if($nilai_apm){
                $exe->update([
                    'nilai_apm' => $nilai_apm
                ]);
            }

        }else{
            //insert
            $exe = DB::connection('sqlsrv_2')->table('spm.index_pendidikan')
            ->insert([
                'kode_wilayah' => $kode_wilayah,
                'tahun' => $tahun,
                'nilai_ipm' => $nilai_ipm,
                'nilai_hls' => $nilai_hls,
                'nilai_rls' => $nilai_rls,
                'nilai_amh' => $nilai_amh,
                'nilai_apk' => $nilai_apk,
                'nilai_apm' => $nilai_apm
            ])
            ;
        }

        $return = array();
        $return['sukses'] = $exe ? true : false;
        $return['rows'] = DB::connection('sqlsrv_2')->table('spm.index_pendidikan')
        ->where('kode_wilayah','=',$kode_wilayah)
        ->where('tahun','=',$tahun)->get();

        return $return;

    }

    static function getIndexPendidikan(Request $request){
        $kode_wilayah = $request->kode_wilayah ? $request->kode_wilayah : null;
        $tahun = $request->tahun ? $request->tahun : null;

        $fetch = DB::connection('sqlsrv_2')->table('spm.index_pendidikan')
        ->orderBy('tahun','ASC');

        if($kode_wilayah){
            $fetch->where('kode_wilayah','=',$kode_wilayah);
        }
        
        if($tahun){
            $fetch->where('tahun','=',$tahun);
        }

        $return = array();
        $return['total'] = $fetch->count();
        $return['rows'] = $fetch->get();

        return $return;
    }

    static function simpantabel42(Request $request){
        $realisasi_pencapaian_pemenuhan_spm_id = $request->realisasi_pencapaian_pemenuhan_spm_id ? $request->realisasi_pencapaian_pemenuhan_spm_id : DB::raw("newid()");

        $fetch_cek = DB::connection('sqlsrv_spm_bdg')->table('f42_realisasi_pencapaian_pemenuhan_spm')->where('realisasi_pencapaian_pemenuhan_spm_id','=',$realisasi_pencapaian_pemenuhan_spm_id)->get();

        if(sizeof($fetch_cek) > 0){
            //update
            $exe = DB::connection('sqlsrv_spm_bdg')->table('f42_realisasi_pencapaian_pemenuhan_spm')
            ->where('realisasi_pencapaian_pemenuhan_spm_id','=',$realisasi_pencapaian_pemenuhan_spm_id)
            ->update([
                'keluaran' => $request->keluaran,
                'satuan' => $request->satuan,
                'target' => $request->target,
                'volume' => $request->volume,
                'rupiah' => $request->rupiah,
                'realisasi' => $request->realisasi,
                'capaian' => $request->capaian,
                'fisik' => $request->fisik,
                'keuangan' => $request->keuangan,
                'sumber_dana' => $request->sumber_dana,
                'permasalahan' => $request->permasalahan,
                'solusi' => $request->solusi,
                'last_update' => DB::raw('getdate()'),
                'soft_delete' => 0
            ]);

        }else{
            //insert
            $exe = DB::connection('sqlsrv_spm_bdg')->table('f42_realisasi_pencapaian_pemenuhan_spm')
            ->insert([
                'realisasi_pencapaian_pemenuhan_spm_id' => $realisasi_pencapaian_pemenuhan_spm_id,
                'keluaran' => $request->keluaran,
                'satuan' => $request->satuan,
                'target' => $request->target,
                'volume' => $request->volume,
                'rupiah' => $request->rupiah,
                'realisasi' => $request->realisasi,
                'capaian' => $request->capaian,
                'fisik' => $request->fisik,
                'keuangan' => $request->keuangan,
                'sumber_dana' => $request->sumber_dana,
                'permasalahan' => $request->permasalahan,
                'solusi' => $request->solusi,
                'create_date' => DB::raw('getdate()'),
                'last_update' => DB::raw('getdate()'),
                'soft_delete' => 0
            ]);
        }

        $return = array();
        $return['sukses'] = ($exe ? true : false);
        $return['rows'] = DB::connection('sqlsrv_spm_bdg')->table('f42_realisasi_pencapaian_pemenuhan_spm')->where('realisasi_pencapaian_pemenuhan_spm_id','=',$realisasi_pencapaian_pemenuhan_spm_id)->get();

        return $return;
    }

    static function simpantabel41(Request $request){
        $realisasi_pencapaian_pemenuhan_pelayanan_dasar_ats_id = $request->realisasi_pencapaian_pemenuhan_pelayanan_dasar_ats_id ? $request->realisasi_pencapaian_pemenuhan_pelayanan_dasar_ats_id : DB::raw("newid()");

        $fetch_cek = DB::connection('sqlsrv_spm_bdg')->table('f41_realisasi_pencapaian_pemenuhan_pelayanan_dasar_ats')->where('realisasi_pencapaian_pemenuhan_pelayanan_dasar_ats_id','=',$realisasi_pencapaian_pemenuhan_pelayanan_dasar_ats_id)->get();

        if(sizeof($fetch_cek) > 0){
            //update
            $exe = DB::connection('sqlsrv_spm_bdg')->table('f41_realisasi_pencapaian_pemenuhan_pelayanan_dasar_ats')
            ->where('realisasi_pencapaian_pemenuhan_pelayanan_dasar_ats_id','=',$realisasi_pencapaian_pemenuhan_pelayanan_dasar_ats_id)
            ->update([
                'keluaran' => $request->keluaran,
                'satuan' => $request->satuan,
                'target' => $request->target,
                'volume' => $request->volume,
                'rupiah' => $request->rupiah,
                'realisasi' => $request->realisasi,
                'capaian' => $request->capaian,
                'fisik' => $request->fisik,
                'keuangan' => $request->keuangan,
                'sumber_dana' => $request->sumber_dana,
                'permasalahan' => $request->permasalahan,
                'solusi' => $request->solusi,
                'last_update' => DB::raw('getdate()'),
                'soft_delete' => 0
            ]);

        }else{
            //insert
            $exe = DB::connection('sqlsrv_spm_bdg')->table('f41_realisasi_pencapaian_pemenuhan_pelayanan_dasar_ats')
            ->insert([
                'realisasi_pencapaian_pemenuhan_pelayanan_dasar_ats_id' => $realisasi_pencapaian_pemenuhan_pelayanan_dasar_ats_id,
                'keluaran' => $request->keluaran,
                'satuan' => $request->satuan,
                'target' => $request->target,
                'volume' => $request->volume,
                'rupiah' => $request->rupiah,
                'realisasi' => $request->realisasi,
                'capaian' => $request->capaian,
                'fisik' => $request->fisik,
                'keuangan' => $request->keuangan,
                'sumber_dana' => $request->sumber_dana,
                'permasalahan' => $request->permasalahan,
                'solusi' => $request->solusi,
                'create_date' => DB::raw('getdate()'),
                'last_update' => DB::raw('getdate()'),
                'soft_delete' => 0
            ]);
        }

        $return = array();
        $return['sukses'] = ($exe ? true : false);
        $return['rows'] = DB::connection('sqlsrv_spm_bdg')->table('f41_realisasi_pencapaian_pemenuhan_pelayanan_dasar_ats')->where('realisasi_pencapaian_pemenuhan_pelayanan_dasar_ats_id','=',$realisasi_pencapaian_pemenuhan_pelayanan_dasar_ats_id)->get();

        return $return;
    }

    static function tabel42(Request $request){
        $realisasi_pencapaian_pemenuhan_spm_id = $request->realisasi_pencapaian_pemenuhan_spm_id;

        $sql = "SELECT
                    hitung.*
                FROM
                    f42_realisasi_pencapaian_pemenuhan_spm hitung
                WHERE 
                    hitung.soft_delete = 0
                ";

        if($realisasi_pencapaian_pemenuhan_spm_id){
            $sql .= " AND hitung.realisasi_pencapaian_pemenuhan_spm_id = '".$realisasi_pencapaian_pemenuhan_spm_id."'";
        }

        $sql .= " ORDER BY create_date desc";
        
        $fetch = DB::connection('sqlsrv_spm_bdg')->select(DB::raw($sql));

        $return = array();
        $return['rows'] = $fetch;
        $return['total'] = sizeof($fetch);

        return $return;
    }
    
    static function tabel41(Request $request){
        $realisasi_pencapaian_pemenuhan_pelayanan_dasar_ats_id = $request->realisasi_pencapaian_pemenuhan_pelayanan_dasar_ats_id;

        $sql = "SELECT
                    hitung.*
                FROM
                    f41_realisasi_pencapaian_pemenuhan_pelayanan_dasar_ats hitung
                WHERE 
                    hitung.soft_delete = 0
                ";

        if($realisasi_pencapaian_pemenuhan_pelayanan_dasar_ats_id){
            $sql .= " AND hitung.realisasi_pencapaian_pemenuhan_pelayanan_dasar_ats_id = '".$realisasi_pencapaian_pemenuhan_pelayanan_dasar_ats_id."'";
        }

        $sql .= " ORDER BY create_date desc";
        
        $fetch = DB::connection('sqlsrv_spm_bdg')->select(DB::raw($sql));

        $return = array();
        $return['rows'] = $fetch;
        $return['total'] = sizeof($fetch);

        return $return;
    }

    static function tabel43(Request $request){
        $sql = "select * from draft_form_4_3_khs";

        $fetch = DB::connection('sqlsrv_spm_bdg')->select(DB::raw($sql));

        $return = array();
        $return['total'] = sizeof($fetch);
        $return['rows'] = $fetch;

        return $return;
    }

    static function simpanTabel31(Request $request){
        $rencana_pemenuhan_pelayanan_dasar_ats_id = $request->rencana_pemenuhan_pelayanan_dasar_ats_id ? $request->rencana_pemenuhan_pelayanan_dasar_ats_id : DB::raw("newid()");

        $fetch_cek = DB::connection('sqlsrv_spm_bdg')->table('f31_rencana_pemenuhan_pelayanan_dasar_ats')->where('rencana_pemenuhan_pelayanan_dasar_ats_id','=',$rencana_pemenuhan_pelayanan_dasar_ats_id)->get();

        if(sizeof($fetch_cek) > 0){
            //update
            $exe = DB::connection('sqlsrv_spm_bdg')->table('f31_rencana_pemenuhan_pelayanan_dasar_ats')
            ->where('rencana_pemenuhan_pelayanan_dasar_ats_id','=',$rencana_pemenuhan_pelayanan_dasar_ats_id)
            ->update([
                'program' => $request->program,
                'outcame' => $request->outcame,
                'kegiatan' => $request->kegiatan,
                'sub_kegiatan' => $request->sub_kegiatan,
                'keluaran_output' => $request->keluaran_output,
                'satuan' => $request->satuan,
                'jumlah_sasaran_pemenuhan_n' => $request->jumlah_sasaran_pemenuhan_n,
                'alokasi_anggaran_n' => $request->alokasi_anggaran_n,
                'sumber_dana' => $request->sumber_dana,
                'target_n1' => $request->target_n1,
                'harga_satuan_n1' => $request->harga_satuan_n1,
                'target_n2' => $request->target_n2,
                'harga_satuan_n2' => $request->harga_satuan_n2,
                'target_n3' => $request->target_n3,
                'harga_satuan_n3' => $request->harga_satuan_n3,
                'target_n4' => $request->target_n4,
                'harga_satuan_n4' => $request->harga_satuan_n4,
                'target_n5' => $request->target_n5,
                'harga_satuan_n5' => $request->harga_satuan_n5,
                'kondisi_akhir_n5' => $request->kondisi_akhir_n5,
                'tanggal' => DB::raw('getdate()'),
                'last_update' => DB::raw('getdate()'),
                'soft_delete' => 0
            ]);

        }else{
            //insert
            $exe = DB::connection('sqlsrv_spm_bdg')->table('f31_rencana_pemenuhan_pelayanan_dasar_ats')
            ->insert([
                'rencana_pemenuhan_pelayanan_dasar_ats_id' => $rencana_pemenuhan_pelayanan_dasar_ats_id,
                'program' => $request->program,
                'outcame' => $request->outcame,
                'kegiatan' => $request->kegiatan,
                'sub_kegiatan' => $request->sub_kegiatan,
                'keluaran_output' => $request->keluaran_output,
                'satuan' => $request->satuan,
                'jumlah_sasaran_pemenuhan_n' => $request->jumlah_sasaran_pemenuhan_n,
                'alokasi_anggaran_n' => $request->alokasi_anggaran_n,
                'sumber_dana' => $request->sumber_dana,
                'target_n1' => $request->target_n1,
                'harga_satuan_n1' => $request->harga_satuan_n1,
                'target_n2' => $request->target_n2,
                'harga_satuan_n2' => $request->harga_satuan_n2,
                'target_n3' => $request->target_n3,
                'harga_satuan_n3' => $request->harga_satuan_n3,
                'target_n4' => $request->target_n4,
                'harga_satuan_n4' => $request->harga_satuan_n4,
                'target_n5' => $request->target_n5,
                'harga_satuan_n5' => $request->harga_satuan_n5,
                'kondisi_akhir_n5' => $request->kondisi_akhir_n5,
                'tanggal' => DB::raw('getdate()'),
                'create_date' => DB::raw('getdate()'),
                'last_update' => DB::raw('getdate()'),
                'soft_delete' => 0
            ]);
        }

        $return = array();
        $return['sukses'] = ($exe ? true : false);
        $return['rows'] = DB::connection('sqlsrv_spm_bdg')->table('f31_rencana_pemenuhan_pelayanan_dasar_ats')->where('rencana_pemenuhan_pelayanan_dasar_ats_id','=',$rencana_pemenuhan_pelayanan_dasar_ats_id)->get();

        return $return;
    }

    static function tabel31(Request $request){
        $rencana_pemenuhan_pelayanan_dasar_ats_id = $request->rencana_pemenuhan_pelayanan_dasar_ats_id;

        $sql = "SELECT
                    hitung.*
                FROM
                    f31_rencana_pemenuhan_pelayanan_dasar_ats hitung
                WHERE 
                    hitung.soft_delete = 0
                ";

        if($rencana_pemenuhan_pelayanan_dasar_ats_id){
            $sql .= " AND hitung.rencana_pemenuhan_pelayanan_dasar_ats_id = '".$rencana_pemenuhan_pelayanan_dasar_ats_id."'";
        }

        $sql .= " ORDER BY create_date desc";
        
        $fetch = DB::connection('sqlsrv_spm_bdg')->select(DB::raw($sql));

        $return = array();
        $return['rows'] = $fetch;
        $return['total'] = sizeof($fetch);

        return $return;
    }

    static function simpanTabel21(Request $request){
        $perhitungan_kebutuhan_ats_id = $request->perhitungan_kebutuhan_ats_id ? $request->perhitungan_kebutuhan_ats_id : DB::raw("newid()");

        $fetch_cek = DB::connection('sqlsrv_spm_bdg')->table('f21_perhitungan_kebutuhan_pelayanan_dasar_ats')->where('perhitungan_kebutuhan_ats_id','=',$perhitungan_kebutuhan_ats_id)->get();

        if(sizeof($fetch_cek) > 0){
            //update
            $exe = DB::connection('sqlsrv_spm_bdg')->table('f21_perhitungan_kebutuhan_pelayanan_dasar_ats')
            ->where('perhitungan_kebutuhan_ats_id','=',$perhitungan_kebutuhan_ats_id)
            ->update([
                'harga_satuan' => $request->harga_satuan,
                'faktor_tidak_sekolah_id' => $request->faktor_tidak_sekolah_id,
                'bentuk_pemenuhan_pelayanan_dasar' => $request->bentuk_pemenuhan_pelayanan_dasar,
                'jumlah_sasaran' => $request->jumlah_sasaran,
                'penanggung_jawab_id' => $request->penanggung_jawab_id,
                'last_update' => DB::raw('getdate()'),
                'soft_delete' => 0
            ]);

        }else{
            //insert
            $exe = DB::connection('sqlsrv_spm_bdg')->table('f21_perhitungan_kebutuhan_pelayanan_dasar_ats')
            ->insert([
                'perhitungan_kebutuhan_ats_id' => $perhitungan_kebutuhan_ats_id,
                'harga_satuan' => $request->harga_satuan,
                'faktor_tidak_sekolah_id' => $request->faktor_tidak_sekolah_id,
                'bentuk_pemenuhan_pelayanan_dasar' => $request->bentuk_pemenuhan_pelayanan_dasar,
                'jumlah_sasaran' => $request->jumlah_sasaran,
                'penanggung_jawab_id' => $request->penanggung_jawab_id,
                'create_date' => DB::raw('getdate()'),
                'last_update' => DB::raw('getdate()'),
                'soft_delete' => 0
            ]);
        }

        $return = array();
        $return['sukses'] = ($exe ? true : false);
        $return['rows'] = DB::connection('sqlsrv_spm_bdg')->table('f21_perhitungan_kebutuhan_pelayanan_dasar_ats')->where('perhitungan_kebutuhan_ats_id','=',$perhitungan_kebutuhan_ats_id)->get();

        return $return;
    }

    static function getDinas(Request $request){
        $sql = "select * from ref.dinas";

        $fetch = DB::connection('sqlsrv_spm_bdg')->select(DB::raw($sql));

        $return = array();
        $return['rows'] = $fetch;
        $return['total'] = sizeof($fetch);

        return $return;
    }

    static function tabel21(Request $request){
        $perhitungan_kebutuhan_ats_id = $request->perhitungan_kebutuhan_ats_id;

        $sql = "SELECT
                    hitung.*,
                    faktor.nama as faktor_tidak_sekolah,
                    dinas.nama as penanggung_jawab
                FROM
                    f21_perhitungan_kebutuhan_pelayanan_dasar_ats hitung
                LEFT JOIN ref.faktor_tidak_sekolah faktor on faktor.faktor_tidak_sekolah_id = hitung.faktor_tidak_sekolah_id
                LEFT JOIN ref.dinas dinas on dinas.penanggung_jawab_id = hitung.penanggung_jawab_id
                where hitung.soft_delete = 0
                ";

        if($perhitungan_kebutuhan_ats_id){
            $sql .= " AND hitung.perhitungan_kebutuhan_ats_id = '".$perhitungan_kebutuhan_ats_id."'";
        }

        $sql .= " ORDER BY create_date desc";
        
        $fetch = DB::connection('sqlsrv_spm_bdg')->select(DB::raw($sql));

        $return = array();
        $return['rows'] = $fetch;
        $return['total'] = sizeof($fetch);

        return $return;
    }

    static function simpanRencanaPemenuhanSPM(Request $request){
        $kegiatan_id = $request->kegiatan_id;

        $fetch_cek = DB::connection('sqlsrv_spm_bdg')->table('f32_rencana_pemenuhan_spm_kerangka_pendanaan')->where('kegiatan_id','=',$kegiatan_id)->get();

        if(sizeof($fetch_cek) > 0){
            //update
            $exe = DB::connection('sqlsrv_spm_bdg')->table('f32_rencana_pemenuhan_spm_kerangka_pendanaan')
            ->where('kegiatan_id','=',$kegiatan_id)
            ->update([
                'jumlah_sasaran_pemenuhan_n' => $request->jumlah_sasaran_pemenuhan_n,
                'alokasi_anggaran_n' => $request->alokasi_anggaran_n,
                'sumber_dana' => $request->sumber_dana,
                'target_n1' => $request->target_n1,
                'harga_satuan_n1' => $request->harga_satuan_n1,
                'target_n2' => $request->target_n2,
                'harga_satuan_n2' => $request->harga_satuan_n2,
                'target_n3' => $request->target_n3,
                'harga_satuan_n3' => $request->harga_satuan_n3,
                'target_n4' => $request->target_n4,
                'harga_satuan_n4' => $request->harga_satuan_n4,
                'target_n5' => $request->target_n5,
                'harga_satuan_n5' => $request->harga_satuan_n5,
                'kondisi_akhir_n5' => ($request->harga_satuan_n5 * $request->target_n5)
                
            ]);

        }else{
            //insert
            $exe = DB::connection('sqlsrv_spm_bdg')->table('f32_rencana_pemenuhan_spm_kerangka_pendanaan')
            ->insert([
                'jumlah_sasaran_pemenuhan_n' => $request->jumlah_sasaran_pemenuhan_n,
                'alokasi_anggaran_n' => $request->alokasi_anggaran_n,
                'sumber_dana' => $request->sumber_dana,
                'target_n1' => $request->target_n1,
                'harga_satuan_n1' => $request->harga_satuan_n1,
                'target_n2' => $request->target_n2,
                'harga_satuan_n2' => $request->harga_satuan_n2,
                'target_n3' => $request->target_n3,
                'harga_satuan_n3' => $request->harga_satuan_n3,
                'target_n4' => $request->target_n4,
                'harga_satuan_n4' => $request->harga_satuan_n4,
                'target_n5' => $request->target_n5,
                'harga_satuan_n5' => $request->harga_satuan_n5,
                'kondisi_akhir_n5' => ($request->harga_satuan_n5 * $request->target_n5)
                
            ]);
        }

        $return = array();
        $return['sukses'] = ($exe ? true : false);
        $return['rows'] = DB::connection('sqlsrv_spm_bdg')->table('f32_rencana_pemenuhan_spm_kerangka_pendanaan')->where('kegiatan_id','=',$kegiatan_id)->get();

        return $return;
    }

    static function getRencanaPemenuhanSPMFlat(Request $request){
        $kegiatan_id = $request->kegiatan_id ? $request->kegiatan_id : null;

        $sql = "SELECT
                    f32.*,
                    kegiatan.*
                FROM
                    f32_rencana_pemenuhan_spm_kerangka_pendanaan f32
                    LEFT JOIN ref.kegiatan kegiatan ON kegiatan.kegiatan_id = f32.kegiatan_id 
                WHERE
                    f32.kegiatan_id = '{$kegiatan_id}'";

        $fetch = DB::connection('sqlsrv_spm_bdg')->select(DB::raw($sql));

        $return = array();
        $return['rows'] = $fetch;
        $return['total'] = sizeof($fetch);

        return $return;
    }
    
    static function rootRencanaPemenuhanSPM(Request $request){
        $level_kegiatan_id = $request->level_kegiatan_id ? $request->level_kegiatan_id : null;
        $master_kegiatan_id = $request->master_kegiatan_id ? $request->master_kegiatan_id : null;

        $fetch = self::getRencanaPemenuhanSPM(1, '000000');

        return $fetch;
    }

    static function getRencanaPemenuhanSPM($level_kegiatan_id, $master_kegiatan_id){
        // $level_kegiatan_id = $request->level_kegiatan_id ? $request->level_kegiatan_id : null;
        // $master_kegiatan_id = $request->master_kegiatan_id ? $request->master_kegiatan_id : null;

        $sql = "SELECT
                    kegiatan.*,
                    f32.* 
                FROM
                    f32_rencana_pemenuhan_spm_kerangka_pendanaan f32
                    LEFT JOIN ref.kegiatan kegiatan ON kegiatan.kegiatan_id = f32.kegiatan_id 
                WHERE
                    level_kegiatan_id = $level_kegiatan_id
                AND master_kegiatan_id = '{$master_kegiatan_id}'";

        $fetch = DB::connection('sqlsrv_spm_bdg')->select(DB::raw($sql));

        for ($iFetch=0; $iFetch < sizeof($fetch); $iFetch++) { 
            if($fetch[$iFetch]->level_kegiatan_id < 5){
                $fetch[$iFetch]->children = self::getRencanaPemenuhanSPM(((int)$fetch[$iFetch]->level_kegiatan_id+1), $fetch[$iFetch]->kegiatan_id);
            }

        }

        $return = array();
        $return['rows'] = $fetch;
        $return['total'] = sizeof($fetch);

        return $return;
    }

    static function simpanPenerimaSPM(Request $request){
        $pelayanan_dasar_id = $request->pelayanan_dasar_id;

        $fetch_cek = DB::connection('sqlsrv_spm_bdg')->table('f22_perhitungan_kebutuhan_penerima_spm')->where('pelayanan_dasar_id','=',$pelayanan_dasar_id)->get();

        if(sizeof($fetch_cek) > 0){
            //update
            $exe = DB::connection('sqlsrv_spm_bdg')->table('f22_perhitungan_kebutuhan_penerima_spm')
            ->where('pelayanan_dasar_id','=',$pelayanan_dasar_id)
            ->update([
                'tahun' => $request->tahun,
                'kebutuhan' => $request->kebutuhan,
                'ketersediaan' => $request->ketersediaan,
                'selisih' => $request->selisih,
                'harga_satuan' => $request->harga_satuan,
                'kebutuhan_biaya' => $request->kebutuhan_biaya
            ]);

        }else{
            //insert
            $exe = DB::connection('sqlsrv_spm_bdg')->table('f22_perhitungan_kebutuhan_penerima_spm')
            ->insert([
                'perhitungan_kebutuhan_penerima_spm_id' => DB::raw("newid()"),
                'pelayanan_dasar_id' => $request->pelayanan_dasar_id,
                'tahun' => $request->tahun,
                'kebutuhan' => $request->kebutuhan,
                'ketersediaan' => $request->ketersediaan,
                'selisih' => $request->selisih,
                'harga_satuan' => $request->harga_satuan,
                'kebutuhan_biaya' => $request->kebutuhan_biaya
            ]);
        }

        $return = array();
        $return['sukses'] = ($exe ? true : false);
        $return['rows'] = DB::connection('sqlsrv_spm_bdg')->table('f22_perhitungan_kebutuhan_penerima_spm')->where('pelayanan_dasar_id','=',$pelayanan_dasar_id)->get();

        return $return;
    }
    
    static function getPenerimaSPM(Request $request){
        $pelayanan_dasar_id = $request->pelayanan_dasar_id;

        $sql = "SELECT
                    f22.perhitungan_kebutuhan_penerima_spm_id,
                    dasar.pelayanan_dasar_id,
                    dasar.no_urut,
                    dasar.pelayanan_dasar,
                    dasar.satuan_pendidikan_id,
                    dasar.komponen_kebutuhan_spm,
                    dasar.sasaran_spm,
                    dasar.keluaran,
                    dasar.satuan,
                    f22.kebutuhan,
                    f22.ketersediaan,
                    f22.selisih,
                    f22.harga_satuan,
                    f22.kebutuhan_biaya,
                    f22.tahun,
                    f22.create_date,
                    f22.last_update,
                    f22.soft_delete
                FROM
                    ref.pelayanan_dasar dasar
                    LEFT JOIN f22_perhitungan_kebutuhan_penerima_spm f22 ON dasar.pelayanan_dasar_id = f22.pelayanan_dasar_id";

        if($pelayanan_dasar_id){
            $sql .= " WHERE dasar.pelayanan_dasar_id = '".$pelayanan_dasar_id."'";
        }
        
        $fetch = DB::connection('sqlsrv_spm_bdg')->select(DB::raw($sql));

        $return = array();
        $return['rows'] = $fetch;
        $return['total'] = sizeof($fetch);

        return $return;
    }

    static function simpanVervalAts(Request $request){
        $status_ats_id = $request->status_ats_id;
        $alasan_bukan_ats_id = $request->alasan_bukan_ats_id;
        $nik = $request->nik;

        $return = array();

        $exe = DB::connection('sqlsrv_spm_bdg')->table('kependudukan')
        ->where('nik','=',$nik)
        ->update([
            'status_ats_id' => $status_ats_id
        ]);

        if($exe){
            $return['sukses'] = true;
        }else{
            $return['sukses'] = false;
        }

        return $return;
    }
    
    static function simpanVervalPDMiskin(Request $request){
        $is_miskin = $request->is_miskin;
        $nik = $request->nik;

        $return = array();

        $exe = DB::connection('sqlsrv_spm_bdg')->table('peserta_didik')
        ->where('nik','=',$nik)
        ->update([
            'is_miskin' => $is_miskin
        ]);

        if($exe){
            $return['sukses'] = true;
        }else{
            $return['sukses'] = false;
        }

        return $return;
    }

    static function getRekapBerandaSPM(Request $request){
        $return = array();
        
        $sql = "SELECT
                    sum(case when peserta_didik.bentuk_pendidikan_id IN (5,6,13,15) then 1 else 0 end) as total,
                    sum(case when peserta_didik.bentuk_pendidikan_id IN (5) then 1 else 0 end) as total_sd,
                    sum(case when peserta_didik.bentuk_pendidikan_id IN (6) then 1 else 0 end) as total_smp,
                    sum(case when peserta_didik.bentuk_pendidikan_id IN (13) then 1 else 0 end) as total_sma,
                    sum(case when peserta_didik.bentuk_pendidikan_id IN (15) then 1 else 0 end) as total_smk
                FROM
                    peserta_didik
                    LEFT JOIN kependudukan ON kependudukan.peserta_didik_id = peserta_didik.peserta_didik_id 
                WHERE
                    peserta_didik.penerima_kip = 1";
        $fetch = DB::connection('sqlsrv_spm_bdg')->select(DB::raw($sql));
        
        $return['kip'] = $fetch;

        $sql2 = "SELECT
                    ISNULL(sum(case when peserta_didik.bentuk_pendidikan_id IN (5,6,13,15) then 1 else 0 end),0) as total,
                    ISNULL(sum(case when peserta_didik.bentuk_pendidikan_id IN (5) then 1 else 0 end),0) as total_sd,
                    ISNULL(sum(case when peserta_didik.bentuk_pendidikan_id IN (6) then 1 else 0 end),0) as total_smp,
                    ISNULL(sum(case when peserta_didik.bentuk_pendidikan_id IN (13) then 1 else 0 end),0) as total_sma,
                    ISNULL(sum(case when peserta_didik.bentuk_pendidikan_id IN (15) then 1 else 0 end),0) as total_smk
                FROM
                    peserta_didik
                    LEFT JOIN kependudukan ON kependudukan.peserta_didik_id = peserta_didik.peserta_didik_id 
                WHERE
                    peserta_didik.is_miskin = 1";
        
        $fetch2 = DB::connection('sqlsrv_spm_bdg')->select(DB::raw($sql2));
        
        $return['miskin'] = $fetch2;

        return $return;

    }

    static function getAnakTidakSekolah(Request $request){
        $start = $request->start ? $request->start : 0;
        $limit = $request->limit ? $request->limit : 20;
        $nik = $request->nik ? $request->nik : null;
        
        $select_body = "SELECT ROW_NUMBER () OVER ( ORDER BY kependudukan.nama ASC ) AS urutan, peserta_didik.peserta_didik_id, status_ats.nama_status as status_ats, kependudukan.* ";
        $select_from = "FROM
                            kependudukan
                            LEFT JOIN peserta_didik ON peserta_didik.peserta_didik_id = kependudukan.peserta_didik_id 
                            LEFT JOIN ref.status_ats status_ats on status_ats.status_ats_id = kependudukan.status_ats_id
                        WHERE
                            peserta_didik.peserta_didik_id IS NULL 
                        ";

        if($nik){
            $select_from .= " AND kependudukan.nik = '".$nik."'";
        }

        $sql = "{$select_body}
                {$select_from}";
        
        $sql .= " ORDER BY kependudukan.nama OFFSET {$start} ROWS FETCH NEXT {$limit} ROWS ONLY";
        
        $fetch = DB::connection('sqlsrv_spm_bdg')->select(DB::raw($sql));
        
        $select_body = "SELECT sum(1) as total ";

        $sql = "{$select_body}
                {$select_from}";

        $fetch_count = DB::connection('sqlsrv_spm_bdg')->select(DB::raw($sql));
        

        
        $return = array();
        $return['total'] = $fetch_count[0]->total;
        $return['rows'] = $fetch;

        return $return;
        
    }
    
    static function getPDMiskin(Request $request){
        $start = $request->start ? $request->start : 0;
        $limit = $request->limit ? $request->limit : 20;
        
        $select_body = "SELECT ROW_NUMBER () OVER ( ORDER BY peserta_didik.nama ASC ) AS urutan, (CASE WHEN is_miskin = 9 then 'Terindikasi PD Miskin' when is_miskin = 1 then 'PD Miskin' else '-' end) as miskinkah,peserta_didik.* ";
        $select_from = "FROM
                            peserta_didik
                            LEFT JOIN kependudukan ON peserta_didik.peserta_didik_id = kependudukan.peserta_didik_id 
                        WHERE
                            peserta_didik.penerima_kip = 1
                        ";

        $sql = "{$select_body}
                {$select_from}";
        
        $sql .= " ORDER BY peserta_didik.is_miskin DESC, peserta_didik.nama OFFSET {$start} ROWS FETCH NEXT {$limit} ROWS ONLY";
        
        $fetch = DB::connection('sqlsrv_spm_bdg')->select(DB::raw($sql));
        
        $select_body = "SELECT sum(1) as total ";

        $sql = "{$select_body}
                {$select_from}";

        $fetch_count = DB::connection('sqlsrv_spm_bdg')->select(DB::raw($sql));
        

        
        $return = array();
        $return['total'] = $fetch_count[0]->total;
        $return['rows'] = $fetch;

        return $return;
        
    }
    
    static function getSPMUsiaSekolah(Request $request){
        $sql = "select * from draft_form_1_3_a";

        $fetch = DB::connection('sqlsrv_spm_bdg')->select(DB::raw($sql));

        $return = array();
        $return['total'] = sizeof($fetch);
        $return['rows'] = $fetch;

        return $return;
    }
    
    static function getSPMLuarWilayah(Request $request){
        $sql = "select * from draft_form_1_3_b";

        $fetch = DB::connection('sqlsrv_spm_bdg')->select(DB::raw($sql));

        $return = array();
        $return['total'] = sizeof($fetch);
        $return['rows'] = $fetch;

        return $return;
    }
    
    static function getSPMSatuanPendidikan(Request $request){
        $sql = "select * from draft_form_1_3_c";

        $fetch = DB::connection('sqlsrv_spm_bdg')->select(DB::raw($sql));

        $return = array();
        $return['total'] = sizeof($fetch);
        $return['rows'] = $fetch;

        return $return;
    }
    
    static function getSPMPendidik(Request $request){
        $sql = "select * from draft_form_1_3_d";

        $fetch = DB::connection('sqlsrv_spm_bdg')->select(DB::raw($sql));

        $return = array();
        $return['total'] = sizeof($fetch);
        $return['rows'] = $fetch;

        return $return;
    }
    
    static function getSPMKepsek(Request $request){
        $sql = "select * from draft_form_1_3_e";

        $fetch = DB::connection('sqlsrv_spm_bdg')->select(DB::raw($sql));

        $return = array();
        $return['total'] = sizeof($fetch);
        $return['rows'] = $fetch;

        return $return;
    }
    
    static function getSPMTenagaPenunjang(Request $request){
        $sql = "select * from draft_form_1_3_f";

        $fetch = DB::connection('sqlsrv_spm_bdg')->select(DB::raw($sql));

        $return = array();
        $return['total'] = sizeof($fetch);
        $return['rows'] = $fetch;

        return $return;
    }

    public function getSekolah(Request $request){
        $sql = "select * from rekap_sekolah where sekolah_id = '".($request->input('sekolah_id')?$request->input('sekolah_id'):'00A44E47-1526-E111-806C-7D290BED42F0')."' and semester_id = '".($request->input('semester_id')?$request->input('semester_id'):'20181')."' and soft_delete = 0";

        $fetch = DB::connection('sqlsrv_spm')->select($sql);

        $return = array();
        $return['total'] = sizeof($fetch);
        $return['rows'] = $fetch;

        return $return;
    }

    public function InstrumenRootExcel(Request $request){
        $return = self::InstrumenRoot($request);
        $sekolah = self::getSekolah($request);

        return view('excel/UnduhExcelSPMSekolah', [ 
            'sekolah' => $sekolah, 
            'return' => $return, 
            'judul' => 'SPM', 
            'semester_id' => ($request->input('semester_id') ? $request->input('semester_id') : '20191'), 
            'sub_judul' => "Tanggal Rekap Terakhir: ".date('Y-m-d H:i:s') 
        ]);
    }

    // public function getPencapaian(Request $request){

    //     $sql_p = "SELECT
    //         rekap.sekolah_id,
    //         rekap.semester_id,
    //         (
    //             (
    //                 (
    //                 CASE
    //                     WHEN ( rekap.bentuk_pendidikan_id = 5 OR rekap.bentuk_pendidikan_id = 9 ) 
    //                     THEN ( ( CASE WHEN ip_2__1_gap < 0 AND ip_2__1_target != 0 THEN 0 ELSE 1 END ) + ( CASE WHEN ip_2__2_gap < 0 AND ip_2__2_target != 0 THEN 0 ELSE 1 END ) ) 
    //                     ELSE ( ( CASE WHEN ip_2__3_gap < 0 AND ip_2__3_target != 0 THEN 0 ELSE 1 END ) + ( CASE WHEN ip_2__4_gap < 0 AND ip_2__4_target != 0 THEN 0 ELSE 1 END ) ) 
    //                 END 
    //                 ) 
    //             ) / 
    //             ( CASE WHEN ( rekap.bentuk_pendidikan_id = 5 OR rekap.bentuk_pendidikan_id = 9 ) THEN CAST ( 2 AS FLOAT ) ELSE CAST ( 2 AS FLOAT ) END ) * 100 
    //         ) AS ip_2,
    //         ( CASE WHEN ip_2__1_gap < 0 AND ip_2__1_target != 0 THEN 0 ELSE 1 END ) * 100 as ip_2_1,
    //         ( CASE WHEN ip_2__2_gap < 0 AND ip_2__2_target != 0 THEN 0 ELSE 1 END ) * 100 as ip_2_2,
    //         ( CASE WHEN ip_2__3_gap < 0 AND ip_2__3_target != 0 THEN 0 ELSE 1 END ) * 100 as ip_2_3,
    //         ( CASE WHEN ip_2__4_gap < 0 AND ip_2__4_target != 0 THEN 0 ELSE 1 END ) * 100 as ip_2_4,
    //         ip_2__1_target as ip_2_1_target,
    //         ip_2__2_target as ip_2_2_target,
    //         ip_2__3_target as ip_2_3_target,
    //         ip_2__4_target as ip_2_4_target,
    //         ip_2__1_capaian as ip_2_1_capaian,
    //         ip_2__2_capaian as ip_2_2_capaian,
    //         ip_2__3_capaian as ip_2_3_capaian,
    //         ip_2__4_capaian as ip_2_4_capaian,
    //         ip_2__1_gap as ip_2_1_gap,
    //         ip_2__2_gap as ip_2_2_gap,
    //         ip_2__3_gap as ip_2_3_gap,
    //         ip_2__4_gap as ip_2_4_gap,
    //         (
    //             CASE
    //                 WHEN ( rekap.bentuk_pendidikan_id = 6 OR rekap.bentuk_pendidikan_id = 10 ) 
    //                 THEN
    //                     (
    //                         (
    //                         ( CASE WHEN ip_3__1_gap < 0 AND ip_3__1_target != 0 THEN 0 ELSE 1 END ) + 
    //                         ( CASE WHEN ip_3__2_gap < 0 AND ip_3__2_target != 0 THEN 0 ELSE 1 END ) 
    //                     ) / CAST ( 2 AS FLOAT ) * 100 
    //                 ) ELSE 100 
    //             END 
    //         ) AS ip_3,
    //         ( CASE WHEN ip_3__1_gap < 0 AND ip_3__1_target != 0 THEN 0 ELSE 1 END ) * 100 as ip_3_1,
    //         ( CASE WHEN ip_3__2_gap < 0 AND ip_3__2_target != 0 THEN 0 ELSE 1 END ) * 100 as ip_3_2,
    //         ip_3__1_target as ip_3_1_target,
    //         ip_3__2_target as ip_3_2_target,
    //         ip_3__1_capaian as ip_3_1_capaian,
    //         ip_3__2_capaian as ip_3_2_capaian,
    //         ip_3__1_gap as ip_3_1_gap,
    //         ip_3__2_gap as ip_3_2_gap,
    //         (
    //         CASE  
    //             WHEN ( rekap.bentuk_pendidikan_id = 5 OR rekap.bentuk_pendidikan_id = 9 ) 
    //             THEN ( ( ( CASE WHEN ip_4__1_gap < 0 AND ip_4__1_target != 0 THEN 0 ELSE 1 END ) ) / CAST ( 1 AS FLOAT ) * 100 ) 
    //             ELSE ( ( ( CASE WHEN ip_4__2_gap < 0 AND ip_4__2_target != 0 THEN 0 ELSE 1 END ) + ( CASE WHEN ip_4__3_gap < 0 AND ip_4__3_target != 0 THEN 0 ELSE 1 END ) ) / CAST ( 2 AS FLOAT ) * 100 ) 
    //         END 
    //         ) AS ip_4,
    //         ( CASE WHEN ip_4__1_gap < 0 AND ip_4__1_target != 0 THEN 0 ELSE 1 END ) * 100 as ip_4_1,
    //         ( CASE WHEN ip_4__2_gap < 0 AND ip_4__2_target != 0 THEN 0 ELSE 1 END ) * 100 as ip_4_2,
    //         ( CASE WHEN ip_4__3_gap < 0 AND ip_4__3_target != 0 THEN 0 ELSE 1 END ) * 100 as ip_4_3,
    //         ip_4__1_target as ip_4_1_target,
    //         ip_4__2_target as ip_4_2_target,
    //         ip_4__3_target as ip_4_3_target,
    //         ip_4__1_capaian as ip_4_1_capaian,
    //         ip_4__2_capaian as ip_4_2_capaian,
    //         ip_4__3_capaian as ip_4_3_capaian,
    //         ip_4__1_gap as ip_4_1_gap,
    //         ip_4__2_gap as ip_4_2_gap,
    //         ip_4__3_gap as ip_4_3_gap,
    //         (
    //             CASE
    //                 WHEN ( rekap.bentuk_pendidikan_id = 5 OR rekap.bentuk_pendidikan_id = 9 ) 
    //                 THEN ( ( ( CASE WHEN ip_5__1_gap < 0 AND ip_5__1_target != 0 THEN 0 ELSE 1 END ) + ( CASE WHEN ip_5__2_gap < 0 AND ip_5__2_target != 0 THEN 0 ELSE 1 END ) ) / CAST ( 2 AS FLOAT ) * 100 
    //                 ) ELSE 100 
    //             END 
    //         ) AS ip_5,
    //         ( CASE WHEN ip_5__1_gap < 0 AND ip_5__1_target != 0 THEN 0 ELSE 1 END ) * 100 as ip_5_1,
    //         ( CASE WHEN ip_5__2_gap < 0 AND ip_5__2_target != 0 THEN 0 ELSE 1 END ) * 100 as ip_5_2,
    //         (
    //             CASE
    //                 WHEN ( rekap.bentuk_pendidikan_id = 5 OR rekap.bentuk_pendidikan_id = 9 ) 
    //                 THEN 100 
    //                 ELSE ( ( CASE WHEN ip_6_gap < 0 AND ip_6_target != 0 THEN 0 ELSE 1 END ) * 100 ) 
    //             END 
    //         ) AS ip_6,
    //         (
    //             CASE
    //                 WHEN ( rekap.bentuk_pendidikan_id = 5 OR rekap.bentuk_pendidikan_id = 9 ) 
    //                 THEN ( ( ( CASE WHEN ip_7__1_gap < 0 AND ip_7__1_target != 0 THEN 0 ELSE 1 END ) + ( CASE WHEN ip_7__2_gap < 0 AND ip_7__2_target != 0 THEN 0 ELSE 1 END ) ) / CAST ( 2 AS FLOAT ) * 100 ) 
    //                 ELSE 100 
    //             END 
    //         ) AS ip_7,
    //         ( CASE WHEN ip_7__1_gap < 0 AND ip_7__1_target != 0 THEN 0 ELSE 1 END ) * 100 as ip_7_1,
    //         ( CASE WHEN ip_7__2_gap < 0 AND ip_7__2_target != 0 THEN 0 ELSE 1 END ) * 100 as ip_7_2,
    //         (
    //             CASE
    //                 WHEN ( rekap.bentuk_pendidikan_id = 5 OR rekap.bentuk_pendidikan_id = 9 ) 
    //                 THEN 100 
    //                 ELSE ( ( ( CASE WHEN ip_8__1_gap < 0 AND ip_8__1_target != 0 THEN 0 ELSE 1 END ) + ( CASE WHEN ip_8__2_gap < 0 AND ip_8__2_target != 0 THEN 0 ELSE 1 END ) ) / CAST ( 2 AS FLOAT ) * 100 ) 
    //             END 
    //         ) AS ip_8,
    //         ( CASE WHEN ip_8__1_gap < 0 AND ip_8__1_target != 0 THEN 0 ELSE 1 END ) * 100 as ip_8_1,
    //         ( CASE WHEN ip_8__2_gap < 0 AND ip_8__2_target != 0 THEN 0 ELSE 1 END ) * 100 as ip_8_2,
    //         (
    //             CASE
    //                 WHEN ( rekap.bentuk_pendidikan_id = 5 OR rekap.bentuk_pendidikan_id = 9 ) 
    //                 THEN 100 
    //                 ELSE (( CASE WHEN ip_9_gap < 0 AND ip_9_target != 0 THEN 0 ELSE 1 END ) * 100 ) 
    //             END 
    //         ) AS ip_9,
    //         (
    //             CASE
    //                 WHEN ( rekap.bentuk_pendidikan_id = 5 OR rekap.bentuk_pendidikan_id = 9 ) 
    //                 THEN 100 
    //                 ELSE ( ( CASE WHEN ip_10_gap < 0 AND ip_10_target != 0 THEN 0 ELSE 1 END ) * 100 ) 
    //             END 
    //         ) AS ip_10,
    //         (
    //             CASE
    //                 WHEN ( rekap.bentuk_pendidikan_id = 5 OR rekap.bentuk_pendidikan_id = 9 ) 
    //                 THEN 100 
    //                 ELSE ( ( CASE WHEN ip_11_gap < 0 AND ip_11_target != 0 THEN 0 ELSE 1 END ) * 100 ) 
    //             END 
    //         ) AS ip_11,
    //         (
    //             CASE
    //                 WHEN ( rekap.bentuk_pendidikan_id = 5 OR rekap.bentuk_pendidikan_id = 9 ) 
    //                 THEN ( ( ( CASE WHEN ip_14__1_gap < 0 AND ip_14__1_target != 0 THEN 0 ELSE 1 END ) + ( CASE WHEN ip_14__2_gap < 0 AND ip_14__2_target != 0 THEN 0 ELSE 1 END ) ) / CAST ( 2 AS FLOAT ) * 100 ) 
    //                 ELSE 100 
    //             END 
    //         ) AS ip_14,
    //         ( CASE WHEN ip_14__1_gap < 0 AND ip_14__1_target != 0 THEN 0 ELSE 1 END ) * 100 as ip_14_1,
    //         ( CASE WHEN ip_14__2_gap < 0 AND ip_14__2_target != 0 THEN 0 ELSE 1 END ) * 100 as ip_14_2,
    //         (
    //             CASE
    //                 WHEN ( rekap.bentuk_pendidikan_id = 5 OR rekap.bentuk_pendidikan_id = 9 ) 
    //                 THEN ( ( CASE WHEN ip_15__1_gap < 0 AND ip_15__1_target != 0 THEN 0 ELSE 1 END ) * 100 ) 
    //                 ELSE 100 
    //             END 
    //         ) AS ip_15,
    //         ( CASE WHEN ip_15__1_gap < 0 AND ip_15__1_target != 0 THEN 0 ELSE 1 END ) * 100 as ip_15_1,
    //         (
    //             CASE  
    //                 WHEN ( rekap.bentuk_pendidikan_id = 5 OR rekap.bentuk_pendidikan_id = 9 ) 
    //                 THEN 100 
    //                 ELSE ( ( CASE WHEN ip_16__1_gap < 0 AND ip_16__1_target != 0 THEN 0 ELSE 1 END ) * 100 ) 
    //             END 
    //         ) AS ip_16,
    //         ( CASE WHEN ip_16__1_gap < 0 AND ip_16__1_target != 0 THEN 0 ELSE 1 END ) * 100 as ip_16_1,
    //         (
    //             CASE   
    //                 WHEN ( rekap.bentuk_pendidikan_id = 5 OR rekap.bentuk_pendidikan_id = 9 ) 
    //                 THEN 100 
    //                 ELSE ( ( CASE WHEN ip_17_gap < 0 AND ip_17_target != 0 THEN 0 ELSE 1 END ) * 100 ) 
    //             END 
    //         ) AS ip_17,
    //         (
    //             CASE
    //                 WHEN ( rekap.bentuk_pendidikan_id = 5 OR rekap.bentuk_pendidikan_id = 9 ) 
    //                 THEN ( ( ( CASE WHEN ip_18__1_gap < 0 AND ip_18__1_target != 0 THEN 0 ELSE 1 END ) ) / CAST ( 1 AS FLOAT ) * 100 ) ELSE ( ( ( CASE WHEN ip_18__2_gap < 0 AND ip_18__2_target != 0 THEN 0 ELSE 1 END ) ) / CAST ( 1 AS FLOAT ) * 100 ) 
    //             END 
    //         ) AS ip_18,
    //         ( CASE WHEN ip_18__1_gap < 0 AND ip_18__1_target != 0 THEN 0 ELSE 1 END ) * 100 as ip_18_1,
    //         ( CASE WHEN ip_18__2_gap < 0 AND ip_18__2_target != 0 THEN 0 ELSE 1 END ) * 100 as ip_18_2,
    //         ( ( CASE WHEN ip_19__1_gap < 0 AND ip_19__1_target != 0 THEN 0 ELSE 1 END ) * 100 ) AS ip_19,
    //         ( ( CASE WHEN ip_20__1_gap < 0 AND ip_20__1_target != 0 THEN 0 ELSE 1 END ) * 100 ) AS ip_20,
    //         (
    //             CASE   
    //                 WHEN ( rekap.bentuk_pendidikan_id = 5 OR rekap.bentuk_pendidikan_id = 9 ) 
    //                 THEN ( ( ( CASE WHEN ip_21__1_gap < 0 AND ip_21__1_target != 0 THEN 0 ELSE 1 END ) ) / CAST ( 1 AS FLOAT ) * 100 ) ELSE ( ( ( CASE WHEN ip_21__2_gap < 0 AND ip_21__2_target != 0 THEN 0 ELSE 1 END ) ) / CAST ( 1 AS FLOAT ) * 100 ) 
    //             END 
    //         ) AS ip_21,
    //         ( CASE WHEN ip_21__1_gap < 0 AND ip_21__1_target != 0 THEN 0 ELSE 1 END ) * 100 as ip_21_1,
    //         ( CASE WHEN ip_21__2_gap < 0 AND ip_21__2_target != 0 THEN 0 ELSE 1 END ) * 100 as ip_21_2,
    //         (
    //             CASE  
    //                 WHEN ( rekap.bentuk_pendidikan_id = 5 OR rekap.bentuk_pendidikan_id = 9 ) 
    //                 THEN ( ( ( CASE WHEN ip_22__1_gap < 0 AND ip_22__1_target != 0 THEN 0 ELSE 1 END ) + ( CASE WHEN ip_22__2_gap < 0 AND ip_22__2_target != 0 THEN 0 ELSE 1 END ) ) / CAST ( 2 AS FLOAT ) * 100 ) 
    //                 ELSE ( ( ( CASE WHEN ip_22__1_gap < 0 AND ip_22__1_target != 0 THEN 0 ELSE 1 END ) + ( CASE WHEN ip_22__3_gap < 0 AND ip_22__3_target != 0 THEN 0 ELSE 1 END ) ) / CAST ( 2 AS FLOAT ) * 100 ) 
    //             END 
    //         ) AS ip_22,
    //         ( CASE WHEN ip_22__1_gap < 0 AND ip_22__1_target != 0 THEN 0 ELSE 1 END ) * 100 as ip_22_1,
    //         ( CASE WHEN ip_22__2_gap < 0 AND ip_22__2_target != 0 THEN 0 ELSE 1 END ) * 100 as ip_22_2,
    //         ( CASE WHEN ip_22__3_gap < 0 AND ip_22__3_target != 0 THEN 0 ELSE 1 END ) * 100 as ip_22_3,
    //         (
    //             CASE
    //                 WHEN ( rekap.bentuk_pendidikan_id = 5 OR rekap.bentuk_pendidikan_id = 9 ) 
    //                 THEN ( ( ( CASE WHEN ip_23__1_gap < 0 AND ip_23__1_target != 0 THEN 0 ELSE 1 END ) + ( CASE WHEN ip_23__2_gap < 0 AND ip_23__2_target != 0 THEN 0 ELSE 1 END ) ) / CAST ( 2 AS FLOAT ) * 100 ) 
    //                 ELSE ( ( ( CASE WHEN ip_23__1_gap < 0 AND ip_23__1_target != 0 THEN 0 ELSE 1 END ) + ( CASE WHEN ip_23__3_gap < 0 AND ip_23__3_target != 0 THEN 0 ELSE 1 END ) ) / CAST ( 2 AS FLOAT ) * 100 ) 
    //             END 
    //         ) AS ip_23,
    //         ( CASE WHEN ip_23__1_gap < 0 AND ip_23__1_target != 0 THEN 0 ELSE 1 END ) * 100 as ip_23_1,
    //         ( CASE WHEN ip_23__2_gap < 0 AND ip_23__2_target != 0 THEN 0 ELSE 1 END ) * 100 as ip_23_2,
    //         ( CASE WHEN ip_23__3_gap < 0 AND ip_23__3_target != 0 THEN 0 ELSE 1 END ) * 100 as ip_23_3,
    //         (
    //             CASE 
    //                 WHEN ( rekap.bentuk_pendidikan_id = 5 OR rekap.bentuk_pendidikan_id = 9 ) THEN
    //                 ( ( ( CASE WHEN ip_24__1_gap < 0 AND ip_24__1_target != 0 THEN 0 ELSE 1 END ) ) / CAST ( 1 AS FLOAT ) * 100 ) ELSE ( ( ( CASE WHEN ip_24__2_gap < 0 AND ip_24__2_target != 0 THEN 0 ELSE 1 END ) ) / CAST ( 1 AS FLOAT ) * 100 ) 
    //             END 
    //         ) AS ip_24,
    //         ( CASE WHEN ip_24__1_gap < 0 AND ip_24__1_target != 0 THEN 0 ELSE 1 END ) * 100 as ip_24_1,
    //         ( CASE WHEN ip_24__2_gap < 0 AND ip_24__2_target != 0 THEN 0 ELSE 1 END ) * 100 as ip_24_2,
    //         (
    //             CASE  
    //                 WHEN ( rekap.bentuk_pendidikan_id = 5 OR rekap.bentuk_pendidikan_id = 9 ) 
    //                 THEN ( ( ( CASE WHEN ip_25__1_gap < 0 AND ip_25__1_target != 0 THEN 0 ELSE 1 END ) + ( CASE WHEN ip_25__2_gap < 0 AND ip_25__2_target != 0 THEN 0 ELSE 1 END ) ) / CAST ( 2 AS FLOAT ) * 100 ) 
    //                 ELSE ( ( ( CASE WHEN ip_25__1_gap < 0 AND ip_25__1_target != 0 THEN 0 ELSE 1 END ) + ( CASE WHEN ip_25__3_gap < 0 AND ip_25__3_target != 0 THEN 0 ELSE 1 END ) ) / CAST ( 2 AS FLOAT ) * 100 ) 
    //             END 
    //         ) AS ip_25,
    //         ( CASE WHEN ip_25__1_gap < 0 AND ip_25__1_target != 0 THEN 0 ELSE 1 END ) * 100 as ip_25_1,
    //         ( CASE WHEN ip_25__2_gap < 0 AND ip_25__2_target != 0 THEN 0 ELSE 1 END ) * 100 as ip_25_2,
    //         ( CASE WHEN ip_25__3_gap < 0 AND ip_25__3_target != 0 THEN 0 ELSE 1 END ) * 100 as ip_25_3,
    //         (
    //             CASE
    //                 WHEN ( rekap.bentuk_pendidikan_id = 5 OR rekap.bentuk_pendidikan_id = 9 ) 
    //                 THEN ( ( ( CASE WHEN ip_26__1_gap < 0 AND ip_26__1_target != 0 THEN 0 ELSE 1 END ) ) / CAST ( 1 AS FLOAT ) * 100 ) 
    //                 ELSE ( ( ( CASE WHEN ip_26__1_gap < 0 AND ip_26__1_target != 0 THEN 0 ELSE 1 END ) + ( CASE WHEN ip_26__2_gap < 0 AND ip_26__2_target != 0 THEN 0 ELSE 1 END ) + ( CASE WHEN ip_26__3_gap < 0 AND ip_26__3_target != 0 THEN 0 ELSE 1 END ) ) / CAST ( 3 AS FLOAT ) * 100 ) 
    //             END 
    //         ) AS ip_26,
    //         ( CASE WHEN ip_26__1_gap < 0 AND ip_26__1_target != 0 THEN 0 ELSE 1 END ) * 100 as ip_26_1,
    //         ( CASE WHEN ip_26__2_gap < 0 AND ip_26__2_target != 0 THEN 0 ELSE 1 END ) * 100 as ip_26_2,
    //         ( CASE WHEN ip_26__3_gap < 0 AND ip_26__3_target != 0 THEN 0 ELSE 1 END ) * 100 as ip_26_3,
    //         ( ( ( CASE WHEN ip_27__1_gap < 0 AND ip_27__1_target != 0 THEN 0 ELSE 1 END ) + ( CASE WHEN ip_27__2_gap < 0 AND ip_27__2_target != 0 THEN 0 ELSE 1 END ) + ( CASE WHEN ip_27__3_gap < 0 AND ip_27__3_target != 0 THEN 0 ELSE 1 END ) ) / CAST ( 3 AS FLOAT ) * 100 ) AS ip_27,
    //         ( CASE WHEN ip_27__1_gap < 0 AND ip_27__1_target != 0 THEN 0 ELSE 1 END ) * 100 as ip_27_1,
    //         ( CASE WHEN ip_27__2_gap < 0 AND ip_27__2_target != 0 THEN 0 ELSE 1 END ) * 100 as ip_27_2,
    //         ( CASE WHEN ip_27__3_gap < 0 AND ip_27__3_target != 0 THEN 0 ELSE 1 END ) * 100 as ip_27_3
    //     FROM
    //         spm.spm_sekolah_indikator indikator
    //         JOIN rekap_sekolah rekap ON rekap.sekolah_id = indikator.sekolah_id 
    //         WHERE--	
    //         indikator.semester_id = 20181 
    //         AND rekap.semester_id = 20181 
    //         AND indikator.sekolah_id = '".($request->input('sekolah_id') ? $request->input('sekolah_id') : "00A44E47-1526-E111-806C-7D290BED42F0")."'
    //         AND rekap.bentuk_pendidikan_id IN ( 5, 6 )";

    //     return DB::connection('sqlsrv_spm')->select($sql_p);
    // }

    // public function getPencapaian(Request $request){
    //     $sql_p = "select * from spm.spm_sekolah where soft_delete = 0 and sekolah_id = '".($request->input('sekolah_id') ? $request->input('sekolah_id') : "00A44E47-1526-E111-806C-7D290BED42F0")."' and semester_id = '".($request->input('semester_id') ? $request->input('semester_id') : "20191")."'";

    //     return DB::connection('sqlsrv_spm')->select($sql_p);
    // }

    public function getSPMKabupatenPerSekolah(Request $request){
        $sql = "SELECT
            spm.sekolah_id,
            rekap_sekolah.nama as nama,
            round( AVG ( persen ), 0 ) AS persen 
        FROM
            spm.spm_sekolah spm
            JOIN rekap_sekolah ON rekap_sekolah.sekolah_id = spm.sekolah_id 
            AND rekap_sekolah.semester_id = spm.semester_id 
        WHERE
            spm.soft_delete = 0 
            AND rekap_sekolah.soft_delete = 0 
            AND rekap_sekolah.kode_wilayah_kabupaten = '116000' 
        GROUP BY
            spm.sekolah_id,
            rekap_sekolah.nama
        ORDER BY
            round( AVG ( persen ), 2 ) DESC";
        
        return DB::connection('sqlsrv_spm')->select($sql);
    }

    public function getSPMKabupatenPerKecamatan(Request $request){
        $sql = "SELECT
            kode_wilayah_kecamatan,
            kecamatan as wilayah,
            round( AVG ( persen ), 2 ) AS persen 
        FROM
            spm.spm_sekolah spm
            JOIN rekap_sekolah ON rekap_sekolah.sekolah_id = spm.sekolah_id 
            AND rekap_sekolah.semester_id = spm.semester_id 
        WHERE
            spm.soft_delete = 0 
            AND rekap_sekolah.soft_delete = 0 
            AND rekap_sekolah.kode_wilayah_kabupaten = '116000' 
        GROUP BY
            kode_wilayah_kecamatan,
            kecamatan
        ORDER BY
            round( AVG ( persen ), 2 ) DESC";

        return DB::connection('sqlsrv_spm')->select($sql);
    }

    public function getSPMKabupaten(Request $request){
        $sql = "SELECT
            kode_wilayah_kabupaten,
            round(avg(persen),2) as persen,
            min(spm.last_update) as tanggal_rekap_terakhir
        FROM
            spm.spm_sekolah spm
            JOIN rekap_sekolah ON rekap_sekolah.sekolah_id = spm.sekolah_id 
            AND rekap_sekolah.semester_id = spm.semester_id 
        WHERE
            spm.soft_delete = 0 
            AND rekap_sekolah.soft_delete = 0 
            AND rekap_sekolah.kode_wilayah_kabupaten = '".($request->input('kode_wilayah') ? $request->input('kode_wilayah') : '116000' )."' 
            group by kode_wilayah_kabupaten
        ";

        return DB::connection('sqlsrv_spm')->select($sql);
    }

    public function Instrumen(Request $request){

        $param_induk = "";
        $param_tingkat = "AND tingkat = 1";

        if($request->input('kode_instrumen_spm')){
            $param_induk = "AND induk_kode_instrumen_spm = '".$request->input('kode_instrumen_spm')."'";
            $param_tingkat = "";
        }

        $sql = "
        SELECT
            a.*,
            spms.target,
            spms.capaian,
            spms.gap,
            spms.predikat,
            spms.persen,
            spms.satuan,
            (select count(1) as total from spm.instrumen_spm b where b.induk_kode_instrumen_spm = a.kode_instrumen_spm and soft_delete = 0) as anak_total 
        FROM
            spm.instrumen_spm a
            LEFT JOIN spm.spm_sekolah spms on spms.kode_instrumen_spm = a.kode_instrumen_spm and spms.semester_id = '".($request->input('semester_id') ? $request->input('semester_id') : "20191")."' and spms.sekolah_id = '".($request->input('sekolah_id') ? $request->input('sekolah_id') : "00A44E47-1526-E111-806C-7D290BED42F0")."'
        WHERE
            ".($request->input('jenjang') ? $request->input('jenjang') : 'sd')." = 1 
            AND a.soft_delete = 0 
            {$param_induk}
            {$param_tingkat}
        ORDER BY
            a.kode_instrumen_spm
        ";

        // return $sql;die;

        // $pencapaian = self::getPencapaian($request);
        
        $fetch = DB::connection('sqlsrv_spm')->select($sql);

        for ($i=0; $i < sizeof($fetch); $i++) { 

            // if($fetch[$i]->tingkat < 3 && $fetch[$i]->kode_alias != null){
        //         if($fetch[$i]->nama_kolom_pencapaian){
        //             if (property_exists($pencapaian[0], $fetch[$i]->nama_kolom_pencapaian)){
        //                 $fetch[$i]->pencapaian = $pencapaian[0]->{$fetch[$i]->nama_kolom_pencapaian};

        //                 if($fetch[$i]->tingkat > 1){
        //                     $fetch[$i]->pencapaian_target = property_exists($pencapaian[0], $fetch[$i]->nama_kolom_pencapaian."_target") ? $pencapaian[0]->{$fetch[$i]->nama_kolom_pencapaian."_target"} : 0;
        //                     $fetch[$i]->pencapaian_pencapaian = property_exists($pencapaian[0], $fetch[$i]->nama_kolom_pencapaian."_capaian") ? $pencapaian[0]->{$fetch[$i]->nama_kolom_pencapaian."_capaian"} : 0;
        //                     $fetch[$i]->pencapaian_gap = property_exists($pencapaian[0], $fetch[$i]->nama_kolom_pencapaian."_gap") ? $pencapaian[0]->{$fetch[$i]->nama_kolom_pencapaian."_gap"} : 0;
        //                 }
        //             }else{
        //                 $fetch[$i]->pencapaian = "0";

        //                 if($fetch[$i]->tingkat > 1){
        //                     $fetch[$i]->pencapaian_target = "0";
        //                     $fetch[$i]->pencapaian_pencapaian = "0";
        //                     $fetch[$i]->pencapaian_gap = "0";
        //                 }
        //             }
        //         }else{
        //             $fetch[$i]->pencapaian = "0";
        //         }
            // }

            if($fetch[$i]->anak_total > 0){
                $request->merge(['kode_instrumen_spm' => $fetch[$i]->kode_instrumen_spm]);
                $fetch[$i]->children = self::Instrumen($request);

                //hitung persentase induknya

            }else{
                $fetch[$i]->children = [];
            }

            $fetch[$i]->nama = str_replace("\n", "", $fetch[$i]->nama);
            $fetch[$i]->target = (int)$fetch[$i]->target;
            $fetch[$i]->capaian = (int)$fetch[$i]->capaian;
            $fetch[$i]->gap = (int)$fetch[$i]->gap;
            $fetch[$i]->persen = (float)$fetch[$i]->persen;

        }

        $return = array();
        $return['total'] = sizeof($fetch);
        $return['rows'] = $fetch;

        return $return;
    }

    public function InstrumenRoot(Request $request){
        return self::Instrumen($request);
    }
}