// Document ready
document.addEventListener('DOMContentLoaded', function() {
    // AOS animasyon kütüphanesini başlat
    AOS.init({
        duration: 800,
        easing: 'ease-in-out',
        once: true,
        mirror: false
    });

    // Sayfa yükleme animasyonu
    document.body.classList.add('page-loaded');

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

    // Buton dalgalanma efekti
    const rippleButtons = document.querySelectorAll('.btn-ripple');
    rippleButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const rect = button.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            const ripple = document.createElement('span');
            ripple.className = 'ripple-effect';
            ripple.style.left = `${x}px`;
            ripple.style.top = `${y}px`;
            
            button.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });

    // Navbar scroll efekti
    window.addEventListener('scroll', function() {
        const navbar = document.querySelector('.navbar');
        if (navbar) {
            if (window.scrollY > 50) {
                navbar.classList.add('navbar-scrolled');
            } else {
                navbar.classList.remove('navbar-scrolled');
            }
        }
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

                // Hover efekti
                star.addEventListener('mouseenter', function() {
                    const hoverRating = index + 1;
                    stars.forEach((s, i) => {
                        if (i < hoverRating) {
                            s.classList.add('hover');
                        } else {
                            s.classList.remove('hover');
                        }
                    });
                });

                star.addEventListener('mouseleave', function() {
                    stars.forEach(s => {
                        s.classList.remove('hover');
                    });
                });
            });
        });
    }

    // Sayfa geçiş animasyonları
    const transitionLinks = document.querySelectorAll('.transition-link');
    transitionLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const target = this.getAttribute('href');
            
            document.body.classList.add('page-transitioning');
            
            setTimeout(() => {
                window.location.href = target;
            }, 300);
        });
    });

    // Proje kartları için animasyon tetikleyici
    const projectCards = document.querySelectorAll('.project-card, .profile-card');
    if (projectCards.length > 0) {
        const observerOptions = {
            threshold: 0.1
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animated');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);
        
        projectCards.forEach(card => {
            observer.observe(card);
        });
    }
}); 