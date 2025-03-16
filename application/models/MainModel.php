<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MainModel extends CI_Model {

    public function __construct() {
        parent::__construct();
        // Load database
        $this->load->database();
    }

    // Menambahkan pengguna baru
    public function create_user($data) {
        return $this->db->insert('user', $data);
    }

    // Mengambil semua pengguna beserta nama kolom
    public function get_table($table, $where = []) {
        // Mengambil data dari tabel $table
        if (!empty($where)) {
            // Jika ada kondisi where, gunakan get_where
            $query = $this->db->get_where($table, $where);
        } else {
            // Jika tidak ada kondisi where, gunakan get
            $query = $this->db->get($table);
        }
        
        // Mengambil nama kolom
        $header = $this->db->list_fields($table);

        // Mengembalikan data dan nama kolom dalam array
        return [
            'header' => $header,
            'data' => $query->result_array()
        ];
    }

    // Mengambil pengguna berdasarkan ID
    public function get_user_by_id($id) {
        $query = $this->db->get_where('user', array('id' => $id));
        return $query->row();
    }

    // Memperbarui pengguna
    public function update_user($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('user', $data);
    }

    // Menghapus pengguna
    public function delete_user($id) {
        $this->db->where('id', $id);
        return $this->db->delete('user');
    }
}