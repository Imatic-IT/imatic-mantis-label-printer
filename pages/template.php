<?php

if ((bool)plugin_config_get('basicAuthEnabled', false) == true) {

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        header("Access-Control-Allow-Origin: " . plugin_config_get('niimblueBaseUrl', 'http://localhost:5173'));
        header("Access-Control-Allow-Methods: GET, OPTIONS");
        header("Access-Control-Allow-Headers: Authorization, Content-Type");
        header("Access-Control-Max-Age: 86400");
        exit(0);
    }

    $basicAuthUser = plugin_config_get('basicAuth')['username'] ?? '';
    $basicAuthPass = plugin_config_get('basicAuth')['password'] ?? '';

    if (!isset($_SERVER['PHP_AUTH_USER']) || $_SERVER['PHP_AUTH_USER'] !== $basicAuthUser || $_SERVER['PHP_AUTH_PW'] !== $basicAuthPass) {
        header("Access-Control-Allow-Origin: " . plugin_config_get('niimblueBaseUrl', 'http://localhost:5173'));
        header('WWW-Authenticate: Basic realm="NiimBlue Access"');
        header('HTTP/1.0 401 Unauthorized');
        echo 'Unauthorized';
        exit;
    }
}

$plugin = plugin_get('ImaticLabelPrinter');
$templatesPath = $plugin->getTemplatesPath();

$templateId = gpc_get_string('templateId', '');
$templateFile = $templatesPath . '/' . $templateId . '.json';

if (!$templateId) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing template id']);
    exit;
}

if (!file_exists($templateFile)) {
    http_response_code(404);
    echo json_encode(['error' => 'Template not found']);
    exit;
}

$string = file_get_contents($templateFile);
$jsonTempalate = json_decode($string, true);

$replacedTemplate = $plugin->deepReplacePlaceholders($jsonTempalate, $plugin->getReplacements());

header("Access-Control-Allow-Origin: " . plugin_config_get('niimblueBaseUrl', 'http://localhost:5173'));
echo json_encode($replacedTemplate);
exit;

