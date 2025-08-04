<?php

class ImaticLabelPrinterPlugin extends MantisPlugin
{
    CONST NIIMBLUE_QR_CODE = 'QRCode';

    public function register()
    {
        $this->name = 'Imatic Label Printer';
        $this->description = 'This plugin allows you to print labels for issues in MantisBT.';
        $this->version = '0.0.1';
        $this->requires = [
            'MantisCore' => '2.0.0',
        ];
        $this->page = 'config_page';
        $this->author = 'Imatic Software s.r.o.';
        $this->contact = 'info@imatic.cz';
        $this->url = 'https://www.imatic.cz/';
    }

    public function config(): array
    {
        return [
            'niimblueBaseUrl' => 'http://localhost:5173',
            'basicAuth' => [
                'username' => 'niimTemplater',
                'password' => 'uSLR8SokEqoFYfX',
            ],
            'replacements' => [
                'bugId',
                'summary',
                'bugUrl',
                'branding',
                'hotline',
            ],
            'placeholderDelimiters' => [
                'open' => '{',
                'close' => '}'
            ],
            'branding' => 'www.imatic.cz',
            'hotline' => '+420 944 162 732',
            'assigned_templates' => [],
            'basicAuthEnabled' => false,
            'githubApiUrl' => 'https://api.github.com/repos/Imatic-IT/niimblue-templates/contents',
            'githubRawBaseUrl' => 'https://raw.githubusercontent.com/Imatic-IT/niimblue-templates/master',
            'githubToken' => '',
        ];
    }

    public function hooks(): array
    {
        return [
            'EVENT_VIEW_BUG_DETAILS' => 'bug_view_details',
            'EVENT_LAYOUT_BODY_END' => 'layout_body_end_hook',
        ];
    }

    public function deepReplacePlaceholders(array $target, array $replacements): array
    {
        foreach ($target as $key => $val) {
            if (is_array($val)) {
                $target[$key] = $this->deepReplacePlaceholders($val, $replacements);
            } elseif (is_string($val) && array_key_exists($val, $replacements)) {
                if (isset($target['type']) && $target['type'] === self::NIIMBLUE_QR_CODE) {
                    $target['text'] = $replacements[$val];
                } else {
                    $target[$key] = $replacements[$val];
                }
            }
        }
        return $target;
    }

    function bug_view_details()
    {
        if (isset($_GET['id'])) {
            $assignedTemplates = plugin_config_get('assigned_templates');
            $projectId = (int)bug_get_field($_GET['id'], 'project_id');

            if (!$this->projectHasAssignedTemplate($projectId)) return;
            $templateId = htmlspecialchars($assignedTemplates[$projectId], ENT_QUOTES, 'UTF-8');

            $niimblueBaseUrl = $this->getNiimblueBaseUrl();

            $templateUrl = config_get_global('path') . plugin_page('template.php') . '&templateId=' . $templateId . '&id=' . bug_format_id($_GET['id']);

            $data = '{"templateUrl": "' . $templateUrl . '"}';

            $encoded = base64_encode($data);
            echo '<a id="printLabelsButton" data="' . $encoded . '" target="_blank" href="' . $niimblueBaseUrl . '/?data=' . urlencode($encoded) . '" class="btn btn-primary btn-white btn-round btn-sm">&#128424; Tag</a>';
        }
    }

    private function projectHasAssignedTemplate(int $projectId): bool
    {
        $assignedTemplates = plugin_config_get('assigned_templates');
        return isset($assignedTemplates[$projectId]) && $assignedTemplates[$projectId] != -1;
    }


    private function getNiimblueBaseUrl()
    {
        $niimblueBaseUrl = plugin_config_get('niimblueBaseUrl', 'http://localhost:5173');
        return htmlspecialchars($niimblueBaseUrl, ENT_QUOTES, 'UTF-8');
    }

    public function getReplacements()
    {
        $replacements = plugin_config_get('replacements');
        $bugId = bug_format_id($_GET['id']);
        $mantis_url = config_get_global('path') . 'view.php?id=' . $bugId;
        $summary = bug_get_field($bugId, 'summary');

        $values = array_map(function ($item) use ($bugId, $mantis_url, $summary) {
            switch ($item) {
                case 'bugId':
                case self::NIIMBLUE_QR_CODE:
                    return $bugId;
                case 'bugUrl':
                    return $mantis_url;
                case 'branding':
                    return plugin_config_get('branding');
                case 'hotline':
                    return plugin_config_get('hotline');
                case 'summary':
                    return $summary;
                default:
                    return '';
            }
        }, $replacements);

        $keysWithDelimiters = array_map(function ($item) {
            return '{' . $item . '}';
        }, $replacements);

        $replacements = array_combine($keysWithDelimiters, $values);

        return $replacements;
    }

    public function layout_body_end_hook($p_event)
    {
        echo '<script  src="' . plugin_file('bundle.js') . '&v=' . $this->version . '" defer></script>;
            <link rel="stylesheet" type="text/css" href="' . plugin_file('style.css') . '&v=' . $this->version . '" />';
    }

    public function getTemplatesPath(): string
    {
        return $templates_dir = __DIR__ . '/templates';
    }

    public function getTemplateFilePaths(): array
    {
        return glob($this->getTemplatesPath() . '/*.json');
    }

    public function getTemplatesFiles(): array
    {
        $template_files = $this->getTemplateFilePaths();

        $templates = array_map(function ($path) {
            return basename($path, '.json');
        }, $template_files);

        return $templates;
    }

    public function getTemplatesNames(): array
    {
        $template_files = $this->getTemplateFilePaths();

        $templates = array_map(function ($path) {
            return pathinfo($path, PATHINFO_FILENAME);
        }, $template_files);

        return $templates;
    }
    public function getTemplatesNamesFromGithub(): array
    {
        $githubToken = plugin_config_get('githubToken', '');
        if (empty($githubToken)) {
            return [];
        }

        $apiUrl = plugin_config_get('githubApiUrl');

        $headers = [
            "User-Agent: NiimBlue-App",
            "Accept: application/vnd.github.v3+json"
        ];

        if (!empty($githubToken)) {
            $headers[] = "Authorization: token $githubToken";
        }

        $opts = [
            "http" => [
                "method" => "GET",
                "header" => implode("\r\n", $headers)
            ]
        ];

        $context = stream_context_create($opts);
        $response = @file_get_contents($apiUrl, false, $context);

        if ($response === false) {
            return [];
        }

        $files = json_decode($response, true);

        if (!is_array($files)) {
            return [];
        }

        $templateNames = [];

        foreach ($files as $file) {
            if (isset($file['name']) && str_ends_with($file['name'], '.json')) {
                $templateNames[] = pathinfo($file['name'], PATHINFO_FILENAME);
            }
        }

        return $templateNames;
    }
}