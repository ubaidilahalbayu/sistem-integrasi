<!-- Modal -->
<?php
$title_header = !empty($title_header) ? $title_header : 'Form';
?>
<div class="modal fade" id="modalImport" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Import
                    <?= $title_header ?></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="import_form" action="<?= base_url('proses') ?>" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="menu" value="<?= str_replace(' ', '_', strtolower($title_header)) ?>">
                    <?php if ($title_header != "Rekapitulasi Kehadiran") {
                    ?>
                    <!-- <div class="row justify-content-start">
                        <div class="col-lg-4 mb-3">
                            <input type="checkbox" class="btn-check" id="dari_file" autocomplete="off">
                            <label class="btn btn-outline-primary" for="dari_file">
                                <svg class="bi flex-shrink-0 mb-1 me-1" style="width: 15px; height: 15px;" role="img"
                                    aria-label="Danger:">
                                    <use xlink:href="#check-circle-fill" />
                                </svg>Dari File</label><br>
                        </div>
                    </div> -->
                    <?php
                    }
                    ?>
                    <div class="row justify-content-start" id="import_dari_file" <?= $title_header != "Rekapitulasi Kehadiran" ? 'style="display: none;"' : '' ?>>
                        <div class="col-lg-12 mb-3">
                            <label for="formFile" class="form-label">File Excel</label>
                            <input class="form-control" id="formFile" name="formFile" type="file"
                                accept=".xls,.xlsx,.csv" <?= $title_header == 'Rekapitulasi Kehadiran' ? '' : 'disabled' ?>>
                        </div>
                    </div>
                    <div class="row justify-content-start" id="import_tidak_dari_file" <?= $title_header != "Rekapitulasi Kehadiran" ? '' : 'style="display: none;"' ?>>
                        <?php
                        if ($title_header == "Jadwal Kuliah") {
                            view('components/form/jadwal_kuliah');
                        } else {
                            if (!empty($header_table)) {
                                foreach ($header_table as $key => $value) {
                        ?>
                                    <div class="col-lg-6 mb-3">
                                        <label for="<?= $value ?>"
                                            class="form-label"><?= str_replace('_', ' ', ucwords($value)) ?></label>
                                        <textarea class="form-control" id="<?= $value ?>" name="<?= $value ?>" rows="1"></textarea>
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
                <button type="submit" class="btn btn-primary" id="import_submit" form="import_form">Submit</button>
            </div>
        </div>
    </div>
</div>