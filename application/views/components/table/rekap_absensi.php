<?php
if (count($data_jadwal) > 0) {
    if (count($data_mhs_ambil_jadwal) > 0) {
?>
    <thead style="z-index: ;">
        <tr>
            <th rowspan="3">NIM</th>
            <th rowspan="3">Nama Mahasiswa</th>
            <th colspan="<?= count($data_tanggal_jadwal) ?>" class="text-center">Tanggal</th>
            <?php
            if (count($data_tanggal_jadwal) >= 16) {
            ?>
                <th rowspan="3"><button class="btn btn-sm btn-success" type="button">Tambah</button></th>
            <?php
            }
            ?>
        </tr>
        </tr>
        <tr>
            <?php
            foreach ($data_tanggal_jadwal as $key => $value) {
            ?>
                <th><?= $value ?></th>
            <?php
            }
            ?>
        </tr>
        <tr>
            <?php
            foreach ($data_isi_absen_dsn as $key => $value) {
            ?>
                <th><?= $value ?></th>
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
                foreach ($data_isi_absen_mhs[$key] as $key2 => $value2) {
            ?>
                <td><?= $value2['keterangan'] ?></td>
            <?php
            }
            ?>
            <td></td>
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