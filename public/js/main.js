// Document ready
document.addEventListener('DOMContentLoaded', function() {
    // Flash mesajları için otomatik kapanma
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            const closeBtn = alert.querySelector('.btn-close');
            if (closeBtn) {
                closeBtn.click();
            }
        }, 5000); // 5 saniye sonra kapat
    });

    // Form validasyonu
    const forms = document.querySelectorAll('.needs-validation');
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });

    // Teklif tutarı kontrolü
    const amountInput = document.getElementById('amount');
    if (amountInput) {
        const minBudget = parseFloat(document.getElementById('min_budget')?.value || 0);
        const maxBudget = parseFloat(document.getElementById('max_budget')?.value || 0);
        
        amountInput.addEventListener('change', function() {
            const value = parseFloat(this.value);
            if (value < minBudget || value > maxBudget) {
                this.setCustomValidity(`Teklif tutarı ${minBudget} - ${maxBudget} aralığında olmalıdır.`);
            } else {
                this.setCustomValidity('');
            }
        });
    }

    // Silme onayı
    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            if (!confirm('Bu işlemi gerçekten yapmak istiyor musunuz?')) {
                event.preventDefault();
            }
        });
    });

    // Tarih alanları için min değer ayarla
    const dateInputs = document.querySelectorAll('input[type="date"]');
    dateInputs.forEach(input => {
        if (input.id === 'deadline' && !input.value) {
            const today = new Date();
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const dd = String(today.getDate()).padStart(2, '0');
            const formattedDate = yyyy + '-' + mm + '-' + dd;
            input.min = formattedDate;
        }
    });

    // Derecelendirme yıldızları
    const ratingInputs = document.querySelectorAll('.rating-input');
    if (ratingInputs.length > 0) {
        ratingInputs.forEach(input => {
            const stars = input.querySelectorAll('.star');
            const ratingValue = input.querySelector('input[type="hidden"]');
            
            stars.forEach((star, index) => {
                star.addEventListener('click', function() {
                    const rating = index + 1;
                    ratingValue.value = rating;
                    
                    // Yıldızları güncelle
                    stars.forEach((s, i) => {
                        if (i < rating) {
                            s.classList.add('active');
                        } else {
                            s.classList.remove('active');
                        }
                    });
                });
            });
        });
    }
}); 