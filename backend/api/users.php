<?php

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

echo json_encode([
    [
        "id" => 1,
        "username" => "Max",
        "firstname" => "Topr",
        "lastname" => "Max",
        "email" => "Max",
        "pw" => "Max",
        "created" => "Max",
    ]
]);