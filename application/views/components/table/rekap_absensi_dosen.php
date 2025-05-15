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
<div class="col-lg-3 mb-3">
    <label for="ganti_hari">Pilih Hari</label>
    <select id="ganti_hari" menu="absen" prm_dsn="1" class="form-select">
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
            <input type="radio" name="pilih-mk-abs" prm_dsn="1" value="<?= "DJ_@_".$key ?>" class="btn-check btn-pilih-mk-abs" id="btn-check-outlined-<?=$key?>" autocomplete="off" <?= $selected_idx == "DJ_@_".$key ? 'checked' : '' ?>>
            <label class="btn btn-outline-danger" for="btn-check-outlined-<?=$key?>">MK-<?= $value['id'] ?></label>
<?php
    }
?>
    </div>
    <div class="col-lg-12 mb-3">
        <div class="table-responsive">
            <table class="table table-info table-striped">
                <tr>
                    <th>No. MK</th>
                    <td>MK-<?=$data_jadwal[$index_jadwal]['id']?></td>
                    <th>Dosen</th>
                    <th>Kode</th>
                    <th>Jml.</th>
                </tr>
                <tr>
                    <th>Kode MK</th>
                    <td><?= $data_jadwal[$index_jadwal]['kode_mk'] ?></td>
                    <td><?= $data_jadwal[$index_jadwal]['pengampu_1'] ?></td>
                    <td><?= $data_jadwal[$index_jadwal]['nip'] ?></td>
                    <td><?= $data_jadwal[$index_jadwal]['jml'] ?></td>
                </tr>
                <tr>
                    <th>Nama MK</th>
                    <td><?= $data_jadwal[$index_jadwal]['nama_mk'] ?></td>
                    <td><?= $data_jadwal[$index_jadwal]['nip2'] == '-' ? '' : $data_jadwal[$index_jadwal]['pengampu_2'] ?></td>
                    <td><?= $data_jadwal[$index_jadwal]['nip2'] == '-' ? '' : $data_jadwal[$index_jadwal]['nip2'] ?></td>
                    <td><?= $data_jadwal[$index_jadwal]['nip2'] == '-' ? '' : $data_jadwal[$index_jadwal]['jml2'] ?></td>
                </tr>
                <tr>
                    <th>Kelas</th>
                    <td><?= $data_jadwal[$index_jadwal]['kode_kelas'] ?></td>
                    <td><?= $data_jadwal[$index_jadwal]['nip3'] == '-' ? '' : $data_jadwal[$index_jadwal]['pengampu_3'] ?></td>
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
                        <th scope="col">Pertemuan Ke-</th>
                        <th scope="col">Kode</th>
                        <th scope="col">Dosen Masuk</th>
                        <th scope="col">Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($data_tanggal_jadwal as $key => $value) {
                    ?>
                    <tr>
                        <?php
                        $nip = isset($data_isi_absen_dsn[$value]) ? $data_isi_absen_dsn[$value] : '-';
                        $dosen_masuk = '-';
                        if ($nip == $data_jadwal[$index_jadwal]['nip']) {
                            $dosen_masuk = $data_jadwal[$index_jadwal]['pengampu_1'];
                        }
                        else if ($nip == $data_jadwal[$index_jadwal]['nip2']) {
                            $dosen_masuk = $data_jadwal[$index_jadwal]['pengampu_2'];
                        }
                        else if ($nip == $data_jadwal[$index_jadwal]['nip3']) {
                            $dosen_masuk = $data_jadwal[$index_jadwal]['pengampu_3'];
                        }
                        ?>
                        <th><?= $key+1 ?></th>
                        <th><?= $nip ?></th>
                        <th><?= $dosen_masuk ?></th>
                        <th><?= $value ?></th>
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