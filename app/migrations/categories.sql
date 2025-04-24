-- Kategoriler tablosu
CREATE TABLE IF NOT EXISTS categories (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Örnek kategorileri ekle
INSERT INTO categories (name, slug, description) VALUES
('Web Geliştirme', 'web-gelistirme', 'Web siteleri, web uygulamaları ve e-ticaret platformları ile ilgili projeler'),
('Mobil Uygulama', 'mobil-uygulama', 'iOS, Android ve diğer mobil platformlar için uygulama geliştirme'),
('Grafik Tasarım', 'grafik-tasarim', 'Logo, afiş, broşür, sosyal medya görselleri ve diğer grafik tasarım hizmetleri'),
('İçerik Yazarlığı', 'icerik-yazarligi', 'Blog yazıları, ürün tanıtımları, SEO içerikleri ve her türlü metin yazarlığı'),
('Dijital Pazarlama', 'dijital-pazarlama', 'SEO, SEM, sosyal medya pazarlaması ve reklam kampanyaları yönetimi'),
('Video ve Animasyon', 'video-animasyon', 'Video düzenleme, animasyon ve motion graphics projeleri'),
('Ses ve Müzik', 'ses-muzik', 'Ses düzenleme, müzik prodüksiyonu ve seslendirme hizmetleri'),
('Veri Analizi', 'veri-analizi', 'Veri madenciliği, istatistiksel analiz ve veri görselleştirme'),
('Yazılım Geliştirme', 'yazilim-gelistirme', 'Masaüstü uygulamalar, API geliştirme ve diğer yazılım projeleri'),
('Oyun Geliştirme', 'oyun-gelistirme', 'Mobil, web ve masaüstü platformları için oyun geliştirme'),
('Çeviri ve Yerelleştirme', 'ceviri-yerellestirme', 'Metinlerin ve içeriklerin farklı dillere çevrilmesi'),
('Hukuki Hizmetler', 'hukuki-hizmetler', 'Sözleşmeler, yasal belgeler ve diğer hukuki konularda danışmanlık'),
('Muhasebe ve Finans', 'muhasebe-finans', 'Finansal raporlama, muhasebe ve vergi danışmanlığı'); 