<?php

class ImaticLabelPrinterPlugin extends MantisPlugin
{
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
            'defaultTemplate' => '',
            'githubApiUrl' => 'https://api.github.com/repos/Imatic-IT/niimblue-templates/contents',
            'githubRawBaseUrl' => 'https://raw.githubusercontent.com/Imatic-IT/niimblue-templates/master',
            'githubToken' => '',
            'canPushTemplatesAccessLevel' => ADMINISTRATOR
        ];
    }

    public function hooks(): array
    {
        return [
            'EVENT_VIEW_BUG_DETAILS' => 'bug_view_details',
            'EVENT_LAYOUT_BODY_END' => 'layout_body_end_hook',
        ];
    }


    function bug_view_details()
    {
        if (!isset($_GET['id'])) {
            return;
        }

        $bugId = (int)$_GET['id'];
        $projectId = (int)bug_get_field($bugId, 'project_id');
        $assignedTemplates = plugin_config_get('assigned_templates');

        if (!$this->projectHasAssignedTemplate($projectId)) {
            return;
        }

        $templateId = htmlspecialchars($assignedTemplates[$projectId], ENT_QUOTES, 'UTF-8');
        $niimBlueBaseUrl = $this->getNiimblueBaseUrl();
        $canPushTemplates = access_has_global_level(plugin_config_get('canPushTemplatesAccessLevel'));

        $jsonData = json_encode([
            'templateId' => $templateId,
            'replacements' => $this->getReplacements(),
            'canPushTemplates' => $canPushTemplates,
        ]);

        $encodedData = base64_encode($jsonData);

        echo '<a id="printLabelsButton" data="' . $encodedData . '" target="_blank" href="'
            . $niimBlueBaseUrl . '/?data=' . urlencode($encodedData)
            . '" class="btn btn-primary btn-white btn-round btn-sm">&#128424; Tag</a>';
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