<?php
if (empty($export)) {
?>
<div class="row justify-content-start">
    <div class="col-lg-12 mb-3 text-center">
        <label for=""><b>Tambah Mahasiswa Untuk Kode Jadwal MK-<?= $data_jadwal[$index_jadwal]['id'] ?>:</b></label>
        <input type="hidden" name="id_jadwal" id="id_jadwal" value="<?= $data_jadwal[$index_jadwal]['id'] ?>">
    </div>
    <div class="col-lg-12 mb-3">
        <label for="nim">Pilih Mahasiswa</label>
        <select class="form-select js-select-jq" id="nim" name="nim">
            <?php
            foreach ($mhs as $key => $value) {
            ?>
                <option value="<?= $value['nim'] ?>"><?= $value['nama_mahasiswa'] ?> ( <?= $value['nim'] ?> )</option>
            <?php
            }
            ?>
        </select>
    </div>
</div>
<?php
}else{
?>
<div class="col-lg-6 mb-3">
    <label for="filter_semester">Filter Semester</label>
    <select class="form-select" id="filter_semester" name="<?= !empty($export) ? 'jadwal_kuliah_@_' : '' ?>semester_char">
        <?php
            foreach ($smt as $key => $value) {
        ?>
            <option value="<?= $value['tahun_1'].$value['tahun_2'].$value['semester'] ?>" <?= $selected_smt == $value['tahun_1'].$value['tahun_2'].$value['semester'] ? 'selected' : '' ?>>Semester <?= $value['semester'] == 1 ? 'Ganjil' : 'Genap' ?> <?= $value['tahun_1']."/".$value['tahun_2'] ?></option>
        <?php
            }
        ?>
    </select>
</div>
<?php
}
?>