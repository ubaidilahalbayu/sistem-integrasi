<div class="animasi-dashboard">
    <div>
        <h5>WELCOME <?= !empty($this->session->userdata('dosen')) ? strtoupper($this->session->userdata('dosen')) : (!empty($this->session->userdata('username')) ? strtoupper($this->session->userdata('username')) : 'USER') ?></h5>
    </div>
    <i class="bi bi-person-fill fs-1 opacity-50"></i>
</div>
<div class="kontener">
    <h3>List Pertemuan <?= $semester ?></h3>
    <div class="row justify-content-start mt-3">
        <div class="col-lg-4 mb-3">
            <b>Keterangan*</b>
            <div class="row justyfy-content-start ms-4">
                <div class="col-lg-12 bg-danger">
                    Jumlah pertemuan kurang atau sama dengan 25%
                </div>
            </div>
            <div class="row justyfy-content-start ms-4">
                <div class="col-lg-12 bg-warning">
                    Jumlah pertemuan kurang atau sama dengan 50%
                </div>
            </div>
            <div class="row justyfy-content-start ms-4">
                <div class="col-lg-12 bg-info">
                    Jumlah pertemuan kurang atau sama dengan 75%
                </div>
            </div>
            <div class="row justyfy-content-start ms-4">
                <div class="col-lg-12 bg-success">
                    Jumlah pertemuan kurang atau sama dengan 100%
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 10%;">Kode MK</th>
                            <th style="width: 40%;">Nama MK</th>
                            <th style="width: 20%;">Jadwal</th>
                            <th style="width: 30%;">Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($persentase as $key => $value) { 
                            $persentase = $value['jlh_pertemuan']/16*100;
                            if ($persentase > 75) {
                                $bg = "success";
                            }elseif ($persentase > 50) {
                                $bg = "info";
                            }elseif ($persentase > 25) {
                                $bg = "warning";
                            }else {
                                $bg = "danger";
                            }
                        ?>
                            <tr>
                                <td><a href="javascript:void(0)" class="dash-mk" hari="<?= $value['hari'] ?>" smt="<?= $value['smt'] ?>"><?= 'MK-'.$value['id'] ?></a></td>
                                <td><?= $value['nama_mk'] ?></td>
                                <td><?= $value['jadwal'] ?></td>
                                <td>
                                    <div class="progress" style="height: 30px;">
                                        <div class="progress-bar bg-<?= $bg ?> text-dark" style="width: <?= $persentase ?>%;"><?= $persentase."%" ?></div>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php if (empty($this->session->userdata('dosen'))) { ?>
<script>
    $(document).ready(function () {
        $(".table").on("dblclick", ".dash-mk", function () {
            let dayName = $(this).attr("hari");
            let smt = $(this).attr("smt");
            let data_tambahan = {
                param_smt: smt,
                param_hr: dayName,
                param_idx_jdw: "DJ_@_0",
                param_id: $(this).html(),
            };
            appendContentMenu('rekap_absensi', data_tambahan);
        });
    });
</script>
<?php } ?>