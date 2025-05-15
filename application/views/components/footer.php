<script src="<?= base_url('assets/bootstrap-5.3.3/js/bootstrap.js') ?>"></script>
<script src='<?= base_url('assets/data-tables/datatables.js') ?>'></script>
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
        // Remove 'mybtn-2' and add 'mybtn-1' to all buttons
        $("#dashboard").removeClass("mybtn-2").addClass("mybtn-1");
        $("#rekap_absensi").removeClass("mybtn-2").addClass("mybtn-1");
        $("#rekap_absensi_dosen").removeClass("mybtn-2").addClass("mybtn-1");
        $("#rekap_absensi_mhs").removeClass("mybtn-2").addClass("mybtn-1");
        $("#data_mk").removeClass("mybtn-2").addClass("mybtn-1");
        $("#data_mahasiswa").removeClass("mybtn-2").addClass("mybtn-1");
        $("#data_dosen").removeClass("mybtn-2").addClass("mybtn-1");
        $("#data_kelas").removeClass("mybtn-2").addClass("mybtn-1");
        $("#data_semester").removeClass("mybtn-2").addClass("mybtn-1");
        $("#jadwal_kuliah").removeClass("mybtn-2").addClass("mybtn-1");

        // Change the active menu item
        $("#" + menu_aktif).removeClass("mybtn-1").addClass("mybtn-2");
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
                    appendContentMenu('rekap_absensi', data_tambahan);
                }else{
                    alert(data.message);
                }
            } else {
                alert("No data received or an error occurred.");
            }
        }, false);
    }
</script>
<?php
if (!empty($this->session->flashdata('alert'))) {
    $script[] = '<script type="text/javascript">
        $(document).ready(function() {
            window.setTimeout(function() {
                $(".alert").fadeTo(1000, 0).slideUp(1000, function() {
                    $(this).remove();
                });
            }, 5000);

        });
    </script>';
}
if (!empty($script)) {
    foreach ($script as $key => $item) {
        echo $item;
    }
}
?>
</body>

</html>