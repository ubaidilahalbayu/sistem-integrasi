<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Main extends CI_Controller
{
	private $semester_now, $tahun_1, $tahun_2;
	private $hari_indonesia;
	public function __construct()
	{
		parent::__construct();
		$this->load->model('MainModel');
		$this->load->library('excel');
		$this->semester_now = 1;
		$this->tahun_1 = date('Y');
		$this->tahun_2 = date('Y', strtotime('+1 year'));
		if (strtotime('Y-m-d') < strtotime(date("Y")."-08-01")) {
			$this->semester_now = 2;
			$this->tahun_1 = date('Y', strtotime('-1 year'));
			$this->tahun_2 = date('Y');
		}
		$smt_data_now = array(
			'tahun_1' => $this->tahun_1,
			'tahun_2' => $this->tahun_2,
			'semester' => $this->semester_now,
		);
		$this->MainModel->auto_create_smt($smt_data_now);
		$this->hari_indonesia = array(
			'Sunday'    => 'Minggu',
			'Monday'    => 'Senin',
			'Tuesday'   => 'Selasa',
			'Wednesday' => 'Rabu',
			'Thursday'  => 'Kamis',
			'Friday'    => 'Jumat',
			'Saturday'  => 'Sabtu',
		);
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
		// if ($this->session->userdata('level') != 1) {
		// 	$dataAlert = [
		// 		'status' => 'warning',
		// 		'message' => 'Akses Ditolak!'
		// 	];
		// 	$this->session->set_flashdata('alert', $dataAlert);
		// 	redirect('/login');
		// }
		view('admin');
	}
	public function login()
	{
		if (!empty($this->input->post())) {
			$username = $this->input->post('username');
			$password = $this->input->post('password');
			$where = [
				'username' => $username,
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
					if ($get_user[0]['level'] == 3) {
						$where = ['nip' => $get_user[0]['username']];
						$get_dosen = $this->MainModel->get_table('data_dosen', $where)['data'];
						$this->session->set_userdata('dosen', $get_dosen[0]['nama_dosen']);
					}
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
		if ($this->session->userdata('level') == 3) {
			$nama_content_arr = ['dashboard', 'rekap_absensi_dosen'];
			if (!in_array($nama_content, $nama_content_arr)) {
				$dataAlert = [
					'status' => 'warning',
					'message' => 'Akses Ditolak!'
				];
				$this->session->set_flashdata('alert', $dataAlert);
				$nama_content = 'dashboard';
			}
		}
		if ($nama_content == 'dashboard') {
			$data['title_header'] = 'Dashboard';
			$semester_char = $this->tahun_1.$this->tahun_2.$this->semester_now;//DEFAULT SEMESTER SEKARANG
			$semester = 'Semester ';
			if ($this->semester_now == 2) {
				$semester .= 'Genap Tahun Akademik '.$this->tahun_1.'/'.$this->tahun_2;
			}else{
				$semester .= 'Ganjil Tahun Akademik '.$this->tahun_1.'/'.$this->tahun_2;
			}
			$where = array("semester_char" => $semester_char);
			$data['persentase'] = $this->MainModel->get_table("persentase", false, true, $where);
			$data['semester'] = $semester;
		} else if ($nama_content == 'rekap_absensi') {
			$where = [];
			$index_jadwal = 0;
			$semester_char = $this->tahun_1.$this->tahun_2.$this->semester_now;//DEFAULT SEMESTER SEKARANG
			$hari_pilihan = $this->hari_indonesia[date('l')];//DEFAULT HARI INI
			if (!empty($this->input->post('param_smt')) && !empty($this->input->post('param_hr')) && !empty($this->input->post('param_idx_jdw'))) {
				$semester_char = $this->input->post('param_smt');
				$hari_pilihan = $this->input->post('param_hr');
				$index_jadwal = $this->input->post('param_idx_jdw');
				$index_jadwal = explode('_@_', $index_jadwal);
				if ($index_jadwal[0] == "DJ") {
					$index_jadwal = $index_jadwal[1];
				}else{
					show_404();
				}
			}
			$where['hari'] = $hari_pilihan;
			$where['semester_char'] = $semester_char;
			if (!empty($this->input->post('param_id'))) {
				$id =  $this->input->post('param_id');
				$id = explode('-', $id)[1];
				$where['jadwal_kuliah.id'] = $id; 
			}
			$getData = $this->MainModel->get_table_rekap_absensi($where, $index_jadwal);
			$data = $getData;
			$data['title_header'] = 'Rekap Absensi';
			// $data['jdw'] = $this->MainModel->get_table('jadwal_kuliah')['data'];
			$data['dsn'] = $this->MainModel->get_table('data_dosen')['data'];
			$data['mhs'] = $this->MainModel->get_table('data_mahasiswa')['data'];
			$data['mk'] = $this->MainModel->get_table('data_mk')['data'];
			$data['smt'] = $this->MainModel->get_table('data_semester')['data'];
			$data['hr'] = $this->hari_indonesia;
			$data['index_jadwal'] = $index_jadwal;
			$data['selected_smt'] = $semester_char;
			$data['selected_hari'] = $hari_pilihan;
			$data['selected_idx'] = "DJ_@_".$index_jadwal;
			$data['pilih_tanggal'] = date('Y-m-d');
			if (!empty($this->input->post('param_tgl'))) {
				$pilih_tanggal = $this->input->post('param_tgl');
				$pilih_tanggal = explode('_@_', $pilih_tanggal);
				if ($pilih_tanggal[0] == '#rekap_absensi') {
					$data['pilih_tanggal'] = $pilih_tanggal[1];
				}
			}
		} else if ($nama_content == 'rekap_absensi_dosen') {
			$where = [];
			$index_jadwal = 0;
			$semester_char = $this->tahun_1.$this->tahun_2.$this->semester_now;//DEFAULT SEMESTER SEKARANG
			$hari_pilihan = $this->hari_indonesia[date('l')];//DEFAULT HARI INI
			if (!empty($this->input->post('param_smt'))) {
				$semester_char = $this->input->post('param_smt');
				$hari_pilihan = $this->input->post('param_hr');
				$index_jadwal = $this->input->post('param_idx_jdw');
				$index_jadwal = explode('_@_', $index_jadwal);
				if ($index_jadwal[0] == "DJ") {
					$index_jadwal = $index_jadwal[1];
				}else{
					show_404();
				}
			}
			// $where['hari'] = $hari_pilihan;
			$where['semester_char'] = $semester_char;
			if (!empty($this->input->post('param_id'))) {
				$id =  $this->input->post('param_id');
				$id = explode('-', $id)[1];
				$where['jadwal_kuliah.id'] = $id; 
			}
			$getData = $this->MainModel->get_table_rekap_absensi($where, $index_jadwal);
			$data = $getData;
			$data['title_header'] = 'Rekap Absensi Dosen';
			// $data['jdw'] = $this->MainModel->get_table('jadwal_kuliah')['data'];
			$data['dsn'] = $this->MainModel->get_table('data_dosen')['data'];
			$data['mhs'] = $this->MainModel->get_table('data_mahasiswa')['data'];
			$data['mk'] = $this->MainModel->get_table('data_mk')['data'];
			$data['smt'] = $this->MainModel->get_table('data_semester')['data'];
			$data['hr'] = $this->hari_indonesia;
			$data['index_jadwal'] = $index_jadwal;
			$data['selected_smt'] = $semester_char;
			$data['selected_hari'] = $hari_pilihan;
			$data['selected_idx'] = "DJ_@_".$index_jadwal;
			$data['pilih_tanggal'] = date('Y-m-d');
			if (!empty($this->input->post('param_tgl'))) {
				$pilih_tanggal = $this->input->post('param_tgl');
				$pilih_tanggal = explode('_@_', $pilih_tanggal);
				if ($pilih_tanggal[0] == '#rekap_absensi') {
					$data['pilih_tanggal'] = $pilih_tanggal[1];
				}
			}
		} else if ($nama_content == 'rekap_absensi_mhs') {
			$where = [];
			$index_jadwal = 0;
			$semester_char = $this->tahun_1.$this->tahun_2.$this->semester_now;//DEFAULT SEMESTER SEKARANG
			$hari_pilihan = $this->hari_indonesia[date('l')];//DEFAULT HARI INI
			if (!empty($this->input->post('param_smt')) && !empty($this->input->post('param_hr')) && !empty($this->input->post('param_idx_jdw'))) {
				$semester_char = $this->input->post('param_smt');
				$hari_pilihan = $this->input->post('param_hr');
				$index_jadwal = $this->input->post('param_idx_jdw');
				$index_jadwal = explode('_@_', $index_jadwal);
				if ($index_jadwal[0] == "DJ") {
					$index_jadwal = $index_jadwal[1];
				}else{
					show_404();
				}
			}
			$where['hari'] = $hari_pilihan;
			$where['semester_char'] = $semester_char;
			if (!empty($this->input->post('param_id'))) {
				$id =  $this->input->post('param_id');
				$id = explode('-', $id)[1];
				$where['jadwal_kuliah.id'] = $id; 
			}
			$getData = $this->MainModel->get_table_rekap_absensi($where, $index_jadwal);
			$data = $getData;
			$data['title_header'] = 'Rekap Absensi Mahasiswa';
			// $data['jdw'] = $this->MainModel->get_table('jadwal_kuliah')['data'];
			$data['dsn'] = $this->MainModel->get_table('data_dosen')['data'];
			$data['mhs'] = $this->MainModel->get_table('data_mahasiswa')['data'];
			$data['mk'] = $this->MainModel->get_table('data_mk')['data'];
			$data['smt'] = $this->MainModel->get_table('data_semester')['data'];
			$data['hr'] = $this->hari_indonesia;
			$data['index_jadwal'] = $index_jadwal;
			$data['selected_smt'] = $semester_char;
			$data['selected_hari'] = $hari_pilihan;
			$data['selected_idx'] = "DJ_@_".$index_jadwal;
			$data['pilih_tanggal'] = date('Y-m-d');
			if (!empty($this->input->post('param_tgl'))) {
				$pilih_tanggal = $this->input->post('param_tgl');
				$pilih_tanggal = explode('_@_', $pilih_tanggal);
				if ($pilih_tanggal[0] == '#rekap_absensi') {
					$data['pilih_tanggal'] = $pilih_tanggal[1];
				}
			}
		} else if ($nama_content == 'jadwal_kuliah') {
			$semester_char = $this->tahun_1.$this->tahun_2.$this->semester_now;//DEFAULT SEMESTER SEKARANG
			$hari_pilihan = $this->hari_indonesia[date('l')];//DEFAULT HARI INI
			if (!empty($this->input->post('param_smt')) && !empty($this->input->post('param_hr'))) {
				$semester_char = $this->input->post('param_smt');
				$hari_pilihan = $this->input->post('param_hr');
			}
			$where = [];
			$where['hari'] = $hari_pilihan;
			$where['semester_char'] = $semester_char;
			$data['semester_print'] = "Semester ".($semester_char[8] == 1 ? "Ganjil" : "Genap")." ".$semester_char[0].$semester_char[1].$semester_char[2].$semester_char[3]."/".$semester_char[4].$semester_char[5].$semester_char[6].$semester_char[7];
			$data['title_header'] = 'Jadwal Kuliah';
			$getData = $this->MainModel->get_table('jadwal_kuliah', true, true, $where);
			$data['header_table'] = $getData['header'];
			$data['data'] = $getData['data'];
			$data['mk'] = $this->MainModel->get_table('data_mk')['data'];
			$data['dsn'] = $this->MainModel->get_table('data_dosen')['data'];
			$data['kls'] = $this->MainModel->get_table('data_kelas')['data'];
			$data['smt'] = $this->MainModel->get_table('data_semester')['data'];
			$data['hr'] = $this->hari_indonesia;
			$data['selected_smt'] = $semester_char;
			$data['selected_hari'] = $hari_pilihan;
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

	public function ngisi_absen()
	{
		$data_response = array(
			'status' => false,
			'message' => "Error Not Found"
		);
		if (!empty($this->input->post())) {
			try{
				$semester_char = $this->input->post('param_smt');
				$hari_pilihan = $this->input->post('param_hr');
				$index_jadwal = $this->input->post('param_idx_jdw');
				$value = $this->input->post('param_value');
				$value = explode('_@_', $value);
				if (empty($this->input->post('param_tgl'))) {
					$data_menu = array(
						'param_smt' => $semester_char,
						'param_hr' => $hari_pilihan,
						'param_idx_jdw' => $index_jadwal,
						'param_tgl' => "#rekap_absensi_@_".$value[2]
					);
					$index_jadwal = explode('_@_', $index_jadwal);
					$index_jadwal = $index_jadwal[1];
					if (empty($this->input->post('param_mhs'))) {
						$data = [];
						$data['nip'] = $value[0];
						$data['id_jadwal'] = $value[1];
						$data['tanggal'] = $value[2];
						$where = [];
						$where['id_jadwal'] = $value[1];
						$where['tanggal'] = $value[2];
						$data_response = $this->MainModel->update_or_create_absen($data, $where);
					}else{
						$data = [];
						$data['keterangan'] = $value[0];
						$data['id_mhs'] = $value[1];
						$data['tanggal'] = $value[2];
						$where = [];
						$where['id_mhs'] = $value[1];
						$where['tanggal'] = $value[2];
						$data_response = $this->MainModel->update_or_create_absen($data, $where, true);
					}
				}else{
					$data_menu = array(
						'param_smt' => $semester_char,
						'param_hr' => $hari_pilihan,
						'param_idx_jdw' => $index_jadwal,
						'param_tgl' => "#rekap_absensi_@_".$value[0]
					);
					//GET ID JADWAL
					$where['hari'] = $hari_pilihan;
					$where['semester_char'] = $semester_char;
					$index_jadwal = explode('_@_', $index_jadwal);
					if ($index_jadwal[0] == "DJ") {
						$index_jadwal = $index_jadwal[1];
					}else{
						show_404();
					}
					$getData = $this->MainModel->get_table_rekap_absensi($where, $index_jadwal);
					$data_jadwal = $getData['data_jadwal_selected'];
					$data_update = [];
					$data_update['tanggal'] = $value[0];
					$where_update['tanggal'] = $value[1];
					$where_update['id_jadwal'] = $data_jadwal['id'];
					$data_response = $this->MainModel->update_tgl_absen($data_update, $where_update);
					if (!$data_response['status']) {
						$data_menu['param_tgl'] = "#rekap_absensi_@_".$value[1];
					}
				}
				$data_response['data'] = $data_menu;
			} catch (Exception $e) {
				$data_response['message'] = $e->getMessage();
        	}
				
		}
        header('Content-Type: application/json');
		echo json_encode($data_response);
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
						$pilihan_rekap = $this->input->post('pilihan_rekap');
						$data_extract = $this->excel->getExtractAbsenV3($path);
						// echo json_encode($data_extract);
						// die;
						$dataAlert = $this->MainModel->create_absen_by_file_v2($data_extract);
					} elseif ($menu == "jadwal_kuliah") {
						$data_extract = $this->excel->getExtractJadwal($path);
						$dataAlert = $this->MainModel->create_jadwal_by_file($data_extract);
					} else {
						$data_extract = $this->excel->getExtractGeneral($path);
						$dataAlert = $this->MainModel->create($menu, $data_extract, true);
					}
				} catch (\Throwable $e) {
					$dataAlert['message'] = $e->getMessage();
				}
			} else {
				$data = $this->input->post();
				if ($menu == "rekap_absensi") {
					$dataAlert = $this->MainModel->create('mhs_ambil_jadwal', $data);
				} else {
					$dataAlert = $this->MainModel->create($menu, $data);
				}
			}
			$this->session->set_flashdata('menu_now', $menu);
			$this->session->set_flashdata('alert', $dataAlert);
			// redirect('/');
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
				if ($where != "all") {
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
				}
				if ($menu == "rekap_absensi") {
					if (!empty($where['tanggal'])) {
						unset($where['id_jadwal']);
						$dataAlert = $this->MainModel->delete('isi_absen_mhs', $where);
						if ($dataAlert['status'] == 'success') {
							$dataAlert = $this->MainModel->delete('isi_absen_dosen', $where);
						}
					}else{
						$dataAlert = $this->MainModel->delete('mhs_ambil_jadwal', $where);
					}
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
	public function export()
	{
		if (!empty($this->input->post())) {
			$menu = $this->input->post('menu');
			$dataAlert = [
				'status' => 'danger',
				'message' => 'Gagal Export'
			];
			try {
				$where = $this->input->post();
				unset($where['menu']);
				foreach ($where as $key => $value) {
					if ($value != "all") {
						$where[str_replace('_@_', '.', $key)] = $value;
					}
					unset($where[$key]);
				}
				if ($menu == 'rekap_absensi') {
					$data = $this->MainModel->get_table_rekap_absensi($where, 0, true);
					// echo json_encode($data);die;
					$title = ucwords(str_replace('_', ' ', $menu));
					if (count($data['data_jadwal']) > 0) {
						$this->excel->exportAbsensi($data);
						$dataAlert = [
							'status' => 'success',
							'message' => 'Berhasil Export'
						];
					} else {
						$dataAlert = [
							'status' => 'warning',
							'message' => 'Data Tidak Ada'
						];
					}
				} elseif ($menu == 'jadwal_kuliah') {
					$data = $this->MainModel->get_table($menu, false, true, $where);
					$title = ucwords(str_replace('_', ' ', $menu));
					if (count($data) > 0) {
						$this->excel->exportJadwalKuliah($data);
						$dataAlert = [
							'status' => 'success',
							'message' => 'Berhasil Export'
						];
					} else {
						$dataAlert = [
							'status' => 'warning',
							'message' => 'Data Tidak Ada'
						];
					}
				} else {
					$data = $this->MainModel->get_table($menu, true, true, $where);
					$title = ucwords(str_replace('_', ' ', $menu));
					if (count($data['data']) > 0) {
						$this->excel->exportGeneral($data, $title);
						$dataAlert = [
							'status' => 'success',
							'message' => 'Berhasil Export'
						];
					} else {
						$dataAlert = [
							'status' => 'warning',
							'message' => 'Data Tidak Ada'
						];
					}
				}
			} catch (\Throwable $e) {
				$dataAlert['message'] = $e->getMessage();
			}
			if ($dataAlert['status'] != 'success') {
				$this->session->set_flashdata('menu_now', $menu);
				$this->session->set_flashdata('alert', $dataAlert);
			}
			redirect('/');
		} else {
			show_404();
		}
	}
	public function update_user_dosen()
	{
		$where = ['level' => 3];
		$delete_user = $this->MainModel->delete('user', $where);
		if ($delete_user['status'] == 'success') {
			$this->db->where('nip !=', '-');
			$data_dosen = $this->db->get('data_dosen')->result_array();
			foreach ($data_dosen as $key => $value) {
				$nip = $value['nip'];
				$insert = array(
					'username' => $nip,
					'password' => md5($nip),
					'level' => 3,
				);
				$insert = $this->MainModel->create('user', $insert);
				if ($insert['status'] != 'success') {
					break;
				}
			}
			echo $insert['message'];
		}
	}
}
