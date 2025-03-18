<!-- Modal -->
<?php
$title_header = !empty($title_header) ? $title_header : 'Form';
?>
<div class="modal fade" id="modalExport" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Export
                    <?= $title_header ?></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="export_form">
                    <input type="hidden" name="menu" value="<?= str_replace(' ', '_', strtolower($title_header)) ?>">
                    <div class="row justify-content-start">
                        <?php
                        if ($title_header == "Rekap Absensi") {
                            view('components/form/rekap_absensi', $data['export'] = 1);
                        ?>
                            <?php
                        } else {
                            if (!empty($header_table)) {
                                foreach ($header_table as $key => $value) {
                            ?>
                                    <div class="col-lg-4 mb-3">
                                        <label for="<?= $value ?>" class="form-label">Filter
                                            <?= str_replace('_', ' ', ucwords($value)) ?></label>
                                        <select class="form-select" id="<?= $value ?>" name="<?= $value ?>">
                                            <option value="all">Semua</option>
                                            <option value="1">1</option>
                                        </select>
                                    </div>
                        <?php
                                }
                            }
                        }
                        ?>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="export_submit">Submit</button>
            </div>
        </div>
    </div>
</div>