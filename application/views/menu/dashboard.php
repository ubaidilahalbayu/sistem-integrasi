<h1 class="py-5 text-center">WELCOME
    <?= !empty($this->session->userdata('username')) ? strtoupper($this->session->userdata('username')) : 'USER' ?></h1>