<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Main extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('MainModel');
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
			$data['title_header'] = 'Rekap Absensi';
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
				echo json_encode($_FILES["formFile"]);
				die;
			} else {
				$data = $this->input->post();
				if ($menu == "rekap_absensi") {
					
				}else{
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
				'message' => 'Gagal Disimpan'
			];
			$where = $this->input->post('param');
			try{
				$where = explode(';_@_;', $where);
				$where = [
					$where[0] => $where[1],
					$where[2] => $where[3]
				];
			} catch(\Throwable $e){
				$this->session->set_flashdata('menu_now', $menu);
				$dataAlert['message'] = $e->getMessage();
				$this->session->set_flashdata('alert', $dataAlert);
				redirect('/');
				die;
			}
			$data = $this->input->post();
			if ($menu == "rekap_absensi") {
				
			}else{
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
					'message' => 'Gagal Disimpan'
				];
				$where = $this->input->post('param_delete');
				try{
					$where = explode(';_@_;', $where);
					$where = [
						$where[0] => $where[1],
						$where[2] => $where[3]
					];
				} catch(\Throwable $e){
					$this->session->set_flashdata('menu_now', $menu);
					$dataAlert['message'] = $e->getMessage();
					$this->session->set_flashdata('alert', $dataAlert);
					redirect('/');
					die;
				}
				if ($menu == "rekap_absensi") {
					
				}else{
					$dataAlert = $this->MainModel->delete($menu, $where);
				}
				$this->session->set_flashdata('menu_now', $menu);
				$this->session->set_flashdata('alert', $dataAlert);
				redirect('/');
			}else{
				show_404();
			}
		} else {
			show_404();
		}
	}
}