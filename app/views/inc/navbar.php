<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="<?php echo SITE_URL; ?>">Freelancer Platformu</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?php echo ($_SERVER['REQUEST_URI'] == SITE_URL || $_SERVER['REQUEST_URI'] == SITE_URL . '/') ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>">Ana Sayfa</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/projects') !== false) ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/projects">Projeler</a>
                </li>
                
                <?php if(isLoggedIn()): ?>
                    <?php if(isFreelancer()): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/bids/my') !== false) ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/bids/my">Tekliflerim</a>
                    </li>
                    <?php elseif(isEmployer()): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/projects/my') !== false) ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/projects/myProjects">Projelerim</a>
                    </li>
                    <?php endif; ?>
                    
                    <li class="nav-item">
                        <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/contracts') !== false) ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/contracts">Sözleşmelerim</a>
                    </li>
                <?php endif; ?>
                
                <li class="nav-item">
                    <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/pages/about') !== false) ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/pages/about">Hakkımızda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/pages/contact') !== false) ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/pages/contact">İletişim</a>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <?php if(isLoggedIn()): ?>
                    <?php if(isAdmin()): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin') !== false) ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/admin">Admin Panel</a>
                    </li>
                    <?php endif; ?>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user"></i> <?php echo $_SESSION['user_name']; ?>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/users/profile">Profilim</a></li>
                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/users/edit">Profil Düzenle</a></li>
                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/users/emailPreferences">E-posta Tercihleri</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/users/logout">Çıkış Yap</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/users/login') !== false) ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/users/login">Giriş Yap</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/users/register') !== false) ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/users/register">Kayıt Ol</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav> 