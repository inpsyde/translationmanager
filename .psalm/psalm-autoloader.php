<?php

declare(strict_types=1);

$psalmDir = rtrim(__DIR__, '/');
$projectDir = rtrim(dirname($psalmDir), '/');

$psalmIncludes = [
    'wp.php',
];

foreach ($psalmIncludes as $include) {
    /** @noinspection PhpIncludeInspection */
    require_once "{$psalmDir}/{$include}";
}

define(
    'ABSPATH',
    sprintf(
        '%1$s/../../../',
        $projectDir
    )
);

unset(
    $psalmDir,
    $psalmIncludes,
    $projectDir
);
