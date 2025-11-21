<?php
  $nama = $_GET['nama'] ?? 'nama tidak ditemukan';
  $umur = $_GET['umur'] ?? 'umur tidak ditemukan';
?>


<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>

<div class="container-fluid">

  <h1>About</h1>
  <p><?= $nama; ?></p>
  <p><?= $umur; ?></p>

</div>


<?= $this->endSection() ?>