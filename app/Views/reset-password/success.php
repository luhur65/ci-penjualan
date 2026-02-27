<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Password Berhasil Direset</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- AdminLTE / Bootstrap (optional, sesuaikan project kamu) -->
  <link rel="stylesheet" href="<?= base_url('libraries/adminlte/plugins/fontawesome-free/css/all.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('libraries/adminlte/dist/css/adminlte.min.css') ?>">
</head>

<body class="hold-transition login-page">

  <div class="login-box">
    <div class="card">
      <div class="card-body text-center">

        <div class="mb-3">
          <i class="fas fa-check-circle text-success" style="font-size: 60px;"></i>
        </div>

        <h4 class="text-success mb-3">
          Password Berhasil Direset
        </h4>

        <p class="mb-4">
          Password Anda telah berhasil diperbarui.<br>
          Silakan login kembali menggunakan password baru Anda.
        </p>

        <a href="<?= base_url('login') ?>" class="btn btn-primary btn-block">
          <i class="fas fa-sign-in-alt"></i> Kembali ke Login
        </a>

      </div>
    </div>
  </div>

  <div class="text-center mt-3">
    <small>
      Copyright &copy; <?= date('Y') ?>
    </small>
  </div>

  <script>
    // Optional: auto redirect setelah 5 detik
    setTimeout(function() {
      window.location.href = "<?= base_url('login') ?>";
    }, 5000);
  </script>

</body>

</html>