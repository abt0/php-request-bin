<?php

declare(strict_types=1);

require_once './src/FormattedRequest.php';

fwrite(
    fopen('php://stdout', 'wb'),
    (new FormattedRequest())->output()
);

echo 'See STDOUT';
