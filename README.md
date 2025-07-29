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
- Project-specific templates can be assigned in the configuration page.

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
];

```

## Template Setup

Templates must be cloned manually:

```bash
cd plugins/ImaticLabelPrinter
git clone https://github.com/Imatic-IT/niimblue-templates templates

```

### Templates are .json files stored under:

```
plugins/ImaticLabelPrinter/templates/
```

Each project in Mantis can be assigned a template via the plugin’s configuration page.

## How it works

    The plugin adds a “Print labels” button to the issue view page.

    When clicked, it opens the Niimblue app in a new tab with the selected template and issue data.

    Template placeholders (e.g., {bugId}, {bugUrl}) are replaced automatically before rendering.

    QR codes is replaced with the issue URL automatically.

## Niimblue Integration

This plugin uses a **forked version** of the [Niimblue](https://github.com/MultiMote/niimblue) project to render label
templates in a modern, browser-based UI.

> ⚠️ We use our own **modified [fork](https://github.com/Imatic-IT/niimblue)** of Niimblue with improvements and
> bugfixes specific to Mantis integration.

### 🏢 Maintained by

Imatic IT, s.r.o.

- www.imatic.cz
- info@imatic.cz
