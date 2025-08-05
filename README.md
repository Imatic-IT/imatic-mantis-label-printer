# 🏷️ Imatic Label Printer Plugin for MantisBT

This MantisBT plugin allows you to **print issue labels** using customizable templates rendered through
the [Niimblue templating tool](https://github.com/Imatic-IT/niimblue-templates).

---

## ✨ Features

- Adds a **“Print labels”** button to issue detail pages.
- Labels are rendered using **external Niimblue templates**.
- Labels can contain:
    - Issue ID
    - Issue link (QR code)
    - Custom branding
    - Hotline
    - Summary
- Project-specific templates can be assigned in the configuration page.
- Templates are fetched dynamically from a GitHub repository.

---

## 🛠️ Installation

1. Clone the plugin into the MantisBT plugins directory:
   ```bash
   git clone https://github.com/Imatic-IT/niimblue-templates plugins/ImaticLabelPrinter/templates
   ```
2. Place this plugin under:
   ```bash
   plugins/ImaticLabelPrinter
   ```
3. Enable the plugin in MantisBT:

## ⚙️ Configuration

You can access the plugin’s configuration via the MantisBT plugin configuration page.

Example configuration:

```php
[
    'niimblueBaseUrl' => 'http://localhost:5173', // URL of Niimblue renderer
    'basicAuth' => [
        'username' => 'niimTemplater',
        'password' => 'uSLR8SokEqoFYfX',
    ],
    'replacements' => [
        'bugId',
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

    // GitHub template sync settings
    'githubApiUrl' => 'https://api.github.com/repos/Imatic-IT/niimblue-templates/contents',
    'githubRawBaseUrl' => 'https://raw.githubusercontent.com/Imatic-IT/niimblue-templates/master',
    'githubToken' => '',
    'canPushTemplatesAccessLevel' => ADMINISTRATOR // Access level required to push new templates in nimmblue interface
];
```

## Supported Placeholders

In your Niimblue templates, you can use the following placeholders to dynamically insert issue data into the label
content and QR codes:

| Placeholder  | Description                 |
|--------------|-----------------------------|
| `{bugId}`    | The ID of the current issue |
| `{summary}`  | The issue summary/title     |
| `{hotline}`  | Hotline number from config  |
| `{branding}` | Branding text from config   |
| `{bugUrl}`   | Direct URL to the issue     |

These placeholders will be automatically replaced with their corresponding values before rendering the template.

You can place them **anywhere in the text or QR code** fields of your JSON template.

Each project in Mantis can be assigned a template via the plugin’s configuration page.

## How it works

- The plugin adds a “Print labels” button to the issue view page.
- When clicked, it opens the Niimblue app in a new tab with the selected template and issue data.
- Template placeholders (e.g., `{bugId}`, `{bugUrl}`) are replaced automatically before rendering.

## Niimblue Integration

This plugin uses a **forked version** of the [Niimblue](https://github.com/MultiMote/niimblue) project to render label
templates in a modern, browser-based UI.

> ⚠️ We use our own **modified [fork](https://github.com/Imatic-IT/niimblue/tree/master)** of Niimblue with improvements
> and
> bugfixes specific to Mantis integration.

### Maintained by

Imatic IT, s.r.o.

- [www.imatic.cz](https://www.imatic.cz)
- info@imatic.cz