<?php
view('components/modal/modal_import');
view('components/modal/modal_export');
view('components/modal/modal_form');
view('components/modal/modal_confirm');
?>
<div class="row justify-content-start mt-5">
    <div class="col-lg-2 d-grid gap-2 mb-3">
        <button type="button" class="btn btn-success" data-bs-toggle="modal"
            data-bs-target="#modalExport">Export</button>
    </div>
    <div class="col-lg-2 d-grid gap-2 mb-3">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
            data-bs-target="#modalImport">Import</button>
    </div>
    <div class="col-lg-2 d-grid gap-2 mb-3">
        <button type="button" class="btn btn-danger" id="hapusSemua">Delete All</button>
    </div>
    <?php
    if ($title_header == "Rekap Absensi") {
    ?>
        <div class="col-lg-3 mb-3">
            <select id="ganti_rekap" class="form-select">
                <option value="1">Daftar Absen</option>
                <option value="2" <?= !empty($this->session->flashdata('selected_rekap')) ? ($this->session->flashdata('selected_rekap') == 2 ? 'selected' : '') : '' ?>>Absen Dosen</option>
                <option value="3" <?= !empty($this->session->flashdata('selected_rekap')) ? ($this->session->flashdata('selected_rekap') == 3 ? 'selected' : '') : '' ?>>Absen Mahasiswa</option>
            </select>
        </div>
    <?php
    }elseif ($title_header == "Jadwal Kuliah") {
    ?>
        <div class="col-lg-12 mb-3">
            <h5 class="text-center"><?=$semester_print?></h5>
        </div>
        <div class="col-lg-3 mb-3">
            <label for="ganti_semester">Pilih Semester</label>
            <select id="ganti_semester" class="form-select">
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
            <select id="ganti_hari" class="form-select">
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
    <div class="col-lg-12 mb-3">
        <div class="table-responsive">
            <table id="myTable" class="table table-info table-striped">
                
                <?php
                if ($title_header == "Rekap Absensi") {
                ?>
                <thead>
                    <tr>
                        <th>NO</th>
                    </tr>
                </thead>
                <?php
                }else{
                    if (empty($header_table)) {
                ?>
                    <thead></thead>
                    <tbody></tbody>
                <?php
                } else {
                ?>
                    <thead>
                        <tr>
                            <?php
                            foreach ($header_table as $key => $value) {
                            ?>
                                <th><?= ucwords(str_replace('_', ' ', $value)) ?></th>
                            <?php
                            }
                            ?>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($data)) {
                            foreach ($data as $key1 => $dt) {
                        ?>
                                <tr>
                                    <?php
                                    foreach ($header_table as $key2 => $value) {
                                        if ($value == "id" && $title_header == "Jadwal Kuliah") {
                                    ?>
                                            <td><a href="#"><?= "MK-".$dt[$value] ?></a></td>
                                    <?php
                                        }else{
                                    ?>
                                            <td><?= $dt[$value] ?></td>
                                    <?php
                                        }
                                    }
                                    ?>
                                    <td><button type="button" class="btn btn-warning"
                                            param="<?= $header_table[0] . ';_@_;' . $dt[$header_table[0]] . ';_@_;' . $header_table[1] . ';_@_;' . $dt[$header_table[1]] ?>"
                                            name="<?= $dt[$header_table[0]] ?>" table="<?= $title_header ?>" <?= !empty($this->session->flashdata('selected_rekap')) && $title_header == "Rekap Absensi" ? "selected_rekap='" . $this->session->flashdata('selected_rekap') . "'" : '' ?> <?= !empty($this->session->flashdata('selected_rekap')) && $title_header == "Rekap Absensi" ? ($this->session->flashdata('selected_rekap') == 1 || $this->session->flashdata('selected_rekap') == 2 ? "id='" . $dt['id_jadwal'] . "'" : "id='" . $dt['id_absen'] . "'") : '' ?> <?= $title_header == "Jadwal Kuliah" ? "nip='" . $dt['nip'] . "' nip2='" . $dt['nip2'] . "' nip3='" . $dt['nip3'] . "'" : '' ?>>Edit</button> <button type="button"
                                            class="btn btn-danger"
                                            param="<?= $header_table[0] . ';_@_;' . $dt[$header_table[0]] . ';_@_;' . $header_table[1] . ';_@_;' . $dt[$header_table[1]] ?>"
                                            name="<?= $dt[$header_table[0]] ?>"
                                            head="<?= ucwords(str_replace('_', ' ', $header_table[0])) ?>"
                                            table="<?= $title_header ?>" <?= !empty($this->session->flashdata('selected_rekap')) && $title_header == "Rekap Absensi" ? "selected_rekap='" . $this->session->flashdata('selected_rekap') . "'" : '' ?>>Hapus</button></td>
                                </tr>
                        <?php
                            }
                        }
                        ?>
                    </tbody>
                <?php
                    }
                }
                ?>
            </table>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#myTable').DataTable({
            columnDefs: [{
                className: 'text-center',
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
        $('#import_submit').on('click', function() {
            $('#loadingModal').modal('show');
            $('#modalImport').modal('hide');
            $('#import_form').submit();
            $('#loadingModal').modal('hide');
        });
        $('#export_submit').on('click', function() {
            $('#loadingModal').modal('show');
            $('#modalExport').modal('hide');
            $('#export_form').submit();
            $('#loadingModal').modal('hide');
        });

        $('#myTable').on('click', '.btn-warning', function() {
            let name_table = $(this).attr('table');
            let previousTdValues = $(this).closest('td').prevAll().map(function() {
                return $(this).html(); // Mengambil nilai dari setiap <td> sebelumnya
            }).get(); // Mengubah hasil menjadi array
            let index_td = previousTdValues.length - 1;

            if (name_table == "Rekap Absensi") {
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

                $("#edit_form #nip").val(nip);
                $("#edit_form #nip2").val(nip2);
                $("#edit_form #nip3").val(nip3);
                $("#edit_form #kode_mk").val(previousTdValues[9]);
                $("#edit_form #kode_kelas").val(previousTdValues[6]);
                $("#edit_form #hari").val(previousTdValues[2]);
                $("#edit_form #jam_mulai").val(previousTdValues[1]);
                $("#edit_form #jam_selesai").val(previousTdValues[0]);
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

        $('#myTable').on('click', '.btn-danger', function() {
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


        $("#import_form").on('change', '.check_pilihan_rekap', function() {
            if ($(this).val() == 1) {
                $('#pilihan_rekap_1').removeAttr('style');
                $('#pilihan_rekap_1 select', 'pilihan_rekap_1 input').each(function() {
                    $(this).attr('disabled', false);
                });
                $('#pilihan_rekap_2').attr('style', 'display: none;');
                $('#pilihan_rekap_2 select').each(function() {
                    $(this).attr('disabled', true);
                });
                $('#pilihan_rekap_3').attr('style', 'display: none;');
                $('#pilihan_rekap_3 select', 'pilihan_rekap_3 input').each(function() {
                    $(this).attr('disabled', true);
                });
            } else if ($(this).val() == 2) {
                $('#pilihan_rekap_2').removeAttr('style');
                $('#pilihan_rekap_2 select').each(function() {
                    $(this).attr('disabled', false);
                });
                $('#pilihan_rekap_1').attr('style', 'display: none;');
                $('#pilihan_rekap_1 select', 'pilihan_rekap_1 input').each(function() {
                    $(this).attr('disabled', true);
                });
                $('#pilihan_rekap_3').attr('style', 'display: none;');
                $('#pilihan_rekap_3 select', 'pilihan_rekap_3 input').each(function() {
                    $(this).attr('disabled', true);
                });
            } else if ($(this).val() == 3) {
                $('#pilihan_rekap_3').removeAttr('style');
                $('#pilihan_rekap_3 select', 'pilihan_rekap_3 input').each(function() {
                    $(this).attr('disabled', false);
                });
                $('#pilihan_rekap_2').attr('style', 'display: none;');
                $('#pilihan_rekap_2 select').each(function() {
                    $(this).attr('disabled', true);
                });
                $('#pilihan_rekap_1').attr('style', 'display: none;');
                $('#pilihan_rekap_1 select', 'pilihan_rekap_1 input').each(function() {
                    $(this).attr('disabled', true);
                });
            }
        });

        $('#ganti_rekap').on('change', function() {
            if ($(this).val() == 1) {
                appendContentMenu('rekap_absensi');
            } else if ($(this).val() == 2) {
                let data_tambahan = {
                    param_dosen: 1
                };
                appendContentMenu('rekap_absensi', data_tambahan);
            } else if ($(this).val() == 3) {
                let data_tambahan = {
                    param_mhs: 1
                };
                appendContentMenu('rekap_absensi', data_tambahan);
            }
        });
        $('#ganti_semester').on('change', function() {
            let data_tambahan = {
                param_smt: $(this).val(),
                param_hr: $("#ganti_hari").val(),
            };
            appendContentMenu('jadwal_kuliah', data_tambahan);
        });
        $('#ganti_hari').on('change', function() {
            let data_tambahan = {
                param_smt: $("#ganti_semester").val(),
                param_hr: $(this).val(),
            };
            appendContentMenu('jadwal_kuliah', data_tambahan);
        });
    });
</script>