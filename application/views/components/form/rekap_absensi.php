<?php
if (empty($export)) {
?>
<div class="row justify-content-start">
    <div class="col-lg-3 mb-3">
        <label for="">Opsi Rekap Absensi:</label>
    </div>
    <div class="col-lg-6 mb-3">
        <input type="radio" class="btn-check check_pilihan_rekap" name="pilihan_rekap" id="option1" autocomplete="off" <?= !empty($this->session->flashdata('selected_rekap')) ? ($this->session->flashdata('selected_rekap') == 1 ? 'checked' : (!empty($edit) ? 'disabled' : '')) : (!empty($edit) ? 'disabled' : '') ?> value="1">
        <label class="btn btn-outline-primary" for="option1">New Absen</label>
        <input type="radio" class="btn-check check_pilihan_rekap" name="pilihan_rekap" id="option2" autocomplete="off" value="2" <?= !empty($this->session->flashdata('selected_rekap')) ? ($this->session->flashdata('selected_rekap') == 2 ? 'checked' : (!empty($edit) ? 'disabled' : '')) : (!empty($edit) ? 'disabled' : '') ?>>
        <label class="btn btn-outline-primary" for="option2">Dosen</label>
        <input type="radio" class="btn-check check_pilihan_rekap" name="pilihan_rekap" id="option3" autocomplete="off" value="3" <?= !empty($this->session->flashdata('selected_rekap')) ? ($this->session->flashdata('selected_rekap') == 3 ? 'checked' : (!empty($edit) ? 'disabled' : '')) : (!empty($edit) ? 'disabled' : '') ?>>
        <label class="btn btn-outline-primary" for="option3">Mahasiswa</label>
    </div>
</div>
<div class="row justify-content-start" id="pilihan_rekap_1" <?= !empty($this->session->flashdata('selected_rekap')) ? ($this->session->flashdata('selected_rekap') != 1 ? 'style="display: none"' : '') : '' ?>>
    <div class="col-lg-6 mb-3">
        <label for="id_jadwal">Jadwal Kuliah</label>
        <select class="form-select" id="id_jadwal" name="id_jadwal">
            <?php
            foreach ($jdw as $key => $value) {
            ?>
                <option value="<?= $value['id'] ?>"><?= $value['nama_mk'] ?>-<?= $value['semester'] ?>-<?= $value['kode_kelas'] ?>-<?= $value['hari'] ?></option>
            <?php
            }
            ?>
        </select>
    </div>
    <div class="col-lg-6 mb-3">
        <label for="tanggal">Tanggal</label>
        <input type="date" name="tanggal" id="tanggal" class="form-control">
    </div>
</div>
<div class="row justify-content-start" id="pilihan_rekap_2" <?= !empty($this->session->flashdata('selected_rekap')) ? ($this->session->flashdata('selected_rekap') != 2 ? 'style="display: none"' : '') : '' ?>>
    <div class="col-lg-6 mb-3">
        <label for="id_jadwal">Pilih Jadwal Kuliah</label>
        <select class="form-select" id="id_jadwal" name="id_jadwal">
            <?php
            foreach ($jdw as $key => $value) {
            ?>
                <option value="<?= $value['id'] ?>"><?= $value['nama_mk'] ?>-<?= $value['semester'] ?>-<?= $value['kode_kelas'] ?>-<?= $value['hari'] ?></option>
            <?php
            }
            ?>
        </select>
    </div>
    <div class="col-lg-6 mb-3">
        <label for="nip">Dosen</label>
        <select class="form-select" id="nip" name="nip">
            <?php
            foreach ($dsn as $key => $value) {
            ?>
                <option value="<?= $value['nip'] ?>"><?= $value['nama_gelar_depan'] ?> <?= $value['nama_dosen'] ?>, <?= $value['nama_gelar_belakang'] ?></option>
            <?php
            }
            ?>
        </select>
    </div>
    <div class="col-lg-6 mb-3">
        <label for="jumlah_hadir">Jumlah Hadir</label>
        <input type="number" name="jumlah_hadir" id="jumlah_hadir" value="1" class="form-control">
    </div>
</div>
<div class="row justify-content-start" id="pilihan_rekap_3" <?= !empty($this->session->flashdata('selected_rekap')) ? ($this->session->flashdata('selected_rekap') != 3 ? 'style="display: none"' : '') : '' ?>>
    <div class="col-lg-6 mb-3">
        <label for="id_absen">Pilih Absen</label>
        <select class="form-select" id="id_absen" name="id_absen">
            <?php
            foreach ($abs as $key => $value) {
            ?>
                <option value="<?= $value['id'] ?>"><?= $value['nama_mk'] ?>-<?= $value['kode_kelas'] ?>-<?= $value['tanggal'] ?>-<?= $value['jam_mulai'] ?>-<?= $value['jam_selesai'] ?></option>
            <?php
            }
            ?>
        </select>
    </div>
    <div class="col-lg-6 mb-3">
        <label for="nim">Mahasiswa</label>
        <select class="form-select" id="nim" name="nim">
            <?php
            foreach ($mhs as $key => $value) {
            ?>
                <option value="<?= $value['nim'] ?>"><?= $value['nama_mahasiswa'] ?> ( <?= $value['nim'] ?> )</option>
            <?php
            }
            ?>
        </select>
    </div>
    <div class="col-lg-6 mb-3">
        <label for="keterangan">Keterangan</label>
        <select class="form-select" id="keterangan" name="keterangan">
            <option value="1">Hadir</option>
            <option value="I">Izin</option>
            <option value="S">Sakit</option>
            <option value="0">Tidak Hadir</option>
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