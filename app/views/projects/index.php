<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Projeler</h1>
        </div>
        <div class="col-md-4 text-end">
            <?php if(isLoggedIn() && isEmployer()): ?>
                <a href="<?php echo SITE_URL; ?>/projects/add" class="btn btn-primary"><i class="fas fa-plus"></i> Yeni Proje Ekle</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Filtreler -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Filtreler</h5>
        </div>
        <div class="card-body">
            <form action="<?php echo SITE_URL; ?>/projects" method="get" class="row g-3">
                <div class="col-md-3">
                    <label for="category" class="form-label">Kategori</label>
                    <select name="category" id="category" class="form-select">
                        <option value="">Tümü</option>
                        <?php foreach($data['categories'] as $category): ?>
                            <option value="<?php echo $category->id; ?>" <?php echo ($data['filter']['category'] == $category->id) ? 'selected' : ''; ?>>
                                <?php echo $category->name; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="min_budget" class="form-label">Min. Bütçe</label>
                    <input type="number" name="min_budget" id="min_budget" class="form-control" value="<?php echo $data['filter']['min_budget']; ?>">
                </div>
                <div class="col-md-3">
                    <label for="max_budget" class="form-label">Max. Bütçe</label>
                    <input type="number" name="max_budget" id="max_budget" class="form-control" value="<?php echo $data['filter']['max_budget']; ?>">
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Durum</label>
                    <select name="status" id="status" class="form-select">
                        <option value="active" <?php echo ($data['filter']['status'] == 'active') ? 'selected' : ''; ?>>Aktif</option>
                        <option value="all" <?php echo ($data['filter']['status'] == 'all') ? 'selected' : ''; ?>>Tümü</option>
                    </select>
                </div>
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-primary">Filtrele</button>
                    <a href="<?php echo SITE_URL; ?>/projects" class="btn btn-secondary">Sıfırla</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Proje Listesi -->
    <?php if(empty($data['projects'])): ?>
        <div class="alert alert-info">Filtrelere uygun proje bulunamadı.</div>
    <?php else: ?>
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
                            <p class="card-text"><small class="text-muted"><i class="fas fa-clock me-1"></i><?php echo date('d.m.Y', strtotime($project->deadline)); ?> tarihine kadar</small></p>
                        </div>
                        <div class="card-footer d-flex justify-content-between align-items-center">
                            <small class="text-muted"><i class="fas fa-user me-1"></i><?php echo isset($project->employer_name) ? $project->employer_name : 'İşveren'; ?></small>
                            <a href="<?php echo SITE_URL; ?>/projects/showProject/<?php echo $project->id; ?>" class="btn btn-sm btn-outline-primary">Detayları Gör</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?> 