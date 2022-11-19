<?php

declare(strict_types=1);

use App\Utils;

if (isset($_COOKIE['jwt'])) {
    unset($_COOKIE['jwt']);
    $cookie_options = [
        'expires' => -1,
        'path' => '/',
        'domain' => $_SERVER['HTTP_HOST'],
        'secure' => Utils::isSecure(),
        'httponly' => true,
        'samesite' => 'Strict'
    ];
    setcookie('jwt', '', $cookie_options);
}

?>
<script src="../dist/js/jquery-3.6.1.min.js"></script>
<script>
    $(document).ready(function() {
        sessionStorage.clear();
        window.location.href = '/signin';
    });
</script>