<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card form-card">
            <div class="card-header">
                <h2>Kayıt Ol</h2>
                <p>Platformumuza üye olmak için formu doldurun</p>
            </div>
            <div class="card-body">
                <form action="<?php echo SITE_URL; ?>/users/register" method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo $data['csrf_token']; ?>">
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Ad Soyad:</label>
                        <input type="text" name="name" id="name" class="form-control <?php echo (!empty($data['name_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['name']; ?>">
                        <div class="invalid-feedback"><?php echo $data['name_err']; ?></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">E-posta:</label>
                        <input type="email" name="email" id="email" class="form-control <?php echo (!empty($data['email_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['email']; ?>">
                        <div class="invalid-feedback"><?php echo $data['email_err']; ?></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Şifre:</label>
                        <input type="password" name="password" id="password" class="form-control <?php echo (!empty($data['password_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['password']; ?>">
                        <div class="invalid-feedback"><?php echo $data['password_err']; ?></div>
                        <small id="passwordHelp" class="form-text text-muted">En az 6 karakter uzunluğunda olmalıdır.</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Şifre Tekrar:</label>
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control <?php echo (!empty($data['confirm_password_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['confirm_password']; ?>">
                        <div class="invalid-feedback"><?php echo $data['confirm_password_err']; ?></div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Kullanıcı Tipi:</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="role" id="role_freelancer" value="freelancer" <?php echo $data['role'] == 'freelancer' ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="role_freelancer">
                                Freelancer - Projelere teklif vereceğim
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="role" id="role_employer" value="employer" <?php echo $data['role'] == 'employer' ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="role_employer">
                                İşveren - Proje yayınlayacağım
                            </label>
                        </div>
                        <div class="invalid-feedback"><?php echo $data['role_err']; ?></div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col">
                            <input type="submit" value="Kayıt Ol" class="btn btn-primary btn-block w-100">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col">
                            <p>Zaten bir hesabınız var mı? <a href="<?php echo SITE_URL; ?>/users/login">Giriş Yap</a></p>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?> 