<?php require APPROOT . '/views/inc/header.php'; ?>

<div class="container">
    <h1 class="mb-4">Tekliflerim</h1>
    
    <?php if(empty($data['bids'])): ?>
        <div class="alert alert-info">Henüz teklif vermişsiniz.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Proje</th>
                        <th>Tutar</th>
                        <th>Teslim Süresi</th>
                        <th>Durum</th>
                        <th>Tarih</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($data['bids'] as $bid): ?>
                        <tr>
                            <td>
                                <a href="<?php echo SITE_URL; ?>/projects/showProject/<?php echo $bid->project_id; ?>">
                                    <?php echo $bid->project_title; ?>
                                </a>
                            </td>
                            <td><?php echo number_format($bid->amount, 2); ?> TL</td>
                            <td><?php echo $bid->delivery_time; ?> gün</td>
                            <td>
                                <?php if($bid->status == 'pending'): ?>
                                    <span class="badge bg-warning text-dark">Beklemede</span>
                                <?php elseif($bid->status == 'accepted'): ?>
                                    <span class="badge bg-success">Kabul Edildi</span>
                                <?php elseif($bid->status == 'rejected'): ?>
                                    <span class="badge bg-danger">Reddedildi</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('d.m.Y', strtotime($bid->created_at)); ?></td>
                            <td>
                                <?php if($bid->status == 'pending' && $bid->project_status == 'active'): ?>
                                    <a href="<?php echo SITE_URL; ?>/bids/edit/<?php echo $bid->id; ?>" class="btn btn-sm btn-primary">Düzenle</a>
                                    <a href="<?php echo SITE_URL; ?>/bids/delete/<?php echo $bid->id; ?>" class="btn btn-sm btn-danger btn-delete">Sil</a>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-secondary" disabled>İşlem Yapılamaz</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
    
    <div class="mt-3">
        <a href="<?php echo SITE_URL; ?>/projects" class="btn btn-primary">Projelere Göz At</a>
    </div>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?> 