<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Dashboard</title>

  <!-- Bootstrap & Icons -->
  <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> -->
   
    <link rel="stylesheet" href="<?= base_url('assets/bootstrap-5.3.3/css/bootstrap.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/select2/css/select.css'); ?>">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    body {
      font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
      overflow-x: hidden;
    }

    .sidebar {
      height: 100vh;
      width: 17%;
      position: fixed;
      top: 0;
      left: 0;
      background-color: #343a40;
      color: #fff;
      padding-top: 1rem;
      transition: transform 0.3s ease, width 0.3s ease;
      z-index: 1000;
    }

    .sidebar.collapsed {
      transform: translateX(-100%);
    }

    .sidebar a {
      color: #adb5bd;
      display: block;
      padding: 0.75rem 1.5rem;
      text-decoration: none;
      transition: background 0.3s ease;
    }

    .sidebar a:hover, .sidebar a.active {
      background-color: #495057;
      color: #fff;
    }

    .content {
      margin-left: 17%;
      padding: 2rem;
      transition: margin-left 0.3s ease;
    }

    .content.expanded {
      margin-left: 0;
    }

    .topbar {
      background-color: whitesmoke;
      padding: 1rem 2rem;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-radius: 8px;
    }
    .kontener {
      background-color: whitesmoke;
      padding: 1rem 2rem;
      margin-top: 25px;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
      border-radius: 8px;
    }
    .animasi-dashboard {
      background-color: whitesmoke;
      animation: shadowPulse 2s ease-in-out infinite;
      display: flex;
      justify-content: space-between; /* content between */
      align-items: center;
      border-radius: 12px; /* rounded corners */
      padding: 16px; /* optional spacing */
      color: #343a40; /* text color agar kontras */
    }

    @keyframes shadowPulse {
      0% {
        box-shadow: 0 0 0 rgba(0, 0, 0, 0);
      }
      50% {
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
      }
      100% {
        box-shadow: 0 0 0 rgba(0, 0, 0, 0);
      }
    }
    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
            position: fixed;
            z-index: 1000;
            width: 250px;
        }

        .sidebar.show {
            transform: translateX(0);
        }

        .content {
            margin-left: 0;
        }
        .topbar{
          direction: rtl;
        }
        .namauser{
          direction: ltr;
        }
    }
    .myloading {
      color: white;
      padding: 1rem;
      text-align: center;
      animation: bgFade 2s infinite alternate;
    }

    @keyframes bgFade {
      from {
        background-color: #000000; /* Hitam */
      }
      to {
        background-color: #888888; /* Abu-abu */
      }
    }

  </style>
</head>
<body>
<svg xmlns="http://www.w3.org/2000/svg" class="d-none">
        <symbol id="check-circle-fill" viewBox="0 0 16 16">
            <path
                d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
        </symbol>
        <symbol id="info-fill" viewBox="0 0 16 16">
            <path
                d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
        </symbol>
        <symbol id="exclamation-triangle-fill" viewBox="0 0 16 16">
            <path
                d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
        </symbol>
    </svg>
    <?php
        view('components/modal/modal_loading');
    ?>
  <!-- Sidebar -->
  <div class="sidebar" id="sidebar">
    <div class="text-center mb-4">
      <img src="<?= base_url('assets/image/icon.png') ?>" alt="Icon" width="80" class="rounded-circle shadow">
      <h5 class="mt-2">Sistem Rekapitulasi Kehadiran</h5>
    </div>
    <a href="#dashboard" id="dashboard"><i class="bi bi-house"></i> Dashboard</a>
    <?php        
      if ($this->session->userdata('level') != 3) {
    ?>
      <a  href="#data_semester" id="data_semester"><i class="bi bi-folder"></i> Data Semester</a>
      <a  href="#jadwal_kuliah" id="jadwal_kuliah"><i class="bi bi-calendar"></i> Jadwal Kuliah</a>
      <a  href="#rekapitulasi_kehadiran" id="rekapitulasi_kehadiran"><i class="bi bi-clipboard"></i> Rekapitulasi Kehadiran</a>
    <?php } ?>
    <a  href="#rekapitulasi_kehadiran_dosen" id="rekapitulasi_kehadiran_dosen"><i class="bi bi-journal-check"></i> Rekapitulasi Kehadiran Dosen</a>
    <?php
      if ($this->session->userdata('level') != 3) {
    ?>
      <a  href="#rekapitulasi_kehadiran_mhs" id="rekapitulasi_kehadiran_mhs"><i class="bi bi-clipboard-check"></i> Rekapitulasi Kehadiran Mahasiswa</a>
      <a  href="#data_dosen" id="data_dosen"><i class="bi bi-person"></i> Data Dosen</a>
      <a  href="#data_mahasiswa" id="data_mahasiswa"><i class="bi bi-people"></i> Data Mahasiswa</a>
      <a  href="#data_mk" id="data_mk"><i class="bi bi-book"></i> Data MK</a>
      <a  href="#data_kelas" id="data_kelas"><i class="bi bi-building"></i> Data Kelas</a>
    <?php } ?>
    <a href="#setting_password" id="setting_password"><i class="bi bi-gear"></i> Ubah Kata Sandi</a>
    <a href="#laporan_aktivitas" id="laporan_aktivitas"><i class="bi bi-clock-history"></i> Laporan Aktivitas</a>
    <a href="<?= base_url('logout') ?>"><i class="bi bi-box-arrow-right"></i> Keluar</a>
  </div>

  <!-- Content -->
  <div class="content expanded" id="mainContent">
    <!-- Topbar -->
    <div class="topbar">
      <button class="btn btn-outline-secondary" onclick="toggleSidebar()">
        <i class="bi bi-list"></i>
      </button>
      <h4 class="mb-0" id="title_header"><?= !empty($title_header) ? $title_header : '' ?></h4>
      <div class="namauser"><img src="<?= base_url('assets/image/user_default.png') ?>" alt="User" height="50px" width="50px"><strong>
        <?= !empty($this->session->userdata('dosen')) ? $this->session->userdata('dosen') : (!empty($this->session->userdata('username')) ? $this->session->userdata('username') : 'user') ?></strong></div>
    </div>

    <!-- Dashboard cards -->
    <!-- <div class="mt-4">
      <div class="row g-4">
        <div class="col-md-4">
          <div class="p-4 bg-primary text-white rounded shadow-sm d-flex justify-content-between align-items-center">
            <div>
              <h5>Total Users</h5>
              <h2>120</h2>
            </div>
            <i class="bi bi-people-fill fs-1 opacity-50"></i>
          </div>
        </div>
        <div class="col-md-4">
          <div class="p-4 bg-success text-white rounded shadow-sm d-flex justify-content-between align-items-center">
            <div>
              <h5>Completed Tasks</h5>
              <h2>85</h2>
            </div>
            <i class="bi bi-check-circle-fill fs-1 opacity-50"></i>
          </div>
        </div>
        <div class="col-md-4">
          <div class="p-4 bg-warning text-dark rounded shadow-sm d-flex justify-content-between align-items-center">
            <div>
              <h5>Pending Tasks</h5>
              <h2>35</h2>
            </div>
            <i class="bi bi-clock-fill fs-1 opacity-75"></i>
          </div>
        </div>
      </div>
    </div> -->

    <div class="mt-4">
        <?php
        view('components/alert');
        ?>
        <div id="mycontent">
        </div>
    </div>

    <!-- User table
    <div class="mt-5">
      <h4 class="mb-3">User List</h4>
      <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
          <thead class="table-dark">
            <tr>
              <th>#</th>
              <th>Name</th>
              <th>Email</th>
              <th>Role</th>
              <th>Registered At</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>1</td>
              <td>Ubaidilah Al Bayu</td>
              <td>ubaidilah@example.com</td>
              <td>Admin</td>
              <td>2025-06-01</td>
              <td>
                <button class="btn btn-sm btn-info"><i class="bi bi-eye"></i></button>
                <button class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></button>
                <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div> -->

  <!-- Script -->
    <script>
        function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const content = document.getElementById('mainContent');
    
        if (window.innerWidth <= 768) {
            sidebar.classList.toggle('show'); // Mobile: slide in/out
        } else {
            sidebar.classList.toggle('collapsed'); // Desktop: collapse/expand
            content.classList.toggle('expanded');
        }
        }
    
        // Auto tampilkan sidebar di desktop saat load
        window.addEventListener('load', () => {
        const sidebar = document.getElementById('sidebar');
        const content = document.getElementById('mainContent');
    
        if (window.innerWidth > 768) {
            sidebar.classList.remove('collapsed');
            content.classList.remove('expanded');
        }
        });
    </script>
    <script src="<?= base_url('assets/bootstrap-5.3.3/js/bootstrap.js') ?>"></script>
<script src='<?= base_url('assets/data-tables/datatables.js') ?>'></script>
<script src='<?= base_url('assets/select2/js/select.min.js') ?>'></script>
<script>
    function base_url(nextUrl = '') {
        let pathparts = location.pathname.split('/');
        let url;
        if (location.hostname === 'localhost') {
            url = location.origin + '/' + pathparts[1].trim('/') + '/' + nextUrl;
        } else {
            url = location.origin + '/' + nextUrl;
        }
        return url;
    }

    function ajaxRequest(link_url, data = {}, type = 'get', callback, showLoading = true) {
        if (showLoading) {
            $("#loadingModal").modal('show'); // Tampilkan loading modal jika showLoading true
        }

        $.ajax({
            type: type,
            url: base_url(link_url),
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            data: data,
            success: function(response) {
                // Panggil fungsi callback dengan response
                callback(response);
            },
            complete: function() {
                if (showLoading) {
                    $("#loadingModal").modal('hide'); // Sembunyikan loading modal jika showLoading true
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error: ", status, error);
                callback(null); // Panggil callback dengan null pada error
            },
        });
    }

    function changeActive(menu_aktif) {
        // Remove 'active' and add 'mybtn-1' to all buttons
        $("#dashboard").removeClass("active");
        $("#rekapitulasi_kehadiran").removeClass("active");
        $("#rekapitulasi_kehadiran_dosen").removeClass("active");
        $("#rekapitulasi_kehadiran_mhs").removeClass("active");
        $("#data_mk").removeClass("active");
        $("#data_mahasiswa").removeClass("active");
        $("#data_dosen").removeClass("active");
        $("#data_kelas").removeClass("active");
        $("#data_semester").removeClass("active");
        $("#jadwal_kuliah").removeClass("active");
        $("#setting_password").removeClass("active");
        $("#laporan_aktivitas").removeClass("active");

        // Change the active menu item
        $("#" + menu_aktif).addClass("active");
    }

    function appendContentMenu(nama_content, data_tambahan = {}) {
        let dataMenu = {
            nama_content: nama_content
        };
        let dataSend = Object.assign({}, dataMenu, data_tambahan)
        ajaxRequest('menu', dataSend, 'post', function(data) {
            if (data) {
                if (data.status) {
                    changeActive(nama_content);
                    $("#mycontent").html(data.html);
                    $("#title_header").html(data.data.title_header);
                } else {
                    alert("Error !! : " + data.message)
                }
            } else {
                alert("No data received or an error occurred.");
            }
        });
    }

    function updateAbsensi(dataSend)
    {
        ajaxRequest('ngisi_absen', dataSend, 'post', function(data) {
            if (data) {
                if (data.status) {
                    // alert(data.message);
                    let data_tambahan = {
                        param_smt: data.data.param_smt,
                        param_hr: data.data.param_hr,
                        param_idx_jdw: data.data.param_idx_jdw,
                        param_tgl: data.data.param_tgl,
                    };
                    appendContentMenu('rekapitulasi_kehadiran', data_tambahan);
                }else{
                    alert(data.message);
                }
            } else {
                alert("No data received or an error occurred.");
            }
        }, false);
    }
    $(document).ready(function () {
        $("#form-login").on("click", ".btn-light", function () {
            let link_img = $("#icon-pass").attr("src");
            link_img = link_img.split("/");
            if ($("#password").attr("type") == "password") {
                link_img[link_img.length-1] = "mata_buka.png";
                link_img = link_img.join("/");
                $("#icon-pass").attr("src", link_img);
                $("#password").attr("type", "text");
            }else{
                link_img[link_img.length-1] = "mata_tutup.png";
                link_img = link_img.join("/");
                $("#icon-pass").attr("src", link_img);
                $("#password").attr("type", "password");
            }
        });
    });
</script>
<?php
$menu_now = !empty($this->session->flashdata('menu_now')) ? $this->session->flashdata('menu_now') : 'dashboard';

$script = [
    "
<script type='text/javascript'>
$(document).ready(function() {
    appendContentMenu('" . $menu_now . "');
    $('#dashboard').on('click', function() {
        appendContentMenu('dashboard');
    });
    $('#rekapitulasi_kehadiran').on('click', function() {
        appendContentMenu('rekapitulasi_kehadiran');
    });
    $('#rekapitulasi_kehadiran_dosen').on('click', function() {
        appendContentMenu('rekapitulasi_kehadiran_dosen');
    });
    $('#rekapitulasi_kehadiran_mhs').on('click', function() {
        appendContentMenu('rekapitulasi_kehadiran_mhs');
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
    $('#setting_password').on('click', function() {
        appendContentMenu('setting_password');
    });
    $('#laporan_aktivitas').on('click', function() {
        appendContentMenu('laporan_aktivitas');
    });
});
</script>"
];
if (!empty($this->session->flashdata('alert'))) {
?>
<script type="text/javascript">
    $(document).ready(function() {
        window.setTimeout(function() {
            $(".alert").fadeTo(1000, 0).slideUp(1000, function() {
                $(this).remove();
            });
        }, 5000);
    });
</script>
<?php
}
if (!empty($script)) {
    foreach ($script as $key => $item) {
        echo $item;
    }
}
?>
</body>
</html>