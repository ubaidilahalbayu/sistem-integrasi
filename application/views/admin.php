<?php
$data_header['css'] = ["<link rel='stylesheet' href='" . base_url('assets/data-tables/datatables.css') . "'>"];
view('components/header', $data_header);
?>

<nav class="navbar navbar-expand-lg fixed-top mycolor-1">
    <!-- <a class="navbar-brand" href="#">Brand</a> -->
    <!-- <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button> -->
    <!-- <div class="container row"> -->
    <div class="col-lg-10 d-grid gap-3">
        <h4 class="text-center" id="title_header"><?= !empty($title_header) ? $title_header : '' ?></h4>
    </div>
    <div class="col-lg-2 d-grid gap-3">
        <ul class="navbar-nav">
            <li class="nav-item">
                <div class="text-center">
                    <img src="<?= base_url('assets/image/user_default.png') ?>" alt="User" height="50px" width="50px">
                    <div>
                        <?= !empty($this->session->userdata('dosen')) ? $this->session->userdata('dosen') : (!empty($this->session->userdata('username')) ? $this->session->userdata('username') : 'user') ?>
                    </div>
                </div>
            </li>
        </ul>
    </div>
    <!-- </div> -->
</nav>

<div class="d-flex">
    <!-- Adjust margin-top to match navbar height -->
    <div class="mysidebar mycolor-1">
        <p class="p-2 py-3">
            <img src="<?= base_url('assets/image/icon.png') ?>" alt="Icon" height="60px" width="60px">
            Sistem Rekap Absensi
        </p>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link mybtn-1" href="#dashboard" id="dashboard">Dashboard</a>
            </li>
            <?php        
    		if ($this->session->userdata('level') != 3) {
            ?>
                <li class="nav-item">
                    <a class="nav-link mybtn-1" href="#data_semester" id="data_semester">Data Semester</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link mybtn-1" href="#jadwal_kuliah" id="jadwal_kuliah">Jadwal Kuliah</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link mybtn-1" href="#rekap_absensi" id="rekap_absensi">Rekap Absensi</a>
                </li>
            <?php
            }
            ?>
            <li class="nav-item">
                <a class="nav-link mybtn-1" href="#rekap_absensi_dosen" id="rekap_absensi_dosen">Rekap Absensi Dosen</a>
            </li>
            <?php
		    if ($this->session->userdata('level') != 3) {
            ?>
                <li class="nav-item">
                    <a class="nav-link mybtn-1" href="#rekap_absensi_mhs" id="rekap_absensi_mhs">Rekap Absensi Mahasiswa</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link mybtn-1" href="#data_dosen" id="data_dosen">Data Dosen</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link mybtn-1" href="#data_mahasiswa" id="data_mahasiswa">Data Mahasiswa</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link mybtn-1" href="#data_mk" id="data_mk">Data MK</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link mybtn-1" href="#data_kelas" id="data_kelas">Data Kelas</a>
                </li>
            <?php
            }
            ?>
            <li class="nav-item">
                <a class="nav-link mybtn-1" href="<?= base_url('logout') ?>">Logout</a>
            </li>
        </ul>
        <?php
        view('components/copyright');
        ?>
    </div>
    <div class="mycontent">
        <?php
        view('components/alert');
        ?>
        <div id="mycontent">
        </div>
    </div>
</div>
<?php
$menu_now = !empty($this->session->flashdata('menu_now')) ? $this->session->flashdata('menu_now') : 'dashboard';
$selected_rekap = !empty($this->session->flashdata('selected_rekap')) ? $this->session->flashdata('selected_rekap') : 0;

$data_footer['script'] = [
    "
<script type='text/javascript'>
$(document).ready(function() {
    appendContentMenu('" . $menu_now . "');
    $('#dashboard').on('click', function() {
        appendContentMenu('dashboard');
    });
    $('#rekap_absensi').on('click', function() {
        appendContentMenu('rekap_absensi');
    });
    $('#rekap_absensi_dosen').on('click', function() {
        appendContentMenu('rekap_absensi_dosen');
    });
    $('#rekap_absensi_mhs').on('click', function() {
        appendContentMenu('rekap_absensi_mhs');
    });
    $('#data_mk').on('click', function() {
        appendContentMenu('data_mk');
    });
    $('#data_mahasiswa').on('click', function() {
        appendContentMenu('data_mahasiswa');
    });
    $('#data_dosen').on('click', function() {
        appendContentMenu('data_dosen');
    });
    $('#data_kelas').on('click', function() {
        appendContentMenu('data_kelas');
    });
    $('#data_semester').on('click', function() {
        appendContentMenu('data_semester');
    });
    $('#jadwal_kuliah').on('click', function() {
        appendContentMenu('jadwal_kuliah');
    });
});
</script>"
];
view('components/footer', $data_footer);
?>