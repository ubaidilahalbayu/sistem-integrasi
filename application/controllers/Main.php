<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Main extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('MainModel');
		$this->load->library('excel');
	}

	public function index()
	{
		if (empty($this->session->userdata('username')) && empty($this->session->userdata('level'))) {
			$dataAlert = [
				'status' => 'danger',
				'message' => 'Silahkan Login Terlebih Dahulu!'
			];
			$this->session->set_flashdata('alert', $dataAlert);
			redirect('/login');
		}
		if ($this->session->userdata('level') != 1) {
			$dataAlert = [
				'status' => 'warning',
				'message' => 'Akses Ditolak!'
			];
			$this->session->set_flashdata('alert', $dataAlert);
			redirect('/login');
		}
		view('admin');
	}
	public function login()
	{
		if (!empty($this->input->post())) {
			$username = $this->input->post('username');
			$password = $this->input->post('password');
			$where = [
				'user.username' => $username,
			];
			$get_user = $this->MainModel->get_table('user', $where)['data'];
			$dataAlert = [
				'status' => 'danger',
				'message' => 'Username atau Password Salah!'
			];
			if (count($get_user) > 0) {
				if (md5($password) == $get_user[0]['password']) {
					$this->session->set_userdata('username', $get_user[0]['username']);
					$this->session->set_userdata('level', $get_user[0]['level']);
					$dataAlert = [
						'status' => 'success',
						'message' => 'Berhasil Login ' . $get_user[0]['username']
					];
					$this->session->set_flashdata('alert', $dataAlert);
					redirect('/');
				} else {
					$this->session->set_flashdata('alert', $dataAlert);
					redirect('/login');
				}
			} else {
				$this->session->set_flashdata('alert', $dataAlert);
				redirect('/login');
			}
			die;
		}
		view('login');
	}
	public function logout()
	{
		$this->session->sess_destroy();
		redirect('login');
	}
	public function menu()
	{
		$nama_content = !empty($this->input->post('nama_content')) ? $this->input->post('nama_content') : '';
		$data = [];
		if ($nama_content == 'dashboard') {
			$data['title_header'] = 'Dashboard';
		} else if ($nama_content == 'rekap_absensi') {
			$where = [];
			if (!empty($this->input->post('param_dosen'))) {
				$where['dosen'] = 1;
				$this->session->set_flashdata('selected_rekap', 2);
			} elseif (($this->input->post('param_mhs'))) {
				$where['mhs'] = 1;
				$this->session->set_flashdata('selected_rekap', 3);
			} else {
				$this->session->set_flashdata('selected_rekap', 1);
			}
			$data['title_header'] = 'Rekap Absensi';
			$getData = $this->MainModel->get_table('rekap_absensi', $where);
			$data['header_table'] = $getData['header'];
			$data['data'] = $getData['data'];
			$data['abs'] = $this->MainModel->get_table('rekap_absensi')['data'];
			$data['jdw'] = $this->MainModel->get_table('jadwal_kuliah')['data'];
			$data['dsn'] = $this->MainModel->get_table('data_dosen')['data'];
			$data['mhs'] = $this->MainModel->get_table('data_mahasiswa')['data'];
		} else if ($nama_content == 'jadwal_kuliah') {
			$data['title_header'] = 'Jadwal Kuliah';
			$getData = $this->MainModel->get_table('jadwal_kuliah');
			$data['header_table'] = $getData['header'];
			$data['data'] = $getData['data'];
			$data['mk'] = $this->MainModel->get_table('data_mk')['data'];
			$data['dsn'] = $this->MainModel->get_table('data_dosen')['data'];
			$data['kls'] = $this->MainModel->get_table('data_kelas')['data'];
		} else if ($nama_content == 'data_mk') {
			$data['title_header'] = 'Data MK';
			$getData = $this->MainModel->get_table('data_mk');
			$data['header_table'] = $getData['header'];
			$data['data'] = $getData['data'];
		} else if ($nama_content == 'data_mahasiswa') {
			$data['title_header'] = 'Data Mahasiswa';
			$getData = $this->MainModel->get_table('data_mahasiswa');
			$data['header_table'] = $getData['header'];
			$data['data'] = $getData['data'];
		} else if ($nama_content == 'data_dosen') {
			$data['title_header'] = 'Data Dosen';
			$getData = $this->MainModel->get_table('data_dosen');
			$data['header_table'] = $getData['header'];
			$data['data'] = $getData['data'];
		} else if ($nama_content == 'data_kelas') {
			$data['title_header'] = 'Data Kelas';
			$getData = $this->MainModel->get_table('data_kelas');
			$data['header_table'] = $getData['header'];
			$data['data'] = $getData['data'];
		} else if ($nama_content == 'data_semester') {
			$data['title_header'] = 'Data Semester';
			$getData = $this->MainModel->get_table('data_semester');
			$data['header_table'] = $getData['header'];
			$data['data'] = $getData['data'];
		} else if ($nama_content == 'data_user') {
			$data['title_header'] = 'Data User';
			$getData = $this->MainModel->get_table('user');
			$data['header_table'] = $getData['header'];
			$data['data'] = $getData['data'];
		}
		view("menu/" . ($nama_content != 'dashboard' ? 'tampil_data' : $nama_content), $data, true);
	}
	public function proses()
	{
		if (!empty($this->input->post())) {
			$menu = $this->input->post('menu');
			$dataAlert = [
				'status' => 'danger',
				'message' => 'Gagal Disimpan'
			];

			if (!empty($_FILES["formFile"]["name"])) {
				try {
					$path = $_FILES["formFile"]["tmp_name"];
					$object = PHPExcel_IOFactory::load($path);
					if ($menu == "rekap_absensi") {
						foreach($object->getWorksheetIterator() as $worksheet)
						{
							$highestRow = $worksheet->getHighestRow();
							$highestColumn = $worksheet->getHighestColumn();	
							for($row=5; $row<=$highestRow; $row++)
							{
								$nama = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
								$jurusan = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
								$angkatan = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
								$temp_data[] = array(
									'nama'	=> $nama,
									'jurusan'	=> $jurusan,
									'angkatan'	=> $angkatan
								); 	
							}
						}
					}elseif($menu == "jadwal_kuliah"){
						$data_dosen = [];
						$data_mk = [];
						$data_kelas = [];
						$data_jadwal = [];
						foreach($object->getWorksheetIterator() as $worksheet)
						{
							if ($worksheet->getTitle() != "JADWAL") {
								continue;
							}
							$highestRow = $worksheet->getHighestRow();
							$highestColumn = $worksheet->getHighestColumn();	
							for($row=5; $row<=$highestRow; $row++)
							{
								//DATA JADWAL
								$kode_mk = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
								$kode_kelas = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
								$nip = $worksheet->getCellByColumnAndRow(11, $row)->getValue();
								$nip2 = $worksheet->getCellByColumnAndRow(12, $row)->getValue();
								$nip3 = $worksheet->getCellByColumnAndRow(13, $row)->getValue();
								$hari = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
								$jam_mulai = date((String)$worksheet->getCellByColumnAndRow(2, $row)->getValue());
								$jam_selesai = date((String)$worksheet->getCellByColumnAndRow(3, $row)->getValue());
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
						echo json_encode($data_jadwal);
						die;
					}else{
						$temp_data = [];
						foreach($object->getWorksheetIterator() as $worksheet)
						{
							$highestRow = $worksheet->getHighestRow();
							$highestColumn = $worksheet->getHighestColumn();	
							for($row=5; $row<=$highestRow; $row++)
							{
								$nama = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
								$jurusan = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
								$angkatan = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
								$temp_data[] = array(
									'nama'	=> $nama,
									'jurusan'	=> $jurusan,
									'angkatan'	=> $angkatan
								); 	
							}
						}
					}
				} catch (\Throwable $e) {
					$dataAlert['message'] = $e->getMessage();
				}
				echo json_encode($temp_data);
				die;
			} else {
				$data = $this->input->post();
				if ($menu == "rekap_absensi") {
					$pilihan_rekap = $this->input->post('pilihan_rekap');
					if ($pilihan_rekap == 2) {
						$dataAlert = $this->MainModel->create('isi_absen_dosen', $data);
					} elseif ($pilihan_rekap == 3) {
						$dataAlert = $this->MainModel->create('isi_absen_mhs', $data);
					} else {
						$dataAlert = $this->MainModel->create('absensi', $data);
					}
				} else {
					$dataAlert = $this->MainModel->create($menu, $data);
				}
			}
			$this->session->set_flashdata('menu_now', $menu);
			$this->session->set_flashdata('alert', $dataAlert);
			redirect('/');
		} else {
			show_404();
		}
	}
	public function edit()
	{
		if (!empty($this->input->post())) {
			$menu = $this->input->post('menu');
			$dataAlert = [
				'status' => 'danger',
				'message' => 'Gagal Diubah'
			];
			$where = $this->input->post('param');
			try {
				$where = explode(';_@_;', $where);
				$where = [
					$where[0] => $where[1],
					$where[2] => $where[3]
				];
			} catch (\Throwable $e) {
				$this->session->set_flashdata('menu_now', $menu);
				$dataAlert['message'] = $e->getMessage();
				$this->session->set_flashdata('alert', $dataAlert);
				redirect('/');
				die;
			}
			$data = $this->input->post();
			if ($menu == "rekap_absensi") {
				$dataAlert = $this->MainModel->update('absensi', $data, $where);
			} else {
				$dataAlert = $this->MainModel->update($menu, $data, $where);
			}
			$this->session->set_flashdata('menu_now', $menu);
			$this->session->set_flashdata('alert', $dataAlert);
			redirect('/');
		} else {
			show_404();
		}
	}
	public function hapus()
	{
		if (!empty($this->input->post('_method'))) {
			if ($this->input->post('_method') == "DELETE") {
				$menu = $this->input->post('menu');
				$dataAlert = [
					'status' => 'danger',
					'message' => 'Gagal Hapus'
				];
				$where = $this->input->post('param_delete');
				try {
					$where = explode(';_@_;', $where);
					$where = [
						$where[0] => $where[1],
						$where[2] => $where[3]
					];
				} catch (\Throwable $e) {
					$this->session->set_flashdata('menu_now', $menu);
					$dataAlert['message'] = $e->getMessage();
					$this->session->set_flashdata('alert', $dataAlert);
					redirect('/');
					die;
				}
				if ($menu == "rekap_absensi") {
				} else {
					$dataAlert = $this->MainModel->delete($menu, $where);
				}
				$this->session->set_flashdata('menu_now', $menu);
				$this->session->set_flashdata('alert', $dataAlert);
				redirect('/');
			} else {
				show_404();
			}
		} else {
			show_404();
		}
	}
}