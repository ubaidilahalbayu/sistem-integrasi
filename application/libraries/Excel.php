<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once('PHPExcel.php');

class Excel extends PHPExcel
{

	public function __construct()
	{
		parent::__construct();
	}

	public function getExtractAbsenV3($path)
	{
		$object = PHPExcel_IOFactory::load($path);
		$data_semester = [];
		$data_dosen = [];
		$data_mk = [];
		$data_kelas = [];
		$data_jadwal = [];
		$data_mahasiswa['nim'] = [];
		$data_mahasiswa['nama_mahasiswa'] = [];
		$data_mahasiswa['angkatan'] = [];
		$data_mhs_ambil_jadwal = [];
		$data_isi_absen_mhs = [];
		$data_isi_absen_dsn = [];
		//BUAT DOSEN KOSONG
		$data_dosen[] = array(
			'nip' => '-',
			'nama_gelar_depan' => '-',
			'nama_dosen' => '-',
			'nama_gelar_belakang' => '-',
		);
		$semester_char = '';
		foreach ($object->getWorksheetIterator() as $worksheet) {
			$highestRow = $worksheet->getHighestRow();
			$highestColumn = $worksheet->getHighestColumn();
			if ($worksheet->getTitle() == "Kode MK") {
				//MENGETAHUI SMESTER DARI TITLE
				$title = $worksheet->getCellByColumnAndRow(0, 1)->getValue();
				$title = explode(' - ', $title);
				$semester = 1;
				if (stripos($title[1], "Genap") !== false) {
					$semester = 2;
				}
				$th = explode(' ', $title[1]);
				$semester_char = str_replace('/', '', $th[2]).(string)$semester;
				//DATA SEMESTER
				$smt = explode('/', $th[2]);
				$data_semester = array(
					'tahun_1' => $smt[0],
					'tahun_2' => $smt[1],
					'semester' => $semester,
				);
				//DATA MK
				for ($i=3; $i < $highestRow; $i++) {
					$kode_mk = $worksheet->getCellByColumnAndRow(2, $i)->getValue();
					$nama_mk = $worksheet->getCellByColumnAndRow(3, $i)->getValue();
					if (empty($kode_mk) || empty($nama_mk)) {
						continue;
					}
					$data_mk[] = array(
						'kode_mk' => $kode_mk,
						'nama_mk' => $nama_mk,
						'sks' => 2,//DEFAULT SKS 2 Karena rata-rata sks 2
						'semester' => $semester
					);
				}
			}elseif($worksheet->getTitle() == "Rekap Dosen") {
				//DATA DOSEN
				for ($i=5; $i < $highestRow; $i++) { 
					$nama_gelar_depan = '';
					$nama_gelar_belakang = '';
					$nip = $worksheet->getCellByColumnAndRow(1, $i)->getValue();
					$dosen = $worksheet->getCellByColumnAndRow(2, $i)->getValue();
					if (empty($nip) || empty($dosen)) {
						continue;
					}
					$dosen = explode(', ', $dosen);
					$nama_dosen = $dosen[0];

					//PENGESTRAKAN GELAR DOSEN
					for ($j=1; $j < count($dosen); $j++) { 
						$cek = explode('. ', $dosen[$j]);
						if (count($cek) > 1) {
							for ($k=0; $k < count($cek); $k++) { 
								if (stripos($cek[$k], 'Dr') !== false || stripos($cek[$k], 'Prof') !== false || stripos($cek[$k], 'Ir') !== false) {
									$nama_gelar_depan .= $cek[$k].". ";
								}else{
									if ($nama_gelar_belakang == '') {
										$nama_gelar_belakang .= ", ".$cek[$k].". ";
									}else {
										if ($k < count($cek)-1) {
											$nama_gelar_belakang .= $cek[$k].". ";
										}else{
											$nama_gelar_belakang .= $cek[$k].".";
										}
									}
								}
							}
						}else{
							if ($nama_gelar_belakang == '') {
								$nama_gelar_belakang .= ", ".$dosen[$j];
							}else{
								$nama_gelar_belakang .= $dosen[$j];
							}

						}
						if ($j < count($dosen)-1) {
							$nama_gelar_belakang .= ", ";
						}
					}
					//DATA DOSEN
					$data_dosen[] = array(
						'nip' => $nip,
						'nama_gelar_depan' => $nama_gelar_depan,
						'nama_dosen' => $nama_dosen,
						'nama_gelar_belakang' => $nama_gelar_belakang,
					);
				}
			}else{
				$kode_mk = $worksheet->getCellByColumnAndRow(1, 3)->getCalculatedValue();
				$kode_kelas = $worksheet->getCellByColumnAndRow(1, 5)->getCalculatedValue();
				$nip = !empty($worksheet->getCellByColumnAndRow(3, 3)->getValue()) ? $worksheet->getCellByColumnAndRow(3, 3)->getValue() : '-';
				$nip2 = !empty($worksheet->getCellByColumnAndRow(3, 4)->getValue()) ? $worksheet->getCellByColumnAndRow(3, 4)->getValue() : '-';
				$nip3 = !empty($worksheet->getCellByColumnAndRow(3, 5)->getValue()) ? $worksheet->getCellByColumnAndRow(3, 5)->getValue() : '-';
				$jadwal = $worksheet->getCellByColumnAndRow(1, 6)->getCalculatedValue();
				$ruang = !empty($worksheet->getCellByColumnAndRow(1, 7)->getCalculatedValue()) ? $worksheet->getCellByColumnAndRow(1, 6)->getCalculatedValue() : '-';
				if (empty($kode_mk) || empty($kode_kelas) || empty($jadwal)) {
					continue;
				}

				//DATA KELAS
				$data_kelas[] = array(
					'kode_kelas' => $kode_kelas,
					'nama_kelas' => $kode_kelas
				);


				$jdw = explode('/', str_replace(' ', '', $jadwal));
				if (count($jdw) < 2) {
					$jdw = explode(',', str_replace(' ', '', $jadwal));
					if (count($jdw) < 2) {
						$jdw = explode('.', str_replace(' ', '', $jadwal));
					}
				}
				$hari = $jdw[0];
				$jam = explode('-', $jdw[1]);
				$jam_mulai = $jam[0];
				$jam_selesai = str_replace("WIB", "", $jam[1]);
				//DATA JADWAL KULIAH
				$data_jadwal[] = array(
					'kode_mk' => $kode_mk,
					'kode_kelas' => $kode_kelas,
					'nip' => $nip,
					'nip2' => $nip2,
					'nip3' => $nip3,
					'hari' => ucfirst($hari),
					'jam_mulai' => $jam_mulai,
					'jam_selesai' => $jam_selesai,
					'ruang' => $ruang,
					'semester_char' => $semester_char,
				);

				//DATA ISI ABSEN DOSEN
				$isi_absen_dsn = [];
				foreach (range("D", chr(ord($highestColumn) - 2)) as $value) {
					$tanggal = $worksheet->getCell($value."11")->getCalculatedValue();
					if (empty($tanggal)) {
						continue;
					}
					$nip = $worksheet->getCell($value."12")->getValue();
					if (empty($nip) || $nip == ''  || $nip == ' ') {
						$nip = '-';
					}elseif (stripos($nip, "=") !== false) {
						$nip = $worksheet->getCell($value."12")->getCalculatedValue();
						if (empty($nip) || $nip == '' || $nip == ' ') {
							$nip = '-';
						}
					}
					$tanggal = PHPExcel_Shared_Date::ExcelToPHP($tanggal);
					$isi_absen_dsn[] = array(
						'nip' => $nip,
						'tanggal' => date('Y-m-d', $tanggal),
					);
				};
				$data_isi_absen_dsn[] = $isi_absen_dsn;

				$mhs_ambil_jadwal = [];
				$isi_absen_mhs = [];
				for ($i=13; $i < $highestRow; $i++) { 
					//DATA MAHASISWA
					$nim = $worksheet->getCellByColumnAndRow(1, $i)->getValue();
					$nama_mahasiswa = $worksheet->getCellByColumnAndRow(2, $i)->getValue();
					if (empty($nim) || empty($nama_mahasiswa)) {
						continue;
					}
					$angkatan = "20".$nim[7].$nim[8];
					if (!in_array($nim, $data_mahasiswa['nim'])) {
						$data_mahasiswa['nim'][] = $nim;
						$data_mahasiswa['nama_mahasiswa'][] = $nama_mahasiswa;
						$data_mahasiswa['angkatan'][] = $angkatan;
					}

					//DATA MAHASISWA AMBIL JADWAL
					$mhs_ambil_jadwal[] = $nim;

					//DATA ISI ABSEN MAHASISWA
					$push_absen = [];
					foreach (range("D", chr(ord($highestColumn) - 2)) as $value) {
						$tanggal = $worksheet->getCell($value."11")->getCalculatedValue();
						$tanggal = PHPExcel_Shared_Date::ExcelToPHP($tanggal);
						if (empty($tanggal)) {
							continue;
						}
						$keterangan = $worksheet->getCell($value.$i)->getValue();
						$keterangan = empty($keterangan) ? "-" : (strtolower($keterangan) == "i" || strtolower($keterangan) == "s" ? 2 : $keterangan);
						$push_absen[] = array('tanggal' => date('Y-m-d', $tanggal), 'keterangan' => $keterangan);
					}
					$isi_absen_mhs[] = $push_absen;
				}
				$data_mhs_ambil_jadwal[] = $mhs_ambil_jadwal;
				$data_isi_absen_mhs[] = $isi_absen_mhs;
			}
		}
		return array(
			'data_semester' => $data_semester,
			'data_dosen' => $data_dosen,
			'data_kelas' => $data_kelas,
			'data_mk' => $data_mk,
			'data_jadwal' => $data_jadwal,
			'data_mahasiswa' => $data_mahasiswa,
			'data_mhs_ambil_jadwal' => $data_mhs_ambil_jadwal,
			'data_isi_absen_dsn' => $data_isi_absen_dsn,
			'data_isi_absen_mhs' => $data_isi_absen_mhs,
		);
	}
	public function getExtractAbsenV2($path)
	{
		$object = PHPExcel_IOFactory::load($path);
		$data_semester = [];
		$data_dosen = [];
		$data_mk = [];
		$data_kelas = [];
		$data_jadwal = [];
		$data_mahasiswa['nim'] = [];
		$data_mahasiswa['nama_mahasiswa'] = [];
		$data_mahasiswa['angkatan'] = [];
		$data_mhs_ambil_jadwal = [];
		$data_isi_absen_mhs = [];
		$data_isi_absen_dsn = [];
		//BUAT DOSEN KOSONG
		$data_dosen[] = array(
			'nip' => '-',
			'nama_gelar_depan' => '-',
			'nama_dosen' => '-',
			'nama_gelar_belakang' => '-',
		);
		$semester_char = '';
		foreach ($object->getWorksheetIterator() as $worksheet) {
			$highestRow = $worksheet->getHighestRow();
			$highestColumn = $worksheet->getHighestColumn();
			if ($worksheet->getTitle() == "Kode MK") {
				//MENGETAHUI SMESTER DARI TITLE
				$title = $worksheet->getCellByColumnAndRow(0, 1)->getValue();
				$title = explode(' - ', $title);
				$semester = 1;
				if (stripos($title[1], "Genap") !== false) {
					$semester = 2;
				}
				$th = explode(' ', $title[1]);
				$semester_char = str_replace('/', '', $th[2]).(string)$semester;
				//DATA SEMESTER
				$smt = explode('/', $th[2]);
				$data_semester = array(
					'tahun_1' => $smt[0],
					'tahun_2' => $smt[1],
					'semester' => $semester,
				);
				//DATA MK
				for ($i=3; $i < $highestRow; $i++) { 
					$data_mk[] = array(
						'kode_mk' => $worksheet->getCellByColumnAndRow(2, $i)->getValue(),
						'nama_mk' => $worksheet->getCellByColumnAndRow(3, $i)->getValue(),
						'sks' => 2,//DEFAULT SKS 2 Karena rata-rata sks 2
						'semester' => $semester
					);
				}
			}elseif($worksheet->getTitle() == "Rekap Dosen") {
				//DATA DOSEN
				for ($i=5; $i < $highestRow; $i++) { 
					$nama_gelar_depan = '';
					$nama_gelar_belakang = '';
					$nip = $worksheet->getCellByColumnAndRow(1, $i)->getValue();
					$dosen = $worksheet->getCellByColumnAndRow(2, $i)->getValue();
					if (empty($nip) || empty($dosen)) {
						continue;
					}
					$dosen = explode(', ', $dosen);
					$nama_dosen = $dosen[0];

					//PENGESTRAKAN GELAR DOSEN
					for ($j=1; $j < count($dosen); $j++) { 
						$cek = explode('. ', $dosen[$j]);
						if (count($cek) > 1) {
							for ($k=0; $k < count($cek); $k++) { 
								if (stripos($cek[$k], 'Dr') !== false || stripos($cek[$k], 'Prof') !== false || stripos($cek[$k], 'Ir') !== false) {
									$nama_gelar_depan .= $cek[$k].". ";
								}else{
									if ($nama_gelar_belakang == '') {
										$nama_gelar_belakang .= ", ".$cek[$k].". ";
									}else {
										if ($k < count($cek)-1) {
											$nama_gelar_belakang .= $cek[$k].". ";
										}else{
											$nama_gelar_belakang .= $cek[$k].".";
										}
									}
								}
							}
						}else{
							if ($nama_gelar_belakang == '') {
								$nama_gelar_belakang .= ", ".$dosen[$j];
							}else{
								$nama_gelar_belakang .= $dosen[$j];
							}

						}
						if ($j < count($dosen)-1) {
							$nama_gelar_belakang .= ", ";
						}
					}
					//DATA DOSEN
					$data_dosen[] = array(
						'nip' => $nip,
						'nama_gelar_depan' => $nama_gelar_depan,
						'nama_dosen' => $nama_dosen,
						'nama_gelar_belakang' => $nama_gelar_belakang,
					);
				}
			}else{
				$kode_mk = $worksheet->getCellByColumnAndRow(1, 3)->getFormattedValue();
				$kode_kelas = $worksheet->getCellByColumnAndRow(1, 5)->getValue();
				$nip = !empty($worksheet->getCellByColumnAndRow(3, 3)->getValue()) ? $worksheet->getCellByColumnAndRow(3, 3)->getValue() : '-';
				$nip2 = !empty($worksheet->getCellByColumnAndRow(3, 4)->getValue()) ? $worksheet->getCellByColumnAndRow(3, 4)->getValue() : '-';
				$nip3 = !empty($worksheet->getCellByColumnAndRow(3, 5)->getValue()) ? $worksheet->getCellByColumnAndRow(3, 5)->getValue() : '-';
				$jadwal = $worksheet->getCellByColumnAndRow(1, 7)->getValue();
				$ruang = !empty($worksheet->getCellByColumnAndRow(1, 6)->getValue()) ? $worksheet->getCellByColumnAndRow(1, 6)->getValue() : '-';
				if (empty($kode_mk) || empty($kode_kelas) || empty($jadwal)) {
					continue;
				}

				//DATA KELAS
				$data_kelas[] = array(
					'kode_kelas' => $kode_kelas,
					'nama_kelas' => $kode_kelas
				);

				$jadwal = explode('/', str_replace(' ', '', $jadwal));
				$hari = $jadwal[0];
				$jam = explode('-', $jadwal[1]);
				$jam_mulai = $jam[0];
				$jam_selesai = str_replace("WIB", "", $jam[1]);
				//DATA JADWAL KULIAH
				$data_jadwal[] = array(
					'kode_mk' => $kode_mk,
					'kode_kelas' => $kode_kelas,
					'nip' => $nip,
					'nip2' => $nip2,
					'nip3' => $nip3,
					'hari' => $hari,
					'jam_mulai' => $jam_mulai,
					'jam_selesai' => $jam_selesai,
					'ruang' => $ruang,
					'semester_char' => $semester_char,
				);

				//DATA ISI ABSEN DOSEN
				$isi_absen_dsn = [];
				foreach (range("D", chr(ord($highestColumn) - 2)) as $value) {
					$tanggal = $worksheet->getCell($value."11")->getCalculatedValue();
					$tanggal = PHPExcel_Shared_Date::ExcelToPHP($tanggal);
					$isi_absen_dsn[] = array(
						'nip' => $worksheet->getCell($value."12")->getValue(),
						'tanggal' => date('Y-m-d', $tanggal),
					);
				};
				$data_isi_absen_dsn[] = $isi_absen_dsn;

				$mhs_ambil_jadwal = [];
				$isi_absen_mhs = [];
				for ($i=13; $i < $highestRow; $i++) { 
					//DATA MAHASISWA
					$nim = $worksheet->getCellByColumnAndRow(1, $i)->getValue();
					$nama_mahasiswa = $worksheet->getCellByColumnAndRow(2, $i)->getValue();
					if (empty($nim) || empty($nama_mahasiswa)) {
						continue;
					}
					$angkatan = "20".$nim[7].$nim[8];
					if (!in_array($nim, $data_mahasiswa['nim'])) {
						$data_mahasiswa['nim'][] = $nim;
						$data_mahasiswa['nama_mahasiswa'][] = $nama_mahasiswa;
						$data_mahasiswa['angkatan'][] = $angkatan;
					}

					//DATA MAHASISWA AMBIL JADWAL
					$mhs_ambil_jadwal[] = $nim;

					//DATA ISI ABSEN MAHASISWA
					$push_absen = [];
					foreach (range("D", chr(ord($highestColumn) - 2)) as $value) {
						$keterangan = $worksheet->getCell($value.$i)->getValue();
						$keterangan = strtolower($keterangan) == "i" || strtolower($keterangan) == "s" ? 2 : $keterangan;
						$tanggal = $worksheet->getCell($value."11")->getCalculatedValue();
						$tanggal = PHPExcel_Shared_Date::ExcelToPHP($tanggal);
						$push_absen[] = array('tanggal' => date('Y-m-d', $tanggal), 'keterangan' => $keterangan);
					}
					$isi_absen_mhs[] = $push_absen;
				}
				$data_mhs_ambil_jadwal[] = $mhs_ambil_jadwal;
				$data_isi_absen_mhs[] = $isi_absen_mhs;
			}
		}
		return array(
			'data_semester' => $data_semester,
			'data_dosen' => $data_dosen,
			'data_kelas' => $data_kelas,
			'data_mk' => $data_mk,
			'data_jadwal' => $data_jadwal,
			'data_mahasiswa' => $data_mahasiswa,
			'data_mhs_ambil_jadwal' => $data_mhs_ambil_jadwal,
			'data_isi_absen_dsn' => $data_isi_absen_dsn,
			'data_isi_absen_mhs' => $data_isi_absen_mhs,
		);
	}
	public function getExtractAbsen($path, $pilihan_rekap)
	{
		$object = PHPExcel_IOFactory::load($path);
		$data_dosen["dosen1"] = [];
		$data_dosen["dosen2"] = [];
		$data_dosen["dosen3"] = [];
		$data_mk = [];
		$data_kelas = [];
		$data_jadwal = [];
		$data_mahasiswa = [];
		$data_absen = [];
		$data_isi_absen = [];
		foreach ($object->getWorksheetIterator() as $worksheet) {

			//DATA DOSEN
			$dosen1 = $worksheet->getCell("C6")->getValue();
			$data_dosen2 = $worksheet->getCell("C7")->getValue();
			$data_dosen3 = $worksheet->getCell("C8")->getValue();
			array_push($data_dosen["dosen1"], $dosen1);
			array_push($data_dosen["dosen2"], $data_dosen2);
			array_push($data_dosen["dosen3"], $data_dosen3);
			
			//DATA MK
			$mk = $worksheet->getCell("C4")->getValue();
			$mk = explode(" - ", $mk);
			$kd_mk = $mk[0];
			$nm_mk = $mk[1];
			$mk = array(
				"kode_mk" => $kd_mk,
				"nama_mk" => $nm_mk,
				"sks" => 1, //default nya 1 sementara
				"semester" => 1, 
			);
			array_push($data_mk, $mk);

			//DATA KELAS
			$kelas = $worksheet->getCell("C5")->getValue();
			$kelas_push = array(
				"kode_kelas" => $kelas,
				"nama_kelas" => $kelas,
			);
			array_push($data_kelas, $kelas_push);

			//DATA JADWAL
			$hari = $worksheet->getCell("C9")->getValue();
			$jam = $worksheet->getCell("C10")->getValue();
			$jam = explode(" - ", $jam);
			$jam_mulai = $jam[0];
			$jam_selesai = str_replace(" WIB", "", $jam[1]);
			$jadwal = array(
				"kode_mk" => $kd_mk,
				"kode_kelas" => $kelas,
				"hari" => $hari,
				"jam_mulai" => $jam_mulai,
				"jam_selesai" => $jam_selesai
			);
			array_push($data_jadwal, $jadwal);

			$highestRow = $worksheet->getHighestRow();
			$highestColumn = $worksheet->getHighestColumn();
			//DATA ABSEN
			$absen = [];
			foreach (range("D", $highestColumn) as $col) { 
				$tanggal = $worksheet->getCell($col."13")->getFormattedValue();
				if (empty($tanggal)) {
					continue;
				}
				$tanggal = array("tanggal" => date('Y-m-d', strtotime($tanggal)));
				array_push($absen, $tanggal);
			}
			// echo json_encode($absen);die;
			array_push($data_absen, $absen);
			$isi_absen = [];
			for ($row = 14; $row <= $highestRow; $row++) {
				//DATA MAHASISWA
				$nim = $worksheet->getCell("B".$row)->getValue();
				$nama = $worksheet->getCell("C".$row)->getValue();
				if (empty($nim) && empty($nama)) {
					continue;
				}
				$mahasiswa = array(
					"nim" => $nim,
					"nama_mahasiswa" => $nama,
					"angkatan" => "2025", //Default angkatan 2025
				);
				array_push($data_mahasiswa, $mahasiswa);
				
				//DATA ISI ABSEN
				$isi_absen_push = [];
				foreach (range("D", $highestColumn) as $col) { 
					$keterangan = $worksheet->getCell($col.$row)->getValue();
					if (empty($keterangan)) {
						continue;
					}
					$isi_sekali = array(
						"nim" => $nim,
						"keterangan" => $keterangan,
					);
					array_push($isi_absen_push, $isi_sekali);
				}
				array_push($isi_absen, $isi_absen_push);
			}
			array_push($data_isi_absen, $isi_absen);
		}
		return [
			'data_jadwal' => $data_jadwal,
			'data_dosen' => $data_dosen,
			'data_mk' => $data_mk,
			'data_kelas' => $data_kelas,
			'data_absen' => $data_absen,
			'data_isi_absen' => $data_isi_absen,
			'data_mahasiswa' => $data_mahasiswa,
		];
	}
	public function getExtractJadwal($path)
	{
		$object = PHPExcel_IOFactory::load($path);
		$data_dosen = [];
		$data_mk = [];
		$data_kelas = [];
		$data_jadwal = [];
		foreach ($object->getWorksheetIterator() as $worksheet) {
			if ($worksheet->getTitle() != "JADWAL") {
				continue;
			}
			$highestRow = $worksheet->getHighestRow();
			$highestColumn = $worksheet->getHighestColumn();
			for ($row = 5; $row <= $highestRow; $row++) {
				//DATA JADWAL
				$kode_mk = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
				$kode_kelas = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
				$nip = $worksheet->getCellByColumnAndRow(11, $row)->getValue();
				$nip2 = $worksheet->getCellByColumnAndRow(12, $row)->getValue();
				$nip3 = $worksheet->getCellByColumnAndRow(13, $row)->getValue();
				$hari = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
				$jam_mulai = $worksheet->getCellByColumnAndRow(2, $row)->getFormattedValue();
				$jam_selesai = (string)$worksheet->getCellByColumnAndRow(3, $row)->getFormattedValue();
				if (empty($kode_mk) || empty($kode_kelas) || empty($nip) || empty($hari) || empty($jam_mulai) || empty($jam_selesai)) {
					continue;
				}
				// echo json_encode($worksheet->getCellByColumnAndRow(1, $row)->getValue());
				// die;
				$data_jadwal[] = array(
					'kode_mk' => $kode_mk,
					'kode_kelas' => $kode_kelas,
					'nip' => $nip,
					'nip2' => $nip2,
					'nip3' => $nip3,
					'hari' => $hari,
					'jam_mulai' => $jam_mulai,
					'jam_selesai' => $jam_selesai
				);

				//DATA DOSEN
				$nipExp = explode(', ', $nip);
				$nama_dosen = !empty($nipExp[0]) ? $nipExp[0] : '-';
				$nama_gelar_belakang = !empty($nipExp[1]) ? $nipExp[1] : '-';
				if (!isset($data_dosen['nama_dosen'])) {
					$data_dosen['nama_dosen'] = []; // Inisiasi Pertama
					$data_dosen['nama_gelar_belakang'] = [];
					$data_dosen['nip'] = [];
				}
				// Simpan Dosen 1
				if (!in_array($nama_dosen, $data_dosen['nama_dosen'])) {
					$data_dosen['nip'][] = $nip;
					$data_dosen['nama_dosen'][] = $nama_dosen; // Menyimpan Dosen 1
					$data_dosen['nama_gelar_belakang'][] = $nama_gelar_belakang;
				}
				$nipExp = explode(', ', $nip2);
				$nama_dosen = !empty($nipExp[0]) ? $nipExp[0] : '-';
				$nama_gelar_belakang = !empty($nipExp[1]) ? $nipExp[1] : '-';
				// Simpan Dosen 2
				if (!in_array($nama_dosen, $data_dosen['nama_dosen'])) {
					$data_dosen['nip'][] = $nip2;
					$data_dosen['nama_dosen'][] = $nama_dosen; // Menyimpan Dosen 2
					$data_dosen['nama_gelar_belakang'][] = $nama_gelar_belakang;
				}
				$nipExp = explode(', ', $nip3);
				$nama_dosen = !empty($nipExp[0]) ? $nipExp[0] : '-';
				$nama_gelar_belakang = !empty($nipExp[1]) ? $nipExp[1] : '-';
				// Simpan Dosen 3
				if (!in_array($nama_dosen, $data_dosen['nama_dosen'])) {
					$data_dosen['nip'][] = $nip3;
					$data_dosen['nama_dosen'][] = $nama_dosen; // Menyimpan Dosen 3
					$data_dosen['nama_gelar_belakang'][] = $nama_gelar_belakang;
				}

				//DATA MK
				$nama_mk = $worksheet->getCellByColumnAndRow(10, $row)->getValue();
				$sks = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
				$semester = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
				if (!isset($data_mk['kode_mk'])) {
					$data_mk['kode_mk'] = []; // Inisiasi Pertama
					$data_mk['nama_mk'] = [];
					$data_mk['sks'] = [];
					$data_mk['semester'] = [];
				}
				// Simpan MK
				if (!in_array($kode_mk, $data_mk['kode_mk'])) {
					$data_mk['kode_mk'][] = $kode_mk; // Menyimpan Dosen 1
					$data_mk['nama_mk'][] = $nama_mk;
					$data_mk['sks'][] = $sks;
					$data_mk['semester'][] = $semester;
				}

				//Data Kelas
				if (!isset($data_kelas['kode_kelas'])) {
					$data_kelas['kode_kelas'] = []; // Inisiasi Pertama
					$data_kelas['nama_kelas'] = [];
				}
				// Simpan MK
				if (!in_array($kode_kelas, $data_kelas['kode_kelas'])) {
					$data_kelas['kode_kelas'][] = $kode_kelas; // Menyimpan Dosen 1
					$data_kelas['nama_kelas'][] = $kode_kelas;
				}
			}
		}
		return [
			'data_jadwal' => $data_jadwal,
			'data_dosen' => $data_dosen,
			'data_mk' => $data_mk,
			'data_kelas' => $data_kelas,
		];
	}

	public function getExtractGeneral($path)
	{
		$object = PHPExcel_IOFactory::load($path);
		$data = [];
		$head = [];
		foreach ($object->getWorksheetIterator() as $worksheet) {
			$highestRow = $worksheet->getHighestRow();
			$highestColumn = $worksheet->getHighestColumn();
			for ($row = 3; $row <= $highestRow; $row++) {
				if ($row == 3) { // Inisialisasi head
					foreach (range('B', $highestColumn) as $value) {
						$head[] = strtolower(str_replace(' ', '_', $worksheet->getCell($value . $row)->getValue()));
					}
				} else {
					$push = [];
					foreach ($head as $key => $value) {
						$push[$value] = $worksheet->getCellByColumnAndRow($key + 1, $row)->getValue();
					}
					$data[] = $push;
				}
			}
		}
		return $data;
	}

	public function exportAbsensi($data)
	{
		$data_dosen = $data['data_dosen'];
		$data_jadwal = $data['data_jadwal'];
		$data_mhs_ambil_jadwal = $data['data_mhs_ambil_jadwal'];
		$kode_mk = $data['kode_mk'];
		$kode_mk_jadwal = $data['kode_mk_jadwal'];
		$data_isi_absen_mhs = $data['data_isi_absen_mhs'];
		$this->getProperties()->setCreator('My Name')->setLastModifiedBy('My Name')->setTitle('ABSEN')->setSubject('ABSEN')->setDescription('ABSEN')->setKeywords('ABSEN');
		$style_head = array(
			'font' => array('bold' => true, 'size' => 10), // Set font nya jadi bold
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
			),
			'borders' => array(
				'top' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border top dengan garis tipis
				'right' => array('style'  => PHPExcel_Style_Border::BORDER_THIN),  // Set border right dengan garis tipis
				'bottom' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border bottom dengan garis tipis
				'left' => array('style'  => PHPExcel_Style_Border::BORDER_THIN) // Set border left dengan garis tipis
			),
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb' => '808080')
			)
		);
		$style_body = array(
			'font' => array('size' => 10),
			'alignment' => array(
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
			),
			'borders' => array(
				'top' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border top dengan garis tipis
				'right' => array('style'  => PHPExcel_Style_Border::BORDER_THIN),  // Set border right dengan garis tipis
				'bottom' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border bottom dengan garis tipis
				'left' => array('style'  => PHPExcel_Style_Border::BORDER_THIN) // Set border left dengan garis tipis
			),
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb' => 'E5C298')
			)
		);
		$style_body2 = array(
			'font' => array('size' => 10),
			'alignment' => array(
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
			),
			'borders' => array(
				'top' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border top dengan garis tipis
				'right' => array('style'  => PHPExcel_Style_Border::BORDER_THIN),  // Set border right dengan garis tipis
				'bottom' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border bottom dengan garis tipis
				'left' => array('style'  => PHPExcel_Style_Border::BORDER_THIN) // Set border left dengan garis tipis
			)
		);
		$style_href = array(
			'font' => array('bold' => true, 'size' => 10),
			'alignment' => array(
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, // Set text jadi di tengah secara vertical (middle)
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			),
			'borders' => array(
				'top' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border top dengan garis tipis
				'right' => array('style'  => PHPExcel_Style_Border::BORDER_THIN),  // Set border right dengan garis tipis
				'bottom' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border bottom dengan garis tipis
				'left' => array('style'  => PHPExcel_Style_Border::BORDER_THIN) // Set border left dengan garis tipis
			),
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb' => 'FF0000')
			)
		);
		
		//SHEET KODE MK
		$active_sheet = $this->getActiveSheet();
		$active_sheet->setTitle('Kode MK');
		//TITLE
		$active_sheet->setCellValue("A1", "PRODI MANAJEMEN INFORMATIKA (D3) - Semester Ganjil 2024/2025");
		$active_sheet->mergeCells("A1:F1");
		$active_sheet->getStyle("A1")->getFont()->setBold(true);
		$active_sheet->getStyle("A1")->getFont()->setSize(12);
		$active_sheet->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$active_sheet->getStyle("A1")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$active_sheet->getStyle("A1")->applyFromArray(array('fill' => array("type" => PHPExcel_Style_Fill::FILL_SOLID, "color" => array("rgb" => "808080"))));
		//HEAD TABLE
		$active_sheet->setCellValue("A2", "No");
		$active_sheet->getStyle("A2")->applyFromArray($style_head);
		$active_sheet->setCellValue("B2", "Kode");
		$active_sheet->getStyle("B2")->applyFromArray($style_head);
		$active_sheet->setCellValue("C2", "Kode MK");
		$active_sheet->getStyle("C2")->applyFromArray($style_head);
		$active_sheet->setCellValue("D2", "Nama Mata Kuliah");
		$active_sheet->getStyle("D2")->applyFromArray($style_head);
		$active_sheet->setCellValue("E2", "Jumlah Ruang");
		$active_sheet->getStyle("E2")->applyFromArray($style_head);
		$active_sheet->setCellValue("F2", "Keterangan");
		$active_sheet->getStyle("F2")->applyFromArray($style_head);
		//BODY TABLE
		foreach ($data_mk as $key => $value) {
			$active_sheet->setCellValue("A".(String)($key+3), $key+1);
			$active_sheet->getStyle("A".(String)($key+3))->applyFromArray($style_body2);
			$active_sheet->setCellValue("B".(String)($key+3), $kode_mk[$key]);
			$active_sheet->getStyle("B".(String)($key+3))->applyFromArray($style_href);
			$active_sheet->setCellValue("C".(String)($key+3), $value['kode_mk']);
			$active_sheet->getStyle("C".(String)($key+3))->applyFromArray($style_body2);
			$active_sheet->setCellValue("D".(String)($key+3), $value['nama_mk']);
			$active_sheet->getStyle("D".(String)($key+3))->applyFromArray($style_body2);
			$active_sheet->setCellValue("E".(String)($key+3), '');
			$active_sheet->getStyle("E".(String)($key+3))->applyFromArray($style_body2);
			$active_sheet->setCellValue("F".(String)($key+3), '');
			$active_sheet->getStyle("F".(String)($key+3))->applyFromArray($style_body2);
		}
		$highest_column = $active_sheet->getHighestColumn();
		$highest_row = $active_sheet->getHighestRow();
		foreach (range("A", $highest_column) as $key => $value) {
			$active_sheet->getColumnDimensionByColumn($key)->setAutoSize(true);
		}
		for ($i=1; $i <=$highest_row ; $i++) { 
			$active_sheet->getRowDimension($i)->setZeroHeight(true);
		}

		//SHEET Rekap Dosen
		$active_sheet = $this->createSheet();
		$active_sheet->setTitle('Rekap Dosen');
		//TITLE
		$active_sheet->setCellValue("A1", "PRODI MANAJEMEN INFORMATIKA (D3)");
		$active_sheet->mergeCells("A1:D1");
		$active_sheet->getStyle("A1")->getFont()->setBold(true);
		$active_sheet->getStyle("A1")->getFont()->setSize(12);
		$active_sheet->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$active_sheet->getStyle("A1")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$active_sheet->getStyle("A1")->applyFromArray(array('fill' => array("type" => PHPExcel_Style_Fill::FILL_SOLID, "color" => array("rgb" => "808080"))));
		//HEAD TABLE
		$active_sheet->setCellValue("A2", "No");
		$active_sheet->mergeCells("A2:A4");
		$active_sheet->getStyleByColumnAndRow(0, 2, 0, 4)->applyFromArray($style_head);
		// $active_sheet->getStyle("A2")->applyFromArray($style_head);
		$active_sheet->setCellValue("B2", "Kode");
		$active_sheet->mergeCells("B2:B4");
		$active_sheet->getStyleByColumnAndRow(1, 2, 1, 4)->applyFromArray($style_head);
		// $active_sheet->getStyle("B2")->applyFromArray($style_head);
		$active_sheet->setCellValue("C2", "Nama Dosen");
		$active_sheet->mergeCells("C2:C4");
		$active_sheet->getStyleByColumnAndRow(2, 2, 2, 4)->applyFromArray($style_head);
		// $active_sheet->getStyle("C2")->applyFromArray($style_head);
		$active_sheet->setCellValue("D2", "Total Masuk");
		$active_sheet->mergeCells("D2:D4");
		$active_sheet->getStyleByColumnAndRow(3, 2, 3, 4)->applyFromArray($style_head);
		// $active_sheet->getStyle("D2")->applyFromArray($style_head);
		//BODY TABLE
		foreach ($data_dosen as $key => $value) {
			$active_sheet->setCellValue("A".(String)($key+5), $key+1);
			$active_sheet->getStyle("A".(String)($key+5))->applyFromArray($style_body2);
			$active_sheet->setCellValue("B".(String)($key+5), $value['nip']);
			$active_sheet->getStyle("B".(String)($key+5))->applyFromArray($style_body2);
			$active_sheet->setCellValue("C".(String)($key+5), $value['dosen']);
			$active_sheet->getStyle("C".(String)($key+5))->applyFromArray($style_body2);
			$active_sheet->setCellValue("D".(String)($key+5), $value['jumlah_hadir']);
			$active_sheet->getStyle("D".(String)($key+5))->applyFromArray($style_body);
		}
		$highest_column = $active_sheet->getHighestColumn();
		$highest_row = $active_sheet->getHighestRow();
		foreach (range("A", $highest_column) as $key => $value) {
			$active_sheet->getColumnDimensionByColumn($key)->setAutoSize(true);
		}
		for ($i=1; $i <=$highest_row ; $i++) { 
			$active_sheet->getRowDimension($i)->setZeroHeight(true);
		}

		// Proses file 
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename="absen_mahasiswa.xlsx"'); // Set nama file
		header('Cache-Control: max-age=0');
		$write = PHPExcel_IOFactory::createWriter($this, 'Excel2007');
		$write->save('php://output');
	}

	public function exportJadwalKuliah($data)
	{
		$this->getProperties()->setCreator('My Name')->setLastModifiedBy('My Name')->setTitle('JADWAL')->setSubject('JADWAL')->setDescription('JADWAL')->setKeywords('JADWAL');
		$style_head = array(
			'font' => array('bold' => true), // Set font nya jadi bold
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
			),
			'borders' => array(
				'top' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border top dengan garis tipis
				'right' => array('style'  => PHPExcel_Style_Border::BORDER_THIN),  // Set border right dengan garis tipis
				'bottom' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border bottom dengan garis tipis
				'left' => array('style'  => PHPExcel_Style_Border::BORDER_THIN) // Set border left dengan garis tipis
			),
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb' => '808080')
			)
		);
		$style_body = array(
			'alignment' => array(
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
			),
			'borders' => array(
				'top' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border top dengan garis tipis
				'right' => array('style'  => PHPExcel_Style_Border::BORDER_THIN),  // Set border right dengan garis tipis
				'bottom' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border bottom dengan garis tipis
				'left' => array('style'  => PHPExcel_Style_Border::BORDER_THIN) // Set border left dengan garis tipis
			),
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb' => 'E5C298')
			)
		);
		$style_body2 = array(
			'alignment' => array(
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
			),
			'borders' => array(
				'top' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border top dengan garis tipis
				'right' => array('style'  => PHPExcel_Style_Border::BORDER_THIN),  // Set border right dengan garis tipis
				'bottom' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border bottom dengan garis tipis
				'left' => array('style'  => PHPExcel_Style_Border::BORDER_THIN) // Set border left dengan garis tipis
			)
		);

		$this->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0, 1, "JADWAL KULIAH SEMESTER GENAP 2024/2025 (JANUARI - JUNI 2025)"); // Set Title paling atas column 0 = A, baris 1
		$this->getActiveSheet()->mergeCellsByColumnAndRow(0, 1, 14, 1); // Set Merge Cell pada kolom 1 sampai Sesuai panjang Col
		$this->getActiveSheet()->getStyleByColumnAndRow(0, 1)->getFont()->setBold(TRUE); // Set bold kolom 1
		$this->getActiveSheet()->getStyleByColumnAndRow(0, 1)->getFont()->setSize(15); // Set font size 15 untuk kolom 1
		$this->getActiveSheet()->getStyleByColumnAndRow(0, 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); // Set text center untuk kolom 1
		
		$this->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0, 2, "PROGRAM STUDI D3 MANAJEMEN INFORMATIKA"); // Set Title paling atas column 0 = A, baris 1
		$this->getActiveSheet()->mergeCellsByColumnAndRow(0, 2, 14, 2); // Set Merge Cell pada kolom 1 sampai Sesuai panjang Col
		$this->getActiveSheet()->getStyleByColumnAndRow(0, 2)->getFont()->setBold(TRUE); // Set bold kolom 1
		$this->getActiveSheet()->getStyleByColumnAndRow(0, 2)->getFont()->setSize(15); // Set font size 15 untuk kolom 1
		$this->getActiveSheet()->getStyleByColumnAndRow(0, 2)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); // Set text center untuk kolom 1

		//SET HEADER
		$this->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0, 3, "NO.");
		$this->getActiveSheet()->mergeCellsByColumnAndRow(0, 3, 0, 4);
		$this->getActiveSheet()->getStyleByColumnAndRow(0, 3)->applyFromArray($style_head);
		$this->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1, 3, "HARI");
		$this->getActiveSheet()->mergeCellsByColumnAndRow(1, 3, 1, 4);
		$this->getActiveSheet()->getStyleByColumnAndRow(1, 3)->applyFromArray($style_head);
		$this->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2, 3, "JAM");
		$this->getActiveSheet()->mergeCellsByColumnAndRow(2, 3, 3, 3);
		$this->getActiveSheet()->getStyleByColumnAndRow(2, 3)->applyFromArray($style_head);
		$this->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2, 4, "MASUK");
		$this->getActiveSheet()->getStyleByColumnAndRow(2, 4)->applyFromArray($style_head);
		$this->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3, 4, "KELUAR");
		$this->getActiveSheet()->getStyleByColumnAndRow(3, 4)->applyFromArray($style_head);
		$this->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4, 3, "RUANG");
		$this->getActiveSheet()->mergeCellsByColumnAndRow(4, 3, 4, 4);
		$this->getActiveSheet()->getStyleByColumnAndRow(4, 3)->applyFromArray($style_head);
		$this->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5, 3, "SKS");
		$this->getActiveSheet()->mergeCellsByColumnAndRow(5, 3, 5, 4);
		$this->getActiveSheet()->getStyleByColumnAndRow(5, 3)->applyFromArray($style_head);
		$this->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6, 3, "SEM.");
		$this->getActiveSheet()->mergeCellsByColumnAndRow(6, 3, 6, 4);
		$this->getActiveSheet()->getStyleByColumnAndRow(6, 3)->applyFromArray($style_head);
		$this->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7, 3, "KELAS");
		$this->getActiveSheet()->mergeCellsByColumnAndRow(7, 3, 7, 4);
		$this->getActiveSheet()->getStyleByColumnAndRow(7, 3)->applyFromArray($style_head);
		$this->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8, 3, "SIMAK");
		$this->getActiveSheet()->mergeCellsByColumnAndRow(8, 3, 8, 4);
		$this->getActiveSheet()->getStyleByColumnAndRow(8, 3)->applyFromArray($style_head);
		$this->setActiveSheetIndex(0)->setCellValueByColumnAndRow(9, 3, "KODE MK");
		$this->getActiveSheet()->mergeCellsByColumnAndRow(9, 3, 9, 4);
		$this->getActiveSheet()->getStyleByColumnAndRow(9, 3)->applyFromArray($style_head);
		$this->setActiveSheetIndex(0)->setCellValueByColumnAndRow(10, 3, "MATA KULIAH");
		$this->getActiveSheet()->mergeCellsByColumnAndRow(10, 3, 10, 4);
		$this->getActiveSheet()->getStyleByColumnAndRow(10, 3)->applyFromArray($style_head);
		$this->setActiveSheetIndex(0)->setCellValueByColumnAndRow(11, 3, "PENGAMPU 1");
		$this->getActiveSheet()->mergeCellsByColumnAndRow(11, 3, 11, 4);
		$this->getActiveSheet()->getStyleByColumnAndRow(11, 3)->applyFromArray($style_head);
		$this->setActiveSheetIndex(0)->setCellValueByColumnAndRow(12, 3, "PENGAMPU 2");
		$this->getActiveSheet()->mergeCellsByColumnAndRow(12, 3, 12, 4);
		$this->getActiveSheet()->getStyleByColumnAndRow(12, 3)->applyFromArray($style_head);
		$this->setActiveSheetIndex(0)->setCellValueByColumnAndRow(13, 3, "PENGAMPU 3");
		$this->getActiveSheet()->mergeCellsByColumnAndRow(13, 3, 13, 4);
		$this->getActiveSheet()->getStyleByColumnAndRow(13, 3)->applyFromArray($style_head);
		$this->setActiveSheetIndex(0)->setCellValueByColumnAndRow(14, 3, "DIFF");
		$this->getActiveSheet()->mergeCellsByColumnAndRow(14, 3, 14, 4);
		$this->getActiveSheet()->getStyleByColumnAndRow(14, 3)->applyFromArray($style_head);
		$this->setActiveSheetIndex(0)->setCellValueByColumnAndRow(15, 3, "BENTROK");
		$this->getActiveSheet()->mergeCellsByColumnAndRow(15, 3, 15, 4);
		$this->getActiveSheet()->getStyleByColumnAndRow(15, 3)->applyFromArray($style_head);
		$this->setActiveSheetIndex(0)->setCellValueByColumnAndRow(16, 3, "STATUS JADWAL");
		$this->getActiveSheet()->mergeCellsByColumnAndRow(16, 3, 16, 4);
		$this->getActiveSheet()->getStyleByColumnAndRow(16, 3)->applyFromArray($style_head);
		
		//SET BODY
		$hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
		$row = 5;
		foreach ($hari as $key => $value) {
			$no = 1;
			$ada = false;
			for ($i=0; $i < count($data); $i++) { 
				if ($data[$i]['hari'] == $value) {
					if ($ada) {
						$this->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0, $row, $no);
						$this->getActiveSheet()->getStyleByColumnAndRow(0, $row)->applyFromArray($style_body);
						$this->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1, $row, $data[$i]['hari']);
						$this->getActiveSheet()->getStyleByColumnAndRow(1, $row)->applyFromArray($style_body);
						$this->setActiveSheetIndex(0)->setCellValueByColumnAndRow(2, $row, $data[$i]['jam_mulai']);
						$this->getActiveSheet()->getStyleByColumnAndRow(2, $row)->applyFromArray($style_body);
						$this->setActiveSheetIndex(0)->setCellValueByColumnAndRow(3, $row, $data[$i]['jam_selesai']);
						$this->getActiveSheet()->getStyleByColumnAndRow(3, $row)->applyFromArray($style_body);
						$this->setActiveSheetIndex(0)->setCellValueByColumnAndRow(4, $row, $data[$i]['kode_kelas']);
						$this->getActiveSheet()->getStyleByColumnAndRow(4, $row)->applyFromArray($style_body);
						$this->setActiveSheetIndex(0)->setCellValueByColumnAndRow(5, $row, $data[$i]['sks']);
						$this->getActiveSheet()->getStyleByColumnAndRow(5, $row)->applyFromArray($style_body);
						$this->setActiveSheetIndex(0)->setCellValueByColumnAndRow(6, $row, $data[$i]['semester']);
						$this->getActiveSheet()->getStyleByColumnAndRow(6, $row)->applyFromArray($style_body);
						$this->setActiveSheetIndex(0)->setCellValueByColumnAndRow(7, $row, $data[$i]['kode_kelas']);
						$this->getActiveSheet()->getStyleByColumnAndRow(7, $row)->applyFromArray($style_body);
						$this->setActiveSheetIndex(0)->setCellValueByColumnAndRow(8, $row, $data[$i]['kode_kelas']);
						$this->getActiveSheet()->getStyleByColumnAndRow(8, $row)->applyFromArray($style_body);
						$this->setActiveSheetIndex(0)->setCellValueByColumnAndRow(9, $row, $data[$i]['kode_mk']);
						$this->getActiveSheet()->getStyleByColumnAndRow(9, $row)->applyFromArray($style_body);
						$this->setActiveSheetIndex(0)->setCellValueByColumnAndRow(10, $row, $data[$i]['nama_mk']);
						$this->getActiveSheet()->getStyleByColumnAndRow(10, $row)->applyFromArray($style_body);
						$this->setActiveSheetIndex(0)->setCellValueByColumnAndRow(11, $row, $data[$i]['pengampu_1']);
						$this->getActiveSheet()->getStyleByColumnAndRow(11, $row)->applyFromArray($style_body);
						$this->setActiveSheetIndex(0)->setCellValueByColumnAndRow(12, $row, $data[$i]['pengampu_2']);
						$this->getActiveSheet()->getStyleByColumnAndRow(12, $row)->applyFromArray($style_body);
						$this->setActiveSheetIndex(0)->setCellValueByColumnAndRow(13, $row, $data[$i]['pengampu_3']);
						$this->getActiveSheet()->getStyleByColumnAndRow(13, $row)->applyFromArray($style_body);
						$this->setActiveSheetIndex(0)->setCellValueByColumnAndRow(14, $row, $data[$i]['diff']);
						$this->getActiveSheet()->getStyleByColumnAndRow(14, $row)->applyFromArray($style_body);
						$this->setActiveSheetIndex(0)->setCellValueByColumnAndRow(15, $row, $data[$i]['bentrok']);
						$this->getActiveSheet()->getStyleByColumnAndRow(15, $row)->applyFromArray($style_body);
						$this->setActiveSheetIndex(0)->setCellValueByColumnAndRow(16, $row, '');
						$this->getActiveSheet()->getStyleByColumnAndRow(16, $row)->applyFromArray($style_body);
						$no++;
					}else{
						$ada = true;
						$this->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0, $row, strtoupper($value));
						$this->getActiveSheet()->mergeCellsByColumnAndRow(0, $row, 16, $row);
						$this->getActiveSheet()->getStyleByColumnAndRow(0, $row)->getFont()->setBold(TRUE);
						$this->getActiveSheet()->getStyleByColumnAndRow(0, $row)->applyFromArray($style_body2);
						$i--;
					}
					$row++;
				}
			}
		}
		$tahun = date('Y');
		for ($i = 0; $i <= 16; $i++) {
			$this->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);
		}

		// Set orientasi kertas jadi LANDSCAPE
		$this->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		// Set judul file nya
		$this->getActiveSheet(0)->setTitle("JADWAL");
		$this->setActiveSheetIndex(0);
		// Proses file 
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename="Jadwal MI Tahun Ajaran ' . $tahun . '.xlsx"'); // Set nama file
		header('Cache-Control: max-age=0');
		$write = PHPExcel_IOFactory::createWriter($this, 'Excel2007');
		$write->save('php://output');

		
	}

	public function exportGeneral($data, $title)
	{
		$this->getProperties()->setCreator('My Name')->setLastModifiedBy('My Name')->setTitle($title)->setSubject($title)->setDescription($title)->setKeywords($title);
		$style_head = array(
			'font' => array('bold' => true), // Set font nya jadi bold
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
			),
			'borders' => array(
				'top' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border top dengan garis tipis
				'right' => array('style'  => PHPExcel_Style_Border::BORDER_THIN),  // Set border right dengan garis tipis
				'bottom' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border bottom dengan garis tipis
				'left' => array('style'  => PHPExcel_Style_Border::BORDER_THIN) // Set border left dengan garis tipis
			),
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb' => '808080')
			)
		);
		$style_body = array(
			'alignment' => array(
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
			),
			'borders' => array(
				'top' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border top dengan garis tipis
				'right' => array('style'  => PHPExcel_Style_Border::BORDER_THIN),  // Set border right dengan garis tipis
				'bottom' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border bottom dengan garis tipis
				'left' => array('style'  => PHPExcel_Style_Border::BORDER_THIN) // Set border left dengan garis tipis
			)
		);
		$head = $data['header'];
		$body = $data['data'];
		$tahun = date('Y');
		$col = count($head);
		$this->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0, 1, $title . " Tahun " . $tahun); // Set Title paling atas column 0 = A, baris 1
		$this->getActiveSheet()->mergeCellsByColumnAndRow(0, 1, $col, 1); // Set Merge Cell pada kolom 1 sampai Sesuai panjang Col
		$this->getActiveSheet()->getStyleByColumnAndRow(0, 1)->getFont()->setBold(TRUE); // Set bold kolom 1
		$this->getActiveSheet()->getStyleByColumnAndRow(0, 1)->getFont()->setSize(15); // Set font size 15 untuk kolom 1
		$this->getActiveSheet()->getStyleByColumnAndRow(0, 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); // Set text center untuk kolom 1
		// Buat header tabel nya pada baris ke 3
		$this->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0, 3, "No"); // Set No Header
		// Apply style header yang telah kita buat tadi ke masing-masing kolom header
		$this->getActiveSheet()->getStyleByColumnAndRow(0, 3)->applyFromArray($style_head);
		foreach ($head as $key => $value) {
			$this->setActiveSheetIndex(0)->setCellValueByColumnAndRow($key + 1, 3, ucwords(str_replace('_', ' ', $value))); // Set Header
			// Apply style header yang telah kita buat tadi ke masing-masing kolom header
			$this->getActiveSheet()->getStyleByColumnAndRow($key + 1, 3)->applyFromArray($style_head);
		}
		$no = 1; // Untuk penomoran tabel, di awal set dengan 1
		$numrow = 4; // Set baris pertama untuk isi tabel adalah baris ke 4
		foreach ($body as $data) { // Lakukan looping pada isi body
			$this->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0, $numrow, $no); // Set Nomor column 0 = A, baris numrow 
			// Apply style body yang telah kita buat tadi ke masing-masing baris (isi tabel)
			$this->getActiveSheet()->getStyleByColumnAndRow(0, $numrow)->applyFromArray($style_body);
			foreach ($head as $key => $value) {
				$this->setActiveSheetIndex(0)->setCellValueByColumnAndRow($key + 1, $numrow, $data[$value]); // Set Isi Body
				// Apply style body yang telah kita buat tadi ke masing-masing baris (isi tabel)
				$this->getActiveSheet()->getStyleByColumnAndRow($key + 1, $numrow)->applyFromArray($style_body);
			}
			$no++; // Tambah 1 setiap kali looping
			$numrow++; // Tambah 1 setiap kali looping
		}

		for ($i = 0; $i <= $col; $i++) {
			$this->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);
		}

		// Set orientasi kertas jadi LANDSCAPE
		$this->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		// Set judul file nya
		$this->getActiveSheet(0)->setTitle($title);
		$this->setActiveSheetIndex(0);
		// Proses file 
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename="' . $title . ' Tahun ' . $tahun . '.xlsx"'); // Set nama file
		header('Cache-Control: max-age=0');
		$write = PHPExcel_IOFactory::createWriter($this, 'Excel2007');
		$write->save('php://output');
	}
}