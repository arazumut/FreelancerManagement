<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Kullanıcı Düzenle</h5>
                </div>
                <div class="card-body">
                    <?php if(isset($_SESSION['flash_messages']['admin_error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['flash_messages']['admin_error']['message']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['flash_messages']['admin_error']); ?>
                    <?php endif; ?>
                    
                    <form action="<?php echo SITE_URL; ?>/admin/editUser/<?php echo $data['id']; ?>" method="post">
                        <input type="hidden" name="csrf_token" value="<?php echo $data['csrf_token']; ?>">
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Ad Soyad</label>
                                    <input type="text" name="name" id="name" class="form-control" value="<?php echo $data['name']; ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">E-posta</label>
                                    <input type="email" name="email" id="email" class="form-control" value="<?php echo $data['email']; ?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="role">Rol</label>
                                    <select name="role" id="role" class="form-control">
                                        <option value="admin" <?php echo ($data['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                                        <option value="freelancer" <?php echo ($data['role'] == 'freelancer') ? 'selected' : ''; ?>>Freelancer</option>
                                        <option value="employer" <?php echo ($data['role'] == 'employer') ? 'selected' : ''; ?>>İşveren</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mt-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="status" name="status" <?php echo ($data['status'] == 'active') ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="status">Hesap Aktif</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="<?php echo SITE_URL; ?>/admin/users" class="btn btn-secondary">Geri Dön</a>
                            <button type="submit" class="btn btn-primary">Kaydet</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?> 