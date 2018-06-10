<?php

    require('../Crypto.php');
    require('config.php');

    $c = new Crypto($keys);

    if (php_sapi_name() == 'cli') {
        echo $c->Encrypt($argv[1]);
    } else {
        echo $c->Encrypt(file_get_contents('php://input'));
    }

