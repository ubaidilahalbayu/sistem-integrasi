<div class="row justify-content-start">
    <div class="col-lg-6 mb-5 mt-5">
        <h4 class="text-center">Ubah Pasword</h4>
    </div>
    <div class="col-lg-12"></div>
    <div class="col-lg-6">
        <div class="row justify-content-start">
            <form id="import_form" action="<?= base_url('proses') ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" value="<?= $this->session->userdata('username') ?>" name="username">
                <input type="hidden" value="setting_password" name="menu">
                <div class="col-lg-12 mb-5">
                    <label for="password" class="form-label">Password Baru</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password" name="password" placeholder="*****" value="<?= !empty($this->session->flashdata('password')) ? $this->session->flashdata('password') : ''?>">
                        <button class="input-group-text btn btn-light btn-passim" type="button"><img src="<?= base_url('assets/image/mata_tutup.png') ?>" height="25px" width="25px" alt="Lihat" id="icon-passim"></button>
                    </div>
                </div>
                <div class="col-lg-12 mb-5">
                    <label for="c_password" class="form-label">Konfirmasi Password Baru</label>
                    <div class="input-group">
                        <input type="c_password" class="form-control" id="c_password" name="c_password" placeholder="*****" value="<?= !empty($this->session->flashdata('c_password')) ? $this->session->flashdata('c_password') : ''?>">
                        <button class="input-group-text btn btn-light btn-cpassim" type="button"><img src="<?= base_url('assets/image/mata_tutup.png') ?>" height="25px" width="25px" alt="Lihat" id="icon-cpassim"></button>
                    </div>
                </div>
                <div class="col-lg-12">
                    <button class="btn btn-lg btn-primary" type="submit">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $("#import_form").on("click", ".btn-passim", function () {
            let link_img = $("#icon-passim").attr("src");
            link_img = link_img.split("/");
            if ($("#password").attr("type") == "password") {
                link_img[link_img.length-1] = "mata_buka.png";
                link_img = link_img.join("/");
                $("#icon-passim").attr("src", link_img);
                $("#password").attr("type", "text");
            }else{
                link_img[link_img.length-1] = "mata_tutup.png";
                link_img = link_img.join("/");
                $("#icon-passim").attr("src", link_img);
                $("#password").attr("type", "password");
            }
        });
        $("#import_form").on("click", ".btn-cpassim", function () {
            let link_img = $("#icon-cpassim").attr("src");
            link_img = link_img.split("/");
            if ($("#c_password").attr("type") == "password") {
                link_img[link_img.length-1] = "mata_buka.png";
                link_img = link_img.join("/");
                $("#icon-cpassim").attr("src", link_img);
                $("#c_password").attr("type", "text");
            }else{
                link_img[link_img.length-1] = "mata_tutup.png";
                link_img = link_img.join("/");
                $("#icon-cpassim").attr("src", link_img);
                $("#c_password").attr("type", "password");
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
    });
</script>