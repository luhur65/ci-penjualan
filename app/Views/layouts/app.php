<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= getenv('app.name') ?: config('app.name') ?> | Dharma Bakti Situmorang</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?= base_url('public/libraries/adminlte/plugins/fontawesome-free/css/all.min.css') ?>">

  <!-- Theme style -->
  <link rel="stylesheet" href="<?= base_url('public/libraries/adminlte/dist/css/adminlte-customized.min.css?version=' . config('App')->version) ?>">

  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="<?= base_url('public/libraries/adminlte/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') ?>">

  <!-- Select2 -->
  <link rel="stylesheet" href="<?= base_url('public/libraries/adminlte/plugins/select2/css/select2.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('public/libraries/adminlte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') ?>">

  <!-- JqGrid -->
  <link rel="stylesheet" href="<?= base_url('public/libraries/jqgrid/582/css/ui.jqgrid-bootstrap4.css') ?>" />

  <!-- Jquery UI -->
  <link rel="stylesheet" href="<?= base_url('public/libraries/jquery-ui/1.13.1/jquery-ui.min.css') ?>">

  <!-- Dropzone -->
  <link rel="stylesheet" href="<?= base_url('public/libraries/adminlte/plugins/dropzone/min/dropzone.min.css') ?>">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="<?= base_url('public/libraries/css/pager.css?version=' . config('App')->version) ?>">
  <link rel="stylesheet" href="<?= base_url('public/libraries/css/sidebar.css?version=' . config('App')->version) ?>">
  <link rel="stylesheet" href="<?= base_url('public/libraries/css/style.css?version=' . config('App')->version) ?>">
</head>

<body class="hold-transition sidebar-collapse layout-fixed">
  <div class="modal-loader d-none">
    <div class="modal-loader-content d-flex align-items-center justify-content-center">
      <img src="<?= base_url('public/libraries/images/loading-blue.gif?version=' . config('App')->version) ?>" rel="preload">
      Loading...
    </div>
  </div>

  <div class="loader" id="loader">
    <img src="<?= base_url('public/libraries/images/hour-glass.gif?version=' . config('App')->version) ?>" rel="preload">
    <span>Loading</span>
  </div>

  <div class="processing-loader d-none" id="processingLoader">
    <img src="<?= base_url('public/libraries/images/loading-color.gif?version=' . config('App')->version) ?>" rel="preload">
    <span>Processing</span>
  </div>

  <div class="wrapper">
    <?= $this->include('layouts/navbar') ?>
    <?= $this->include('layouts/sidebar') ?>

    <div class="content-wrapper">
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-12">
              <ol class="breadcrumb">
                <li class="breadcrumb-item active">
                  <?= ucfirst(service('uri')->getSegment(1)) ?>
                </li>
              </ol>
            </div>
          </div>
        </div>
      </div>

      <section class="content">
        <?= $this->renderSection('content') ?>
      </section>
    </div>

    <footer class="main-footer">
      <strong>Design &copy; by <a href="#">DHARMA BAKTI SITUMORANG</a>.</strong>
    </footer>
  </div>

  <!-- jQuery -->
  <script src="<?= base_url('public/libraries/adminlte/plugins/jquery/jquery.min.js') ?>"></script>

  <!-- Highlight -->
  <script src="<?= base_url('public/libraries/js/highlight.js') ?>"></script>

  <!-- Bootstrap 4 -->
  <script src="<?= base_url('public/libraries/adminlte/plugins/bootstrap/js/bootstrap.min.js') ?>"></script>

  <!-- ChartJS -->
  <script src="<?= base_url('public/libraries/adminlte/plugins/chart.js/Chart.min.js') ?>"></script>

  <!-- overlayScrollbars -->
  <script src="<?= base_url('public/libraries/adminlte/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') ?>"></script>

  <!-- AdminLTE App -->
  <script src="<?= base_url('public/libraries/adminlte/dist/js/adminlte.js') ?>"></script>

  <!-- Select2 -->
  <script src="<?= base_url('public/libraries/adminlte/plugins/select2/js/select2.min.js') ?>"></script>

  <!-- JqGrid -->
  <script src="<?= base_url('public/libraries/jqgrid/582/js/i18n/grid.locale-en.js') ?>" type="text/javascript"></script>
  <script src="<?= base_url('public/libraries/jqgrid/582/js/jquery.jqGrid.min.js') ?>" type="text/javascript"></script>

  <!-- Autonumeric -->
  <script src="<?= base_url('public/libraries/autonumeric/4.5.4/autonumeric.min.js') ?>" type="text/javascript"></script>

  <!-- Inputmask -->
  <script src="<?= base_url('public/libraries/inputmask/5.0.6/jquery.inputmask.min.js') ?>" type="text/javascript"></script>

  <!-- jQuery UI -->
  <script src="<?= base_url('public/libraries/jquery-ui/1.13.1/jquery-ui.min.js') ?>"></script>

  <!-- Dropzone -->
  <script src="<?= base_url('public/libraries/adminlte/plugins/dropzone/min/dropzone.min.js') ?>"></script>

  <!-- Custom JS -->
  <script>
    const APP_URL = `<?= base_url() ?>`
    const API_URL = `<?= env('api.url') ?>`
  </script>
  <script src="<?= base_url('public/libraries/js/extended-jqgrid.js?version=' . config('App')->version) ?>"></script>
  <script src="<?= base_url('public/libraries/js/navbar.js?version=' . config('App')->version) ?>"></script>
  <script src="<?= base_url('public/libraries/js/sidebar.js?version=' . config('App')->version) ?>"></script>
  <script src="<?= base_url('public/libraries/js/script.js?version=' . config('App')->version) ?>"></script>
  <?= $this->renderSection('scripts') ?>
</body>

</html>