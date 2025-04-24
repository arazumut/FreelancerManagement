# PHP Freelancer Platformu

Bu proje, freelancer'lar ve işverenler arasında bir buluşma noktası sağlayan PHP tabanlı bir web uygulamasıdır. MVC (Model-View-Controller) mimarisi kullanılarak geliştirilmiştir.

## Özellikler

- Kullanıcı Sistemi (kayıt, giriş, oturum yönetimi)
- Rol bazlı yetkilendirme (freelancer, işveren, admin)
- Proje yayınlama ve listeleme
- Projelere teklif verme
- Teklifleri kabul etme ve reddetme
- Sözleşme yönetimi
- Fatura oluşturma
- Değerlendirme sistemi
- Bildirimler ve e-posta sistemi
- Mobil uyumlu arayüz

## Kurulum

### Gereksinimler

- PHP 7.4 veya üzeri
- MySQL 5.7 veya üzeri
- Apache veya Nginx
- Composer (opsiyonel)

### Adımlar

1. Projeyi sunucunuza indirin:
   ```
   git clone https://github.com/kullaniciadi/FreelancerManagement.git
   ```

2. Veritabanını oluşturun:
   - MySQL'e giriş yapın
   - `database/schema.sql` dosyasını içe aktarın

3. Konfigürasyon ayarlarını yapın:
   - `app/config/config.php` dosyasını düzenleyin
   - Veritabanı bağlantı bilgilerini güncelleyin
   - Site URL'sini düzenleyin

4. E-posta sistemini kullanmak için (opsiyonel):
   - Composer kullanarak PHPMailer'ı yükleyin:
     ```
     composer require phpmailer/phpmailer
     ```
   - `app/helpers/email_helper.php` dosyasındaki SMTP ayarlarını düzenleyin

5. Web sunucusu yapılandırması:
   - Apache için `.htaccess` dosyaları zaten projeye dahildir
   - Nginx için uygun rewrite kurallarını ekleyin

## Klasör Yapısı

```
FreelancerManagement/
├── app/                    # Uygulama dosyaları
│   ├── bootstrap.php       # Başlangıç dosyası
│   ├── config/             # Konfigürasyon dosyaları
│   ├── controllers/        # Controller sınıfları
│   ├── core/               # Çekirdek MVC sınıfları
│   ├── helpers/            # Yardımcı fonksiyonlar
│   ├── models/             # Model sınıfları
│   └── views/              # View dosyaları
├── database/               # Veritabanı şemaları ve migration dosyaları
├── public/                 # Genel erişilebilir dosyalar
│   ├── css/                # CSS dosyaları
│   ├── js/                 # JavaScript dosyaları
│   ├── img/                # Resim dosyaları
│   ├── .htaccess           # Apache rewrite kuralları
│   └── index.php           # Giriş noktası
└── .htaccess               # Ana dizin için Apache rewrite kuralları
```

## Kullanım

1. Admin hesabı ile giriş yapın (Varsayılan):
   - E-posta: admin@example.com
   - Şifre: admin123

2. Freelancer veya İşveren olarak kaydolun

3. İşveren olarak:
   - Yeni projeler oluşturun
   - Gelen teklifleri değerlendirin
   - Sözleşmeleri yönetin

4. Freelancer olarak:
   - Projeleri keşfedin
   - Projelere teklif verin
   - Kabul edilen tekliflerinizle çalışın

## Güvenlik

Proje güvenlik önlemleri içermektedir:
- CSRF koruması
- XSS filtreleme
- Güvenli form validasyonu
- Şifre hashleme
- Rol bazlı erişim kontrolü

