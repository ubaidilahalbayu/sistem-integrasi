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
