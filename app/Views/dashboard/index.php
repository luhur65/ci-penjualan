<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<div class="container-fluid">

  <h3>Selamat datang, <?= $user['fullname']; ?></h3>
  
  <section class="content">
    <div class="row">
      <!-- card box -->
      
        <div class="card">
          <div class="card-body">
            <h2>2</h2>
            <h3>Pengguna Aktif</h3>

          </div>
        </div>
        
    </div>
  </section>

</div>



<?= $this->endSection() ?>