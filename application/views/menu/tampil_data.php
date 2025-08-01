<?php
view('components/modal/modal_import');
view('components/modal/modal_export');
view('components/modal/modal_form');
view('components/modal/modal_confirm');
// view('components/alert');
if ($title_header == "Rekapitulasi Kehadiran") {
    if (count($data_jadwal) > 0) {
        $paramTambah['export'] = 0;
?>
<div class="modal fade" id="modalTambahMhs" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Tambah Mahasiswa</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="tambah_mhs_form" action="<?= base_url('proses') ?>" method="post">
                    <input type="hidden" name="menu" value="<?= str_replace(' ', '_', strtolower($title_header)) ?>">
                    <?php 
                        view('components/form/rekap_absensi', $paramTambah); 
                    ?>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="tambah_mhs_submit">Submit</button>
            </div>
        </div>
    </div>
</div>
<?php
    }
}
?>
<div class="row justify-content-start mt-5">
    <?php if ($title_header != "Rekapitulasi Kehadiran Dosen" && $title_header != "Rekapitulasi Kehadiran Mahasiswa" && $title_header != "Laporan Aktivitas") {
    if ($title_header == "Rekapitulasi Kehadiran") {
    ?>
    <div class="col-lg-2 d-grid gap-2 mb-3">
        <button type="button" class="btn btn-success" data-bs-toggle="modal"
            data-bs-target="#modalExport">Export</button>
    </div>
    <?php } ?>
    <div class="col-lg-2 d-grid gap-2 mb-3">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
            data-bs-target="#modalImport"><?= $title_header == "Rekapitulasi Kehadiran" ? 'Import' : 'Tambah Data' ?></button>
    </div>
    <?php
		if ($this->session->userdata('level') == 1 && $title_header == "Data Semester") {
    ?>
        <div class="col-lg-2 d-grid gap-2 mb-3">
            <button type="button" class="btn btn-danger" id="hapusSemua">Delete All</button>
        </div>
    <?php }
    } ?>
    <?php
    if ($title_header == "Jadwal Kuliah") {
    ?>
        <div class="col-lg-12 mb-3">
            <h5 class="text-center"><?=$semester_print?></h5>
        </div>
        <div class="col-lg-3 mb-3">
            <label for="ganti_semester">Pilih Semester</label>
            <select id="ganti_semester" menu="jadwal" class="form-select">
                <?php
                    foreach ($smt as $key => $value) {
                ?>
                    <option value="<?= $value['tahun_1'].$value['tahun_2'].$value['semester'] ?>" <?= $selected_smt == $value['tahun_1'].$value['tahun_2'].$value['semester'] ? 'selected' : '' ?>>Semester <?= $value['semester'] == 1 ? 'Ganjil' : 'Genap' ?> <?= $value['tahun_1']."/".$value['tahun_2'] ?></option>
                <?php
                    }
                ?>
            </select>
        </div>
        <div class="col-lg-3 mb-3">
            <label for="ganti_hari">Pilih Hari</label>
            <select id="ganti_hari" menu="jadwal" class="form-select">
                <?php
                    foreach ($hr as $key => $value) {
                ?>
                    <option value="<?= $value ?>" <?= $selected_hari == $value ? 'selected' : '' ?>><?= $value ?></option>
                <?php
                    }
                ?>
            </select>
        </div>
    <?php
    }
    ?>
    
    <?php
    if ($title_header == "Rekapitulasi Kehadiran") {
        view('components/table/rekap_absensi');
    }elseif ($title_header == "Rekapitulasi Kehadiran Dosen") {
        view('components/table/rekap_absensi_dosen');
    }elseif ($title_header == "Rekapitulasi Kehadiran Mahasiswa") {
        view('components/table/rekap_absensi_mhs');
    }else{
    ?>
    <div class="col-lg-12 mb-3">
        <div class="table-responsive">
            <table id="myTable" class="table table-bordered table-striped align-middle">
                <?php
                    if (empty($header_table)) {
                ?>
                    <thead></thead>
                    <tbody></tbody>
                <?php
                } else {
                ?>
                    <thead class="table-dark">
                        <tr>
                            <?php
                            foreach ($header_table as $key => $value) {
                            ?>
                                <th><?= ucwords(str_replace('_', ' ', $value)) ?></th>
                            <?php
                            }
                            if ($title_header != "Laporan Aktivitas") {
                            ?>
                            <th>Action</th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($data)) {
                            foreach ($data as $key1 => $dt) {
                                if ($title_header == "Data Dosen" && $dt['nip'] == "-") {
                                    continue;
                                }
                        ?>
                                <tr>
                                    <?php
                                    foreach ($header_table as $key2 => $value) {
                                        if ($value == "id" && $title_header == "Jadwal Kuliah") {
                                    ?>
                                            <td><a href="#mk-<?= $dt[$value] ?>" class="btn-href-abs"><?= "MK-".$dt[$value] ?></a></td>
                                    <?php
                                        }else{
                                    ?>
                                            <td><?= $dt[$value] ?></td>
                                    <?php
                                        }
                                    }
                                    $paramPost = $header_table[0] . ';_@_;' . $dt[$header_table[0]] . ';_@_;' . $header_table[1] . ';_@_;' . $dt[$header_table[1]];
                                    if ($title_header=="Data Dosen") {
                                        $paramPost = $header_table[0] . ';_@_;' . $dt[$header_table[0]] . ';_@_;' . $header_table[2] . ';_@_;' . $dt[$header_table[2]];
                                    }
                                    if ($title_header != "Laporan Aktivitas") {
                                    ?>
                                    <td><button type="button" class="btn btn-warning"
                                            param="<?= $paramPost ?>"
                                            name="<?= $dt[$header_table[0]] ?>" table="<?= $title_header ?>" <?= !empty($this->session->flashdata('selected_rekap')) && $title_header == "Rekapitulasi Kehadiran" ? "selected_rekap='" . $this->session->flashdata('selected_rekap') . "'" : '' ?> <?= !empty($this->session->flashdata('selected_rekap')) && $title_header == "Rekapitulasi Kehadiran" ? ($this->session->flashdata('selected_rekap') == 1 || $this->session->flashdata('selected_rekap') == 2 ? "id='" . $dt['id_jadwal'] . "'" : "id='" . $dt['id_absen'] . "'") : '' ?> <?= $title_header == "Jadwal Kuliah" ? "nip='" . $dt['nip'] . "' nip2='" . $dt['nip2'] . "' nip3='" . $dt['nip3'] . "' semester_char='" . $dt['semester_char'] . "'" : '' ?>>Edit</button> <button type="button"
                                            class="btn btn-danger btn-hapus"
                                            param="<?= $paramPost ?>"
                                            name="<?= $dt[$header_table[0]] ?>"
                                            head="<?= ucwords(str_replace('_', ' ', $header_table[0])) ?>"
                                            table="<?= $title_header ?>" <?= !empty($this->session->flashdata('selected_rekap')) && $title_header == "Rekapitulasi Kehadiran" ? "selected_rekap='" . $this->session->flashdata('selected_rekap') . "'" : '' ?>>Hapus</button></td>
                                    <?php } ?>
                                </tr>
                        <?php
                            }
                        }
                        ?>
                    </tbody>
                <?php
                    }
                ?>
            </table>
        </div>
    </div>
    <?php
        }
    ?>
</div>
<script>
    $(document).ready(function() {
        $('#myTable').DataTable({
            columnDefs: [{
                className: 'text-center align-middle',
                targets: '_all'
            }, {
                className: 'border border-secondary',
                targets: '_all'
            }]
        });

        $("#dari_file").on("change", function() {
            if ($(this).is(':checked')) {
                $("#import_dari_file").removeAttr("style");
                $("#formFile").attr("disabled", false);
                $("#import_tidak_dari_file").attr("style", "display: none");
            } else {
                $("#import_tidak_dari_file").removeAttr("style");
                $("#import_dari_file").attr("style", "display: none");
                $("#formFile").attr("disabled", true);
                $("#formFile").val('');
            }
        });
        $('#import_form').on('submit', function(e) {
            e.preventDefault(); // Cegah form submit normal
            let formData = new FormData(this); // Ambil semua data form termasuk file
            $("#modalImport").modal('hide');
            $("#loadingModal").modal('show');
            $.ajax({
                url: base_url('proses'), // Ganti dengan URL tujuan upload-mu
                type: 'POST',
                data: formData,
                contentType: false, // penting untuk FormData
                processData: false, // penting agar tidak diubah ke query string
                success: function(response) {
                    $("#loadingModal").modal('hide');
                    location.reload();
                },
                error: function() {
                    $("#loadingModal").modal('hide');
                    location.reload();
                }
            });
        });
        
        $('#export_submit').on('click', function() {
            $('#loadingModal').modal('show');
            $('#modalExport').modal('hide');
            $('#export_form').submit();
            $('#loadingModal').modal('hide');
        });
        $('#tambah_mhs_submit').on('click', function() {
            $('#loadingModal').modal('show');
            $('#modalTambahMhs').modal('hide');
            $('#tambah_mhs_form').submit();
            $('#loadingModal').modal('hide');
        });

        $('#myTable').on('click', '.btn-warning', function() {
            let name_table = $(this).attr('table');
            let previousTdValues = $(this).closest('td').prevAll().map(function() {
                return $(this).html(); // Mengambil nilai dari setiap <td> sebelumnya
            }).get(); // Mengubah hasil menjadi array
            let index_td = previousTdValues.length - 1;

            if (name_table == "Rekapitulasi Kehadiran") {
                let selected_rekap = $(this).attr('selected_rekap');
                let id_selected = $(this).attr('id');

                if (selected_rekap == 1) {
                    $("#edit_form #id_jadwal").val(id_selected);
                    $("#edit_form #tanggal").val(previousTdValues[3]);
                }else if (selected_rekap == 2) {
                    $("#edit_form #id_jadwal").val(id_selected);
                    $("#edit_form #nip").val(previousTdValues[5]);
                    $("#edit_form #jumlah_hadir").val(previousTdValues[0]);
                } else {
                    $("#edit_form #id_absen").val(id_selected);
                    $("#edit_form #nim").val(previousTdValues[6]);
                    $("#edit_form #keterangan").val(previousTdValues[0]);
                }
            } else if (name_table == "Jadwal Kuliah") {
                let nip = $(this).attr("nip");
                let nip2 = $(this).attr("nip2");
                let nip3 = $(this).attr("nip3");
                let semester_char = $(this).attr("semester_char");

                $("#edit_form #nip").val(nip);
                $("#edit_form #nip2").val(nip2);
                $("#edit_form #nip3").val(nip3);
                $("#edit_form #kode_mk").val(previousTdValues[10]);
                $("#edit_form #kode_kelas").val(previousTdValues[7]);
                $("#edit_form #hari").val(previousTdValues[3]);
                $("#edit_form #jam_mulai").val(previousTdValues[2]);
                $("#edit_form #jam_selesai").val(previousTdValues[1]);
                $("#edit_form #ruang").val(previousTdValues[0]);
                $("#edit_form #semester_char").val(semester_char);
            } else {
                $('#edit_form textarea').each(function() {
                    $(this).val(previousTdValues[index_td]);
                    index_td--;
                });
            }

            let name = $(this).attr('name');
            let param = $(this).attr('param');
            $("#title_name").html(name);
            $("#param").val(param);
            $("#modalForm").modal('show');
        });
        $('#edit_submit').on('click', function() {
            $('#loadingModal').modal('show');
            $('#modalForm').modal('hide');
            $('#edit_form').submit();
            $('#loadingModal').modal('hide');
        });

        $('#myTable').on('click', '.btn-hapus', function() {
            $(".hapus-semua").attr('style', 'display: none;');
            $(".hapus-satu").removeAttr('style');
            let name = $(this).attr('name');
            let param = $(this).attr('param');
            let head = $(this).attr('head');
            let name_table = $(this).attr('table');
            let selected_rekap = $(this).attr('selected_rekap');
            $("#title_name_delete").html(name);
            $("#konfir_name_delete").html(name);
            $("#head_delete").html(head);
            $("#title_head_delete").html(name_table);
            $("#param_delete").val(param);
            $("#selected_rekap_delete").val(selected_rekap);
            $("#modalConfirm").modal('show');
        });

        $('#myTable').on('click', '.btn-href-abs', function() {
            let data_tambahan = {
                param_smt: $("#ganti_semester").val(),
                param_hr: $("#ganti_hari").val(),
                param_idx_jdw: $("input[name='pilih-mk-abs']").val() != undefined ? $("input[name='pilih-mk-abs']:checked").val() : "DJ_@_0",
                param_id: $(this).attr("href"),
            };
            appendContentMenu('rekapitulasi_kehadiran', data_tambahan);
        });

        $('#hapusSemua').on('click', function() {
            $("#title_name_delete").html("Semua");
            $(".hapus-satu").attr('style', 'display: none;');
            $(".hapus-semua").removeAttr('style');
            $("#param_delete").val("all");
            $("#selected_rekap_delete").val($("#ganti_rekap").val());
            $("#modalConfirm").modal('show');
        });
        $('#delete_submit').on('click', function() {
            $('#loadingModal').modal('show');
            $('#modalConfirm').modal('hide');
            $('#delete_form').submit();
            $('#loadingModal').modal('hide');
        });

        $('#btn-back-abs').on('click', function() {
            let data_tambahan = {
                param_smt: $("#ganti_semester").val(),
                param_hr: $("#ganti_hari").val(),
            };
            appendContentMenu('jadwal_kuliah', data_tambahan);
        });

        $('#ganti_semester').on('change', function() {
            if ($(this).attr('menu') == "jadwal") {
                let data_tambahan = {
                    param_smt: $(this).val(),
                    param_hr: $("#ganti_hari").val(),
                };
                appendContentMenu('jadwal_kuliah', data_tambahan);
            }else if($(this).attr('menu') == "absen"){
                let menu_absen = 'rekapitulasi_kehadiran';
                let data_tambahan = {
                    param_smt: $(this).val(),
                    param_hr: $("#ganti_hari").val(),
                    param_idx_jdw: $("input[name='pilih-mk-abs']").val() != undefined ? $("input[name='pilih-mk-abs']:checked").val() : "DJ_@_0",
                };
                if ($(this).attr('prm_dsn') != undefined) {
                    menu_absen = 'rekapitulasi_kehadiran_dosen';
                    data_tambahan = {
                        param_smt: $(this).val(),
                        param_hr: $("#ganti_hari").val(),
                        param_idx_jdw: $("input[name='pilih-mk-abs']").val() != undefined ? $("input[name='pilih-mk-abs']:checked").val() : "DJ_@_0",
                        param_dsn: 1,
                    };  
                }
                else if ($(this).attr('prm_mhs') != undefined) {
                    menu_absen = 'rekapitulasi_kehadiran_mhs';
                    data_tambahan = {
                        param_smt: $(this).val(),
                        param_hr: $("#ganti_hari").val(),
                        param_idx_jdw: $("input[name='pilih-mk-abs']").val() != undefined ? $("input[name='pilih-mk-abs']:checked").val() : "DJ_@_0",
                        param_mhs: 1,
                    };  
                }
                
                appendContentMenu(menu_absen, data_tambahan);
            }
        });
        $('#ganti_hari').on('change', function() {
            if ($(this).attr('menu') == "jadwal") {
                let data_tambahan = {
                    param_smt: $("#ganti_semester").val(),
                    param_hr: $(this).val(),
                };
                appendContentMenu('jadwal_kuliah', data_tambahan);
            }else if($(this).attr('menu') == "absen"){
                let menu_absen = 'rekapitulasi_kehadiran';
                let data_tambahan = {
                    param_smt: $("#ganti_semester").val(),
                    param_hr: $(this).val(),
                    param_idx_jdw: $("input[name='pilih-mk-abs']").val() != undefined ? $("input[name='pilih-mk-abs']:checked").val() : "DJ_@_0",
                };
                if ($(this).attr('prm_dsn') != undefined) {
                    menu_absen = 'rekapitulasi_kehadiran_dosen';
                    data_tambahan = {
                        param_smt: $("#ganti_semester").val(),
                        param_hr: $(this).val(),
                        param_idx_jdw: $("input[name='pilih-mk-abs']").val() != undefined ? $("input[name='pilih-mk-abs']:checked").val() : "DJ_@_0",
                        param_dsn: 1,
                    };  
                }
                else if ($(this).attr('prm_mhs') != undefined) {
                    menu_absen = 'rekapitulasi_kehadiran_mhs';
                    data_tambahan = {
                        param_smt: $("#ganti_semester").val(),
                        param_hr: $(this).val(),
                        param_idx_jdw: $("input[name='pilih-mk-abs']").val() != undefined ? $("input[name='pilih-mk-abs']:checked").val() : "DJ_@_0",
                        param_mhs: 1,
                    };  
                }
                appendContentMenu(menu_absen, data_tambahan);
            }
        });
        $("input[name='pilih-mk-abs']").on("change", function () {
            let menu_absen = 'rekapitulasi_kehadiran';
            let data_tambahan = {
                param_smt: $("#ganti_semester").val(),
                param_hr: $("#ganti_hari").val(),
                param_idx_jdw: $(this).val(),
            };
            if ($(this).attr('prm_dsn') != undefined) {
                menu_absen = 'rekapitulasi_kehadiran_dosen';
                data_tambahan = {
                    param_smt: $("#ganti_semester").val(),
                    param_hr: $("#ganti_hari").val(),
                    param_idx_jdw: $(this).val(),
                    param_dsn: 1,
                };  
            }
            else if ($(this).attr('prm_mhs') != undefined) {
                menu_absen = 'rekapitulasi_kehadiran_mhs';
                data_tambahan = {
                    param_smt: $("#ganti_semester").val(),
                    param_hr: $("#ganti_hari").val(),
                    param_idx_jdw: $(this).val(),
                    param_mhs: 1,
                };
            }
            
            appendContentMenu(menu_absen, data_tambahan);
        });

        $('.dropdown').hover(
            function() {
                $(this).addClass('show');
                $(this).find('.dropdown-menu').addClass('show');
            },
            function() {
                $(this).removeClass('show');
                $(this).find('.dropdown-menu').removeClass('show');
            }
        );

        $("#pilih-tanggal-pertemuan").on("change", function () {
            let data_tambahan = {
                param_smt: $("#ganti_semester").val(),
                param_hr: $("#ganti_hari").val(),
                param_idx_jdw: $("input[name='pilih-mk-abs']").val() != undefined ? $("input[name='pilih-mk-abs']:checked").val() : "DJ_@_0",
                param_tgl: $(this).val(),
            };
            appendContentMenu('rekapitulasi_kehadiran', data_tambahan);  
        });
        
        //CHECK SUDAH DICEK ATAU BELUM DOSEN MASUKNYA
        // let is_absen_ready = $("input[name='nip_masuk']").is(':checked');
        $("input[name='nip_masuk']").on("change", function () {
            let dataSend = {
                param_smt: $("#ganti_semester").val(),
                param_hr: $("#ganti_hari").val(),
                param_idx_jdw: $("input[name='pilih-mk-abs']").val() != undefined ? $("input[name='pilih-mk-abs']:checked").val() : "DJ_@_0",
                param_value: $(this).val(),
            };
            let tgl = dataSend.param_value.split('_@_');
            if (!$(this).prop('checked')) {
                dataSend.param_value = "-_@_"+tgl[1]+"_@_"+tgl[2];
            }
            console.log(dataSend);
            
            // if (is_absen_ready) {
                updateAbsensi(dataSend);
            // }else{
            //     if (confirm('Absen Belum Dibuat! Apakah ingin dibuat?')) {
            //         is_absen_ready = true;
            //         updateAbsensi(dataSend);
            //     }else{
            //         $(this).prop('checked', false);
            //     }
            // }
        });
        $("input[name*='mhs_masuk']").on("change", function () {
            let dataSend = {
                param_smt: $("#ganti_semester").val(),
                param_hr: $("#ganti_hari").val(),
                param_idx_jdw: $("input[name='pilih-mk-abs']").val() != undefined ? $("input[name='pilih-mk-abs']:checked").val() : "DJ_@_0",
                param_value: $(this).val(),
                param_mhs: 1
            };
            if (!$(this).prop('checked')) {
                let valValue = $(this).val();
                valValue = valValue.split('_@_');
                valValue[0] = '-';
                valValue = valValue.join('_@_');
                dataSend.param_value = valValue;
            }
            updateAbsensi(dataSend);
        });
        $(".hadir-semua").on("click", function () {
            console.log($(this).attr('param'));
            let dataSend = {
                param_smt: $("#ganti_semester").val(),
                param_hr: $("#ganti_hari").val(),
                param_idx_jdw: $("input[name='pilih-mk-abs']").val() != undefined ? $("input[name='pilih-mk-abs']:checked").val() : "DJ_@_0",
                param_value: $(this).attr('param'),
                param_mhs: 1,
                param_mhs_semua: 1
            };
            
            updateAbsensi(dataSend);
        });
        
        $('.delete-tanggal').on('click', function() {
            $(".hapus-semua").attr('style', 'display: none;');
            $(".hapus-satu").removeAttr('style');
            let name = $(this).attr('name');
            let param = $(this).attr('param');
            let head = $(this).attr('head');
            let name_table = $(this).attr('table');
            $("#title_name_delete").html(name);
            $("#konfir_name_delete").html(name);
            $("#head_delete").html(head);
            $("#title_head_delete").html(name_table);
            $("#param_delete").val(param);
            $("#modalConfirm").modal('show');
        });

        $(".ubah-tanggal").on("change", function () {
            let tgl = $(this).val();
            let tgl_before = $(this).attr("tgl");
            let cek = $(this).attr("cek");
            let value = tgl+"_@_"+tgl_before;
            if (cek == "1") {
                $(this).val(tgl_before);
                alert("Silahkan Pilih Terlebih dahulu Dosen Masuk atau Kehadiran Mahasiswa dibawah!");
            }else{
                let dataSend = {
                    param_smt: $("#ganti_semester").val(),
                    param_hr: $("#ganti_hari").val(),
                    param_idx_jdw: $("input[name='pilih-mk-abs']").val() != undefined ? $("input[name='pilih-mk-abs']:checked").val() : "DJ_@_0",
                    param_value: value,
                    param_tgl: 1
                };
                updateAbsensi(dataSend);
            }
        });

        $("#myTable").on("dblclick", ".dsn-mk", function () {
            let isDsn = $(this).attr("isDsn");
            if (isDsn == "0") {
                let dayName = $(this).attr("hari");;
                let data_tambahan = {
                    param_smt: $("#ganti_semester").val(),
                    param_hr: dayName,
                    param_idx_jdw: "DJ_@_0",
                    param_id: $(this).html(),
                };
                appendContentMenu('rekapitulasi_kehadiran', data_tambahan);
            }
        });
    });
</script>