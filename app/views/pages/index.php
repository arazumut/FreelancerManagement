<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="hero-section text-center">
    <div class="container">
        <h1>Freelancer Platformu'na Hoş Geldiniz</h1>
        <p class="lead">Yeteneklerinizi sergileyebileceğiniz veya ihtiyacınız olan projeleri yayınlayabileceğiniz bir platform</p>
        <div class="mt-4">
            <?php if(!isLoggedIn()): ?>
                <a href="<?php echo SITE_URL; ?>/users/register" class="btn btn-light btn-lg me-2">Kayıt Ol</a>
                <a href="<?php echo SITE_URL; ?>/users/login" class="btn btn-outline-light btn-lg">Giriş Yap</a>
            <?php else: ?>
                <?php if(isFreelancer()): ?>
                    <a href="<?php echo SITE_URL; ?>/projects" class="btn btn-light btn-lg">Projeleri Keşfet</a>
                <?php elseif(isEmployer()): ?>
                    <a href="<?php echo SITE_URL; ?>/projects/add" class="btn btn-light btn-lg">Yeni Proje Ekle</a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="container">
    <!-- İstatistikler -->
    <div class="row text-center mb-5">
        <div class="col-md-3">
            <div class="stats-box">
                <i class="fas fa-project-diagram"></i>
                <h3><?php echo $data['stats']['project_count']; ?></h3>
                <p>Toplam Proje</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-box">
                <i class="fas fa-tasks"></i>
                <h3><?php echo $data['stats']['active_project_count']; ?></h3>
                <p>Aktif Proje</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-box">
                <i class="fas fa-user-tie"></i>
                <h3><?php echo $data['stats']['freelancer_count']; ?></h3>
                <p>Freelancer</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-box">
                <i class="fas fa-briefcase"></i>
                <h3><?php echo $data['stats']['employer_count']; ?></h3>
                <p>İşveren</p>
            </div>
        </div>
    </div>

    <!-- Son Eklenen Projeler -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>Son Eklenen Projeler</h2>
                <a href="<?php echo SITE_URL; ?>/projects" class="btn btn-primary">Tüm Projeleri Gör</a>
            </div>
            
            <?php if(!empty($data['projects'])): ?>
                <div class="row">
                    <?php foreach($data['projects'] as $project): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card project-card h-100">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <span class="badge <?php echo $project->status == 'active' ? 'badge-active' : ($project->status == 'completed' ? 'badge-completed' : 'badge-canceled'); ?>">
                                        <?php echo $project->status == 'active' ? 'Aktif' : ($project->status == 'completed' ? 'Tamamlandı' : 'İptal Edildi'); ?>
                                    </span>
                                    <span class="badge badge-budget"><?php echo number_format($project->min_budget, 2); ?> TL - <?php echo number_format($project->max_budget, 2); ?> TL</span>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $project->title; ?></h5>
                                    <p class="card-text"><?php echo strlen($project->description) > 100 ? substr($project->description, 0, 100) . '...' : $project->description; ?></p>
                                    <p class="card-text"><small class="text-muted"><i class="fas fa-tags me-1"></i><?php echo $project->category_name; ?></small></p>
                                </div>
                                <div class="card-footer d-flex justify-content-between align-items-center">
                                    <small class="text-muted"><i class="fas fa-user me-1"></i><?php echo $project->employer_name; ?></small>
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
    </div>

    <!-- En İyi Freelancer'lar -->
    <?php if(!empty($data['freelancers'])): ?>
        <div class="row mb-4">
            <div class="col-md-12">
                <h2 class="mb-3">En İyi Freelancer'lar</h2>
                <div class="row">
                    <?php foreach($data['freelancers'] as $freelancer): ?>
                        <div class="col-md-3 mb-4">
                            <div class="card profile-card h-100">
                                <div class="card-header">
                                    <div class="profile-avatar">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <h5 class="mb-0"><?php echo $freelancer->name; ?></h5>
                                    <div class="star-rating">
                                        <?php
                                        $rating = $freelancer->rating;
                                        for($i = 1; $i <= 5; $i++) {
                                            if($i <= $rating) {
                                                echo '<i class="fas fa-star"></i>';
                                            } elseif($i - 0.5 <= $rating) {
                                                echo '<i class="fas fa-star-half-alt"></i>';
                                            } else {
                                                echo '<i class="far fa-star"></i>';
                                            }
                                        }
                                        ?>
                                        <span class="ms-1">(<?php echo $rating; ?>)</span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <p class="card-text"><?php echo !empty($freelancer->bio) ? (strlen($freelancer->bio) > 100 ? substr($freelancer->bio, 0, 100) . '...' : $freelancer->bio) : 'Bu kullanıcı henüz bir biyografi eklememiş.'; ?></p>
                                    <?php if(!empty($freelancer->skills)): ?>
                                        <p class="card-text"><small class="text-muted">Yetenekler: <?php echo $freelancer->skills; ?></small></p>
                                    <?php endif; ?>
                                </div>
                                <div class="card-footer">
                                    <a href="<?php echo SITE_URL; ?>/users/profile/<?php echo $freelancer->id; ?>" class="btn btn-sm btn-outline-primary w-100">Profili Görüntüle</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Nasıl Çalışır -->
    <div class="row mb-5">
        <div class="col-md-12">
            <h2 class="text-center mb-4">Nasıl Çalışır?</h2>
            <div class="row">
                <div class="col-md-4 text-center">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-file-alt fa-2x"></i>
                    </div>
                    <h4>1. Proje Oluştur veya Bul</h4>
                    <p>İşveren olarak projenizi ekleyin veya freelancer olarak uygun projeleri bulun.</p>
                </div>
                <div class="col-md-4 text-center">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-handshake fa-2x"></i>
                    </div>
                    <h4>2. Teklif Ver veya Kabul Et</h4>
                    <p>Freelancer'lar teklif verir, işverenler en uygun teklifi seçer ve sözleşme oluşturulur.</p>
                </div>
                <div class="col-md-4 text-center">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                    <h4>3. Tamamla ve Değerlendir</h4>
                    <p>Proje tamamlandığında ödeme yapılır ve taraflar birbirlerini değerlendirir.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?> 