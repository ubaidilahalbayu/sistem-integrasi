<!-- Modal -->
<?php
$title_header = !empty($title_header) ? $title_header : 'Form';
?>
<div class="modal fade" id="modalConfirm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Hapus
                    <?= $title_header ?> (<b id="title_name_delete"></b>) </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="delete_form" action="<?= base_url('hapus') ?>" method="post">
                    <input type="hidden" name="_method" value="DELETE">
                    <input type="hidden" name="menu" value="<?= str_replace(' ', '_', strtolower($title_header)) ?>">
                    <input type="hidden" name="param_delete" id="param_delete">
                    <div class="row justify-content-start">
                        <div class="col-lg-12 mb-3">
                            <h4 class="text-center">Apakah Yakin Ingin Menghapus Data dengan <b id="head_delete"></b> <b
                                    id="konfir_name_delete"></b> dari <b id="title_head_delete"></b> ?</h4>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="delete_submit">Submit</button>
            </div>
        </div>
    </div>
</div>