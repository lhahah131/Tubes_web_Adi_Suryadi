<?php
$env = file_get_contents('.env');
$lines = explode("\n", $env);
foreach ($lines as $line) {
    if (strpos($line, 'DB_') === 0) {
        echo $line . "\n";
    }
}
