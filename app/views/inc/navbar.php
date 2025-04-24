<nav class="navbar navbar-expand-lg navbar-light bg-light mb-3">
    <div class="container">
        <a class="navbar-brand" href="<?php echo SITE_URL; ?>" data-aos="fade-right" data-aos-duration="800">
            <i class="fas fa-laptop-code me-2"></i>Freelancer Platformu
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto" data-aos="fade-down" data-aos-duration="800" data-aos-delay="100">
                <li class="nav-item">
                    <a class="nav-link <?php echo ($_SERVER['REQUEST_URI'] == SITE_URL || $_SERVER['REQUEST_URI'] == SITE_URL . '/') ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>">Ana Sayfa</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], SITE_URL . '/projects') === 0) ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/projects">Projeler</a>
                </li>
                <?php if(isset($_SESSION['user_id'])) : ?>
                    <?php if($_SESSION['user_role'] == 'client') : ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], SITE_URL . '/projects/add') === 0) ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/projects/add">Proje Ekle</a>
                        </li>
                    <?php endif; ?>
                    <?php if($_SESSION['user_role'] == 'admin') : ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], SITE_URL . '/admin') === 0) ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/admin"><i class="fas fa-user-shield me-1"></i>Admin Paneli</a>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], SITE_URL . '/pages/about') === 0) ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/pages/about">Hakkımızda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], SITE_URL . '/pages/contact') === 0) ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/pages/contact">İletişim</a>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto" data-aos="fade-left" data-aos-duration="800" data-aos-delay="100">
                <?php if(isset($_SESSION['user_id'])) : ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-1"></i> <?php echo $_SESSION['user_name']; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/users/profile"><i class="fas fa-id-card me-2"></i>Profilim</a></li>
                            <?php if($_SESSION['user_role'] == 'client') : ?>
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/projects/manage"><i class="fas fa-tasks me-2"></i>Projelerim</a></li>
                            <?php else : ?>
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/bids/mybids"><i class="fas fa-hand-holding-usd me-2"></i>Tekliflerim</a></li>
                            <?php endif; ?>
                            <?php if($_SESSION['user_role'] == 'admin') : ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/admin"><i class="fas fa-user-shield me-2"></i>Admin Paneli</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/users/logout"><i class="fas fa-sign-out-alt me-2"></i>Çıkış Yap</a></li>
                        </ul>
                    </li>
                <?php else : ?>
                    <li class="nav-item">
                        <a class="nav-link btn btn-outline-primary me-2 btn-ripple" href="<?php echo SITE_URL; ?>/users/login">Giriş Yap</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-primary btn-ripple" href="<?php echo SITE_URL; ?>/users/register">Kayıt Ol</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav> 