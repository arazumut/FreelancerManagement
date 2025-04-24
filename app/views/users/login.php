<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card form-card">
            <div class="card-header">
                <h2>Giriş Yap</h2>
                <p>Hesabınıza giriş yapmak için formu doldurun</p>
            </div>
            <div class="card-body">
                <form action="<?php echo SITE_URL; ?>/users/login" method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo $data['csrf_token']; ?>">
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">E-posta:</label>
                        <input type="email" name="email" id="email" class="form-control <?php echo (!empty($data['email_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['email']; ?>">
                        <div class="invalid-feedback"><?php echo $data['email_err']; ?></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Şifre:</label>
                        <input type="password" name="password" id="password" class="form-control <?php echo (!empty($data['password_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['password']; ?>">
                        <div class="invalid-feedback"><?php echo $data['password_err']; ?></div>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="remember" id="remember" class="form-check-input" <?php echo $data['remember'] ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="remember">Beni Hatırla</label>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col">
                            <input type="submit" value="Giriş Yap" class="btn btn-primary btn-block w-100">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col">
                            <a href="<?php echo SITE_URL; ?>/users/forgotPassword" class="btn btn-link">Şifremi Unuttum</a>
                        </div>
                        <div class="col text-end">
                            <p>Hesabınız yok mu? <a href="<?php echo SITE_URL; ?>/users/register">Kayıt Ol</a></p>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?> 