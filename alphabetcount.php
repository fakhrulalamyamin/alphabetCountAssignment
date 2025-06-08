#! /usr/bin/env php

<?php

if ($argc < 2) {
    echo "Usage: php alphabet_counter.php \"Your text here\"\n";
    exit(1);
}

$input = $argv[1];

$counter = preg_match_all('/[A-Za-z]/', $input);

printf("Your Alphabet Count is: " . $counter);