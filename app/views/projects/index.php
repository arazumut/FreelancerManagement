<?php require APPROOT . '/views/inc/header.php'; ?>

<!-- Hero Section -->
<div class="hero-section text-center text-white d-flex align-items-center justify-content-center" style="background: linear-gradient(rgba(0, 0, 0, 0.65), rgba(0, 0, 0, 0.7)), url('https://images.unsplash.com/photo-1522071820081-009f0129c71c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80') center/cover no-repeat fixed; height: 90vh;">
    <div class="container" data-aos="fade-up" data-aos-duration="1200">
        <h1 class="display-4 fw-bold mb-4">Freelancer Platformu'na Hoş Geldiniz</h1>
        <p class="lead mb-5 fs-4">Yeteneklerinizi sergileyin, projelere teklif verin veya uzmanları bulun</p>
        <div class="mt-4 d-flex justify-content-center gap-3 flex-wrap">
            <?php if(!isLoggedIn()): ?>
                <a href="<?php echo SITE_URL; ?>/users/register" class="btn btn-light btn-lg btn-hero">Kayıt Ol</a>
                <a href="<?php echo SITE_URL; ?>/users/login" class="btn btn-outline-light btn-lg btn-hero">Giriş Yap</a>
            <?php else: ?>
                <?php if(isFreelancer()): ?>
                    <a href="<?php echo SITE_URL; ?>/projects" class="btn btn-light btn-lg btn-hero">Projeleri Keşfet</a>
                <?php elseif(isEmployer()): ?>
                    <a href="<?php echo SITE_URL; ?>/projects/add" class="btn btn-light btn-lg btn-hero">Yeni Proje Ekle</a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- İstatistikler -->
<div class="container mt-5 mb-5">
    <div class="row text-center">
        <?php
            $stats = [
                ['icon' => 'fas fa-project-diagram', 'label' => 'Toplam Proje', 'value' => $data['stats']['project_count']],
                ['icon' => 'fas fa-tasks', 'label' => 'Aktif Proje', 'value' => $data['stats']['active_project_count']],
                ['icon' => 'fas fa-user-tie', 'label' => 'Freelancer', 'value' => $data['stats']['freelancer_count']],
                ['icon' => 'fas fa-briefcase', 'label' => 'İşveren', 'value' => $data['stats']['employer_count']],
            ];
            foreach ($stats as $s):
        ?>
        <div class="col-md-3 mb-4">
            <div class="stats-box p-4 shadow rounded">
                <i class="<?php echo $s['icon']; ?> fa-2x mb-2 text-primary"></i>
                <h3 class="fw-bold"><?php echo $s['value']; ?></h3>
                <p><?php echo $s['label']; ?></p>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Son Projeler Slider -->
<div class="container mb-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Son Eklenen Projeler</h2>
        <a href="<?php echo SITE_URL; ?>/projects" class="btn btn-primary">Tüm Projeleri Gör</a>
    </div>
    <?php if(!empty($data['projects'])): ?>
        <div class="row flex-nowrap overflow-auto" style="gap: 20px;">
            <?php foreach($data['projects'] as $project): ?>
                <div class="col-md-4 flex-shrink-0">
                    <div class="card project-card h-100 shadow-sm border-0">
                        <div class="card-header d-flex justify-content-between align-items-center bg-light">
                            <span class="badge <?php echo $project->status == 'active' ? 'badge-active' : ($project->status == 'completed' ? 'badge-completed' : 'badge-canceled'); ?>">
                                <?php echo ucfirst($project->status); ?>
                            </span>
                            <span class="badge bg-primary text-white"><?php echo number_format($project->min_budget, 2); ?> TL - <?php echo number_format($project->max_budget, 2); ?> TL</span>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $project->title; ?></h5>
                            <p class="card-text"><?php echo substr($project->description, 0, 100) . '...'; ?></p>
                        </div>
                        <div class="card-footer d-flex justify-content-between align-items-center">
                            <small class="text-muted"><i class="fas fa-user"></i> <?php echo $project->employer_name; ?></small>
                            <a href="<?php echo SITE_URL; ?>/projects/view/<?php echo $project->id; ?>" class="btn btn-sm btn-outline-primary">Detayları Gör</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">Henüz proje bulunmamaktadır.</div>
    <?php endif; ?>
</div>

<!-- Freelancer Slider -->
<?php if(!empty($data['freelancers'])): ?>
<div class="container mb-5">
    <h2 class="mb-3">En İyi Freelancer'lar</h2>
    <div class="row flex-nowrap overflow-auto" style="gap: 20px;">
        <?php foreach($data['freelancers'] as $freelancer): ?>
        <div class="col-md-3 flex-shrink-0">
            <div class="card profile-card shadow-sm border-0 h-100">
                <div class="card-header bg-light text-center">
                    <div class="profile-avatar mb-2"><i class="fas fa-user fa-2x"></i></div>
                    <h5><?php echo $freelancer->name; ?></h5>
                    <div class="star-rating">
                        <?php
                        $rating = $freelancer->rating;
                        for($i = 1; $i <= 5; $i++) {
                            echo $i <= $rating ? '<i class="fas fa-star text-warning"></i>' : '<i class="far fa-star text-muted"></i>';
                        }
                        ?>
                        <span class="ms-1">(<?php echo $rating; ?>)</span>
                    </div>
                </div>
                <div class="card-body">
                    <p class="card-text"><?php echo strlen($freelancer->bio) > 100 ? substr($freelancer->bio, 0, 100) . '...' : $freelancer->bio; ?></p>
                    <p class="card-text"><small class="text-muted">Yetenekler: <?php echo $freelancer->skills; ?></small></p>
                </div>
                <div class="card-footer">
                    <a href="<?php echo SITE_URL; ?>/users/profile/<?php echo $freelancer->id; ?>" class="btn btn-sm btn-outline-primary w-100">Profili Görüntüle</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Nasıl Çalışır -->
<div class="container mb-5">
    <h2 class="text-center mb-4">Nasıl Çalışır?</h2>
    <div class="row text-center">
        <?php
            $steps = [
                ['icon' => 'fa-file-alt', 'title' => 'Proje Oluştur veya Bul', 'desc' => 'İşveren olarak projenizi ekleyin veya freelancer olarak uygun projeleri bulun.'],
                ['icon' => 'fa-handshake', 'title' => 'Teklif Ver veya Kabul Et', 'desc' => 'Freelancer teklif verir, işveren en uygun teklifi kabul eder.'],
                ['icon' => 'fa-check-circle', 'title' => 'Tamamla ve Değerlendir', 'desc' => 'Proje bittiğinde ödeme yapılır, taraflar birbirini değerlendirir.']
            ];
            foreach ($steps as $s):
        ?>
        <div class="col-md-4 mb-4">
            <div class="bg-light p-4 rounded shadow-sm">
                <div class="rounded-circle bg-primary text-white mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                    <i class="fas <?php echo $s['icon']; ?> fa-2x"></i>
                </div>
                <h4><?php echo $s['title']; ?></h4>
                <p><?php echo $s['desc']; ?></p>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?>
