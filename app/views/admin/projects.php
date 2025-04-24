<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Proje Yönetimi</h5>
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
                        
                        <table class="table table-striped table-hover" id="projectsTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Başlık</th>
                                    <th>İşveren</th>
                                    <th>Kategori</th>
                                    <th>Bütçe</th>
                                    <th>Durum</th>
                                    <th>Tarih</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($data['projects'])) : ?>
                                    <?php foreach($data['projects'] as $project) : ?>
                                        <tr>
                                            <td><?php echo $project->id; ?></td>
                                            <td>
                                                <a href="<?php echo SITE_URL; ?>/projects/view/<?php echo $project->id; ?>" target="_blank">
                                                    <?php echo $project->title; ?>
                                                </a>
                                            </td>
                                            <td><?php echo $project->employer_name; ?></td>
                                            <td><?php echo $project->category_name; ?></td>
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
                                                <div class="btn-group">
                                                    <a href="<?php echo SITE_URL; ?>/admin/editProject/<?php echo $project->id; ?>" class="btn btn-sm btn-primary" title="Düzenle">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <?php if($project->status != 'completed') : ?>
                                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $project->id; ?>" title="Sil">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                                
                                                <!-- Modal -->
                                                <div class="modal fade" id="deleteModal<?php echo $project->id; ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?php echo $project->id; ?>" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="deleteModalLabel<?php echo $project->id; ?>">Projeyi Sil</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p><strong><?php echo $project->title; ?></strong> adlı projeyi silmek istediğinize emin misiniz?</p>
                                                                <p class="text-danger">Bu işlem geri alınamaz!</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                                                                <form action="<?php echo SITE_URL; ?>/admin/deleteProject/<?php echo $project->id; ?>" method="post">
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
                                        <td colspan="8" class="text-center">Henüz proje bulunmamaktadır.</td>
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
        $('#projectsTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Turkish.json"
            },
            "order": [[ 0, "desc" ]]
        });
    });
</script>

<?php require APPROOT . '/views/inc/footer.php'; ?> 