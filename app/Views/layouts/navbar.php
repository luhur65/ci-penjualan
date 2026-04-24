<nav class="main-header navbar navbar-expand navbar-white navbar-light nav-compact" style="min-height: 48px;">
  <ul class="navbar-nav align-items-center">
    <li class="nav-item">
      <a id="sidebarButton" class="nav-link sidebars" data-widget="pushmenu" data-auto-collapse-size="0" href="#" role="button">
        <i class="fas fa-bars"></i>
      </a>
    </li>
  </ul>

  <img src="<?= base_url('public/libraries/images/taslogo.png?version=' . config('App')->version) ?>"
    alt="AdminLTE Logo" class="brand-image"
    style="width: 25px; margin-right: 5px;">
  <strong><?= getenv('app.name') ?></strong>

  <ul class="navbar-nav ml-auto align-items-center">
    <li class="nav-item">
      <div class="datetime-place text-right" style="font-size: 12px; line-height: 1.2; color: #495057;">
        <span class="date-place"></span>
        /
        <span class="time-place"></span>
      </div>
    </li>
    <li class="nav-item dropdown ml-2">
      <a class="nav-link" data-toggle="dropdown" href="#" id="notifBell" style="position:relative; padding: 8px 12px;">
        <i class="far fa-bell" style="font-size: 16px;"></i>
        <span class="badge badge-danger navbar-badge" id="notifBadge" style="display:none; position:absolute; top:2px; right:2px; font-size:9px; padding: 2px 4px; min-width:16px; height:16px; line-height:12px; border-radius:50%; text-align:center;">0</span>
      </a>
      <div class="dropdown-menu dropdown-menu-right" id="notifDropdown" style="width:350px; max-height:400px; overflow:hidden; padding:0;">

        <!-- Header — sticky, tidak ikut scroll -->
        <div class="d-flex align-items-center justify-content-between px-3 py-2"
          style="border-bottom: 1px solid #e8ecf0; background:#f8f9fa; position:sticky; top:0; z-index:1;">
          <span style="font-size:13px; font-weight:600; color:#495057;">
            <i class="far fa-bell mr-1"></i> Notifikasi
          </span>
          <span id="notifBadgeText" class="badge badge-primary" style="display:none;">0 baru</span>
        </div>

        <!-- List — yang scroll -->
        <div id="notifList" style="max-height:340px; overflow-y:auto;">
          <div class="dropdown-item text-center text-muted">Tidak ada notifikasi</div>
        </div>

      </div>
    </li>
  </ul>
</nav>