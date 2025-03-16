<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('MainModel');
    }

	public function index()
	{
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
			$get_user = $this->MainModel->get_table('user', $where);
			$dataAlert = [
				'status' => 'danger',
				'message' => 'Username atau Password Salah!'
			];
			if (count($get_user) > 0) {
				if ($password == $get_user[0]['password']) {
					$this->session->set_userdata('username', $get_user[0]['username']);
					$this->session->set_userdata('level', $get_user[0]['level']);
					redirect('/');
				}else{
					$this->session->set_flashdata('alert', $dataAlert);
					redirect('/login');
				}
			}else{
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
		}
		else if ($nama_content == 'rekap_absensi') {
			$data['title_header'] = 'Rekap Absensi';
		}
		else if ($nama_content == 'data_mk') {
			$data['title_header'] = 'Data MK';
		}
		else if ($nama_content == 'data_mahasiswa') {
			$data['title_header'] = 'Data Mahasiswa';
		}
		else if ($nama_content == 'data_dosen') {
			$data['title_header'] = 'Data Dosen';
		}
		else if ($nama_content == 'data_user') {
			$data['title_header'] = 'Data User';
			$getData = $this->MainModel->get_table('user');
			$data['header_table'] = $getData['header'];
			$data['data'] = $getData['data'];
		}
		view("menu/".($nama_content != 'dashboard' ? 'tampil_data' : $nama_content), $data, true);
	}
}