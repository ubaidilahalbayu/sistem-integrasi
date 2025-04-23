<div class="col-lg-6 mb-3">
    <label for="kode_mk"><?= !empty($export) ? 'Filter ' : '' ?>Mata Kuliah</label>
    <select class="form-select" id="kode_mk" name="<?= !empty($export) ? 'jadwal_kuliah_@_' : '' ?>kode_mk">
        <?php
        if (!empty($export)) {
        ?>
            <option value="all">Semua</option>
        <?php
        }
        foreach ($mk as $key => $value) {
        ?>
            <option value="<?= $value['kode_mk'] ?>"><?= $value['nama_mk'] ?> Semester <?= $value['semester'] ?> <?= $value['semester'] ?> SKS</option>
        <?php
        }
        ?>
    </select>
</div>
<div class="col-lg-6 mb-3">
    <label for="kode_kelas"><?= !empty($export) ? 'Filter ' : '' ?>Kelas</label>
    <select class="form-select" id="kode_kelas" name="<?= !empty($export) ? 'jadwal_kuliah_@_' : '' ?>kode_kelas">
        <?php
        if (!empty($export)) {
        ?>
            <option value="all">Semua</option>
        <?php
        }
        foreach ($kls as $key => $value) {
        ?>
            <option value="<?= $value['kode_kelas'] ?>"><?= $value['nama_kelas'] ?></option>
        <?php
        }
        ?>
    </select>
</div>
<div class="col-lg-6 mb-3">
    <label for="nip"><?= !empty($export) ? 'Filter ' : '' ?>Dosen Pengampu 1</label>
    <select class="form-select" id="nip" name="<?= !empty($export) ? 'jadwal_kuliah_@_' : '' ?>nip">
        <?php
        if (!empty($export)) {
        ?>
            <option value="all">Semua</option>
        <?php
        }
        foreach ($dsn as $key => $value) {
        ?>
            <option value="<?= $value['nip'] ?>"><?= $value['nama_gelar_depan'] ?> <?= $value['nama_dosen'] ?>, <?= $value['nama_gelar_belakang'] ?></option>
        <?php
        }
        ?>
    </select>
</div>
<div class="col-lg-6 mb-3">
    <label for="nip2"><?= !empty($export) ? 'Filter ' : '' ?>Dosen Pengampu 2</label>
    <select class="form-select" id="nip2" name="<?= !empty($export) ? 'jadwal_kuliah_@_' : '' ?>nip2">
        <?php
        if (!empty($export)) {
        ?>
            <option value="all">Semua</option>
        <?php
        }
        foreach ($dsn as $key => $value) {
        ?>
            <option value="<?= $value['nip'] ?>"><?= $value['nama_gelar_depan'] ?> <?= $value['nama_dosen'] ?>, <?= $value['nama_gelar_belakang'] ?></option>
        <?php
        }
        ?>
    </select>
</div>
<div class="col-lg-6 mb-3">
    <label for="nip3"><?= !empty($export) ? 'Filter ' : '' ?>Dosen Pengampu 3</label>
    <select class="form-select" id="nip3" name="<?= !empty($export) ? 'jadwal_kuliah_@_' : '' ?>nip3">
        <?php
        if (!empty($export)) {
        ?>
            <option value="all">Semua</option>
        <?php
        }
        foreach ($dsn as $key => $value) {
        ?>
            <option value="<?= $value['nip'] ?>"><?= $value['nama_gelar_depan'] ?> <?= $value['nama_dosen'] ?>, <?= $value['nama_gelar_belakang'] ?></option>
        <?php
        }
        ?>
    </select>
</div>
<div class="col-lg-6 mb-3">
    <label for="hari"><?= !empty($export) ? 'Filter ' : '' ?>Hari</label>
    <select class="form-select" id="hari" name="<?= !empty($export) ? 'jadwal_kuliah_@_' : '' ?>hari">
        <?php
        if (!empty($export)) {
        ?>
            <option value="all">Semua</option>
        <?php
        }
        ?>
        <option value="Senin">Senin</option>
        <option value="Selasa">Selasa</option>
        <option value="Rabu">Rabu</option>
        <option value="Kamis">Kamis</option>
        <option value="Jumat">Jumat</option>
        <option value="Sabtu">Sabtu</option>
    </select>
</div>
<?php
if (empty($export)) {
?>
    <div class="col-lg-6 mb-3">
        <label for="jam_mulai">Jam Mulai</label>
        <input type="time" id="jam_mulai" name="jam_mulai" class="form-control">
    </div>
    <div class="col-lg-6 mb-3">
        <label for="jam_selesai">Jam Selesai</label>
        <input type="time" id="jam_selesai" name="jam_selesai" class="form-control">
    </div>
<?php
}
?>