<div class="row justify-content-start" id="pilihan_rekap_1">
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
<div class="row justify-content-start" id="pilihan_rekap_2" style="display: none;">
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
    <div class="col-lg-6 mb-3" id="pilihan_rekap_dosen">
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
    <div class="col-lg-6 mb-3" id="pilihan_rekap_mhs">
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