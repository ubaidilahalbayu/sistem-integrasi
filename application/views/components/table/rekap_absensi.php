<div class="col-lg-12 mb-3">
</div>
<div class="col-lg-3 mb-3">
    <label for="ganti_semester">Pilih Semester</label>
    <select id="ganti_semester" menu="absen" class="form-select">
        <?php
            foreach ($smt as $key => $value) {
        ?>
            <option value="<?= $value['tahun_1'].$value['tahun_2'].$value['semester'] ?>" <?= $selected_smt == $value['tahun_1'].$value['tahun_2'].$value['semester'] ? 'selected' : '' ?>>Semester <?= $value['semester'] == 1 ? 'Ganjil' : 'Genap' ?> <?= $value['tahun_1']."/".$value['tahun_2'] ?></option>
        <?php
            }
        ?>
    </select>
</div>
<div class="col-lg-3 mb-3">
    <label for="ganti_hari">Pilih Hari</label>
    <select id="ganti_hari" menu="absen" class="form-select">
        <?php
            foreach ($hr as $key => $value) {
        ?>
            <option value="<?= $value ?>" <?= $selected_hari == $value ? 'selected' : '' ?>><?= $value ?></option>
        <?php
            }
        ?>
    </select>
</div>
<?php
if (count($data_jadwal) > 0) {
?>
    <div class="col-lg-12 mb-3">
<?php
        foreach ($data_jadwal as $key => $value) {
?>
            <input type="radio" name="pilih-mk-abs" value="<?= "DJ_@_".$key ?>" class="btn-check btn-pilih-mk-abs" id="btn-check-outlined-<?=$key?>" autocomplete="off" <?= $selected_idx == "DJ_@_".$key ? 'checked' : '' ?>>
            <label class="btn btn-outline-danger" for="btn-check-outlined-<?=$key?>">MK-<?= $value['id'] ?></label>
<?php
    }
?>
    </div>
    <div class="col-lg-1 d-grid gap-2 mb-3">
        <a href="#jadwal_kuliah" class="btn btn-danger" id="btn-back-abs">Back</a>
    </div>
    <div class="col-lg-12 mb-3">
        <div class="table-responsive">
            <table class="table table-info table-striped">
                <tr>
                    <th>Tanggal</th>
                    <th>
                        <?= !empty($pilih_tanggal) ? $pilih_tanggal : date('Y-m-d') ?>
                    </th>
                </tr>
                <tr>
                    <th>No. MK</th>
                    <td>MK-<?=$data_jadwal[$index_jadwal]['id']?></td>
                    <th>Dosen Yang Masuk</th>
                    <th>Kode</th>
                    <th>Jml.</th>
                </tr>
                <tr>
                    <th>Kode MK</th>
                    <td><?= $data_jadwal[$index_jadwal]['kode_mk'] ?></td>
                    <td>
                        <input type="radio" class="btn-check" name="nip_masuk" id="nip1" autocomplete="off" value="<?= $data_jadwal[$index_jadwal]['nip'].'_@_'.$data_jadwal[$index_jadwal]['id'].'_@_'.$pilih_tanggal ?>" <?= !empty($data_isi_absen_dsn[$pilih_tanggal]) ? ($data_isi_absen_dsn[$pilih_tanggal] == $data_jadwal[$index_jadwal]['nip'] ? 'checked' : '' ): '' ?>>
                        <label class="btn btn-outline-primary" for="nip1"><?= $data_jadwal[$index_jadwal]['pengampu_1'] ?></label>
                    </td>
                    <td><?= $data_jadwal[$index_jadwal]['nip'] ?></td>
                    <td><?= $data_jadwal[$index_jadwal]['jml'] ?></td>
                </tr>
                <tr>
                    <th>Nama MK</th>
                    <td><?= $data_jadwal[$index_jadwal]['nama_mk'] ?></td>
                    <td>
                        <?php if ($data_jadwal[$index_jadwal]['nip2'] != '-'){ ?>
                            <input type="radio" class="btn-check" name="nip_masuk" id="nip2" autocomplete="off" value="<?=$data_jadwal[$index_jadwal]['nip2'].'_@_'.$data_jadwal[$index_jadwal]['id'].'_@_'.$pilih_tanggal ?>"<?= !empty($data_isi_absen_dsn[$pilih_tanggal]) ? ($data_isi_absen_dsn[$pilih_tanggal] == $data_jadwal[$index_jadwal]['nip2'] ? 'checked' : '' ): '' ?>>
                            <label class="btn btn-outline-primary" for="nip2"><?= $data_jadwal[$index_jadwal]['pengampu_2'] ?></label></td>
                        <?php } ?>
                    <td><?= $data_jadwal[$index_jadwal]['nip2'] == '-' ? '' : $data_jadwal[$index_jadwal]['nip2'] ?></td>
                    <td><?= $data_jadwal[$index_jadwal]['nip2'] == '-' ? '' : $data_jadwal[$index_jadwal]['jml2'] ?></td>
                </tr>
                <tr>
                    <th>Kelas</th>
                    <td><?= $data_jadwal[$index_jadwal]['kode_kelas'] ?></td>
                    <td><?php if ($data_jadwal[$index_jadwal]['nip3'] != '-') { ?>
                            <input type="radio" class="btn-check" name="nip_masuk" id="nip3" autocomplete="off" value="<?=$data_jadwal[$index_jadwal]['nip3'].'_@_'.$data_jadwal[$index_jadwal]['id'].'_@_'.$pilih_tanggal ?>" <?=!empty($data_isi_absen_dsn[$pilih_tanggal]) ? ($data_isi_absen_dsn[$pilih_tanggal] == $data_jadwal[$index_jadwal]['nip3'] ? 'checked' : '' ): '' ?>>
                            <label class="btn btn-outline-primary" for="nip3"><?= $data_jadwal[$index_jadwal]['pengampu_3'] ?></label>
                        <?php } ?>
                    </td>
                    <td><?= $data_jadwal[$index_jadwal]['nip3'] == '-' ? '' : $data_jadwal[$index_jadwal]['nip3'] ?></td>
                    <td><?= $data_jadwal[$index_jadwal]['nip3'] == '-' ? '' : $data_jadwal[$index_jadwal]['jml3'] ?></td>
                </tr>
                <tr>
                    <th>Ruang</th>
                    <td><?= $data_jadwal[$index_jadwal]['ruang'] ?></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <th>Hari/Jam</th>
                    <td><?= $data_jadwal[$index_jadwal]['hari'].'/'.$data_jadwal[$index_jadwal]['jam_mulai'].'-'.$data_jadwal[$index_jadwal]['jam_selesai'] ?></td>
                    <td colspan="2">Total Pertemuan</td>
                    <td><?= $data_jadwal[$index_jadwal]['jml']+$data_jadwal[$index_jadwal]['jml2']+$data_jadwal[$index_jadwal]['jml3'] ?></td>
                </tr>
            </table>
        </div>
    </div>
<?php
}
?>
<div class="col-lg-12 mb-3">
    <div class="table-responsive">
        <table id="myTable" class="table table-info table-striped" rekap="1">
            <?php
            $data_tanggal_jadwal = count($data_tanggal_jadwal) > 0 ? $data_tanggal_jadwal : [date('Y-m-d')];
            if (count($data_jadwal) > 0) {
                if (count($data_mhs_ambil_jadwal) > 0) {
            ?>
                <thead style="z-index: ;">
                    <tr>
                        <th rowspan="4" scope="col" style="width: 150px;">NIM</th>
                        <th rowspan="4" scope="col">Nama Mahasiswa</th>
                        <th colspan="<?= count($data_tanggal_jadwal) ?>" class="text-center" scope="colgroup">Pertemuan Ke-</th>
                        <th rowspan="4" scope="col">Action</th>
                    </tr>
                    <tr>
                        <?php
                        foreach ($data_tanggal_jadwal as $key => $value) {
                        ?>
                            <th>
                                <?= $key+1 ?>
                            </th>
                        <?php
                        }
                        ?>
                    </tr>
                    <tr>
                        <?php
                        foreach ($data_tanggal_jadwal as $key => $value) {
                        ?>
                            <th >
                                <div class="dropdown">
                                    <button class="btn <?= $value==$pilih_tanggal ? 'btn-secondary': 'btn-outline-secondary'?> dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"><?= $value ?></button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item <?= $value==$pilih_tanggal ? 'active' : '' ?> select-tanggal" href="#rekap_absensi_@_<?= $value ?>">Select</a></li>
                                        <li><a class="dropdown-item delete-tanggal" href="#rekap_absensi_@_<?= $value ?>"  name="<?= $value ?> MK-<?= $data_jadwal[$index_jadwal]['id'] ?>" table="<?= $title_header ?>" head="Tanggal" param="tanggal;_@_;<?= $value ?>;_@_;id_jadwal;_@_;<?= $data_jadwal[$index_jadwal]['id'] ?>">Delete</a></li>
                                        <li><a class="dropdown-item ubah-tanggal" href="#rekap_absensi_@_<?= $value ?>">Ubah Tanggal:<br><input type="date" id="date_li" class="form-control" value="<?= $value ?>"></a></li>
                                    </ul>
                                </div>
                            </th>
                        <?php
                        }
                        ?>
                    </tr>
                    <tr>
                        <?php
                        foreach ($data_tanggal_jadwal as $key => $value) {
                        ?>
                            <th><?= isset($data_isi_absen_dsn[$value]) ? $data_isi_absen_dsn[$value] : '-' ?></th>
                        <?php
                        }
                        ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($data_mhs_ambil_jadwal as $key => $value) {
                    ?>
                    <tr>
                        <td><?= $value['nim'] ?></td>
                        <td><?= $value['nama_mahasiswa'] ?></td>
                        <?php
                            foreach ($data_tanggal_jadwal as $key2 => $value2) {
                                if ($value2 == $pilih_tanggal) {
                        ?>
                                    <td>
                                        <input type="checkbox" class="btn-check" name="mhs_masuk<?= $key.$key2 ?>" id="mhsk1<?= $key.$key2 ?>" autocomplete="off" value="<?= '1_@_'.$value['id'].'_@_'.$pilih_tanggal ?>" <?=isset($data_isi_absen_mhs[$key][$value2]['keterangan']) ? ($data_isi_absen_mhs[$key][$value2]['keterangan'] == '1' ? 'checked' : '' ): '' ?>>
                                        <label class="btn btn-sm btn-outline-primary mb-1" for="mhsk1<?= $key.$key2 ?>">Hadir</label><br>
                                        <input type="checkbox" class="btn-check" name="mhs_masuk<?= $key.$key2 ?>" id="mhsk2<?= $key.$key2 ?>" autocomplete="off" value="<?= 'i_@_'.$value['id'].'_@_'.$pilih_tanggal ?>" <?=isset($data_isi_absen_mhs[$key][$value2]['keterangan']) ? (strtolower($data_isi_absen_mhs[$key][$value2]['keterangan']) == 'i' ? 'checked' : '' ): '' ?>>
                                        <label class="btn btn-sm btn-outline-warning mb-1" for="mhsk2<?= $key.$key2 ?>">Izin</label><br>
                                        <input type="checkbox" class="btn-check" name="mhs_masuk<?= $key.$key2 ?>" id="mhsk3<?= $key.$key2 ?>" autocomplete="off" value="<?= 's_@_'.$value['id'].'_@_'.$pilih_tanggal ?>" <?=isset($data_isi_absen_mhs[$key][$value2]['keterangan']) ? (strtolower($data_isi_absen_mhs[$key][$value2]['keterangan']) == 's' ? 'checked' : '' ): '' ?>>
                                        <label class="btn btn-sm btn-outline-warning" for="mhsk3<?= $key.$key2 ?>">Sakit</label>
                                    </td>
                        <?php
                                } else{
                        ?>
                                    <td><?= isset($data_isi_absen_mhs[$key][$value2]['keterangan']) ? $data_isi_absen_mhs[$key][$value2]['keterangan'] : '0' ?></td>
                        <?php
                                }
                            }
                        ?>
                        <td>
                            <button type="button" class="btn btn-sm btn-danger" name="<?= $value['nim'] ?> MK-<?= $data_jadwal[$index_jadwal]['id'] ?>" table="<?= $title_header ?>" head="Rekap Absensi Mahasiswa" param="id;_@_;<?= $value['id'] ?>;_@_;nim;_@_;<?= $value['nim'] ?>">Hapus</button>
                        </td>
                    </tr>
                    <?php
                    }
                    ?>
                </tbody>
            <?php
                }else{
            ?>
                <thead></thead>
                <tbody></tbody>
            <?php
                }
            }else{
            ?>
                <thead></thead>
                <tbody></tbody>
            <?php
            }
            ?>
        </table>
    </div>
</div>