<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

require_post();
verify_csrf();
clear_app_session();
redirect('index.php');
