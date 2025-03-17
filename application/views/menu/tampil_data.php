<?php
view('components/modal/modal_import');
view('components/modal/modal_export');
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
                        <td><button type="button" class="btn btn-warning">Edit</button></td>
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
            $("#import_tidak_dari_file").attr("style", "display: none");
        } else {
            $("#import_tidak_dari_file").removeAttr("style");
            $("#import_dari_file").attr("style", "display: none");
        }
    });
});
</script>