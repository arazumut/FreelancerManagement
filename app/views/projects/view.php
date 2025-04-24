<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="container">
    <div class="row mb-3">
        <div class="col-md-12">
            <a href="<?php echo SITE_URL; ?>/projects" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Projelere Dön</a>
            
            <?php if(isLoggedIn() && (isAdmin() || $_SESSION['user_id'] == $data['project']->user_id)): ?>
                <div class="float-end">
                    <a href="<?php echo SITE_URL; ?>/projects/edit/<?php echo $data['project']->id; ?>" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Düzenle
                    </a>
                    <a href="<?php echo SITE_URL; ?>/projects/delete/<?php echo $data['project']->id; ?>" class="btn btn-danger btn-delete">
                        <i class="fas fa-trash"></i> Sil
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h1 class="mb-0"><?php echo $data['project']->title; ?></h1>
            <span class="badge <?php echo $data['project']->status == 'active' ? 'badge-active' : ($data['project']->status == 'completed' ? 'badge-completed' : 'badge-canceled'); ?>">
                <?php echo $data['project']->status == 'active' ? 'Aktif' : ($data['project']->status == 'completed' ? 'Tamamlandı' : 'İptal Edildi'); ?>
            </span>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <p><strong><i class="fas fa-user"></i> İşveren:</strong> <?php echo $data['employer']->name; ?></p>
                    <p><strong><i class="fas fa-calendar-alt"></i> Yayınlanma Tarihi:</strong> <?php echo date('d.m.Y', strtotime($data['project']->created_at)); ?></p>
                    <p><strong><i class="fas fa-clock"></i> Son Teslim Tarihi:</strong> <?php echo date('d.m.Y', strtotime($data['project']->deadline)); ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong><i class="fas fa-tag"></i> Kategori:</strong> <?php echo $data['project']->category_name; ?></p>
                    <p><strong><i class="fas fa-money-bill-wave"></i> Bütçe:</strong> <?php echo number_format($data['project']->min_budget, 2); ?> TL - <?php echo number_format($data['project']->max_budget, 2); ?> TL</p>
                    
                    <?php if(!empty($data['contract'])): ?>
                        <p><strong><i class="fas fa-file-contract"></i> Sözleşme Durumu:</strong> 
                            <span class="badge <?php echo $data['contract']->status == 'active' ? 'badge-active' : ($data['contract']->status == 'completed' ? 'badge-completed' : ($data['contract']->status == 'delivered' ? 'badge-delivered' : 'badge-revision')); ?>">
                                <?php echo $data['contract']->status == 'active' ? 'Aktif' : ($data['contract']->status == 'completed' ? 'Tamamlandı' : ($data['contract']->status == 'delivered' ? 'Teslim Edildi' : 'Revizyon')); ?>
                            </span>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="mb-4">
                <h4>Proje Açıklaması</h4>
                <div class="p-3 bg-light rounded">
                    <?php echo nl2br($data['project']->description); ?>
                </div>
            </div>
            
            <?php if(isLoggedIn() && isFreelancer() && $data['project']->status == 'active' && !$data['userHasBid'] && empty($data['contract'])): ?>
                <div class="mt-4">
                    <h4>Teklif Ver</h4>
                    <form action="<?php echo SITE_URL; ?>/bids/add/<?php echo $data['project']->id; ?>" method="post">
                        <input type="hidden" name="csrf_token" value="<?php echo $data['csrf_token']; ?>">
                        <input type="hidden" name="min_budget" id="min_budget" value="<?php echo $data['project']->min_budget; ?>">
                        <input type="hidden" name="max_budget" id="max_budget" value="<?php echo $data['project']->max_budget; ?>">
                        
                        <div class="mb-3">
                            <label for="amount" class="form-label">Teklif Tutarı (TL) - <small class="text-muted"><?php echo number_format($data['project']->min_budget, 2); ?> - <?php echo number_format($data['project']->max_budget, 2); ?> TL arası</small></label>
                            <input type="number" step="0.01" min="<?php echo $data['project']->min_budget; ?>" max="<?php echo $data['project']->max_budget; ?>" name="amount" id="amount" class="form-control" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="delivery_time" class="form-label">Teslim Süresi (Gün)</label>
                            <input type="number" min="1" name="delivery_time" id="delivery_time" class="form-control" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Teklif Açıklaması</label>
                            <textarea name="description" id="description" rows="4" class="form-control" required></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Teklif Ver</button>
                    </form>
                </div>
            <?php elseif(isLoggedIn() && isFreelancer() && $data['project']->status == 'active' && $data['userHasBid']): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Bu projeye zaten teklif vermişsiniz. <a href="<?php echo SITE_URL; ?>/bids/my">Tekliflerim</a> sayfasından teklifinizi görebilirsiniz.
                </div>
            <?php endif; ?>
            
            <?php if(isLoggedIn() && ($data['project']->user_id == $_SESSION['user_id'] || isAdmin()) && !empty($data['bids'])): ?>
                <div class="mt-4">
                    <h4>Gelen Teklifler</h4>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Freelancer</th>
                                    <th>Tutar</th>
                                    <th>Süre (Gün)</th>
                                    <th>Tarih</th>
                                    <th>Durum</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($data['bids'] as $bid): ?>
                                    <tr>
                                        <td><?php echo $bid->freelancer_name; ?></td>
                                        <td><?php echo number_format($bid->amount, 2); ?> TL</td>
                                        <td><?php echo $bid->delivery_time; ?></td>
                                        <td><?php echo date('d.m.Y', strtotime($bid->created_at)); ?></td>
                                        <td>
                                            <?php if($bid->status == 'pending'): ?>
                                                <span class="badge bg-warning text-dark">Beklemede</span>
                                            <?php elseif($bid->status == 'accepted'): ?>
                                                <span class="badge bg-success">Kabul Edildi</span>
                                            <?php elseif($bid->status == 'rejected'): ?>
                                                <span class="badge bg-danger">Reddedildi</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($data['project']->status == 'active' && $bid->status == 'pending' && empty($data['contract'])): ?>
                                                <a href="<?php echo SITE_URL; ?>/bids/accept/<?php echo $bid->id; ?>" class="btn btn-sm btn-success">Kabul Et</a>
                                                <a href="<?php echo SITE_URL; ?>/bids/reject/<?php echo $bid->id; ?>" class="btn btn-sm btn-danger">Reddet</a>
                                            <?php else: ?>
                                                <button class="btn btn-sm btn-secondary" disabled>İşlem Yapılamaz</button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?> 