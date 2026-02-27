<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= getenv('app.name'); ?> | Login</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?= base_url('public/libraries/adminlte/plugins/fontawesome-free/css/all.min.css') ?>">

  <!-- Theme style -->
  <link rel="stylesheet" href="<?= base_url('public/libraries/adminlte/dist/css/adminlte.min.css') ?>">
</head>

<body class="hold-transition login-page">
  <div class="login-box">
    <div class="login-logo">
      <!-- <?= getenv('app.name'); ?> -->
    </div>

    <div class="card">
      <div class="card-body login-card-body">
        <p class="login-box-msg">Login</p>

        <?php if (session()->getFlashdata('error')): ?>
          <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <?= session()->getFlashdata('error') ?>
          </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')): ?>
          <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <?= session()->getFlashdata('success') ?>
          </div>
        <?php endif; ?>

        <form action="<?= base_url('login') ?>" method="post">
          <?= csrf_field() ?>

          <div class="input-group mb-3">
            <input type="text" name="username" class="form-control" placeholder="Username" value="<?= old('username') ?>" required autofocus>
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-user"></span>
              </div>
            </div>
          </div>

          <div class="input-group mb-3">
            <input type="password" name="password" class="form-control" placeholder="Password" required>
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-eye" id="togglePassword" style="cursor: pointer;"></span>
              </div>
            </div>
          </div>

          <a href="javascript:void(0)" id="forgotPassword">
            <p>Lupa Password?</p>
          </a>

          <div class="row">
            <div class="col-8">
              <!-- <div class="icheck-primary">
                <input type="checkbox" id="remember">
                <label for="remember">
                  Remember Me
                </label>
              </div> -->
            </div>
            <div class="col-4">
              <button type="submit" class="btn btn-primary btn-block">Sign In</button>
            </div>
          </div>
        </form>

      </div>
    </div>

    <div class="text-center mt-3 text-muted">
      <p>Copyright &copy; <?= Date("Y") ?></p>
      <!-- <p>Halaman ini dimuat selama <strong id="elapsedTime"></strong> detik</p> -->
      <!-- <small>Default credentials: admin / admin</small> -->
    </div>
  </div>

  <!-- jQuery -->
  <script src="<?= base_url('public/libraries/adminlte/plugins/jquery/jquery.min.js') ?>"></script>
  <!-- Bootstrap 4 -->
  <script src="<?= base_url('public/libraries/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
  <!-- AdminLTE App -->
  <script src="<?= base_url('public/libraries/adminlte/dist/js/adminlte.min.js') ?>"></script>
  <script>
    const API_URL = `<?= config('Api')->apiURL ?>`
    let isRequestInProgress = false;

    document.addEventListener('DOMContentLoaded', function() {
      const togglePassword = document.querySelector('#togglePassword');
      const password = document.querySelector('input[name="password"]');

      togglePassword.addEventListener('click', function(e) {
        // toggle the type attribute
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        // toggle the eye / eye slash icon
        this.classList.toggle('fa-eye-slash');
      });


      $('#forgotPassword').on('click', function(e) {
        e.preventDefault();


        if (isRequestInProgress) {
          // If a request is already in progress, ignore this click
          return;
        }

        const username = $('input[name="username"]').val().trim();

        // validasi sederhana di client
        if (!username) {
          showNotification('warning', 'Username wajib diisi untuk reset password.');
          $('input[name="username"]').focus();
          return;
        }

        const data = {};
        data.username = username;

        isRequestInProgress = true;

        $.ajax({
          url: `${API_URL}/forgot-password`,
          method: 'POST',
          dataType: 'json',
          contentType: 'application/json',
          data: JSON.stringify(data),
          success: function(res) {
            // res.message dari backend
            showNotification('success', res.message || 'Jika username terdaftar, link reset sudah dikirim ke email.');

            isRequestInProgress = false;
          },
          error: function(xhr) {
            // default message
            let msg = 'Terjadi kesalahan. Coba lagi.';

            // kalau backend balikin JSON error validasi
            if (xhr.status === 422 && xhr.responseJSON) {
              if (xhr.responseJSON.errors && xhr.responseJSON.errors.username) {
                msg = xhr.responseJSON.errors.username;
              } else if (xhr.responseJSON.message) {
                msg = xhr.responseJSON.message;
              }
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
              msg = xhr.responseJSON.message;
            }

            showNotification('danger', msg);

            isRequestInProgress = false;

            $('#forgotPassword').on('click', arguments.callee); // Restore the event handler
            $('#forgotPassword').css('pointer-events', 'auto');
          }
        });
      });

      function showNotification(type, message) {
        // remove previous notification
        $('.alert').remove();

        // create new notification
        const notification = $('<div class="alert alert-' + type + ' alert-dismissible">' +
          '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' +
          message +
          '</div>');

        // append to login box
        $('.login-box-msg').after(notification);

        // auto remove notification after 3 seconds
        setTimeout(function() {
          notification.remove();
        }, 3000);
      }

    });
  </script>
</body>

</html>