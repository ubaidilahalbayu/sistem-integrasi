<?php
if (!empty($this->session->flashdata('alert'))) {
    $dataAlert = $this->session->flashdata('alert');
    $icon = '#exclamation-triangle-fill';
    if ($dataAlert['status'] == 'success') {
        $icon = '#check-circle-fill';
    } elseif ($dataAlert['status'] == 'info') {
        $icon = '#info-fill';
    }
?>
    <div class="alert alert-<?= $dataAlert['status'] ?> d-flex align-items-center alert-dismissible fade show"
        role="alert">
        <svg class="bi flex-shrink-0 me-2" style="width: 20px; height: 20px;" role="img"
            aria-label="Danger:">
            <use xlink:href="<?= $icon ?>" />
        </svg>
        <div><?= $dataAlert['message'] ?></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php
}
?>