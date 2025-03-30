<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once('PHPExcel.php');

class Excel extends PHPExcel
{

	public function __construct()
	{
		parent::__construct();
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
				$jam_mulai = date((string)$worksheet->getCellByColumnAndRow(2, $row)->getValue());
				$jam_selesai = date((string)$worksheet->getCellByColumnAndRow(3, $row)->getValue());
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
}
