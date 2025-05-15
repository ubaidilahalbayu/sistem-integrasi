<!-- Modal -->
<?php
$title_header = !empty($title_header) ? $title_header : 'Form';
?>
<div class="modal fade" id="modalForm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Edit
                    <?= $title_header ?> (<b id="title_name"></b>) </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="edit_form" action="<?= base_url('edit') ?>" method="post">
                    <input type="hidden" name="menu" value="<?= str_replace(' ', '_', strtolower($title_header)) ?>">
                    <input type="hidden" name="param" id="param">
                    <div class="row justify-content-start">
                        <?php
                        $dataED['edit'] = 1;
                        if ($title_header == "Jadwal Kuliah") {
                            $paramView['export'] = 0;
                            view('components/form/jadwal_kuliah', $paramView);
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
                <button type="button" class="btn btn-primary" id="edit_submit">Submit</button>
            </div>
        </div>
    </div>
</div>