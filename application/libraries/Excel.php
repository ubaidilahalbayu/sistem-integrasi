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