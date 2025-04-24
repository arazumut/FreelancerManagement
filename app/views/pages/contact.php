<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="mb-4">İletişim</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Bizimle İletişime Geçin</h5>
                </div>
                <div class="card-body">
                    <form action="<?php echo SITE_URL; ?>/pages/contact" method="post">
                        <input type="hidden" name="csrf_token" value="<?php echo isset($data['csrf_token']) ? $data['csrf_token'] : ''; ?>">
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Adınız Soyadınız</label>
                            <input type="text" name="name" id="name" class="form-control" value="<?php echo isset($data['name']) ? $data['name'] : ''; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">E-posta Adresiniz</label>
                            <input type="email" name="email" id="email" class="form-control" value="<?php echo isset($data['email']) ? $data['email'] : ''; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="subject" class="form-label">Konu</label>
                            <input type="text" name="subject" id="subject" class="form-control" value="<?php echo isset($data['subject']) ? $data['subject'] : ''; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="message" class="form-label">Mesajınız</label>
                            <textarea name="message" id="message" rows="5" class="form-control" required><?php echo isset($data['message']) ? $data['message'] : ''; ?></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Gönder</button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">İletişim Bilgilerimiz</h5>
                </div>
                <div class="card-body">
                    <p><i class="fas fa-map-marker-alt me-2 text-primary"></i> Adres: İstanbul, Türkiye</p>
                    <p><i class="fas fa-envelope me-2 text-primary"></i> E-posta: info@freelancerplatformu.com</p>
                    <p><i class="fas fa-phone me-2 text-primary"></i> Telefon: +90 212 123 45 67</p>
                    
                    <h5 class="mt-4 mb-3">Çalışma Saatlerimiz</h5>
                    <p>Pazartesi - Cuma: 09:00 - 18:00</p>
                    <p>Cumartesi: 10:00 - 14:00</p>
                    <p>Pazar: Kapalı</p>
                    
                    <h5 class="mt-4 mb-3">Sosyal Medya</h5>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-primary fs-4"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-primary fs-4"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-primary fs-4"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-primary fs-4"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mb-5">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Konum</h5>
                </div>
                <div class="card-body p-0">
                    <div class="ratio ratio-16x9">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d385398.5897809314!2d28.731994582812525!3d41.00498228495556!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14caa7040068086b%3A0xe1ccfe98bc01b0d0!2zxLBzdGFuYnVs!5e0!3m2!1str!2str!4v1651132061590!5m2!1str!2str" 
                            width="100%" 
                            height="450" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?> 