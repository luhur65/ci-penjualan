<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Stimulsoft Viewer Test</h3>
                </div>
                <div class="card-body">
                    <div id="viewerContent"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<link href="<?= base_url('public/libraries/stimulsoft/Css/stimulsoft.viewer.office2013.whiteblue.css') ?>" rel="stylesheet">
<script src="<?= base_url('public/libraries/stimulsoft/Scripts/stimulsoft.reports.js') ?>" type="text/javascript"></script>
<script src="<?= base_url('public/libraries/stimulsoft/Scripts/stimulsoft.viewer.js') ?>" type="text/javascript"></script>

<script type="text/javascript">
    var options = new Stimulsoft.Viewer.StiViewerOptions();
    var viewer = new Stimulsoft.Viewer.StiViewer(options, "StiViewer", false);

    // ==========================================
    // MASUKKAN LICENSE KEY STIMULSOFT ANDA DISINI
    // ==========================================
    Stimulsoft.Base.StiLicense.key = "YOUR_LICENSE_KEY_HERE";

    // Bikin report kosong untuk ngetes license key
    var report = new Stimulsoft.Report.StiReport();
    
    // Assign report to viewer
    viewer.report = report;
    viewer.renderHtml("viewerContent");
</script>
<?= $this->endSection() ?>
