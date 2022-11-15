<?php

/**
 * Page for empty routes
 */

declare(strict_types=1);

if (empty($_COOKIE['jwt'])) {
    header('Location: signin');
} else {
    header('Location: dashboard');
}

exit();
