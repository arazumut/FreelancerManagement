<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Proje Düzenle</h5>
                </div>
                <div class="card-body">
                    <?php if(isset($_SESSION['flash_messages']['admin_error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['flash_messages']['admin_error']['message']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['flash_messages']['admin_error']); ?>
                    <?php endif; ?>
                    
                    <form action="<?php echo SITE_URL; ?>/admin/editProject/<?php echo $data['id']; ?>" method="post">
                        <input type="hidden" name="csrf_token" value="<?php echo $data['csrf_token']; ?>">
                        
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="title">Proje Başlığı</label>
                                    <input type="text" name="title" id="title" class="form-control" value="<?php echo $data['title']; ?>" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="category_id">Kategori</label>
                                    <select name="category_id" id="category_id" class="form-control">
                                        <?php foreach($data['categories'] as $category) : ?>
                                            <option value="<?php echo $category->id; ?>" <?php echo ($data['category_id'] == $category->id) ? 'selected' : ''; ?>>
                                                <?php echo $category->name; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="description">Proje Açıklaması</label>
                                    <textarea name="description" id="description" class="form-control" rows="5" required><?php echo $data['description']; ?></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="status">Durum</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="active" <?php echo ($data['status'] == 'active') ? 'selected' : ''; ?>>Aktif</option>
                                        <option value="completed" <?php echo ($data['status'] == 'completed') ? 'selected' : ''; ?>>Tamamlandı</option>
                                        <option value="cancelled" <?php echo ($data['status'] == 'cancelled') ? 'selected' : ''; ?>>İptal Edildi</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="min_budget">Minimum Bütçe (₺)</label>
                                    <input type="number" name="min_budget" id="min_budget" class="form-control" value="<?php echo $data['min_budget']; ?>" required min="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="max_budget">Maksimum Bütçe (₺)</label>
                                    <input type="number" name="max_budget" id="max_budget" class="form-control" value="<?php echo $data['max_budget']; ?>" required min="0">
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="<?php echo SITE_URL; ?>/admin/projects" class="btn btn-secondary">Geri Dön</a>
                            <button type="submit" class="btn btn-primary">Kaydet</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?> 