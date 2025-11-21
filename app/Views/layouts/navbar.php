<nav class="main-header navbar navbar-expand navbar-white navbar-light nav-compact">
  <ul class="navbar-nav">
    <li class="nav-item">
      <a id="sidebarButton" class="nav-link sidebars" data-widget="pushmenu" data-auto-collapse-size="0" href="#" role="button"><i class="fas fa-bars"></i></a>
    </li>
  </ul>

  <img src="<?= base_url('public/libraries/images/taslogo.png?version=' . config('App')->version) ?>" alt="AdminLTE Logo" class="brand-image" style="width: 25px; margin-right: 5px;">
  <strong><?= getenv('app.name') ?></strong>

  <ul class="navbar-nav ml-auto">
    <li class="nav-item">
      <div class="datetime-place text-right">
        <span class="date-place"></span>
        /
        <span class="time-place"></span>
      </div>
    </li>
  </ul>
</nav>