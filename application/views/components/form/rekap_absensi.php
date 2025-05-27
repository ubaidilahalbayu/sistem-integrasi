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
    <select class="form-select" id="filter_semester" name="<?= !empty($export) ? 'data_mk_@_' : '' ?>semester">
            <option value="all">Semua</option>
            <option value="1">1</option>
            <option value="2">2</option>
    </select>
</div>
<div class="col-lg-6 mb-3">
    <label for="filter_tahun_1">Filter Tahun Akademik 1</label>
    <select class="form-select" id="filter_tahun_1" name="<?= !empty($export) ? 'data_semester_@_' : '' ?>tahun_1">
            <option value="all">Semua</option>
            <?php
            for ($year = 2020; $year <= date('Y'); $year++) {
                echo '<option value="'.$year.'">' . $year . '</option>';
            }
            ?>
    </select>
</div>
<div class="col-lg-6 mb-3">
    <label for="filter_tahun_2">Filter Tahun Akademik 2</label>
    <select class="form-select" id="filter_tahun_2" name="<?= !empty($export) ? 'data_semester_@_' : '' ?>tahun_2">
            <option value="all">Semua</option>
            <?php
            for ($year = 2020; $year <= date('Y'); $year++) {
                echo '<option value="'.$year.'">' . $year . '</option>';
            }
            ?>
    </select>
</div>
<?php
}
?>