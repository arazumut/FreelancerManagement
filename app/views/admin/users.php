<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Kullanıcı Yönetimi</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <?php if(isset($_SESSION['flash_messages']['admin_success'])): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?php echo $_SESSION['flash_messages']['admin_success']['message']; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php unset($_SESSION['flash_messages']['admin_success']); ?>
                        <?php endif; ?>

                        <?php if(isset($_SESSION['flash_messages']['admin_error'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php echo $_SESSION['flash_messages']['admin_error']['message']; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php unset($_SESSION['flash_messages']['admin_error']); ?>
                        <?php endif; ?>
                        
                        <table class="table table-striped table-hover" id="usersTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Ad</th>
                                    <th>E-posta</th>
                                    <th>Rol</th>
                                    <th>Durum</th>
                                    <th>Kayıt Tarihi</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($data['users'])) : ?>
                                    <?php foreach($data['users'] as $user) : ?>
                                        <tr>
                                            <td><?php echo $user->id; ?></td>
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
                                                <div class="btn-group">
                                                    <a href="<?php echo SITE_URL; ?>/admin/editUser/<?php echo $user->id; ?>" class="btn btn-sm btn-primary" title="Düzenle">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <?php if($user->id != $_SESSION['user_id'] && $user->role != 'admin') : ?>
                                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $user->id; ?>" title="Sil">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                                
                                                <!-- Modal -->
                                                <div class="modal fade" id="deleteModal<?php echo $user->id; ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?php echo $user->id; ?>" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="deleteModalLabel<?php echo $user->id; ?>">Kullanıcıyı Sil</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p><strong><?php echo $user->name; ?></strong> adlı kullanıcıyı silmek istediğinize emin misiniz?</p>
                                                                <p class="text-danger">Bu işlem geri alınamaz!</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                                                                <form action="<?php echo SITE_URL; ?>/admin/deleteUser/<?php echo $user->id; ?>" method="post">
                                                                    <input type="hidden" name="csrf_token" value="<?php echo $data['csrf_token']; ?>">
                                                                    <button type="submit" class="btn btn-danger">Sil</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="7" class="text-center">Henüz kullanıcı bulunmamaktadır.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#usersTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Turkish.json"
            },
            "order": [[ 0, "desc" ]]
        });
    });
</script>

<?php require APPROOT . '/views/inc/footer.php'; ?> 