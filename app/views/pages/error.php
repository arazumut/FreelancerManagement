<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body py-5">
                    <h1 class="display-1 text-danger fw-bold mb-3"><?php echo $data['code']; ?></h1>
                    <h2 class="mb-4"><?php echo $data['message']; ?></h2>
                    <p class="text-muted mb-4">Aradığınız sayfa bulunamadı veya erişim yetkiniz yok.</p>
                    <a href="<?php echo SITE_URL; ?>" class="btn btn-primary px-4 py-2">Ana Sayfaya Dön</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?> 