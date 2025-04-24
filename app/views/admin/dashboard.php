<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Kullanıcılar</h5>
                            <h2 class="display-4"><?php echo $data['total_users']; ?></h2>
                        </div>
                        <i class="fas fa-users fa-3x"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a href="<?php echo SITE_URL; ?>/admin/users" class="text-white">Detaylar</a>
                    <div><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Projeler</h5>
                            <h2 class="display-4"><?php echo $data['total_projects']; ?></h2>
                        </div>
                        <i class="fas fa-tasks fa-3x"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a href="<?php echo SITE_URL; ?>/admin/projects" class="text-white">Detaylar</a>
                    <div><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card bg-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Teklifler</h5>
                            <h2 class="display-4"><?php echo $data['total_bids']; ?></h2>
                        </div>
                        <i class="fas fa-hand-holding-usd fa-3x"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a href="<?php echo SITE_URL; ?>/admin/bids" class="text-white">Detaylar</a>
                    <div><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card bg-danger text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Sözleşmeler</h5>
                            <h2 class="display-4"><?php echo $data['total_contracts']; ?></h2>
                        </div>
                        <i class="fas fa-file-contract fa-3x"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a href="<?php echo SITE_URL; ?>/admin/contracts" class="text-white">Detaylar</a>
                    <div><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Son Kayıt Olan Kullanıcılar</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Ad</th>
                                    <th>E-posta</th>
                                    <th>Rol</th>
                                    <th>Durum</th>
                                    <th>Kayıt Tarihi</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($data['recent_users'] as $user) : ?>
                                <tr>
                                    <td><?php echo $user->name; ?></td>
                                    <td><?php echo $user->email; ?></td>
                                    <td>
                                        <?php 
                                            switch($user->role) {
                                                case 'admin':
                                                    echo '<span class="badge bg-danger">Admin</span>';
                                                    break;
                                                case 'freelancer':
                                                    echo '<span class="badge bg-primary">Freelancer</span>';
                                                    break;
                                                case 'employer':
                                                    echo '<span class="badge bg-info">İşveren</span>';
                                                    break;
                                                default:
                                                    echo '<span class="badge bg-secondary">Bilinmiyor</span>';
                                            }
                                        ?>
                                    </td>
                                    <td>
                                        <?php if($user->status == 'active') : ?>
                                            <span class="badge bg-success">Aktif</span>
                                        <?php else : ?>
                                            <span class="badge bg-danger">Pasif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('d.m.Y H:i', strtotime($user->created_at)); ?></td>
                                    <td>
                                        <a href="<?php echo SITE_URL; ?>/admin/editUser/<?php echo $user->id; ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <a href="<?php echo SITE_URL; ?>/admin/users" class="btn btn-outline-primary">Tüm Kullanıcılar</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Son Eklenen Projeler</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Başlık</th>
                                    <th>İşveren</th>
                                    <th>Bütçe</th>
                                    <th>Durum</th>
                                    <th>Tarih</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($data['recent_projects'] as $project) : ?>
                                <tr>
                                    <td><?php echo $project->title; ?></td>
                                    <td><?php echo $project->employer_name; ?></td>
                                    <td><?php echo number_format($project->min_budget, 2) . '₺ - ' . number_format($project->max_budget, 2) . '₺'; ?></td>
                                    <td>
                                        <?php 
                                            switch($project->status) {
                                                case 'active':
                                                    echo '<span class="badge bg-success">Aktif</span>';
                                                    break;
                                                case 'completed':
                                                    echo '<span class="badge bg-info">Tamamlandı</span>';
                                                    break;
                                                case 'cancelled':
                                                    echo '<span class="badge bg-danger">İptal Edildi</span>';
                                                    break;
                                                default:
                                                    echo '<span class="badge bg-secondary">Diğer</span>';
                                            }
                                        ?>
                                    </td>
                                    <td><?php echo date('d.m.Y', strtotime($project->created_at)); ?></td>
                                    <td>
                                        <a href="<?php echo SITE_URL; ?>/admin/editProject/<?php echo $project->id; ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <a href="<?php echo SITE_URL; ?>/admin/projects" class="btn btn-outline-primary">Tüm Projeler</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?> 