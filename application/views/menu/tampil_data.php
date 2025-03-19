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
    <div class="col-lg-12 mb-3">
        <div class="table-responsive">
            <table id="myTable" class="table table-info table-striped">
                <?php
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
                                    ?>
                        <td><?= $dt[$value] ?></td>
                        <?php
                                    }
                                    ?>
                        <td><button type="button" class="btn btn-warning"
                                param="<?= $header_table[0].';_@_;'.$dt[$header_table[0]].';_@_;'.$header_table[1].';_@_;'.$dt[$header_table[1]] ?>"
                                name="<?= $dt[$header_table[0]]?>">Edit</button> <button type="button"
                                class="btn btn-danger"
                                param="<?= $header_table[0].';_@_;'.$dt[$header_table[0]].';_@_;'.$header_table[1].';_@_;'.$dt[$header_table[1]] ?>"
                                name="<?= $dt[$header_table[0]] ?>"
                                head="<?= ucwords(str_replace('_', ' ',$header_table[0])) ?>"
                                table="<?= $title_header ?>">Hapus</button></td>
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
            $("#formFile").removeAttr("disabled");
            $("#import_tidak_dari_file").attr("style", "display: none");
        } else {
            $("#import_tidak_dari_file").removeAttr("style");
            $("#import_dari_file").attr("style", "display: none");
            $("#formFile").attr("disabled");
            $("#formFile").val('');
        }
    });
    $('#import_submit').on('click', function() {
        $('#loadingModal').modal('show');
        $('#modalImport').modal('hide');
        $('#import_form').submit();
        $('#loadingModal').modal('hide');
    });

    $('#myTable').on('click', '.btn-warning', function() {
        let previousTdValues = $(this).closest('td').prevAll().map(function() {
            return $(this).html(); // Mengambil nilai dari setiap <td> sebelumnya
        }).get(); // Mengubah hasil menjadi array
        let index_td = previousTdValues.length - 1;

        // $('#edit_form input, #edit_form textarea').each(function() {
        $('#edit_form textarea').each(function() {
            $(this).val(previousTdValues[index_td]);
            index_td--;
        });

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
    $('#delete_submit').on('click', function() {
        $('#loadingModal').modal('show');
        $('#modalConfirm').modal('hide');
        $('#delete_form').submit();
        $('#loadingModal').modal('hide');
    });
});
</script>