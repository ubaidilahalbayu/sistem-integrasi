<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login Page</title>

  <!-- Bootstrap CSS -->
  <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> -->
   
    <link rel="stylesheet" href="<?= base_url('assets/bootstrap-5.3.3/css/bootstrap.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/select2/css/select.css'); ?>">

  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    body {
      background: linear-gradient(135deg, #0d6efd, #6610f2);
      height: 100vh;
      margin: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      animation: fadeIn 1s ease-in;
    }

    .login-container {
      width: 100%;
      max-width: 420px;
      padding: 2rem;
      background-color: #ffffff;
      border-radius: 1rem;
      box-shadow: 0 0 30px rgba(0, 0, 0, 0.1);
      text-align: center;
      animation: scaleIn 0.5s ease;
    }

    .login-container img {
      width: 80px;
      margin-bottom: 1rem;
    }

    .form-control:focus {
      box-shadow: none;
      border-color: #0d6efd;
    }

    .form-check {
      text-align: left;
    }

    .btn-primary {
      background-color: #0d6efd;
      transition: background-color 0.3s ease;
    }

    .btn-primary:hover {
      background-color: #0b5ed7;
    }

    @keyframes fadeIn {
      from { opacity: 0 }
      to { opacity: 1 }
    }

    @keyframes scaleIn {
      from { transform: scale(0.9); opacity: 0 }
      to { transform: scale(1); opacity: 1 }
    }

    .input-group-text {
      background-color: #f8f9fa;
      cursor: pointer;
    }

    .bi-eye, .bi-eye-slash {
      font-size: 1.2rem;
    }
  </style>
</head>
<body>

  <div class="login-container">
    <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="Login Icon" />
    <h3 class="mb-4">Login</h3>

    <form action="<?= base_url('login') ?>" method="POST">
        <?php
        view('components/alert');
        ?>
      <div class="mb-3 text-start">
        <label for="username" class="form-label">Username</label>
        <input type="text" class="form-control" id="username" name="username" placeholder="Enter username" required />
      </div>

      <div class="mb-3 text-start">
        <label for="password" class="form-label">Password</label>
        <div class="input-group">
          <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required />
          <span class="input-group-text" onclick="togglePassword()" title="Show/Hide Password">
            <i class="bi bi-eye-slash" id="toggleEye"></i>
          </span>
        </div>
      </div>
      <!-- <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" id="remember" />
        <label class="form-check-label" for="remember">Remember me</label>
      </div> -->
      <button type="submit" class="btn btn-primary w-100">Login</button>

      <!-- <div class="mt-3">
        <a href="#" class="text-decoration-none">Forgot password?</a>
      </div> -->
    </form>
  </div>

  <script>
    function togglePassword() {
      const passwordInput = document.getElementById('password');
      const toggleEye = document.getElementById('toggleEye');

      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleEye.classList.remove('bi-eye-slash');
        toggleEye.classList.add('bi-eye');
      } else {
        passwordInput.type = 'password';
        toggleEye.classList.remove('bi-eye');
        toggleEye.classList.add('bi-eye-slash');
      }
    }
  </script>
<script src="<?= base_url('assets/bootstrap-5.3.3/js/bootstrap.js') ?>"></script>
<script src='<?= base_url('assets/data-tables/datatables.js') ?>'></script>
<script src='<?= base_url('assets/select2/js/select.min.js') ?>'></script>

<?php
if (!empty($this->session->flashdata('alert'))) {
?>
    <script type="text/javascript">
        $(document).ready(function() {
            window.setTimeout(function() {
                $(".alert").fadeTo(1000, 0).slideUp(1000, function() {
                    $(this).remove();
                });
            }, 5000);
        });
    </script>
<?php
}
?>
</body>
</html>