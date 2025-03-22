<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MainModel extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        // Load database
        $this->load->database();
    }

    public function create($table, $data)
    {
        $this->db->trans_begin();
        $return = [];
        try {
            $header = $this->db->list_fields($table);
            $insert = [];
            foreach ($data as $key => $value) {
                if (in_array($key, $header)) {
                    $insert[$key] = !empty($value) ? $value : NULL;
                }
            }
            $this->db->insert($table, $insert);
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $error = $this->db->error();
                $return['status'] = 'danger';
                $return['message'] = 'Gagal :: ' . $error['message'];
            } else {
                $this->db->trans_commit();
                $return['status'] = 'success';
                $return['message'] = 'Berhasil Simpan tabel ' . $table;
            }
        } catch (\Throwable $e) {
            $this->db->trans_rollback();
            $return['status'] = 'danger';
            $return['message'] = 'Gagal :: ' . $e->getMessage();
        }
        return $return;
    }

    // Mengambil semua pengguna beserta nama kolom
    public function get_table($table, $where = [])
    {
        if ($table == 'jadwal_kuliah') {
            $this->db->select('*');
            $this->db->from($table);
            $this->db->join('data_mk', 'jadwal_kuliah.kode_mk = data_mk.kode_mk');
            $this->db->join('data_dosen', 'jadwal_kuliah.nip = data_dosen.nip');
            $this->db->join('data_kelas', 'jadwal_kuliah.kode_kelas = data_kelas.kode_kelas');
            if (!empty($where)) {
                $this->db->where($where);
            }
            $query = $this->db->get();
            $header = ['id', 'nama_mk', 'semester', 'kode_kelas', 'nama_dosen', 'hari', 'jam_mulai', 'jam_selesai'];
        } elseif ($table == 'rekap_absensi') {
            $this->db->select('*');
            $this->db->from('absensi');
            $this->db->join('jadwal_kuliah', 'absensi.id_jadwal = jadwal_kuliah.id');
            $this->db->join('data_mk', 'jadwal_kuliah.kode_mk = data_mk.kode_mk');
            if (!empty($where['dosen'])) {
                $this->db->join('isi_absen_dosen', 'isi_absen_dosen.id_absen = absensi.id');
                $this->db->join('data_dosen', 'isi_absen_dosen.nip = data_dosen.nip');
                unset($where['dosen']);
                $header = ['id', 'hari',  'tanggal', 'nama_mk', 'semester', 'nama_dosen', 'keterangan'];
            } elseif (!empty($where['mhs'])) {
                $this->db->join('isi_absen_mhs', 'isi_absen_mhs.id_absen = absensi.id');
                $this->db->join('data_mahasiswa', 'isi_absen_mhs.nim = data_mahasiswa.nim');
                unset($where['mhs']);
                $header = ['id', 'hari',  'tanggal', 'nama_mk', 'semester', 'nama_mahasiswa', 'keterangan'];
            } else {
                $header = ['id', 'hari',  'tanggal', 'nama_mk', 'semester'];
            }
            if (!empty($where)) {
                $this->db->where($where);
            }
            $query = $this->db->get();
        } else {
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
        }

        // Mengembalikan data dan nama kolom dalam array
        return [
            'header' => $header,
            'data' => $query->result_array()
        ];
    }

    // Mengambil pengguna berdasarkan ID
    public function get_user_by_id($id)
    {
        $query = $this->db->get_where('user', array('id' => $id));
        return $query->row();
    }

    public function update($table, $data, $where)
    {
        $this->db->trans_begin();
        $return = [];
        try {
            $header = $this->db->list_fields($table);
            $insert = [];
            foreach ($data as $key => $value) {
                if (in_array($key, $header)) {
                    $insert[$key] = !empty($value) ? $value : NULL;
                }
            }
            $this->db->where($where);
            $this->db->update($table, $insert);
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $error = $this->db->error();
                $return['status'] = 'danger';
                $return['message'] = 'Gagal :: ' . $error['message'];
            } else {
                $this->db->trans_commit();
                $return['status'] = 'success';
                $return['message'] = 'Berhasil Ubah tabel ' . $table;
            }
        } catch (\Throwable $e) {
            $this->db->trans_rollback();
            $return['status'] = 'danger';
            $return['message'] = 'Gagal :: ' . $e->getMessage();
        }
        return $return;
    }

    // Menghapus pengguna
    public function delete($table, $where)
    {
        $this->db->trans_begin();
        $return = [];
        try {
            $this->db->where($where);
            $this->db->delete($table);
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $error = $this->db->error();
                $return['status'] = 'danger';
                $return['message'] = 'Gagal :: ' . $error['message'];
            } else {
                $this->db->trans_commit();
                $return['status'] = 'success';
                $return['message'] = 'Berhasil Hapus dari tabel ' . $table;
            }
        } catch (\Throwable $e) {
            $this->db->trans_rollback();
            $return['status'] = 'danger';
            $return['message'] = 'Gagal :: ' . $e->getMessage();
        }
        return $return;
    }
}
