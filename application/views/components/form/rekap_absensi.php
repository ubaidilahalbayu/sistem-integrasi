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
<div class="row justify-content-start" id="pilihan_rekap_2" <?= !empty($this->session->flashdata('selected_rekap')) ? ($this->session->flashdata('selected_rekap') == 1 ? 'style="display: none"' : '') : '' ?>>
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
    <div class="col-lg-6 mb-3" id="pilihan_rekap_dosen" <?= !empty($this->session->flashdata('selected_rekap')) ? ($this->session->flashdata('selected_rekap') != 2 ? 'style="display: none"' : '') : '' ?>>
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
    <div class="col-lg-6 mb-3" id="pilihan_rekap_mhs" <?= !empty($this->session->flashdata('selected_rekap')) ? ($this->session->flashdata('selected_rekap') != 3 ? 'style="display: none"' : '') : '' ?>>
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
            <option value="Hadir">Hadir</option>
            <option value="Izin">Izin</option>
            <option value="Sakit">Sakit</option>
        </select>
    </div>
</div>
<?php
}else{
?>
<div class="col-lg-6 mb-3">
    <label for="filter_jadwal_kuliah">Filter Jadwal Kuliah</label>
    <select class="form-select" id="filter_jadwal_kuliah" name="<?= !empty($export) ? 'absensi_@_' : '' ?>id_jadwal">
            <option value="all">Semua</option>
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
    <label for="kode_mk">Filter Mata Kuliah</label>
    <select class="form-select" id="kode_mk" name="<?= !empty($export) ? 'data_mk_@_' : '' ?>kode_mk">
            <option value="all">Semua</option>
            <?php
            foreach ($mk as $key => $value) {
            ?>
                <option value="<?= $value['kode_mk'] ?>"><?= $value['nama_mk'] ?> Semester <?= $value['semester'] ?> <?= $value['sks'] ?> SKS</option>
            <?php
            }
            ?>
    </select>
</div>
<div class="col-lg-6 mb-3">
    <label for="type_absen">Filter Opsi</label>
    <select class="form-select" id="type_absen" name="type_absen">
            <option value="mhs">Mahasiswa</option>
            <!-- <option value="dsn">Dosen</option> -->
    </select>
</div>
<?php
}
?>