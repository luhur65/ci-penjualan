<?php

use App\Helpers\MenuHelper;

?>
<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <div class="sidebar">
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="image">
        <img src="<?= base_url('public/libraries/adminlte/dist/img/user2-160x160.jpg') ?>" class="img-circle elevation-2" alt="User Image">
      </div>
      <div class="info">
        <a href="#" class="d-block"><?=  session()->get('user')['username'] ?? 'Dharma' ?></a>
      </div>
    </div>

    <div class="form-inline">
      <div class="input-group" data-widget="sidebar-search" data-min-length="9999">
        <input id="search" class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search" onpaste="return false">
        <div class="input-group-append">
          <button class="btn btn-sidebar">
            <i class="fas fa-search fa-fw"></i>
          </button>
        </div>
      </div>
    </div>

    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent" data-widget="treeview" role="menu" data-accordion="false">
        <!-- Tempat menu list -->
      </ul>
    </nav>

  </div>
  <div class="text-center text-white text-small mt-3">
    version <?= config('App')->version ?>
  </div>
</aside>