<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MainModel extends CI_Model
{

    private $user_model;
    public function __construct()
    {
        parent::__construct();
        // Load database
        $this->load->database();
        $this->user_model = $this->db->get_where('data_dosen', array('nip' => $this->session->userdata('username')))->row_array();
        $this->user_model = !empty($this->user_model) ? (!empty($this->user_model['nama_dosen']) ? $this->user_model['nama_dosen'] : $this->session->userdata('username')) : $this->session->userdata('username'); 
    }

    public function create($table, $data, $banyak = false)
    {
        $this->db->trans_begin();
        $return = [];
        $header = $this->db->list_fields($table);
        try {
            if ($banyak) {
                $lanjut = true;
                foreach ($data as $key => $value) {
                    $insert = [];
                    foreach ($value as $key2 => $value2) {
                        if (in_array($key2, $header)) {
                            $insert[$key2] = !empty($value2) ? $value2 : NULL;
                        }
                    }
                    $this->db->insert($table, $insert);
                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                        $error = $this->db->error();
                        $return['status'] = 'danger';
                        $return['message'] = 'Gagal :: ' . $error['message'];
                        $lanjut = false;
                        break;
                    }
                }
                //INSERT AKTIVITAS
                $act = array(
                    'tanggal' => date("Y-m-d"),
                    'waktu' => date('H:i:s'),
                    'aktivitas' => $this->user_model." Menambah Data Ke tabel ".$table,
                );
                $this->db->insert('laporan_aktivitas', $act);
                if ($lanjut) {
                    $this->db->trans_commit();
                    $return['status'] = 'success';
                    $return['message'] = 'Berhasil Simpan tabel ' . $table;
                }
            } else {
                $insert = [];
                foreach ($data as $key => $value) {
                    if (in_array($key, $header)) {
                        $insert[$key] = !empty($value) ? $value : NULL;
                    }
                }
                $this->db->insert($table, $insert);
                //INSERT AKTIVITAS
                $act = array(
                    'tanggal' => date("Y-m-d"),
                    'waktu' => date('H:i:s'),
                    'aktivitas' => $this->user_model." Menambah Data Ke tabel ".$table,
                );
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
            }
        } catch (\Throwable $e) {
            $this->db->trans_rollback();
            $return['status'] = 'danger';
            $return['message'] = 'Gagal :: ' . $e->getMessage();
        }
        return $return;
    }

    public function create_new_password($password, $where)
    {
        $this->db->trans_begin();
        $return = [];
        try {
            $this->db->update('user', $password, $where);
            //INSERT AKTIVITAS
            $act = array(
                'tanggal' => date("Y-m-d"),
                'waktu' => date('H:i:s'),
                'aktivitas' => $this->user_model." Mengubah Kata Sandi",
            );
            $this->db->insert('laporan_aktivitas', $act);
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $error = $this->db->error();
                $return['status'] = 'danger';
                $return['message'] = 'Gagal :: ' . $error['message'];
            } else {
                $this->db->trans_commit();
                $return['status'] = 'success';
                $return['message'] = 'Berhasil Ubah Password Baru';
            }
        } catch (\Throwable $e) {
            $this->db->trans_rollback();
            $return['status'] = 'danger';
            $return['message'] = 'Gagal :: ' . $e->getMessage();
        }
        return $return;
        
    }

    public function auto_create_smt($data)
    {
        $check = $this->get_table('data_semester', false, true, $data);
        if (count($check) < 1) {
            $this->db->insert('data_semester', $data);
        }
    }

    public function create_absen_by_file_v2($data)
    {
        $this->db->trans_begin();
        $return = [];
        $data_semester = $data['data_semester'];
        $data_jadwal = $data['data_jadwal'];
        $data_dosen = $data['data_dosen'];
        $data_mk = $data['data_mk'];
        $data_kelas = $data['data_kelas'];
        $data_mahasiswa = $data['data_mahasiswa'];
        $data_mhs_ambil_jadwal = $data['data_mhs_ambil_jadwal'];
        $data_isi_absen_dsn = $data['data_isi_absen_dsn'];
        $data_isi_absen_mhs = $data['data_isi_absen_mhs'];

        try{
            $lanjut = true;

            //HAPUSIN SEMUA
            // $this->db->empty_table('data_dosen');
            // $this->db->empty_table('data_mahasiswa');
            // $this->db->empty_table('data_semester');
            // $this->db->empty_table('data_kelas');
            
            if ($lanjut) {
                //SIMPAN DATA SEMESTER
                $check = $this->get_table('data_semester', false, true, $data_semester);
                if (count($check) < 1) {
                    $this->db->insert('data_semester', $data_semester);
                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                        $error = $this->db->error();
                        $return['status'] = 'danger';
                        $return['message'] = 'Gagal :: ' . $error['message'];
                        $lanjut = false;
                    }
                }
            }

            $nip_dosen_berubah = [];
            $nip_dosen_fix = [];
            if ($lanjut) {
                //SIMPAN DATA DOSEN
                foreach ($data_dosen as $key => $value) {
                    $where = [];
                    $where['nama_dosen'] = $value['nama_dosen'];
                    $check = $this->get_table('data_dosen', false, true, $where);
                    if (count($check) > 0) {
                        if ($check[0]['nip'] != $value['nip'] && !in_array($value['nip'], $nip_dosen_berubah)) {
                            $nip_dosen_berubah[] = $value['nip'];
                            $nip_dosen_fix[] = $check[0]['nip'];
                        }
                        continue;
                    } else {
                        $where = [];
                        $where['nip'] = $value['nip'];
                        $check = $this->get_table('data_dosen', false, true, $where);
                        if (count($check) > 0) {
                            //BUAT NIP RANDOM SEMENTARA
                            do {
                                $nip = 'D';
                                for ($i = 0; $i < 3; $i++) {
                                    $nip .= random_int(0, 9);
                                }
                                $where = ['nip' => $nip];
                                $check = $this->get_table('data_dosen', false, true, $where);
                            } while (count($check) > 0);
                            if (!in_array($value['nip'], $nip_dosen_berubah)) {
                                $nip_dosen_berubah[] = $value['nip'];
                                $nip_dosen_fix[] = $nip;
                            }
                            $value['nip'] = $nip;
                        }
                        $this->db->insert('data_dosen', $value);
                        if ($this->db->trans_status() === FALSE) {
                            $this->db->trans_rollback();
                            $error = $this->db->error();
                            $return['status'] = 'danger';
                            $return['message'] = 'Gagal :: ' . $error['message'];
                            $lanjut = false;
                            break;
                        }
                    }
                }
            }

            if ($lanjut) {
                //SIMPAN DATA KELAS
                foreach ($data_kelas as $key => $value) {
                    $where = [];
                    $where['kode_kelas'] = $value['kode_kelas'];
                    $check = $this->get_table('data_kelas', false, true, $where);
                    if (count($check) > 0) {
                        continue;
                    } else {
                        $this->db->insert('data_kelas', $value);
                        if ($this->db->trans_status() === FALSE) {
                            $this->db->trans_rollback();
                            $error = $this->db->error();
                            $return['status'] = 'danger';
                            $return['message'] = 'Gagal :: ' . $error['message'];
                            $lanjut = false;
                            break;
                        }
                    }
                }
            }

            if ($lanjut) {
                //SIMPAN DATA MK
                foreach ($data_mk as $key => $value) {
                    $where = [];
                    $where['kode_mk'] = $value['kode_mk'];
                    $check = $this->get_table('data_mk', false, true, $where);
                    if (count($check) > 0) {
                        continue;
                    } else {
                        $this->db->insert('data_mk', $value);
                        if ($this->db->trans_status() === FALSE) {
                            $this->db->trans_rollback();
                            $error = $this->db->error();
                            $return['status'] = 'danger';
                            $return['message'] = 'Gagal :: ' . $error['message'];
                            $lanjut = false;
                            break;
                        }
                    }
                }
            }
            
            $id_jadwal = [];
            if ($lanjut) {
                //SIMPAN DATA JADWAL
                foreach ($data_jadwal as $key => $value) {
                    $where = [];
                    foreach ($value as $key2 => $value2) {
                        $where['jadwal_kuliah.'.$key2] = $value2;
                    }
                    $check = $this->get_table('jadwal_kuliah', false, true, $where);
                    if (count($check) > 0) {
                        continue;
                    } else {
                        if (in_array($value['nip'], $nip_dosen_berubah)) {
                            $index = array_search($value['nip'], $nip_dosen_berubah);
                            $value['nip'] = $nip_dosen_fix[$index];
                        }
                        if (in_array($value['nip2'], $nip_dosen_berubah)) {
                            $index = array_search($value['nip2'], $nip_dosen_berubah);
                            $value['nip2'] = $nip_dosen_fix[$index];
                        }
                        if (in_array($value['nip3'], $nip_dosen_berubah)) {
                            $index = array_search($value['nip3'], $nip_dosen_berubah);
                            $value['nip3'] = $nip_dosen_fix[$index];
                        }
                        $this->db->insert('jadwal_kuliah', $value);
                        $id = $this->db->insert_id();
                        $id_jadwal[$key] = $id;
                        if ($this->db->trans_status() === FALSE) {
                            $this->db->trans_rollback();
                            $error = $this->db->error();
                            $return['status'] = 'danger';
                            $return['message'] = 'Gagal :: ' . $error['message'];
                            $lanjut = false;
                            break;
                        }
                    }
                }
            }

            if ($lanjut) {
                //SIMPAN DATA MAHASISWA
                foreach ($data_mahasiswa['nim'] as $key => $value) {
                    $where = [];
                    $where['nim'] = $value;
                    $check = $this->get_table('data_mahasiswa', false, true, $where);
                    if (count($check) > 0) {
                        continue;
                    } else {
                        $insert = array(
                            'nim' => $value,
                            'nama_mahasiswa' => $data_mahasiswa['nama_mahasiswa'][$key],
                            'angkatan' => $data_mahasiswa['angkatan'][$key],
                        );
                        $this->db->insert('data_mahasiswa', $insert);
                        if ($this->db->trans_status() === FALSE) {
                            $this->db->trans_rollback();
                            $error = $this->db->error();
                            $return['status'] = 'danger';
                            $return['message'] = 'Gagal :: ' . $error['message'];
                            $lanjut = false;
                            break;
                        }
                    }
                }
            }

            $id_mhs_ambil_jadwal = [];
            if ($lanjut) {
                //SIMPAN DATA MHS AMBIL JADWAL
                foreach ($data_mhs_ambil_jadwal as $key => $value) {
                    $push_mhs_ambil_jadwal = [];
                    foreach ($value as $key2 => $value2) {
                        $where = [];
                        $where['id_jadwal'] = $id_jadwal[$key];
                        $where['nim'] = $value2;
                        $check = $this->get_table('mhs_ambil_jadwal', false, true, $where);
                        if (count($check) > 0) {
                            continue;
                        } else {
                            $insert = array(
                                'id_jadwal' => $id_jadwal[$key],
                                'nim' => $value2
                            );
                            $this->db->insert('mhs_ambil_jadwal', $insert);
                            $id = $this->db->insert_id();
                            $push_mhs_ambil_jadwal[] = $id;
                            if ($this->db->trans_status() === FALSE) {
                                $this->db->trans_rollback();
                                $error = $this->db->error();
                                $return['status'] = 'danger';
                                $return['message'] = 'Gagal :: ' . $error['message'];
                                $lanjut = false;
                                break;
                            }
                        }
                    }
                    if (!$lanjut) {
                        break;
                    }
                    $id_mhs_ambil_jadwal[$key] = $push_mhs_ambil_jadwal;
                }
            }
            
            if ($lanjut) {
                //SIMPAN DATA ISI ABSEN DOSEN
                foreach ($data_isi_absen_dsn as $key => $value) {
                    foreach ($value as $key2 => $value2) {
                        $where = [];
                        $where['id_jadwal'] = $id_jadwal[$key];
                        $where['tanggal'] = $value2['tanggal'];
                        $check = $this->get_table('isi_absen_dosen', false, true, $where);
                        if (count($check) > 0) {
                            continue;
                        } else {
                            if (in_array($value2['nip'], $nip_dosen_berubah)) {
                                $index = array_search($value2['nip'], $nip_dosen_berubah);
                                $value2['nip'] = $nip_dosen_fix[$index];
                            }
                            $insert = array(
                                'nip' => $value2['nip'],
                                'id_jadwal' => $id_jadwal[$key],
                                'tanggal' => $value2['tanggal']
                            );
                            $this->db->insert('isi_absen_dosen', $insert);
                            if ($this->db->trans_status() === FALSE) {
                                $this->db->trans_rollback();
                                $error = $this->db->error();
                                $return['status'] = 'danger';
                                $return['message'] = 'Gagal :: ' . $error['message'];
                                $lanjut = false;
                                break;
                            }
                        }
                    }
                    if (!$lanjut) {
                        break;
                    }
                }
            }
            // for ($i=0; $i < count($data_isi_absen_mhs); $i++) { 
            //     echo json_encode(count($id_mhs_ambil_jadwal[$i]))."<==>";
            //     echo "\n";
            //     echo json_encode(count($data_isi_absen_mhs[$i]))."<==>";
            // }
            //                         $this->db->trans_rollback();
            // die;
            if ($lanjut) {
                //SIMPAN DATA ISI ABSEN MAHASISWA
                foreach ($data_isi_absen_mhs as $key => $value) {
                    foreach ($value as $key2 => $value2) {
                        foreach ($value2 as $key3 => $value3) {
                            $where = [];
                            $where['id_mhs'] = $id_mhs_ambil_jadwal[$key][$key2];
                            $where['tanggal'] = $value3['tanggal'];
                            $check = $this->get_table('isi_absen_mhs', false, true, $where);
                            if (count($check) > 0) {
                                continue;
                            } else {
                                $insert = array(
                                    'tanggal' => $value3['tanggal'],
                                    'id_mhs' => $id_mhs_ambil_jadwal[$key][$key2],
                                    'keterangan' => $value3['keterangan']
                                );
                                $this->db->insert('isi_absen_mhs', $insert);
                                if ($this->db->trans_status() === FALSE) {
                                    $this->db->trans_rollback();
                                    $error = $this->db->error();
                                    $return['status'] = 'danger';
                                    $return['message'] = 'Gagal :: ' . $error['message'];
                                    $lanjut = false;
                                    break;
                                }
                            }
                        }
                        if (!$lanjut) {
                            break;
                        }
                    }
                    if (!$lanjut) {
                        break;
                    }
                }
            }

            //USER Dosen
            if ($lanjut) {
                $this->db->where('level', 3);
                $this->db->delete('user');
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $error = $this->db->error();
                    $return['status'] = 'danger';
                    $return['message'] = 'Gagal :: ' . $error['message'];
                    $lanjut = false;
                }
            }
            if ($lanjut) {
                foreach ($data_dosen as $key => $value) {
                    $nip = in_array($value['nip'], $nip_dosen_berubah) ? $nip_dosen_fix[array_search($value['nip'], $nip_dosen_berubah)] : $value['nip'];
                    $insert = array(
                        'username' => $nip,
                        'password' => md5($nip),
                        'level' => 3,
                    );
                    $this->db->insert('user', $insert);
                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                        $error = $this->db->error();
                        $return['status'] = 'danger';
                        $return['message'] = 'Gagal :: ' . $error['message'];
                        $lanjut = false;
                        break;
                    }
                }
            }
            //INSERT AKTIVITAS
            $act = array(
                'tanggal' => date("Y-m-d"),
                'waktu' => date('H:i:s'),
                'aktivitas' => $this->user_model." Mengimport File Rekapitulasi Kehadiran ".json_encode($data_semester),
            );
            $this->db->insert('laporan_aktivitas', $act);
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $error = $this->db->error();
                $return['status'] = 'danger';
                $return['message'] = 'Gagal :: ' . $error['message'];
                $lanjut = false;
            }
            if ($lanjut) {
                //COMMIT TRANSACTION
                $this->db->trans_commit();
                $return['status'] = 'success';
                $return['message'] = 'Berhasil Simpan Rekap Absensi dari File';
            }
        } catch (\Throwable $e) {
            $this->db->trans_rollback();
            $return['status'] = 'danger';
            $return['message'] = 'Gagal :: ' . $e->getMessage();
        }
        return $return;
    }

    public function create_absen_by_file($data)
    {
        $this->db->trans_begin();
        $return = [];
        $data_jadwal = $data['data_jadwal'];
        $data_dosen = $data['data_dosen'];
        $data_mk = $data['data_mk'];
        $data_kelas = $data['data_kelas'];
        $data_mahasiswa = $data['data_mahasiswa'];
        $data_absen = $data['data_absen'];
        $data_isi_absen = $data['data_isi_absen'];

        try{
            $lanjut = true;
            
            //SIMPAN DATA MK
            foreach ($data_mk as $key => $value) {
                $where['kode_mk'] = $value['kode_mk'];
                $check = $this->get_table('data_mk', false, true, $where);
                if (count($check) > 0) {
                    continue;
                } else {
                    $this->db->insert('data_mk', $value);
                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                        $error = $this->db->error();
                        $return['status'] = 'danger';
                        $return['message'] = 'Gagal :: ' . $error['message'];
                        $lanjut = false;
                    }
                }
            }

            //SIMPAN DATA KELAS
            if ($lanjut) {
                foreach ($data_kelas as $key => $value) {
                    $where = [];
                    $where['kode_kelas'] = $value['kode_kelas'];
                    $check = $this->get_table('data_kelas', false, true, $where);
                    if (count($check) > 0) {
                        continue;
                    } else {
                        $this->db->insert('data_kelas', $value);
                        if ($this->db->trans_status() === FALSE) {
                            $this->db->trans_rollback();
                            $error = $this->db->error();
                            $return['status'] = 'danger';
                            $return['message'] = 'Gagal :: ' . $error['message'];
                            $lanjut = false;
                        }
                    }
                }
            }

            //SIMPAN DATA MAHASISWA
            if ($lanjut) {
                foreach ($data_mahasiswa as $key => $value) {
                    $where = [];
                    $where['nim'] = $value['nim'];
                    $check = $this->get_table('data_mahasiswa', false, true, $where);
                    if (count($check) > 0) {
                        continue;
                    } else {
                        $this->db->insert('data_mahasiswa', $value);
                        if ($this->db->trans_status() === FALSE) {
                            $this->db->trans_rollback();
                            $error = $this->db->error();
                            $return['status'] = 'danger';
                            $return['message'] = 'Gagal :: ' . $error['message'];
                            $lanjut = false;
                        }
                    }
                }
            }

            //SIMPAN DATA DOSEN
            $nip1 = [];
            $nip2 = [];
            $nip3 = [];
            if ($lanjut) {
                foreach ($data_dosen['dosen1'] as $key => $value) {
                    $where = [];
                    $where['nama_dosen'] = $value;
                    $check = $this->get_table('data_dosen', false, true, $where);
                    if (count($check) > 0) {
                        array_push($nip1, $check[0]['nip']);
                        continue;
                    } else {
                        //BUAT NIP RANDOM SEMENTARA
                        do {
                            $nip = '';
                            for ($i = 0; $i < 15; $i++) {
                                $nip .= random_int(0, 9);
                            }
                            $where = ['nip' => $nip];
                            $check = $this->get_table('data_dosen', false, true, $where);
                        } while (count($check) > 0);
                        array_push($nip1, $nip);
                        $insert = array(
                            'nip' => $nip,
                            'nama_gelar_depan' => '-',
                            'nama_dosen' => $value,
                            'nama_gelar_belakang' => '-',
                        );
                        $this->db->insert('data_dosen', $insert);
                        if ($this->db->trans_status() === FALSE) {
                            $this->db->trans_rollback();
                            $error = $this->db->error();
                            $return['status'] = 'danger';
                            $return['message'] = 'Gagal :: ' . $error['message'];
                            $lanjut = false;
                        }
                    }
                }
            }
            if ($lanjut) {
                foreach ($data_dosen['dosen2'] as $key => $value) {
                    $where['nama_dosen'] = $value;
                    $check = $this->get_table('data_dosen', false, true, $where);
                    if (count($check) > 0) {
                        array_push($nip2, $check[0]['nip']);
                        continue;
                    } else {
                        //BUAT NIP RANDOM SEMENTARA
                        do {
                            $nip = '';
                            for ($i = 0; $i < 15; $i++) {
                                $nip .= random_int(0, 9);
                            }
                            $where = ['nip' => $nip];
                            $check = $this->get_table('data_dosen', false, true, $where);
                        } while (count($check) > 0);
                        array_push($nip2, $nip);
                        $insert = array(
                            'nip' => $nip,
                            'nama_gelar_depan' => '-',
                            'nama_dosen' => $value,
                            'nama_gelar_belakang' => '-',
                        );
                        $this->db->insert('data_dosen', $insert);
                        if ($this->db->trans_status() === FALSE) {
                            $this->db->trans_rollback();
                            $error = $this->db->error();
                            $return['status'] = 'danger';
                            $return['message'] = 'Gagal :: ' . $error['message'];
                            $lanjut = false;
                        }
                    }
                }
            }
            if ($lanjut) {
                foreach ($data_dosen['dosen3'] as $key => $value) {
                    $where['nama_dosen'] = $value;
                    $check = $this->get_table('data_dosen', false, true, $where);
                    if (count($check) > 0) {
                        array_push($nip3, $check[0]['nip']);
                        continue;
                    } else {
                        //BUAT NIP RANDOM SEMENTARA
                        do {
                            $nip = '';
                            for ($i = 0; $i < 15; $i++) {
                                $nip .= random_int(0, 9);
                            }
                            $where = ['nip' => $nip];
                            $check = $this->get_table('data_dosen', false, true, $where);
                        } while (count($check) > 0);
                        array_push($nip3, $nip);
                        $insert = array(
                            'nip' => $nip,
                            'nama_gelar_depan' => '-',
                            'nama_dosen' => $value,
                            'nama_gelar_belakang' => '-',
                        );
                        $this->db->insert('data_dosen', $insert);
                        if ($this->db->trans_status() === FALSE) {
                            $this->db->trans_rollback();
                            $error = $this->db->error();
                            $return['status'] = 'danger';
                            $return['message'] = 'Gagal :: ' . $error['message'];
                            $lanjut = false;
                        }
                    }
                }
            }

            //SIMPAN DATA JADWAL KULIAH
            $id_jadwal = [];
            if ($lanjut) {
                foreach ($data_jadwal as $key => $value) {
                    $where = [];
                    foreach ($value as $key2 => $value2) {
                        $where['jadwal_kuliah.'.$key2] = $value2;
                    }
                    $where['jadwal_kuliah.nip'] = $nip1[$key];
                    $where['jadwal_kuliah.nip2'] = $nip2[$key];
                    $where['jadwal_kuliah.nip3'] = $nip3[$key];
                    $check = $this->get_table('jadwal_kuliah', false, true, $where);
                    if (count($check) > 0) {
                        array_push($id_jadwal, $check[0]['id']);
                        continue;
                    } else {
                        $value['nip'] = $nip1[$key];
                        $value['nip2'] = $nip2[$key];
                        $value['nip3'] = $nip3[$key];
                        $this->db->insert('jadwal_kuliah', $value);
                        $id = $this->db->insert_id();
                        array_push($id_jadwal, $id);
                        if ($this->db->trans_status() === FALSE) {
                            $this->db->trans_rollback();
                            $error = $this->db->error();
                            $return['status'] = 'danger';
                            $return['message'] = 'Gagal :: ' . $error['message'];
                            $lanjut = false;
                        }
                    }
                }
            }
            
            //SIMPAN DATA ABSEN
            $id_absen = [];
            if ($lanjut) {
                foreach ($data_absen as $key => $value) {
                    $id_absen_push = [];
                    foreach ($value as $key2 => $value2) {
                        $where = [];
                        $where['id_jadwal'] = $id_jadwal[$key];
                        $where['tanggal'] = $value2['tanggal'];
                        $check = $this->get_table('absensi', false, true, $where);
                        if (count($check) > 0) {
                            array_push($id_absen_push, $check[0]['id']);
                            continue;
                        } else {
                            $value2['id_jadwal'] = $id_jadwal[$key];
                            $this->db->insert('absensi', $value2);
                            $id = $this->db->insert_id();
                            array_push($id_absen_push, $id);
                            if ($this->db->trans_status() === FALSE) {
                                $this->db->trans_rollback();
                                $error = $this->db->error();
                                $return['status'] = 'danger';
                                $return['message'] = 'Gagal :: ' . $error['message'];
                                $lanjut = false;
                            }
                        }
                    }
                    array_push($id_absen, $id_absen_push);
                }
            }

            //SIMPAN ISI ABSEN
            if ($lanjut) {
                foreach ($data_isi_absen as $key => $value) {
                    foreach ($value as $key2 => $value2) {
                        foreach ($value2 as $key3 => $value3) {
                            $where = [];
                            $where['id_absen'] = $id_absen[$key][$key3];
                            $where['nim'] = $value3['nim'];
                            $check = $this->get_table('isi_absen_mhs', false, true, $where);
                            if (count($check) > 0) {
                                continue;
                            } else {
                                $value3['id_absen'] = $id_absen[$key][$key3];
                                $this->db->insert('isi_absen_mhs', $value3);
                                if ($this->db->trans_status() === FALSE) {
                                    $this->db->trans_rollback();
                                    $error = $this->db->error();
                                    $return['status'] = 'danger';
                                    $return['message'] = 'Gagal :: ' . $error['message'];
                                    $lanjut = false;
                                }
                            }
                        }
                    }
                }
            }

            if ($lanjut) {
                //COMMIT TRANSACTION
                $this->db->trans_commit();
                $return['status'] = 'success';
                $return['message'] = 'Berhasil Simpan Rekap Absensi dari File';
            }
        } catch (\Throwable $e) {
            $this->db->trans_rollback();
            $return['status'] = 'danger';
            $return['message'] = 'Gagal :: ' . $e->getMessage();
        }
        return $return;
    }

    public function create_jadwal_by_file($data)
    {
        $this->db->trans_begin();
        $return = [];
        $data_jadwal = $data['data_jadwal'];
        $data_dosen = $data['data_dosen'];
        $data_mk = $data['data_mk'];
        $data_kelas = $data['data_kelas'];
        try {
            //SIMPAN SEMESTER
            $lanjut = true;
            foreach ($data_mk['semester'] as $key => $value) {
                $where = ['id_semester' => $value];
                $check = $this->get_table('data_semester', false, true, $where);
                if (count($check) > 0) {
                    continue;
                } else {
                    $insert = [
                        'id_semester' => $value,
                        'tahun' => date('Y'),
                    ];
                    $this->db->insert('data_semester', $insert);
                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                        $error = $this->db->error();
                        $return['status'] = 'danger';
                        $return['message'] = 'Gagal :: ' . $error['message'];
                        $lanjut = false;
                        break;
                    }
                }
            }

            if ($lanjut) {
                //SIMPAN DATA MK
                foreach ($data_mk['kode_mk'] as $key => $value) {
                    $where = ['kode_mk' => $value];
                    $check = $this->get_table('data_mk', false, true, $where);
                    if (count($check) > 0) {
                        continue;
                    } else {
                        $insert = [
                            'kode_mk' => $value,
                            'nama_mk' => $data_mk['nama_mk'][$key],
                            'sks' => $data_mk['sks'][$key],
                            'semester' => $data_mk['semester'][$key],
                        ];
                        $this->db->insert('data_mk', $insert);
                        if ($this->db->trans_status() === FALSE) {
                            $this->db->trans_rollback();
                            $error = $this->db->error();
                            $return['status'] = 'danger';
                            $return['message'] = 'Gagal :: ' . $error['message'];
                            $lanjut = false;
                            break;
                        }
                    }
                }
            }
            if ($lanjut) {
                // SIMPAN DATA KELAS
                foreach ($data_kelas['kode_kelas'] as $key => $value) {
                    $where = ['kode_kelas' => $value];
                    $check = $this->get_table('data_kelas', false, true, $where);
                    if (count($check) > 0) {
                        continue;
                    } else {
                        $insert = [
                            'kode_kelas' => $value,
                            'nama_kelas' => $data_kelas['nama_kelas'][$key],
                        ];
                        $this->db->insert('data_kelas', $insert);
                        if ($this->db->trans_status() === FALSE) {
                            $this->db->trans_rollback();
                            $error = $this->db->error();
                            $return['status'] = 'danger';
                            $return['message'] = 'Gagal :: ' . $error['message'];
                            $lanjut = false;
                            break;
                        }
                    }
                }
            }
            if ($lanjut) {
                // SIMPAN DATA DOSEN
                //UNTUK MENNYIMPAN NIP ASLI KARENA NIP BELUM ADA
                $data_dosen['nip_asli'] = [];
                foreach ($data_dosen['nama_dosen'] as $key => $value) {
                    $where = ['nama_dosen' => $value, 'nama_gelar_belakang' => $data_dosen['nama_gelar_belakang'][$key]];
                    $check = $this->get_table('data_dosen', false, true, $where);
                    if (count($check) > 0) {
                        $data_dosen['nip_asli'][$key] = $check[0]['nip'];
                        continue;
                    } else {
                        //BUAT NIP RANDOM SEMENTARA
                        do {
                            $nip = '';
                            for ($i = 0; $i < 15; $i++) {
                                $nip .= random_int(0, 9);
                            }
                            $where = ['nip' => $nip];
                            $check = $this->get_table('data_dosen', false, true, $where);
                        } while (count($check) > 0);

                        $insert = [
                            'nip' => $nip,
                            'nama_gelar_depan' => '-',
                            'nama_dosen' => $value,
                            'nama_gelar_belakang' => $data_dosen['nama_gelar_belakang'][$key],
                        ];
                        $this->db->insert('data_dosen', $insert);
                        if ($this->db->trans_status() === FALSE) {
                            $this->db->trans_rollback();
                            $error = $this->db->error();
                            $return['status'] = 'danger';
                            $return['message'] = 'Gagal :: ' . $error['message'];
                            $lanjut = false;
                            break;
                        }
                        $data_dosen['nip_asli'][$key] = $nip;
                    }
                }
            }
            if ($lanjut) {
                // SIMPAN DATA JADWAL
                foreach ($data_jadwal as $key => $value) {
                    $nip_asli = $data_dosen['nip_asli'][array_search($value['nip'], $data_dosen['nip'])]; //Mencari nip asli
                    $nip_asli2 = $data_dosen['nip_asli'][array_search($value['nip2'], $data_dosen['nip'])]; //Mencari nip asli
                    $nip_asli3 = $data_dosen['nip_asli'][array_search($value['nip3'], $data_dosen['nip'])]; //Mencari nip asli

                    $where = [
                        'jadwal_kuliah.kode_mk' => $value['kode_mk'],
                        'jadwal_kuliah.kode_kelas' => $value['kode_kelas'],
                        'jadwal_kuliah.nip' => $nip_asli,
                        'jadwal_kuliah.nip2' => $nip_asli2,
                        'jadwal_kuliah.nip3' => $nip_asli3,
                        'jadwal_kuliah.hari' => $value['hari'],
                        'jadwal_kuliah.jam_mulai' => $value['jam_mulai'],
                        'jadwal_kuliah.jam_selesai' => $value['jam_selesai'],
                    ];
                    $check = $this->get_table('jadwal_kuliah', false, true, $where);
                    if (count($check) > 0) {
                        continue;
                    } else {
                        $insert = [
                            'id' => '',
                            'kode_mk' => $value['kode_mk'],
                            'kode_kelas' => $value['kode_kelas'],
                            'nip' => $nip_asli,
                            'nip2' => $nip_asli2,
                            'nip3' => $nip_asli3,
                            'hari' => $value['hari'],
                            'jam_mulai' => $value['jam_mulai'],
                            'jam_selesai' => $value['jam_selesai'],
                        ];
                        $this->db->insert('jadwal_kuliah', $insert);
                        if ($this->db->trans_status() === FALSE) {
                            $this->db->trans_rollback();
                            $error = $this->db->error();
                            $return['status'] = 'danger';
                            $return['message'] = 'Gagal :: ' . $error['message'];
                            $lanjut = false;
                            break;
                        }
                    }
                }
            }
            //INSERT AKTIVITAS
            $act = array(
                'tanggal' => date("Y-m-d"),
                'waktu' => date('H:i:s'),
                'aktivitas' => $this->user_model." Mengimport File Jadwal Kuliah",
            );
            $this->db->insert('laporan_aktivitas', $act);
            if ($lanjut) {
                //COMMIT TRANSACTION
                $this->db->trans_commit();
                $return['status'] = 'success';
                $return['message'] = 'Berhasil Simpan Jadwal Kuliah dari File';
            }
        } catch (\Throwable $e) {
            $this->db->trans_rollback();
            $return['status'] = 'danger';
            $return['message'] = 'Gagal :: ' . $e->getMessage();
        }
        return $return;
    }

    public function update_or_create_absen($data, $where, $is_mhs = false)
    {
        $this->db->trans_begin();
        $return = [];
        try{
            $table = 'isi_absen_dosen';
            $message = array(
                'update' => 'Berhasil Update Dosen Masuk',
                'insert' => 'Berhasil Memilih Dosen Masuk'
            );
            if ($is_mhs) {
                $table = 'isi_absen_mhs';
                $message = array(
                    'update' => 'Berhasil Update Kehadiran',
                    'insert' => 'Berhasil Simpan Kehadiran'
                );
            }
            $this->db->where($where);
            $count = count($this->db->get($table)->result_array());
            if ($count > 0) {
                $this->db->update($table, $data, $where);
                //INSERT AKTIVITAS
                $act = array(
                    'tanggal' => date("Y-m-d"),
                    'waktu' => date('H:i:s'),
                    'aktivitas' => $this->user_model." Mengubah Kehadiran ".$table." Where".json_encode($where)." Menjadi ".json_encode($data),
                );
                $this->db->insert('laporan_aktivitas', $act);
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $error = $this->db->error();
                    $return['status'] = false;
                    $return['message'] = 'Gagal :: ' . $error['message'];
                }else{
                    $this->db->trans_commit();
                    $return['status'] = true;
                    $return['message'] = $message['update'];
                }
            }else{
                $this->db->insert($table, $data);
                //INSERT AKTIVITAS
                $act = array(
                    'tanggal' => date("Y-m-d"),
                    'waktu' => date('H:i:s'),
                    'aktivitas' => $this->user_model." Menambah Kehadiran ".$table." Dengan Data ".json_encode($data),
                );
                $this->db->insert('laporan_aktivitas', $act);
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $error = $this->db->error();
                    $return['status'] = false;
                    $return['message'] = 'Gagal :: ' . $error['message'];
                }else{
                    $this->db->trans_commit();
                    $return['status'] = true;
                    $return['message'] = $message['insert'];
                }
            }
        } catch (\Throwable $e) {
            $this->db->trans_rollback();
            $return['status'] = false;
            $return['message'] = 'Gagal :: ' . $e->getMessage();
        }
        return $return;
    }

    // Mengambil semua pengguna beserta nama kolom
    public function get_table_rekap_absensi($where = [], $index_jadwal = 0, $export = false)
    {
        //GET TABLE JADWAL KULIAH
        $data_jadwal = $this->get_table('jadwal_kuliah', false, true, $where);
        $data_isi_absen_mhs = [];
        $data_tanggal_jadwal = [];
        $data_mhs_ambil_jadwal = [];
        $data_isi_absen_dsn = [];

        if ($export) {
            $data_mhs_ambil_jadwal = [];
            foreach ($data_jadwal as $key => $value) {
                //GET MAHASISWA AMBIL JADWAL
                if (count($data_jadwal) > 0) {
                    $this->db->select('mhs_ambil_jadwal.id, mhs_ambil_jadwal.nim, mhs_ambil_jadwal.id_jadwal, data_mahasiswa.nama_mahasiswa, data_mahasiswa.angkatan');
                    $this->db->from('mhs_ambil_jadwal');
                    $this->db->join('data_mahasiswa', 'mhs_ambil_jadwal.nim = data_mahasiswa.nim');
                    $this->db->where('mhs_ambil_jadwal.id_jadwal', $value['id']);
                    $mhs_ambil_jadwal = $this->db->get()->result_array();
                    $data_mhs_ambil_jadwal[] = $mhs_ambil_jadwal;
                    $isi_absen_mhs = [];
                    $tanggal_jadwal = [];
                    foreach ($mhs_ambil_jadwal as $key2 => $value2) {
                        //GET ISI ABSEN MAHASISWA
                        $this->db->select('*');
                        $this->db->from('isi_absen_mhs');
                        $this->db->where('id_mhs', $value2['id']);
                        $this->db->order_by('tanggal', 'ASC');
                        $absen_mhs = $this->db->get()->result_array();
                        $absen_mhs_fix = [];
                        foreach ($absen_mhs as $key3 => $value3) {
                            //GET TANGGAL JADWAL KULIAH YANG BERJALAN
                            if (!in_array($value3['tanggal'], $tanggal_jadwal)) {
                                $tanggal_jadwal[] = $value3['tanggal'];
                            }
                            $absen_mhs_fix[$value3['tanggal']] = $value3;
                        }
                        $isi_absen_mhs[] = $absen_mhs_fix;
                    }
                    $data_isi_absen_mhs[] = $isi_absen_mhs;
                    //GET DATA ISI ABSEN DOSEN
                    $this->db->select('nip, tanggal');
                    $this->db->from('isi_absen_dosen');
                    $this->db->where(array('id_jadwal' => $value['id']));
                    $this->db->order_by('tanggal', 'ASC');
                    $isi_absen_dsn = $this->db->get()->result_array();
                    $isi_absen_dsn_fix = [];
                    foreach ($isi_absen_dsn as $key => $value2) {
                        if (!in_array($value2['tanggal'], $tanggal_jadwal)) {
                            $tanggal_jadwal[] = $value2['tanggal'];
                        }
                        $isi_absen_dsn_fix[$value2['tanggal']] = $value2['nip'];
                    }
                    $data_tanggal_jadwal[] = $tanggal_jadwal;
                    $data_isi_absen_dsn[] = $isi_absen_dsn_fix;
                }
            }
            $this->db->where('nip!=', '-');
            $data_dosen = $this->db->get("data_dosen")->result_array();
            $data_dosen_fix = [];
            $data_masuk_dosen = [];
            foreach ($data_dosen as $key => $value) {
                $ada = false;
                $total_masuk = 0;
                $masuk_dosen = [];
                foreach ($data_jadwal as $key2 => $value2) {
                    $total_masuk_jdw = 0;
                    if ($value['nip'] == $value2['nip'] || $value['nip'] == $value2['nip2'] || $value['nip'] == $value2['nip3']) {
                        $ada = true;
                        $total_masuk_jdw = $this->db->get_where('isi_absen_dosen', array('id_jadwal'=>$value2['id'], 'nip'=>$value['nip']))->result_array();
                        $total_masuk_jdw = count($total_masuk_jdw);
                        $total_masuk += $total_masuk_jdw;
                    }else{
                        $total_masuk_jdw = "-";
                    }
                    $masuk_dosen[] = $total_masuk_jdw;
                }
                if ($ada) {
                    $value['total_masuk'] = $total_masuk;
                    $data_dosen_fix[] = $value;
                    $data_masuk_dosen[] = $masuk_dosen;
                }
            }
            return array(
                'data_jadwal' => $data_jadwal,
                'data_jadwal_selected' => count($data_jadwal) > 0 ? $data_jadwal[$index_jadwal] : [],
                'data_mhs_ambil_jadwal' => $data_mhs_ambil_jadwal,
                'data_tanggal_jadwal' => $data_tanggal_jadwal,
                'data_isi_absen_dsn' => $data_isi_absen_dsn,
                'data_isi_absen_mhs' => $data_isi_absen_mhs,
                'data_dosen' => $data_dosen_fix,
                'data_rekap_dosen' => $data_masuk_dosen,
            );
        }else{
                //GET MAHASISWA AMBIL JADWAL
            if (count($data_jadwal) > 0) {
                $this->db->select('mhs_ambil_jadwal.id, mhs_ambil_jadwal.nim, mhs_ambil_jadwal.id_jadwal, data_mahasiswa.nama_mahasiswa, data_mahasiswa.angkatan');
                $this->db->from('mhs_ambil_jadwal');
                $this->db->join('data_mahasiswa', 'mhs_ambil_jadwal.nim = data_mahasiswa.nim');
                $this->db->where('mhs_ambil_jadwal.id_jadwal', $data_jadwal[$index_jadwal]['id']);
                $mhs_ambil_jadwal = $this->db->get()->result_array();
                $data_mhs_ambil_jadwal = $mhs_ambil_jadwal;
                foreach ($mhs_ambil_jadwal as $key2 => $value2) {
                    //GET ISI ABSEN MAHASISWA
                    $this->db->select('*');
                    $this->db->from('isi_absen_mhs');
                    $this->db->where('id_mhs', $value2['id']);
                    $this->db->order_by('tanggal', 'ASC');
                    $absen_mhs = $this->db->get()->result_array();
                    $absen_mhs_fix = [];
                    foreach ($absen_mhs as $key3 => $value3) {
                        //GET TANGGAL JADWAL KULIAH YANG BERJALAN
                        if (!in_array($value3['tanggal'], $data_tanggal_jadwal)) {
                            $data_tanggal_jadwal[] = $value3['tanggal'];
                        }
                        $absen_mhs_fix[$value3['tanggal']] = $value3;
                    }
                    $data_isi_absen_mhs[] = $absen_mhs_fix;
                }
                $this->db->select('nip, tanggal');
                $this->db->from('isi_absen_dosen');
                $this->db->where(array('id_jadwal' => $data_jadwal[$index_jadwal]['id']));
                $this->db->order_by('tanggal', 'ASC');
                $isi_absen_dsn = $this->db->get()->result_array();
                foreach ($isi_absen_dsn as $key => $value2) {
                    if (!in_array($value2['tanggal'], $data_tanggal_jadwal)) {
                        $data_tanggal_jadwal[] = $value2['tanggal'];
                    }
                    $data_isi_absen_dsn[$value2['tanggal']] = $value2['nip'];
                }
            }
            return array(
                'data_jadwal' => $data_jadwal,
                'data_jadwal_selected' => count($data_jadwal) > 0 ? $data_jadwal[$index_jadwal] : [],
                'data_mhs_ambil_jadwal' => $data_mhs_ambil_jadwal,
                'data_tanggal_jadwal' => $data_tanggal_jadwal,
                'data_isi_absen_dsn' => $data_isi_absen_dsn,
                'data_isi_absen_mhs' => $data_isi_absen_mhs,
            );
        }
    }
    // Mengambil semua pengguna beserta nama kolom
    public function get_table($table, $get_header = true, $get_data = true, $where = [])
    {
        if (!is_bool($get_header)) {
            if (is_array($get_header)) {
                $where = $get_header;
                $get_header = true;
            }
        }
        if ($table == 'jadwal_kuliah') {
            $this->db->select("jadwal_kuliah.id, jadwal_kuliah.kode_mk, data_mk.nama_mk, data_mk.semester, data_mk.sks, jadwal_kuliah.kode_kelas, data_kelas.nama_kelas, (CONCAT( data_dosen.nama_dosen, data_dosen.nama_gelar_depan, data_dosen.nama_gelar_belakang )) AS pengampu_1, (CONCAT( data_dosen2.nama_dosen, data_dosen2.nama_gelar_depan, data_dosen2.nama_gelar_belakang )) AS pengampu_2, (CONCAT( data_dosen3.nama_dosen, data_dosen3.nama_gelar_depan, data_dosen3.nama_gelar_belakang )) AS pengampu_3,  jadwal_kuliah.hari, jadwal_kuliah.jam_mulai, jadwal_kuliah.jam_selesai, jadwal_kuliah.nip, jadwal_kuliah.nip2, jadwal_kuliah.nip3, TIMEDIFF(jadwal_kuliah.jam_selesai, jadwal_kuliah.jam_mulai) AS diff, (SELECT COUNT(*) FROM jadwal_kuliah jk WHERE jk.hari = jadwal_kuliah.hari AND jk.jam_mulai = jadwal_kuliah.jam_mulai AND jk.jam_selesai = jadwal_kuliah.jam_selesai GROUP BY jk.hari, jk.jam_mulai, jk.jam_selesai) AS bentrok, data_dosen.nama_dosen, data_dosen2.nama_dosen AS nama_dosen2, data_dosen3.nama_dosen AS nama_dosen3, jadwal_kuliah.ruang, jadwal_kuliah.semester_char, (SELECT COUNT(*) FROM isi_absen_dosen WHERE id_jadwal = jadwal_kuliah.id AND nip = jadwal_kuliah.nip) AS jml, (SELECT COUNT(*) FROM isi_absen_dosen WHERE id_jadwal = jadwal_kuliah.id AND nip = jadwal_kuliah.nip2 AND nip != '-') AS jml2, (SELECT COUNT(*) FROM isi_absen_dosen WHERE id_jadwal = jadwal_kuliah.id AND nip = jadwal_kuliah.nip3 AND nip != '-') AS jml3");
            $this->db->from($table);
            $this->db->join('data_mk', 'jadwal_kuliah.kode_mk = data_mk.kode_mk');
            $this->db->join('data_dosen', 'jadwal_kuliah.nip = data_dosen.nip');
            $this->db->join('data_dosen AS data_dosen2', 'jadwal_kuliah.nip2 = data_dosen2.nip');
            $this->db->join('data_dosen AS data_dosen3', 'jadwal_kuliah.nip3 = data_dosen3.nip');
            $this->db->join('data_kelas', 'jadwal_kuliah.kode_kelas = data_kelas.kode_kelas');
            if (!empty($where)) {
                $this->db->where($where);
            }
            $query = $this->db->get();
            $header = ['id', 'kode_mk', 'nama_mk', 'semester', 'kode_kelas', 'pengampu_1', 'pengampu_2', 'pengampu_3', 'hari', 'jam_mulai', 'jam_selesai', 'ruang'];
        } elseif ($table == "persentase") {
            $this->db->select("jadwal_kuliah.id, jadwal_kuliah.kode_mk, data_mk.nama_mk, CONCAT(jadwal_kuliah.hari, ', ', jadwal_kuliah.jam_mulai, '-', jadwal_kuliah.jam_selesai) AS jadwal, COUNT(isi_absen_dosen.id_jadwal) AS jlh_pertemuan, jadwal_kuliah.hari, jadwal_kuliah.semester_char AS smt");
            $this->db->from('jadwal_kuliah');
            $this->db->join('data_mk', 'data_mk.kode_mk=jadwal_kuliah.kode_mk');
            $this->db->join('isi_absen_dosen', 'isi_absen_dosen.id_jadwal=jadwal_kuliah.id');
            $this->db->where('isi_absen_dosen.nip!=', '-');
            $this->db->where('jadwal_kuliah.semester_char', $where['semester_char']);
            $this->db->group_by('jadwal_kuliah.id');
            $query = $this->db->get();
            $get_header = false;
        } elseif ($table=="laporan_aktivitas") {
            $limit = $where['limit'];
            unset($where['limit']);
            $search = $where['search'];
            unset($where['search']);
            $sql = "
                SELECT 
                    @rownum := @rownum + 1 AS no,
                    id_log,
                    tanggal,
                    waktu,
                    aktivitas
                FROM (
                    SELECT id_log, tanggal, waktu, aktivitas
                    FROM laporan_aktivitas
                    WHERE aktivitas LIKE ?
                    ORDER BY tanggal DESC, waktu DESC
                    LIMIT ?
                ) AS sub,
                (SELECT @rownum := 0) AS r
            ";

            $query = $this->db->query($sql, ['%' . $search . '%', (int)$limit]);
            $header = ['no', 'tanggal', 'waktu', 'aktivitas'];
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
        $return = [];
        if ($get_header && $get_data) {
            $return['header'] = $header;
            $return['data'] = $query->result_array();
        } elseif ($get_data) {
            $return = $query->result_array();
        } elseif ($get_header) {
            $return = $header;
        }
        return $return;
    }

    // Mengambil pengguna berdasarkan ID
    public function get_user_by_id($id)
    {
        $query = $this->db->get_where('user', array('id' => $id));
        return $query->row();
    }

    public function update_tgl_absen($data, $where)
    {
        $this->db->trans_begin();
        $return = [];
        try{
            $count = 0;
            $cek_tgl = array('tanggal' => $data['tanggal'], 'id_jadwal' => $where['id_jadwal']);
            $cek_tgl = $this->db->get_where('isi_absen_dosen', $cek_tgl)->result_array();
            if (count($cek_tgl) > 0) {
                $this->db->trans_rollback();
                $return['status'] = false;
                $return['message'] = 'Gagal :: Tanggal Yang dipilih sudah ada, Silahkan Pilih Tanggal Yang Lain!';
            }else{
                $cek_tgl = $this->db->get_where('isi_absen_dosen', $where)->result_array();
                if (count($cek_tgl) > 0) {
                    $this->db->update('isi_absen_dosen', $data, $where);
                    $count += $this->db->affected_rows();
                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                        $error = $this->db->error();
                        $return['status'] = false;
                        $return['message'] = 'Gagal :: ' . $error['message'];
                    }else{
                        $get_mhs_ambil_jadwal = array('id_jadwal'=>$where['id_jadwal']);
                        $get_mhs_ambil_jadwal = $this->db->get_where('mhs_ambil_jadwal', $get_mhs_ambil_jadwal)->result_array();
                        foreach ($get_mhs_ambil_jadwal as $key => $value) {
                            $this->db->update('isi_absen_mhs', $data, array('id_mhs' => $value['id'], 'tanggal '=> $where['tanggal']));
                            $count += $this->db->affected_rows();
                            if ($this->db->trans_status() === FALSE) {
                                break;
                            }
                        }
                        if ( $count > 0) {
                            //INSERT AKTIVITAS
                            $act = array(
                                'tanggal' => date("Y-m-d"),
                                'waktu' => date('H:i:s'),
                                'aktivitas' => $this->user_model." Mengubah Tanggal Kehadiran Where ".json_encode($where)." Menjadi ".json_encode($data),
                            );
                            $this->db->insert('laporan_aktivitas', $act);
                        }
                        if ($this->db->trans_status() === FALSE) {
                            $this->db->trans_rollback();
                            $error = $this->db->error();
                            $return['status'] = false;
                            $return['message'] = 'Gagal :: ' . $error['message'];
                        }else{
                            $this->db->trans_commit();
                            $return['status'] = true;
                            $return['message'] = "Berhasil Update Tanggal Pertemuan";
                        }
                    }
                }else{
                    $this->db->trans_rollback();
                    $return['status'] = false;
                    $return['message'] = 'Gagal :: Pertemuan Belum Dibuat!';
                }
            }
        } catch (\Throwable $e) {
            $this->db->trans_rollback();
            $return['status'] = false;
            $return['message'] = 'Gagal :: ' . $e->getMessage();
        }
        return $return;
    }
    public function update($table, $data, $where)
    {
        $this->db->trans_begin();
        $return = [];
        try {
            $count = 0;
            $header = $this->db->list_fields($table);
            $insert = [];
            foreach ($data as $key => $value) {
                if (in_array($key, $header)) {
                    $insert[$key] = !empty($value) ? $value : NULL;
                }
            }
            $this->db->where($where);
            $this->db->update($table, $insert);
            $count += $this->db->affected_rows();
            if ( $count > 0) {
                //INSERT AKTIVITAS
                $act = array(
                    'tanggal' => date("Y-m-d"),
                    'waktu' => date('H:i:s'),
                    'aktivitas' => $this->user_model." Mengubah Where ".json_encode($where)." Menjadi ".json_encode($insert)." Dari Tabel ".$table,
                );
                $this->db->insert('laporan_aktivitas', $act);
            }
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
            $count = 0;
            if ($where == "all") {
                $this->db->empty_table($table);
                $count += $this->db->affected_rows();
                
            } else {
                $this->db->where($where);
                $this->db->delete($table);
                $count += $this->db->affected_rows();
            }
            if ( $count > 0) {
                //INSERT AKTIVITAS
                $act = array(
                    'tanggal' => date("Y-m-d"),
                    'waktu' => date('H:i:s'),
                    'aktivitas' => $this->user_model." Menghapus Where ".json_encode($where)." Dari Tabel ".$table,
                );
                $this->db->insert('laporan_aktivitas', $act);
            }
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $error = $this->db->error();
                $return['status'] = 'danger';
                $return['message'] = 'Gagal :: ' . $error['message'];
            } elseif ($count == 0) {
                $this->db->trans_rollback();
                $error = $this->db->error();
                $return['status'] = 'danger';
                $return['message'] = 'Gagal :: Tidak Ada Data!';
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
    // Menghapus semua
    public function deleteAll()
    {
        $this->db->trans_begin();
        $return = [];
        try {
            $count = 0;
            $this->db->empty_table('data_semester');
            $this->db->empty_table('data_mk');
            $count += $this->db->affected_rows();
            $this->db->empty_table('data_kelas');
            $count += $this->db->affected_rows();
            $this->db->empty_table('data_dosen');
            $count += $this->db->affected_rows();
            $this->db->empty_table('data_mahasiswa');
            $count += $this->db->affected_rows();
            if ( $count > 0) {
                //INSERT AKTIVITAS
                $act = array(
                    'tanggal' => date("Y-m-d"),
                    'waktu' => date('H:i:s'),
                    'aktivitas' => $this->user_model." Menghapus SELURUH DATABASE !!",
                );
                $this->db->insert('laporan_aktivitas', $act);
            }
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $error = $this->db->error();
                $return['status'] = 'danger';
                $return['message'] = 'Gagal :: ' . $error['message'];
            } elseif ($count == 0) {
                $this->db->trans_rollback();
                $error = $this->db->error();
                $return['status'] = 'danger';
                $return['message'] = 'Gagal :: Database Sudah Kosong!';
            } else {
                $this->db->trans_commit();
                $return['status'] = 'success';
                $return['message'] = 'Berhasil Menghapus Semua Data';
            }
        } catch (\Throwable $e) {
            $this->db->trans_rollback();
            $return['status'] = 'danger';
            $return['message'] = 'Gagal :: ' . $e->getMessage();
        }
        return $return;
    }
}