<div class="col-lg-6 mb-3">
    <label for="kode_mk">Mata Kuliah</label>
    <select class="form-select" id="kode_mk" name="kode_mk">
        <?php
        foreach ($mk as $key => $value) {
        ?>
            <option value="<?= $value['kode_mk'] ?>"><?= $value['nama_mk'] ?> Semester <?= $value['semester'] ?> <?= $value['semester'] ?> SKS</option>
        <?php
        }
        ?>
    </select>
</div>
<div class="col-lg-6 mb-3">
    <label for="kode_kelas">Kelas</label>
    <select class="form-select" id="kode_kelas" name="kode_kelas">
        <?php
        foreach ($kls as $key => $value) {
        ?>
            <option value="<?= $value['kode_kelas'] ?>"><?= $value['nama_kelas'] ?></option>
        <?php
        }
        ?>
    </select>
</div>
<div class="col-lg-6 mb-3">
    <label for="nip">Dosen Pengampu</label>
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
    <label for="hari">Hari</label>
    <select class="form-select" id="hari" name="hari">
        <option value="Senin">Senin</option>
        <option value="Selasa">Selasa</option>
        <option value="Rabu">Rabu</option>
        <option value="Kamis">Kamis</option>
        <option value="Jumat">Jumat</option>
        <option value="Sabtu">Sabtu</option>
    </select>
</div>
<div class="col-lg-6 mb-3">
    <label for="jam_mulai">Jam Mulai</label>
    <input type="time" id="jam_mulai" name="jam_mulai" class="form-control">
</div>
<div class="col-lg-6 mb-3">
    <label for="jam_selesai">Jam Selesai</label>
    <input type="time" id="jam_selesai" name="jam_selesai" class="form-control">
</div>