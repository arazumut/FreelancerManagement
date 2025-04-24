<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="container">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card bg-white shadow-sm rounded border-0 my-4" data-aos="fade-up" data-aos-duration="1000">
                <div class="card-header bg-primary text-white">
                    <h2 class="h4 mb-0"><i class="fas fa-plus-circle me-2"></i>Yeni Proje Ekle</h2>
                </div>
                <div class="card-body">
                    <form action="<?php echo SITE_URL; ?>/projects/add" method="post" class="needs-validation" novalidate>
                        <input type="hidden" name="csrf_token" value="<?php echo $data['csrf_token']; ?>">
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Proje Başlığı</label>
                            <input type="text" class="form-control <?php echo (!empty($data['title_err'])) ? 'is-invalid' : ''; ?>" id="title" name="title" value="<?php echo $data['title']; ?>" placeholder="Örn: Web Sitesi Tasarımı" required>
                            <div class="invalid-feedback">
                                <?php echo $data['title_err']; ?>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="category_id" class="form-label">Kategori</label>
                            <select class="form-select <?php echo (!empty($data['category_id_err'])) ? 'is-invalid' : ''; ?>" id="category_id" name="category_id" required>
                                <option value="" selected disabled>Kategori Seçin</option>
                                <?php foreach($data['categories'] as $category) : ?>
                                    <option value="<?php echo $category->id; ?>" <?php echo ($data['category_id'] == $category->id) ? 'selected' : ''; ?>>
                                        <?php echo $category->name; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">
                                <?php echo $data['category_id_err']; ?>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="min_budget" class="form-label">Minimum Bütçe (TL)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lira-sign"></i></span>
                                    <input type="number" class="form-control <?php echo (!empty($data['min_budget_err'])) ? 'is-invalid' : ''; ?>" id="min_budget" name="min_budget" value="<?php echo $data['min_budget']; ?>" placeholder="En düşük tutar" min="1" step="0.01" required>
                                    <div class="invalid-feedback">
                                        <?php echo $data['min_budget_err']; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="max_budget" class="form-label">Maksimum Bütçe (TL)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lira-sign"></i></span>
                                    <input type="number" class="form-control <?php echo (!empty($data['max_budget_err'])) ? 'is-invalid' : ''; ?>" id="max_budget" name="max_budget" value="<?php echo $data['max_budget']; ?>" placeholder="En yüksek tutar" min="1" step="0.01" required>
                                    <div class="invalid-feedback">
                                        <?php echo $data['max_budget_err']; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="deadline" class="form-label">Son Teslim Tarihi</label>
                            <input type="date" class="form-control <?php echo (!empty($data['deadline_err'])) ? 'is-invalid' : ''; ?>" id="deadline" name="deadline" value="<?php echo $data['deadline']; ?>" required>
                            <div class="invalid-feedback">
                                <?php echo $data['deadline_err']; ?>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Proje Açıklaması</label>
                            <textarea class="form-control <?php echo (!empty($data['description_err'])) ? 'is-invalid' : ''; ?>" id="description" name="description" rows="6" placeholder="Projenizi detaylı bir şekilde açıklayın..." required><?php echo $data['description']; ?></textarea>
                            <div class="invalid-feedback">
                                <?php echo $data['description_err']; ?>
                            </div>
                            <div class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i> İyi bir açıklama, doğru freelancer'ı bulmanızı kolaylaştırır.
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="<?php echo SITE_URL; ?>/projects" class="btn btn-light btn-ripple me-md-2"><i class="fas fa-arrow-left me-1"></i>Geri Dön</a>
                            <button type="submit" class="btn btn-primary btn-ripple"><i class="fas fa-paper-plane me-1"></i>Projeyi Yayınla</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container mb-5">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="alert alert-info" data-aos="fade-up" data-aos-delay="200">
                <h5 class="alert-heading"><i class="fas fa-lightbulb me-2"></i>İpuçları</h5>
                <hr>
                <ul class="mb-0">
                    <li>Projeniz için açık ve anlaşılır bir başlık seçin.</li>
                    <li>Bütçenizi gerçekçi belirlemeye çalışın.</li>
                    <li>Gereksinimleri ve beklentileri açıkça belirtin.</li>
                    <li>Belirli bir son teslim tarihi belirlemek, daha kaliteli teklifler almanızı sağlar.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?> 