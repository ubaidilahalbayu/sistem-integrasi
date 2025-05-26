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

    public function create_absen_by_file_v2($data)
    {
        $this->db->trans_begin();
        $return = [];
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
            
            //SIMPAN DATA DOSEN
            foreach ($data_dosen as $key => $value) {
                $where = [];
                $where['nip'] = $value['nip'];
                $check = $this->get_table('data_dosen', false, true, $where);
                if (count($check) > 0) {
                    continue;
                } else {
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
    public function get_table_rekap_absensi($where = [], $index_jadwal = 0)
    {
        //GET TABLE JADWAL KULIAH
        $data_jadwal = $this->get_table('jadwal_kuliah', false, true, $where);
        $data_isi_absen_mhs = [];
        $data_tanggal_jadwal = [];
        $data_mhs_ambil_jadwal = [];
        $data_isi_absen_dsn = [];

        // foreach ($data_jadwal as $key => $value) {
            //GET MAHASISWA AMBIL JADWAL
        if (count($data_jadwal) > 0) {
            $this->db->select('mhs_ambil_jadwal.id, mhs_ambil_jadwal.nim, mhs_ambil_jadwal.id_jadwal, data_mahasiswa.nama_mahasiswa, data_mahasiswa.angkatan');
            $this->db->from('mhs_ambil_jadwal');
            $this->db->join('data_mahasiswa', 'mhs_ambil_jadwal.nim = data_mahasiswa.nim');
            $this->db->where('mhs_ambil_jadwal.id_jadwal', $data_jadwal[$index_jadwal]['id']);
            $mhs_ambil_jadwal = $this->db->get()->result_array();
            $data_mhs_ambil_jadwal = $mhs_ambil_jadwal;
            // $isi_absen_mhs = [];
            // $tanggal_jadwal = [];
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
            // $data_isi_absen_mhs[] = $isi_absen_mhs;
            // $data_tanggal_jadwal[] = $tanggal_jadwal;
            //GET DATA ISI ABSEN DOSEN
            // foreach ($data_tanggal_jadwal as $key2 => $value2) {
            $this->db->select('nip, tanggal');
            $this->db->from('isi_absen_dosen');
            $this->db->where(array('id_jadwal' => $data_jadwal[$index_jadwal]['id']));
            $this->db->order_by('tanggal', 'ASC');
            $isi_absen_dsn = $this->db->get()->result_array();
            $isi_absen_dsn_fix = [];
            foreach ($isi_absen_dsn as $key => $value2) {
                if (!in_array($value2['tanggal'], $data_tanggal_jadwal)) {
                    $data_tanggal_jadwal[] = $value2['tanggal'];
                }
                $data_isi_absen_dsn[$value2['tanggal']] = $value2['nip'];
            }
            // }
        }
        // }
        return array(
            'data_jadwal' => $data_jadwal,
            'data_jadwal_selected' => count($data_jadwal) > 0 ? $data_jadwal[$index_jadwal] : [],
            'data_mhs_ambil_jadwal' => $data_mhs_ambil_jadwal,
            'data_tanggal_jadwal' => $data_tanggal_jadwal,
            'data_isi_absen_dsn' => $data_isi_absen_dsn,
            'data_isi_absen_mhs' => $data_isi_absen_mhs,
        );
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
            $this->db->select("jadwal_kuliah.id, jadwal_kuliah.kode_mk, data_mk.nama_mk, data_mk.semester, data_mk.sks, jadwal_kuliah.kode_kelas, data_kelas.nama_kelas, (CONCAT( CONCAT(data_dosen.nama_gelar_depan , ', '), data_dosen.nama_dosen, CONCAT(', ', data_dosen.nama_gelar_belakang))) AS pengampu_1, (CONCAT( CONCAT(data_dosen2.nama_gelar_depan , ', '), data_dosen2.nama_dosen, CONCAT(', ', data_dosen2.nama_gelar_belakang))) AS pengampu_2,  (CONCAT( CONCAT(data_dosen3.nama_gelar_depan , ', '), data_dosen3.nama_dosen, CONCAT(', ', data_dosen3.nama_gelar_belakang))) AS pengampu_3,  jadwal_kuliah.hari, jadwal_kuliah.jam_mulai, jadwal_kuliah.jam_selesai, jadwal_kuliah.nip, jadwal_kuliah.nip2, jadwal_kuliah.nip3, TIMEDIFF(jadwal_kuliah.jam_selesai, jadwal_kuliah.jam_mulai) AS diff, (SELECT COUNT(*) FROM jadwal_kuliah jk WHERE jk.hari = jadwal_kuliah.hari AND jk.jam_mulai = jadwal_kuliah.jam_mulai AND jk.jam_selesai = jadwal_kuliah.jam_selesai GROUP BY jk.hari, jk.jam_mulai, jk.jam_selesai) AS bentrok, data_dosen.nama_dosen, data_dosen2.nama_dosen AS nama_dosen2, data_dosen3.nama_dosen AS nama_dosen3, jadwal_kuliah.ruang, jadwal_kuliah.semester_char, (SELECT COUNT(*) FROM isi_absen_dosen WHERE id_jadwal = jadwal_kuliah.id AND nip = jadwal_kuliah.nip) AS jml, (SELECT COUNT(*) FROM isi_absen_dosen WHERE id_jadwal = jadwal_kuliah.id AND nip = jadwal_kuliah.nip2 AND nip != '-') AS jml2, (SELECT COUNT(*) FROM isi_absen_dosen WHERE id_jadwal = jadwal_kuliah.id AND nip = jadwal_kuliah.nip3 AND nip != '-') AS jml3");
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
            if ($where == "all") {
                $this->db->empty_table($table);
            } else {
                $this->db->where($where);
                $this->db->delete($table);
            }
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