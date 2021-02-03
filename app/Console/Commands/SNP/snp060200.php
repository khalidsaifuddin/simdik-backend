<?php

namespace App\Console\Commands\SNP;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

use App\Console\Commands\SNP\snp050000;

class snp060200
{
    static function index($sekolah_id){
        $sql = 'SELECT
            sekolah.sekolah_id,
            kepsek.nuptk_kepsek as nuptk_kepsek,
            sekolah.npsn as npsn,
            ( case when ( ( sarpras.ruang_kelas_total / cast( ( case when rombels.rombel_total < 1 then 1 else rombels.rombel_total end ) as float) * 100 ) / 100 * 7 ) > 7 then 7 else ( ( sarpras.ruang_kelas_total / cast( ( case when rombels.rombel_total < 1 then 1 else rombels.rombel_total end ) as float) * 100 ) / 100 * 7 ) end ) as "06.02.01",
            ( case when sarpras.lab_ipa_total > 0 then 7 else 0 end ) as "06.02.02",
            ( case when sarpras.perpustakaan_total > 0 then 7 else 0 end ) as "06.02.03",
            ( case when sarpras.lapangan_total > 0 then 7 else 0 end ) as "06.02.04",
            ( case when bentuk_pendidikan_id NOT IN (5,6) then ( case when sarpras.lab_biologi_total > 0 then 7 else 0 end ) else 7 end ) as "06.02.05",
            ( case when bentuk_pendidikan_id NOT IN (5,6) then ( case when sarpras.lab_fisika_total > 0 then 7 else 0 end ) else 7 end ) as "06.02.06",
            ( case when bentuk_pendidikan_id NOT IN (5,6) then ( case when sarpras.lab_kimia_total > 0 then 7 else 0 end ) else 7 end ) as "06.02.07",
            ( case when sarpras.lab_komputer_total > 0 then 7 else 0 end ) as "06.02.08",
            ( case when bentuk_pendidikan_id IN (13,15) then ( case when sarpras.lab_bahasa_total > 0 then 7 else 0 end ) else 7 end ) as "06.02.09",
            ( case when ( ( ( cast( sarpras.ruang_kelas_baik as float ) + cast( sarpras.ruang_kelas_rusak_ringan as float ) ) / cast( ( case when sarpras.ruang_kelas_total < 1 then 1 else sarpras.ruang_kelas_total end ) as float ) * 100 ) / 100 * 7 ) > 7 then 7 else ( ( ( cast( sarpras.ruang_kelas_baik as float ) + cast( sarpras.ruang_kelas_rusak_ringan as float ) ) / cast( ( case when sarpras.ruang_kelas_total < 1 then 1 else sarpras.ruang_kelas_total end ) as float ) * 100 ) / 100 * 7 ) end ) as "06.02.10",
            ( case when ( ( ( cast( sarpras.lab_ipa_baik as float ) + cast( sarpras.lab_ipa_rusak_ringan as float ) ) / cast( ( case when sarpras.lab_ipa_total < 1 then 1 else sarpras.lab_ipa_total end ) as float ) * 100 ) / 100 * 7 ) > 7 then 7 else ( ( ( cast( sarpras.lab_ipa_baik as float ) + cast( sarpras.lab_ipa_rusak_ringan as float ) ) / cast( ( case when sarpras.lab_ipa_total < 1 then 1 else sarpras.lab_ipa_total end ) as float ) * 100 ) / 100 * 7 ) end ) as "06.02.11",
            ( case when ( ( ( cast( sarpras.perpustakaan_baik as float ) + cast( sarpras.perpustakaan_rusak_ringan as float ) ) / cast( ( case when sarpras.perpustakaan_total < 1 then 1 else sarpras.perpustakaan_total end ) as float ) * 100 ) / 100 * 7 ) > 7 then 7 else ( ( ( cast( sarpras.perpustakaan_baik as float ) + cast( sarpras.perpustakaan_rusak_ringan as float ) ) / cast( ( case when sarpras.perpustakaan_total < 1 then 1 else sarpras.perpustakaan_total end ) as float ) * 100 ) / 100 * 7 ) end ) as "06.02.12",
            ( case when bentuk_pendidikan_id NOT IN (5,6) then ( ( ( ( cast( sarpras.lab_biologi_baik as float ) + cast( sarpras.lab_biologi_rusak_ringan as float ) ) / ISNULL( cast( ( case when sarpras.lab_biologi_total < 1 then 1 else sarpras.lab_biologi_total end ) as float ), 1 ) * 100 ) / 100 * 7 ) ) else 7 end ) as "06.02.14",
            ( case when bentuk_pendidikan_id NOT IN (5,6) then ( ( ( ( cast( sarpras.lab_fisika_baik as float ) + cast( sarpras.lab_fisika_rusak_ringan as float ) ) / ISNULL( cast( ( case when sarpras.lab_fisika_total < 1 then 1 else sarpras.lab_fisika_total end ) as float ), 1 ) * 100 ) / 100 * 7 ) ) else 7 end ) as "06.02.15",
            ( case when bentuk_pendidikan_id NOT IN (5,6) then ( ( ( ( cast( sarpras.lab_kimia_baik as float ) + cast( sarpras.lab_kimia_rusak_ringan as float ) ) / ISNULL( cast( ( case when sarpras.lab_kimia_total < 1 then 1 else sarpras.lab_kimia_total end ) as float ), 1 ) * 100 ) / 100 * 7 ) ) else 7 end ) as "06.02.16",
            ( case when bentuk_pendidikan_id NOT IN (5,6) then ( ( ( ( cast( sarpras.lab_komputer_baik as float ) + cast( sarpras.lab_komputer_rusak_ringan as float ) ) / ISNULL( cast( ( case when sarpras.lab_komputer_total < 1 then 1 else sarpras.lab_komputer_total end ) as float ), 1 ) * 100 ) / 100 * 7 ) ) else 7 end ) as "06.02.17",
            ( case when bentuk_pendidikan_id NOT IN (5,6) then ( ( ( ( cast( sarpras.lab_bahasa_baik as float ) + cast( sarpras.lab_bahasa_rusak_ringan as float ) ) / ISNULL( cast( ( case when sarpras.lab_bahasa_total < 1 then 1 else sarpras.lab_bahasa_total end ) as float ), 1 ) * 100 ) / 100 * 7 ) ) else 7 end ) as "06.02.18",
            ( case when sarpras.ruang_kepsek_total > 0 then 7 else 0 end ) as "06.03.01",
            ( case when sarpras.ruang_guru_total > 0 then 7 else 0 end ) as "06.03.02",
            ( case when sarpras.ruang_uks_total > 0 then 7 else 0 end ) as "06.03.03",
            ( case when sarpras.ruang_ibadah_total > 0 then 7 else 0 end ) as "06.03.04",
            ( case when sarpras.jamban_total > 0 then 7 else 0 end ) as "06.03.05",
            ( case when sarpras.gudang_total > 0 then 7 else 0 end ) as "06.03.06",
            ( case when sarpras.ruang_sirkulasi_total > 0 then 7 else 0 end ) as "06.03.07",
            ( case when sarpras.ruang_tu_total > 0 then 7 else 0 end ) as "06.03.08",
            ( case when sarpras.ruang_konseling_total > 0 then 7 else 0 end ) as "06.03.09",
            ( case when sarpras.ruang_osis_total > 0 then 7 else 0 end ) as "06.03.10",
            ( ( ( cast( sarpras.ruang_kepsek_baik as float ) + cast( sarpras.ruang_kepsek_rusak_ringan as float ) ) / cast( ( case when sarpras.ruang_kepsek_total < 1 then 1 else sarpras.ruang_kepsek_total end ) as float ) * 100 ) / 100 * 7 ) as "06.03.14",
            ( ( ( cast( sarpras.ruang_guru_baik as float ) + cast( sarpras.ruang_guru_rusak_ringan as float ) ) / cast( ( case when sarpras.ruang_guru_total < 1 then 1 else sarpras.ruang_guru_total end ) as float ) * 100 ) / 100 * 7 ) as "06.03.15",
            ( ( ( cast( sarpras.ruang_uks_baik as float ) + cast( sarpras.ruang_uks_rusak_ringan as float ) ) / cast( ( case when sarpras.ruang_uks_total < 1 then 1 else sarpras.ruang_uks_total end ) as float ) * 100 ) / 100 * 7 ) as "06.03.16",
            ( ( ( cast( sarpras.ruang_ibadah_baik as float ) + cast( sarpras.ruang_ibadah_rusak_ringan as float ) ) / cast( ( case when sarpras.ruang_ibadah_total < 1 then 1 else sarpras.ruang_ibadah_total end ) as float ) * 100 ) / 100 * 7 ) as "06.03.17",
            ( ( ( cast( sarpras.jamban_baik as float ) + cast( sarpras.jamban_rusak_ringan as float ) ) / cast( ( case when sarpras.jamban_total < 1 then 1 else sarpras.jamban_total end ) as float ) * 100 ) / 100 * 7 ) as "06.03.18",
            ( ( ( cast( sarpras.gudang_baik as float ) + cast( sarpras.gudang_rusak_ringan as float ) ) / cast( ( case when sarpras.gudang_total < 1 then 1 else sarpras.gudang_total end ) as float ) * 100 ) / 100 * 7 ) as "06.03.19",
            ( ( ( cast( sarpras.ruang_sirkulasi_baik as float ) + cast( sarpras.ruang_sirkulasi_rusak_ringan as float ) ) / cast( ( case when sarpras.ruang_sirkulasi_total < 1 then 1 else sarpras.ruang_sirkulasi_total end ) as float ) * 100 ) / 100 * 7 ) as "06.03.20",
            ( ( ( cast( sarpras.ruang_tu_baik as float ) + cast( sarpras.ruang_tu_rusak_ringan as float ) ) / cast( ( case when sarpras.ruang_tu_total < 1 then 1 else sarpras.ruang_tu_total end ) as float ) * 100 ) / 100 * 7 ) as "06.03.21",
            ( ( ( cast( sarpras.ruang_konseling_baik as float ) + cast( sarpras.ruang_konseling_rusak_ringan as float ) ) / cast( ( case when sarpras.ruang_konseling_total < 1 then 1 else sarpras.ruang_konseling_total end ) as float ) * 100 ) / 100 * 7 ) as "06.03.22",
            ( ( ( cast( sarpras.ruang_osis_baik as float ) + cast( sarpras.ruang_osis_rusak_ringan as float ) ) / cast( ( case when sarpras.ruang_osis_total < 1 then 1 else sarpras.ruang_osis_total end ) as float ) * 100 ) / 100 * 7 ) as "06.03.23",

            ( case when kepsek.jenjang_pendidikan_id > 22 then 7 else 0 end ) as "05.02.01",
            ( case when kepsek.pangkat_golongan_id > 10 then 7 else 0 end ) as "05.02.04",
            ( case when kepsek.sertifikat_pendidik > 0 then 7 else 0 end ) as "05.02.05",
            ( case when kepsek.sertifikat_kepsek > 0 then 7 else 0 end ) as "05.02.06",

            ( case when tugas_tambahan.kepala_tas > 0 then 7 else 0 end ) as "05.03.01",
            ( case when tugas_tambahan.kepala_tas_formal > 0 then 7 else 0 end ) as "05.03.02",
            ( case when tugas_tambahan.kepala_tas_sertifikasi > 0 then 7 else 0 end ) as "05.03.03",
            
            ( case when bentuk_pendidikan_id NOT IN (5,6) then ( case when tugas_tambahan.kepala_lab_formal > 0 then 7 else 0 end ) else 7 end ) as "05.04.02",
            ( case when bentuk_pendidikan_id NOT IN (5,6) then ( case when tugas_tambahan.kepala_lab_sertifikasi > 0 then 7 else 0 end ) else 7 end ) as "05.04.03",
            
            ( case when bentuk_pendidikan_id NOT IN (5,6) then ( case when tugas_tambahan.kepala_teknisi_lab > 0 then 7 else 0 end ) else 7 end ) as "05.04.05",
            ( case when bentuk_pendidikan_id NOT IN (5,6) then ( case when tugas_tambahan.kepala_teknisi_lab_formal > 0 then 7 else 0 end ) else 7 end ) as "05.04.06",
            
            ( case when bentuk_pendidikan_id NOT IN (5,6) then ( case when tugas_tambahan.teknisi_lab > 0 then 7 else 0 end ) else 7 end ) as "05.04.07",
            ( case when bentuk_pendidikan_id NOT IN (5,6) then ( case when tugas_tambahan.teknisi_lab_formal > 0 then 7 else 0 end ) else 7 end ) as "05.04.08",
            
            ( case when tugas_tambahan.kepala_pustakawan > 0 then 7 else 0 end ) as "05.05.02",
            ( case when tugas_tambahan.kepala_pustakawan_sertifikasi > 0 then 7 else 0 end ) as "05.05.03",
            ( case when tugas_tambahan.kepala_pustakawan_formal > 0 then 7 else 0 end ) as "05.05.06"

        FROM
            sekolah
            LEFT JOIN (
            SELECT
                prasarana.sekolah_id,
                
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 1 AND ( prasarana.id_ruang IS NOT NULL ) THEN 1 ELSE 0 END ) ) AS ruang_kelas_total,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 1 AND ( persentase = 0 ) THEN 1 ELSE 0 END ) ) AS ruang_kelas_baik,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 1 AND ( persentase > 0 AND persentase <= 30 ) THEN 1 ELSE 0 END ) ) AS ruang_kelas_rusak_ringan,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 1 AND ( persentase > 30 AND persentase <= 45 ) THEN 1 ELSE 0 END ) ) AS ruang_kelas_rusak_sedang,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 1 AND ( persentase > 45 AND persentase <= 65 ) THEN 1 ELSE 0 END ) ) AS ruang_kelas_rusak_berat,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id = 1 AND ( persentase > 65 ) THEN 1 ELSE 0 END ) ) AS ruang_kelas_perlu_dibangun_ulang,

                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (2,3,4,5) AND ( prasarana.id_ruang IS NOT NULL ) THEN 1 ELSE 0 END ) ) AS lab_ipa_total,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (2,3,4,5) AND ( persentase = 0 ) THEN 1 ELSE 0 END ) ) AS lab_ipa_baik,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (2,3,4,5) AND ( persentase > 0 AND persentase <= 30 ) THEN 1 ELSE 0 END ) ) AS lab_ipa_rusak_ringan,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (2,3,4,5) AND ( persentase > 30 AND persentase <= 45 ) THEN 1 ELSE 0 END ) ) AS lab_ipa_rusak_sedang,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (2,3,4,5) AND ( persentase > 45 AND persentase <= 65 ) THEN 1 ELSE 0 END ) ) AS lab_ipa_rusak_berat,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (2,3,4,5) AND ( persentase > 65 ) THEN 1 ELSE 0 END ) ) AS lab_ipa_perlu_dibangun_ulang,

                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (10,11) AND ( prasarana.id_ruang IS NOT NULL ) THEN 1 ELSE 0 END ) ) AS perpustakaan_total,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (10,11) AND ( persentase = 0 ) THEN 1 ELSE 0 END ) ) AS perpustakaan_baik,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (10,11) AND ( persentase > 0 AND persentase <= 30 ) THEN 1 ELSE 0 END ) ) AS perpustakaan_rusak_ringan,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (10,11) AND ( persentase > 30 AND persentase <= 45 ) THEN 1 ELSE 0 END ) ) AS perpustakaan_rusak_sedang,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (10,11) AND ( persentase > 45 AND persentase <= 65 ) THEN 1 ELSE 0 END ) ) AS perpustakaan_rusak_berat,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (10,11) AND ( persentase > 65 ) THEN 1 ELSE 0 END ) ) AS perpustakaan_perlu_dibangun_ulang,
                
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (54,20002,20003) AND ( prasarana.id_ruang IS NOT NULL ) THEN 1 ELSE 0 END ) ) AS lapangan_total,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (54,20002,20003) AND ( persentase = 0 ) THEN 1 ELSE 0 END ) ) AS lapangan_baik,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (54,20002,20003) AND ( persentase > 0 AND persentase <= 30 ) THEN 1 ELSE 0 END ) ) AS lapangan_rusak_ringan,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (54,20002,20003) AND ( persentase > 30 AND persentase <= 45 ) THEN 1 ELSE 0 END ) ) AS lapangan_rusak_sedang,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (54,20002,20003) AND ( persentase > 45 AND persentase <= 65 ) THEN 1 ELSE 0 END ) ) AS lapangan_rusak_berat,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (54,20002,20003) AND ( persentase > 65 ) THEN 1 ELSE 0 END ) ) AS lapangan_perlu_dibangun_ulang,

                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (3) AND ( prasarana.id_ruang IS NOT NULL ) THEN 1 ELSE 0 END ) ) AS lab_kimia_total,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (3) AND ( persentase = 0 ) THEN 1 ELSE 0 END ) ) AS lab_kimia_baik,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (3) AND ( persentase > 0 AND persentase <= 30 ) THEN 1 ELSE 0 END ) ) AS lab_kimia_rusak_ringan,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (3) AND ( persentase > 30 AND persentase <= 45 ) THEN 1 ELSE 0 END ) ) AS lab_kimia_rusak_sedang,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (3) AND ( persentase > 45 AND persentase <= 65 ) THEN 1 ELSE 0 END ) ) AS lab_kimia_rusak_berat,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (3) AND ( persentase > 65 ) THEN 1 ELSE 0 END ) ) AS lab_kimia_perlu_dibangun_ulang,

                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (4) AND ( prasarana.id_ruang IS NOT NULL ) THEN 1 ELSE 0 END ) ) AS lab_fisika_total,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (4) AND ( persentase = 0 ) THEN 1 ELSE 0 END ) ) AS lab_fisika_baik,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (4) AND ( persentase > 0 AND persentase <= 30 ) THEN 1 ELSE 0 END ) ) AS lab_fisika_rusak_ringan,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (4) AND ( persentase > 30 AND persentase <= 45 ) THEN 1 ELSE 0 END ) ) AS lab_fisika_rusak_sedang,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (4) AND ( persentase > 45 AND persentase <= 65 ) THEN 1 ELSE 0 END ) ) AS lab_fisika_rusak_berat,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (4) AND ( persentase > 65 ) THEN 1 ELSE 0 END ) ) AS lab_fisika_perlu_dibangun_ulang,

                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (5) AND ( prasarana.id_ruang IS NOT NULL ) THEN 1 ELSE 0 END ) ) AS lab_biologi_total,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (5) AND ( persentase = 0 ) THEN 1 ELSE 0 END ) ) AS lab_biologi_baik,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (5) AND ( persentase > 0 AND persentase <= 30 ) THEN 1 ELSE 0 END ) ) AS lab_biologi_rusak_ringan,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (5) AND ( persentase > 30 AND persentase <= 45 ) THEN 1 ELSE 0 END ) ) AS lab_biologi_rusak_sedang,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (5) AND ( persentase > 45 AND persentase <= 65 ) THEN 1 ELSE 0 END ) ) AS lab_biologi_rusak_berat,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (5) AND ( persentase > 65 ) THEN 1 ELSE 0 END ) ) AS lab_biologi_perlu_dibangun_ulang,

                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (8) AND ( prasarana.id_ruang IS NOT NULL ) THEN 1 ELSE 0 END ) ) AS lab_komputer_total,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (8) AND ( persentase = 0 ) THEN 1 ELSE 0 END ) ) AS lab_komputer_baik,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (8) AND ( persentase > 0 AND persentase <= 30 ) THEN 1 ELSE 0 END ) ) AS lab_komputer_rusak_ringan,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (8) AND ( persentase > 30 AND persentase <= 45 ) THEN 1 ELSE 0 END ) ) AS lab_komputer_rusak_sedang,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (8) AND ( persentase > 45 AND persentase <= 65 ) THEN 1 ELSE 0 END ) ) AS lab_komputer_rusak_berat,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (8) AND ( persentase > 65 ) THEN 1 ELSE 0 END ) ) AS lab_komputer_perlu_dibangun_ulang,

                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (6) AND ( prasarana.id_ruang IS NOT NULL ) THEN 1 ELSE 0 END ) ) AS lab_bahasa_total,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (6) AND ( persentase = 0 ) THEN 1 ELSE 0 END ) ) AS lab_bahasa_baik,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (6) AND ( persentase > 0 AND persentase <= 30 ) THEN 1 ELSE 0 END ) ) AS lab_bahasa_rusak_ringan,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (6) AND ( persentase > 30 AND persentase <= 45 ) THEN 1 ELSE 0 END ) ) AS lab_bahasa_rusak_sedang,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (6) AND ( persentase > 45 AND persentase <= 65 ) THEN 1 ELSE 0 END ) ) AS lab_bahasa_rusak_berat,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (6) AND ( persentase > 65 ) THEN 1 ELSE 0 END ) ) AS lab_bahasa_perlu_dibangun_ulang,

                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (22) AND ( prasarana.id_ruang IS NOT NULL ) THEN 1 ELSE 0 END ) ) AS ruang_kepsek_total,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (22) AND ( persentase = 0 ) THEN 1 ELSE 0 END ) ) AS ruang_kepsek_baik,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (22) AND ( persentase > 0 AND persentase <= 30 ) THEN 1 ELSE 0 END ) ) AS ruang_kepsek_rusak_ringan,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (22) AND ( persentase > 30 AND persentase <= 45 ) THEN 1 ELSE 0 END ) ) AS ruang_kepsek_rusak_sedang,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (22) AND ( persentase > 45 AND persentase <= 65 ) THEN 1 ELSE 0 END ) ) AS ruang_kepsek_rusak_berat,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (22) AND ( persentase > 65 ) THEN 1 ELSE 0 END ) ) AS ruang_kepsek_perlu_dibangun_ulang,

                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (23) AND ( prasarana.id_ruang IS NOT NULL ) THEN 1 ELSE 0 END ) ) AS ruang_guru_total,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (23) AND ( persentase = 0 ) THEN 1 ELSE 0 END ) ) AS ruang_guru_baik,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (23) AND ( persentase > 0 AND persentase <= 30 ) THEN 1 ELSE 0 END ) ) AS ruang_guru_rusak_ringan,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (23) AND ( persentase > 30 AND persentase <= 45 ) THEN 1 ELSE 0 END ) ) AS ruang_guru_rusak_sedang,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (23) AND ( persentase > 45 AND persentase <= 65 ) THEN 1 ELSE 0 END ) ) AS ruang_guru_rusak_berat,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (23) AND ( persentase > 65 ) THEN 1 ELSE 0 END ) ) AS ruang_guru_perlu_dibangun_ulang,

                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (14) AND ( prasarana.id_ruang IS NOT NULL ) THEN 1 ELSE 0 END ) ) AS ruang_uks_total,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (14) AND ( persentase = 0 ) THEN 1 ELSE 0 END ) ) AS ruang_uks_baik,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (14) AND ( persentase > 0 AND persentase <= 30 ) THEN 1 ELSE 0 END ) ) AS ruang_uks_rusak_ringan,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (14) AND ( persentase > 30 AND persentase <= 45 ) THEN 1 ELSE 0 END ) ) AS ruang_uks_rusak_sedang,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (14) AND ( persentase > 45 AND persentase <= 65 ) THEN 1 ELSE 0 END ) ) AS ruang_uks_rusak_berat,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (14) AND ( persentase > 65 ) THEN 1 ELSE 0 END ) ) AS ruang_uks_perlu_dibangun_ulang,

                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (31) AND ( prasarana.id_ruang IS NOT NULL ) THEN 1 ELSE 0 END ) ) AS ruang_ibadah_total,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (31) AND ( persentase = 0 ) THEN 1 ELSE 0 END ) ) AS ruang_ibadah_baik,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (31) AND ( persentase > 0 AND persentase <= 30 ) THEN 1 ELSE 0 END ) ) AS ruang_ibadah_rusak_ringan,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (31) AND ( persentase > 30 AND persentase <= 45 ) THEN 1 ELSE 0 END ) ) AS ruang_ibadah_rusak_sedang,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (31) AND ( persentase > 45 AND persentase <= 65 ) THEN 1 ELSE 0 END ) ) AS ruang_ibadah_rusak_berat,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (31) AND ( persentase > 65 ) THEN 1 ELSE 0 END ) ) AS ruang_ibadah_perlu_dibangun_ulang,

                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (26,27,28,29) AND ( prasarana.id_ruang IS NOT NULL ) THEN 1 ELSE 0 END ) ) AS jamban_total,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (26,27,28,29) AND ( persentase = 0 ) THEN 1 ELSE 0 END ) ) AS jamban_baik,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (26,27,28,29) AND ( persentase > 0 AND persentase <= 30 ) THEN 1 ELSE 0 END ) ) AS jamban_rusak_ringan,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (26,27,28,29) AND ( persentase > 30 AND persentase <= 45 ) THEN 1 ELSE 0 END ) ) AS jamban_rusak_sedang,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (26,27,28,29) AND ( persentase > 45 AND persentase <= 65 ) THEN 1 ELSE 0 END ) ) AS jamban_rusak_berat,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (26,27,28,29) AND ( persentase > 65 ) THEN 1 ELSE 0 END ) ) AS jamban_perlu_dibangun_ulang,

                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (30) AND ( prasarana.id_ruang IS NOT NULL ) THEN 1 ELSE 0 END ) ) AS gudang_total,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (30) AND ( persentase = 0 ) THEN 1 ELSE 0 END ) ) AS gudang_baik,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (30) AND ( persentase > 0 AND persentase <= 30 ) THEN 1 ELSE 0 END ) ) AS gudang_rusak_ringan,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (30) AND ( persentase > 30 AND persentase <= 45 ) THEN 1 ELSE 0 END ) ) AS gudang_rusak_sedang,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (30) AND ( persentase > 45 AND persentase <= 65 ) THEN 1 ELSE 0 END ) ) AS gudang_rusak_berat,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (30) AND ( persentase > 65 ) THEN 1 ELSE 0 END ) ) AS gudang_perlu_dibangun_ulang,

                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (53) AND ( prasarana.id_ruang IS NOT NULL ) THEN 1 ELSE 0 END ) ) AS ruang_sirkulasi_total,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (53) AND ( persentase = 0 ) THEN 1 ELSE 0 END ) ) AS ruang_sirkulasi_baik,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (53) AND ( persentase > 0 AND persentase <= 30 ) THEN 1 ELSE 0 END ) ) AS ruang_sirkulasi_rusak_ringan,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (53) AND ( persentase > 30 AND persentase <= 45 ) THEN 1 ELSE 0 END ) ) AS ruang_sirkulasi_rusak_sedang,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (53) AND ( persentase > 45 AND persentase <= 65 ) THEN 1 ELSE 0 END ) ) AS ruang_sirkulasi_rusak_berat,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (53) AND ( persentase > 65 ) THEN 1 ELSE 0 END ) ) AS ruang_sirkulasi_perlu_dibangun_ulang,

                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (24) AND ( prasarana.id_ruang IS NOT NULL ) THEN 1 ELSE 0 END ) ) AS ruang_tu_total,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (24) AND ( persentase = 0 ) THEN 1 ELSE 0 END ) ) AS ruang_tu_baik,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (24) AND ( persentase > 0 AND persentase <= 30 ) THEN 1 ELSE 0 END ) ) AS ruang_tu_rusak_ringan,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (24) AND ( persentase > 30 AND persentase <= 45 ) THEN 1 ELSE 0 END ) ) AS ruang_tu_rusak_sedang,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (24) AND ( persentase > 45 AND persentase <= 65 ) THEN 1 ELSE 0 END ) ) AS ruang_tu_rusak_berat,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (24) AND ( persentase > 65 ) THEN 1 ELSE 0 END ) ) AS ruang_tu_perlu_dibangun_ulang,

                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (49) AND ( prasarana.id_ruang IS NOT NULL ) THEN 1 ELSE 0 END ) ) AS ruang_konseling_total,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (49) AND ( persentase = 0 ) THEN 1 ELSE 0 END ) ) AS ruang_konseling_baik,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (49) AND ( persentase > 0 AND persentase <= 30 ) THEN 1 ELSE 0 END ) ) AS ruang_konseling_rusak_ringan,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (49) AND ( persentase > 30 AND persentase <= 45 ) THEN 1 ELSE 0 END ) ) AS ruang_konseling_rusak_sedang,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (49) AND ( persentase > 45 AND persentase <= 65 ) THEN 1 ELSE 0 END ) ) AS ruang_konseling_rusak_berat,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (49) AND ( persentase > 65 ) THEN 1 ELSE 0 END ) ) AS ruang_konseling_perlu_dibangun_ulang,

                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (25) AND ( prasarana.id_ruang IS NOT NULL ) THEN 1 ELSE 0 END ) ) AS ruang_osis_total,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (25) AND ( persentase = 0 ) THEN 1 ELSE 0 END ) ) AS ruang_osis_baik,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (25) AND ( persentase > 0 AND persentase <= 30 ) THEN 1 ELSE 0 END ) ) AS ruang_osis_rusak_ringan,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (25) AND ( persentase > 30 AND persentase <= 45 ) THEN 1 ELSE 0 END ) ) AS ruang_osis_rusak_sedang,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (25) AND ( persentase > 45 AND persentase <= 65 ) THEN 1 ELSE 0 END ) ) AS ruang_osis_rusak_berat,
                ( SUM ( CASE WHEN prasarana.jenis_prasarana_id IN (25) AND ( persentase > 65 ) THEN 1 ELSE 0 END ) ) AS ruang_osis_perlu_dibangun_ulang

            FROM
                ruang prasarana WITH ( nolock )
                JOIN ruang_longitudinal prl WITH ( nolock ) ON prl.id_ruang = prasarana.id_ruang 
                AND prl.soft_delete = 0 
                AND prl.semester_id = 20191
                JOIN bangunan WITH ( nolock ) ON prasarana.id_bangunan = bangunan.id_bangunan
                JOIN bangunan_longitudinal WITH ( nolock ) ON bangunan.id_bangunan = bangunan_longitudinal.id_bangunan 
                AND bangunan_longitudinal.soft_delete = 0 
                AND bangunan_longitudinal.semester_id = 20191
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
                    AND prasarana_longitudinal.semester_id = 20191 
                    AND bangunan_longitudinal.soft_delete = 0 
                    AND bangunan_longitudinal.semester_id = 20191 
                ) persen ON persen.id_ruang = prasarana.id_ruang 
            WHERE
                prasarana.soft_delete = 0 
            GROUP BY
                prasarana.sekolah_id 
            ) sarpras ON sarpras.sekolah_id = sekolah.sekolah_id
            LEFT JOIN (
            SELECT
                sekolah.sekolah_id,
                SUM ( 1 ) AS rombel_total 
            FROM
                rombongan_belajar WITH ( nolock )
                JOIN ref.kurikulum kurikulum WITH ( nolock ) ON kurikulum.kurikulum_id = rombongan_belajar.kurikulum_id
                JOIN sekolah WITH ( nolock ) ON sekolah.sekolah_id = rombongan_belajar.sekolah_id
                JOIN ref.mst_wilayah kec WITH ( nolock ) ON kec.kode_wilayah = LEFT ( sekolah.kode_wilayah, 6 )
                JOIN ref.mst_wilayah kab WITH ( nolock ) ON kab.kode_wilayah = kec.mst_kode_wilayah
                JOIN ref.mst_wilayah prop WITH ( nolock ) ON prop.kode_wilayah = kab.mst_kode_wilayah 
            WHERE
                rombongan_belajar.jenis_rombel IN ( 1, 8, 9, 10, 11, 12, 13 ) 
                AND rombongan_belajar.Soft_delete = 0 
                AND rombongan_belajar.semester_id = 20191 
            GROUP BY
                rombongan_belajar.sekolah_id,
                sekolah.sekolah_id,
                sekolah.nama,
                sekolah.npsn,
                prop.nama,
                kab.nama,
                kec.nama 
            ) rombels ON rombels.sekolah_id = sekolah.sekolah_id 
            LEFT JOIN (
                SELECT
                    ROW_NUMBER () OVER ( PARTITION BY ptkt.sekolah_id ORDER BY ptkt.tanggal_surat_tugas DESC ) AS urutan,
                    ptkt.sekolah_id,
                    ptk.nama AS nama_kepsek,
                    ptk.nip AS nip_kepsek,
                    ptk.nuptk as nuptk_kepsek,
                    ptk.no_hp AS hp_kepsek,
                    ptkt.tanggal_surat_tugas,
                    formal.jenjang_pendidikan_id,
                    pangkat.pangkat_golongan_id,
                    sertifikasi.sertifikat_pendidik,
                    sertifikasi.sertifikat_kepsek
                FROM
                    ptk WITH ( nolock )
                    JOIN ptk_terdaftar ptkt WITH ( nolock ) ON ptkt.ptk_id = ptk.ptk_id 
                    AND ptkt.Soft_delete = 0 
                    AND ptkt.tahun_ajaran_id = 2019 
                    AND ptkt.jenis_keluar_id IS NULL 
                    JOIN sekolah WITH ( nolock ) ON sekolah.sekolah_id = ptkt.sekolah_id 
                    AND sekolah.Soft_delete = 0 
                    LEFT JOIN (
                    SELECT
                        ptk_id,
                        MAX ( formal.jenjang_pendidikan_id ) AS jenjang_pendidikan_id 
                    FROM
                        rwy_pend_formal formal 
                    WHERE
                        formal.Soft_delete = 0 
                        AND formal.jenjang_pendidikan_id < 90 
                    GROUP BY
                        ptk_id
                    ) formal on formal.ptk_id = ptk.ptk_id
                    LEFT JOIN (
                        SELECT
                            ptk_id,
                            MAX ( pangkat.pangkat_golongan_id ) AS pangkat_golongan_id 
                        FROM
                            rwy_kepangkatan pangkat 
                        WHERE
                            pangkat.Soft_delete = 0 
                            AND pangkat.pangkat_golongan_id < 99 
                        GROUP BY
                            ptk_id
                    ) pangkat on pangkat.ptk_id = ptk.ptk_id
                    LEFT JOIN (
                        SELECT
                            ptk_id,
                            SUM( case when serti.id_jenis_sertifikasi = 1 then 1 else 0 end ) as sertifikat_pendidik,
                            SUM( case when serti.id_jenis_sertifikasi IN (8,992) then 1 else 0 end ) as sertifikat_kepsek
                        FROM
                            rwy_sertifikasi serti
                        WHERE
                            serti.Soft_delete = 0 
                        GROUP BY
                            serti.ptk_id
                    ) sertifikasi on sertifikasi.ptk_id = ptk.ptk_id
                WHERE
                    ptk.jenis_ptk_id = 20 
                    AND ptk.Soft_delete = 0
            ) kepsek on kepsek.sekolah_id = sekolah.sekolah_id and kepsek.urutan = 1

            LEFT JOIN (
                SELECT
                ptkt.sekolah_id,
                SUM ( CASE WHEN kepala_tas.ptk_id IS NOT NULL THEN 1 ELSE 0 END ) AS kepala_tas,
                SUM ( CASE WHEN kepala_lab.ptk_id IS NOT NULL THEN 1 ELSE 0 END ) AS kepala_lab,
                SUM ( CASE WHEN kepala_lab.ptk_id IS NOT NULL THEN 1 ELSE 0 END ) AS kepala_teknisi_lab,
                SUM ( CASE WHEN teknisi_lab.ptk_id IS NOT NULL THEN 1 ELSE 0 END ) AS teknisi_lab,
                SUM ( CASE WHEN kepala_pustakawan.ptk_id IS NOT NULL THEN 1 ELSE 0 END ) AS kepala_pustakawan,
                SUM ( CASE WHEN kepala_tas.ptk_id IS NOT NULL AND formal.jenjang_pendidikan_id > 5 THEN 1 ELSE 0 END ) AS kepala_tas_formal,
                SUM ( CASE WHEN kepala_lab.ptk_id IS NOT NULL AND formal.jenjang_pendidikan_id > 5 THEN 1 ELSE 0 END ) AS kepala_lab_formal,
                SUM ( CASE WHEN kepala_lab.ptk_id IS NOT NULL AND formal.jenjang_pendidikan_id > 5 THEN 1 ELSE 0 END ) AS kepala_teknisi_lab_formal,
                SUM ( CASE WHEN teknisi_lab.ptk_id IS NOT NULL AND formal.jenjang_pendidikan_id > 5 THEN 1 ELSE 0 END ) AS teknisi_lab_formal,
                SUM ( CASE WHEN kepala_pustakawan.ptk_id IS NOT NULL AND formal.jenjang_pendidikan_id > 5 THEN 1 ELSE 0 END ) AS kepala_pustakawan_formal,
                SUM ( CASE WHEN kepala_tas.ptk_id IS NOT NULL AND sertifikasi.jumlah > 0 THEN 1 ELSE 0 END ) AS kepala_tas_sertifikasi,
                SUM ( CASE WHEN kepala_lab.ptk_id IS NOT NULL AND sertifikasi.jumlah > 0 THEN 1 ELSE 0 END ) AS kepala_lab_sertifikasi,
                SUM ( CASE WHEN kepala_lab.ptk_id IS NOT NULL AND sertifikasi.jumlah > 0 THEN 1 ELSE 0 END ) AS kepala_teknisi_lab_sertifikasi,
                SUM ( CASE WHEN kepala_pustakawan.ptk_id IS NOT NULL AND sertifikasi.jumlah > 0 THEN 1 ELSE 0 END ) AS kepala_pustakawan_sertifikasi 
            FROM
                ptk WITH ( nolock )
                JOIN ptk_terdaftar ptkt WITH ( nolock ) ON ptkt.ptk_id = ptk.ptk_id 
                AND ptkt.Soft_delete = 0 
                AND ptkt.tahun_ajaran_id = 2019 
                AND ptkt.jenis_keluar_id
                IS NULL JOIN sekolah WITH ( nolock ) ON sekolah.sekolah_id = ptkt.sekolah_id 
                AND sekolah.Soft_delete = 0
                LEFT JOIN (
                SELECT
                    ROW_NUMBER () OVER ( PARTITION BY tugas_tambahan.ptk_id ORDER BY tugas_tambahan.jabatan_ptk_id DESC ) AS urutan,
                    tugas_tambahan.ptk_id 
                FROM
                    tugas_tambahan 
                WHERE
                    tugas_tambahan.Soft_delete = 0 
                    AND tst_tambahan IS NULL 
                    AND jabatan_ptk_id = 5 
                ) kepala_tas ON kepala_tas.ptk_id = ptk.ptk_id 
                AND kepala_tas.urutan = 1
                LEFT JOIN (
                    SELECT
                        ROW_NUMBER () OVER ( PARTITION BY tugas_tambahan.ptk_id ORDER BY tugas_tambahan.jabatan_ptk_id DESC ) AS urutan,
                        tugas_tambahan.ptk_id 
                    FROM
                        tugas_tambahan 
                    WHERE
                        tugas_tambahan.Soft_delete = 0 
                        AND tst_tambahan IS NULL 
                        AND jabatan_ptk_id IN ( 7, 25 ) 
                ) kepala_lab ON kepala_lab.ptk_id = ptk.ptk_id 
                AND kepala_lab.urutan = 1
                LEFT JOIN (
                    SELECT
                        ROW_NUMBER () OVER ( PARTITION BY tugas_tambahan.ptk_id ORDER BY tugas_tambahan.jabatan_ptk_id DESC ) AS urutan,
                        tugas_tambahan.ptk_id 
                    FROM
                        tugas_tambahan 
                    WHERE
                        tugas_tambahan.Soft_delete = 0 
                        AND tst_tambahan IS NULL 
                        AND jabatan_ptk_id IN ( 3, 26 ) 
                    ) kepala_pustakawan ON kepala_pustakawan.ptk_id = ptk.ptk_id 
                    AND kepala_pustakawan.urutan = 1
                    LEFT JOIN (
                        SELECT
                            ROW_NUMBER () OVER ( PARTITION BY tugas_tambahan.ptk_id ORDER BY tugas_tambahan.jabatan_ptk_id DESC ) AS urutan,
                            tugas_tambahan.ptk_id 
                        FROM
                            tugas_tambahan 
                        WHERE
                            tugas_tambahan.Soft_delete = 0 
                            AND tst_tambahan IS NULL 
                            AND jabatan_ptk_id IN ( 8 ) 
                    ) teknisi_lab ON teknisi_lab.ptk_id = ptk.ptk_id 
                    AND teknisi_lab.urutan = 1
                    LEFT JOIN ( SELECT ptk_id, MAX ( formal.jenjang_pendidikan_id ) AS jenjang_pendidikan_id FROM rwy_pend_formal formal WHERE formal.Soft_delete = 0 AND formal.jenjang_pendidikan_id < 90 GROUP BY ptk_id ) formal ON formal.ptk_id = ptk.ptk_id
                    LEFT JOIN ( SELECT ptk_id, SUM ( 1 ) AS jumlah FROM rwy_sertifikasi serti WHERE serti.Soft_delete = 0 GROUP BY serti.ptk_id ) sertifikasi ON sertifikasi.ptk_id = ptk.ptk_id 
                WHERE
                    ptk.Soft_delete = 0 
            GROUP BY
                ptkt.sekolah_id
            ) tugas_tambahan on tugas_tambahan.sekolah_id = sekolah.sekolah_id
        WHERE
            sekolah.soft_delete = 0 
            AND sekolah.sekolah_id = \''.$sekolah_id.'\'';
        
        $fetch = DB::connection('sqlsrv')->select($sql);

        // start of ukks
        snp050000::index($fetch[0]->sekolah_id, $fetch[0]->nuptk_kepsek, $fetch[0]->npsn);
        // echo $fetch[0]->nuptk_kepsek.PHP_EOL;
        // end of ukks

        if(sizeof($fetch) > 0){

            foreach ($fetch[0] as $key => $value) {
                if($key != 'sekolah_id' && $key != 'nuptk_kepsek' && $key != 'npsn'){

                    $sql_update = "update 
                                        master_pmp 
                                    set 
                                        r19 = '".$value."', 
                                        last_update = DATEADD(mi, 30, getdate())
                                    where 
                                        sekolah_id = '".$sekolah_id."' 
                                    and urut = '".$key."' 
                                    and r19 < ".$value."";
        
                    try {
                        DB::connection('sqlsrv_pmp')->statement($sql_update);
        
                        echo "[INF] [".$sekolah_id."] MASTER_PMP ".$key." [BERHASIL]".PHP_EOL;
                    } catch (\Throwable $th) {
                        echo "[INF] [".$sekolah_id."] MASTER_PMP ".$key." [GAGAL]".PHP_EOL;
                    }

                }   
            }

        }
            

    }
}