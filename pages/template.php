<?php

$allowedOrigin = plugin_config_get('niimblueBaseUrl', 'http://localhost:5173');
header("Access-Control-Allow-Origin: $allowedOrigin");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Authorization, Content-Type");
header("Access-Control-Max-Age: 86400");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ((bool)plugin_config_get('basicAuthEnabled', false)) {
    $authConfig = plugin_config_get('basicAuth');
    $basicAuthUser = $authConfig['username'] ?? '';
    $basicAuthPass = $authConfig['password'] ?? '';

    if (
        !isset($_SERVER['PHP_AUTH_USER']) ||
        $_SERVER['PHP_AUTH_USER'] !== $basicAuthUser ||
        $_SERVER['PHP_AUTH_PW'] !== $basicAuthPass
    ) {
        header('WWW-Authenticate: Basic realm="NiimBlue Access"');
        header('HTTP/1.0 401 Unauthorized');
        echo 'Unauthorized';
        exit;
    }
}

$templateId = gpc_get_string('templateId', '');

if (!$templateId) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing template id']);
    exit;
}

$githubRawBaseUrl = 'https://raw.githubusercontent.com/Imatic-IT/niimblue-templates/master/';
$githubApiUrl = plugin_config_get('githubApiUrl');

$templateUrl = "$githubRawBaseUrl/$templateId.json";

$jsonContent = @file_get_contents($templateUrl);

if ($jsonContent === false) {
    http_response_code(404);
    echo json_encode(['error' => 'Template not found on GitHub']);
    exit;
}

$jsonTemplate = json_decode($jsonContent, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(500);
    echo json_encode(['error' => 'Invalid JSON in template']);
    exit;
}

$plugin = plugin_get('ImaticLabelPrinter');
$replacedTemplate = $plugin->deepReplacePlaceholders($jsonTemplate, $plugin->getReplacements());

header("Access-Control-Allow-Origin: $allowedOrigin");
echo json_encode($replacedTemplate);
exit;
