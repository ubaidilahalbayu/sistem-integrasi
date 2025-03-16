<?php
$data_header['title'] = 'Login Sistem';
$data_header['body_login'] = 1;
view('components/header', $data_header);
?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-4 bg-white border rounded-5 border-dark">
            <div class="row mt-5">
                <h1 class="text-center">Login</h1>
            </div>
            <div class="row mb-5">
                <h4 class="text-center">Sign in To Continue</h4>
            </div>
            <form action="<?= base_url('login') ?>" method="POST">
                <div class="row justify-content-start">
                    <?php
                    view('components/alert');
                    ?>
                    <div class="col-lg-12 mb-4">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username"
                            placeholder="Your Username Here">
                    </div>
                    <div class="col-lg-12 mb-5">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="*****">
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-lg-6 d-grid gap-1 mb-6">
                        <button class="btn w-100 btn-dark" type="submit">Login</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
view('components/copyright');
view('components/footer');
?>