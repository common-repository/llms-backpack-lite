<?php
namespace BPKPFieldManager;

global $bkpkFM;

if (!empty($_GET["option"])) {
    $bkpkFM->dump($bkpkFM->getData($_GET["option"]));
} else {
    phpinfo();
}

