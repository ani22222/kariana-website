<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/tests/challenger_m1_gen2_test.php';
$suite = runChallengerM1Gen2Suite();
echo json_encode($suite, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
