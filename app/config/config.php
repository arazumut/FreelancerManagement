<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'freelancer_management');

// Site URL
define('SITE_URL', 'http://localhost/FreelancerManagement');

// App Root
define('APPROOT', dirname(dirname(__FILE__)));
define('VIEWS_ROOT', dirname(dirname(__FILE__)) . '/views');
define('PUBLIC_ROOT', dirname(dirname(dirname(__FILE__))) . '/public');

// Session
define('SESSION_NAME', 'freelancer_management');
define('SESSION_EXPIRE', 1800); // 30 dakika
?> 