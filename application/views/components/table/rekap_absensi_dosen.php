<div class="col-lg-12 mb-3">
</div>
<div class="col-lg-3 mb-3">
    <label for="ganti_semester">Pilih Semester</label>
    <select id="ganti_semester" menu="absen" prm_dsn="1" class="form-select">
        <?php
            foreach ($smt as $key => $value) {
        ?>
            <option value="<?= $value['tahun_1'].$value['tahun_2'].$value['semester'] ?>" <?= $selected_smt == $value['tahun_1'].$value['tahun_2'].$value['semester'] ? 'selected' : '' ?>>Semester <?= $value['semester'] == 1 ? 'Ganjil' : 'Genap' ?> <?= $value['tahun_1']."/".$value['tahun_2'] ?></option>
        <?php
            }
        ?>
    </select>
</div>
<div class="col-lg-12 mb-3">
    <div class="table-responsive">
        <table id="myTable" class="table table-info table-striped" rekap="1">
            <thead>
                <tr>
                    <th rowspan="<?= count($data_jadwal) > 0 ? "3" : "1"?>">No</th>
                    <th rowspan="<?= count($data_jadwal) > 0 ? "3" : "1"?>">Kode</th>
                    <th rowspan="<?= count($data_jadwal) > 0 ? "3" : "1"?>">Nama Dosen</th>
                    <th rowspan="<?= count($data_jadwal) > 0 ? "3" : "1"?>">Total Masuk</th>
                <?php
                if (count($data_jadwal) > 0) {
                ?>
                    <th colspan="<?= count($data_jadwal) ?>" class="text-start">Rekapitulasi Kehadiran Dosen</th>
                </tr>
                <tr>
                    <?php
                    foreach ($data_jadwal as $key => $value) {
                    ?>
                        <th>
                            <div class="dropdown">
                                <button type="button" class="btn btn-danger dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><?= "MK-".$value['id'] ?></button>
                                <ul class="dropdown-menu">
                                        <li><a class="dropdown-item active" href="#rekap_absensi_dosen"><?= $value['nama_mk'] ?></a></li>
                                        <li><a class="dropdown-item" href="#rekap_absensi_dosen"><?= "Kelas ".$value['kode_kelas'] ?></a></li>
                                        <li><a class="dropdown-item" href="#rekap_absensi_dosen"><?= $value['hari']."/".$value['jam_mulai']."-".$value['jam_selesai'] ?></a></li>
                                    </ul>
                            </div>
                        </th>
                    <?php
                    }
                    ?>
                </tr>
                <tr>
                    <?php
                    foreach ($data_jadwal as $key => $value) {
                    ?>
                        <th><?= $key+1 ?></th>
                    <?php
                    }
                    ?>
                </tr>
                <?php
                }else{
                ?>
                </tr>
                <?php
                }
                ?>
            </thead>
            <tbody>
                <?php
                $no = 1; 
                foreach ($dsn as $key => $value) {
                    if ($value['nip'] == "-") {
                        continue;
                    }
                    if ($this->session->userdata('level') == 3 && $value['nip'] != $this->session->userdata('username')) {
                        continue;
                    }
                    $nama_dosen = $value['nama_dosen'];
                    $nama_dosen .= !empty($value['nama_gelar_depan']) ? ($value['nama_gelar_depan'] != "-" ? ", ".$value['nama_gelar_depan'] : "") : "";
                    $nama_dosen .= !empty($value['nama_gelar_belakang']) ? ($value['nama_gelar_belakang'] != "-" ? $value['nama_gelar_belakang'] : "") : "";
                    $data_absen = [];
                    $total_masuk = 0;
                    foreach ($data_jadwal as $key2 => $value2) {
                        $this->db->select("COUNT(*) AS count");
                        $this->db->from("isi_absen_dosen");
                        $this->db->where(array("nip" => $value['nip'], "id_jadwal" => $value2['id']));
                        $kehadiran_dosen = $this->db->get()->row_array()['count'];
                        $total_masuk += $kehadiran_dosen;
                        if ($value2['nip'] != $value['nip'] && $value2['nip2'] != $value['nip'] && $value2['nip3'] != $value['nip']) {
                            $kehadiran_dosen = "-";
                        }
                        $data_absen[] = $kehadiran_dosen;
                    }
                ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $value['nip'] ?></td>
                        <td><?= $nama_dosen ?></td>
                        <td><?= $total_masuk ?></td>
                        <?php
                        foreach ($data_absen as $key2 => $value2) {
                        ?>
                            <td><?= $value2 ?></td>
                        <?php
                        }
                        ?>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>