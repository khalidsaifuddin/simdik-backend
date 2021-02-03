SELECT
-- 		top 100
    newid() AS rapor_pd_id,
    20191 AS semester_id,
    peserta_didik.peserta_didik_id,
    rpd.sekolah_id ,
    peserta_didik.nama,
    peserta_didik.nisn,
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
    peserta_didik -- LEFT
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
        AND rombongan_belajar.semester_id = 20191
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
    ) rpd ON rpd.peserta_didik_id = peserta_didik.peserta_didik_id AND rpd.sekolah_id = ar.sekolah_id -- 	AND rpd.urutan = 1
    LEFT JOIN peserta_didik_longitudinal pdl on pdl.peserta_didik_id = peserta_didik.peserta_didik_id and pdl.semester_id = 20191
    JOIN sekolah on sekolah.sekolah_id = rpd.sekolah_id and sekolah.soft_delete = 0
    JOIN ref.mst_wilayah kec on kec.kode_wilayah = left(sekolah.kode_wilayah,6)
    JOIN ref.mst_wilayah kab on kab.kode_wilayah = kec.mst_kode_wilayah
    JOIN ref.mst_wilayah prop on prop.kode_wilayah = kab.mst_kode_wilayah
WHERE
    peserta_didik.Soft_delete = 0
AND prop.kode_wilayah = '010000'
    -- AND rpd.sekolah_id = ''