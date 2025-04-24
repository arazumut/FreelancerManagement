<?php
/**
 * App Core Sınıfı
 * URL'yi parçalar ve controller'a yönlendirir
 * URL Formatı: /controller/method/params
 */
class Core {
    protected $currentController = 'Pages';
    protected $currentMethod = 'index';
    protected $params = [];

    public function __construct() {
        $url = $this->getUrl();

        // Controller kontrolü
        if(isset($url[0]) && file_exists('../app/controllers/' . ucwords($url[0]). '.php')) {
            // Controller varsa set et
            $this->currentController = ucwords($url[0]);
            // İlk indeksi unset et
            unset($url[0]);
        }

        // Controller dosyasını dahil et
        require_once '../app/controllers/'. $this->currentController . '.php';

        // Controller sınıfını örnekle
        $this->currentController = new $this->currentController;

        // Method kontrolü - URL'nin ikinci kısmı
        if(isset($url[1])) {
            // Method Controller'da var mı kontrol et
            if(method_exists($this->currentController, $url[1])) {
                $this->currentMethod = $url[1];
                // İkinci indeksi unset et
                unset($url[1]);
            }
        }

        // Parametreleri ayarla
        $this->params = $url ? array_values($url) : [];

        // Controller method çağrısı
        call_user_func_array([$this->currentController, $this->currentMethod], $this->params);
    }

    // URL'yi parçala ve dizi olarak döndür
    public function getUrl() {
        if(isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            return $url;
        }
        return [];
    }
}
?> 