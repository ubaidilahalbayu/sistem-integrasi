<div class="col-lg-12 mb-3">
</div>
<div class="col-lg-3 mb-3">
    <label for="ganti_semester">Pilih Semester</label>
    <select id="ganti_semester" menu="absen" prm_mhs="1" class="form-select">
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
    <select id="ganti_hari" menu="absen" prm_mhs="1" class="form-select">
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
            <input type="radio" name="pilih-mk-abs" prm_mhs="1" value="<?= "DJ_@_".$key ?>" class="btn-check btn-pilih-mk-abs" id="btn-check-outlined-<?=$key?>" autocomplete="off" <?= $selected_idx == "DJ_@_".$key ? 'checked' : '' ?>>
            <label class="btn btn-outline-danger" for="btn-check-outlined-<?=$key?>">MK-<?= $value['id'] ?></label>
<?php
    }
?>
    </div>
    <div class="col-lg-6 mb-3">
        <div class="table-responsive">
            <table class="table table-info table-striped">
                    <tr>
                        <th>No. MK</th>
                        <td>MK-<?=$data_jadwal[$index_jadwal]['id']?></td>
                    </tr>
                    <tr>
                        <th>Kode MK</th>
                        <td><?= $data_jadwal[$index_jadwal]['kode_mk'] ?></td>
                    </tr>
                    <tr>
                        <th>Nama MK</th>
                        <td><?= $data_jadwal[$index_jadwal]['nama_mk'] ?></td>
                    </tr>
                    <tr>
                        <th>Kelas</th>
                        <td><?= $data_jadwal[$index_jadwal]['kode_kelas'] ?></td>
                    </tr>
                    <tr>
                        <th>Ruang</th>
                        <td><?= $data_jadwal[$index_jadwal]['ruang'] ?></td>
                    </tr>
                    <tr>
                        <th>Hari/Jam</th>
                        <td><?= $data_jadwal[$index_jadwal]['hari'].'/'.$data_jadwal[$index_jadwal]['jam_mulai'].'-'.$data_jadwal[$index_jadwal]['jam_selesai'] ?></td>
                    </tr>
            </table>
        </div>
    </div>
<?php
}
?>
<div class="col-lg-12 mb-3">
    <hr>
</div>
<div class="col-lg-6 mb-3">
    <b>Keterangan*</b>
    <div class="row justyfy-content-end">
        <div class="col-lg-1">
            0 :
        </div>
        <div class="col-lg-3 bg-danger">
            Tidak Hadir
        </div>
    </div>
    <div class="row justyfy-content-start">
        <div class="col-lg-1">
            1 :
        </div>
        <div class="col-lg-3 bg-success">
            Hadir
        </div>
    </div>
    <div class="row justyfy-content-start">
        <div class="col-lg-1">
            2 :
        </div>
        <div class="col-lg-3 bg-warning">
            Izin / Sakit
        </div>
    </div>
    <div class="row justyfy-content-start">
        <div class="col-lg-1">
            - :
        </div>
        <div class="col-lg-3">
            Belum Diisi
        </div>
    </div>
</div>
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
                        <th rowspan="3" scope="col" style="width: 150px;">NIM</th>
                        <th rowspan="3" scope="col">Nama Mahasiswa</th>
                        <th class="text-center" colspan="<?= count($data_tanggal_jadwal) ?>" scope="colgroup">Pertemuan Ke-</th>
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
                            <th ><?= $value ?></th>
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
                        ?>
                                <td class="<?= isset($data_isi_absen_mhs[$key][$value2]['keterangan']) ? ($data_isi_absen_mhs[$key][$value2]['keterangan'] == "1" ? "bg-success" : ($data_isi_absen_mhs[$key][$value2]['keterangan'] == "2" ? "bg-warning" : ($data_isi_absen_mhs[$key][$value2]['keterangan'] == "0" ? "bg-danger" : ""))) : '' ?>"><?= isset($data_isi_absen_mhs[$key][$value2]['keterangan']) ? $data_isi_absen_mhs[$key][$value2]['keterangan'] : '-' ?></td>
                        <?php
                            }
                        ?>
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