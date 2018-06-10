<?php

    require('../Crypto.php');

    $c = new Crypto(null);
    echo $c->GenKey((int)$argv[1]);