<?php
// Endpoint for test harness availability check
http_response_code(200);
header('Content-Type: application/json');
echo json_encode(['status' => 'ready']);
