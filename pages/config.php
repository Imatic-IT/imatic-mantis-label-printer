<?php



form_security_validate('imatic_print_labels_config');
access_ensure_global_level(config_get('manage_plugin_threshold'));

function config_set_if_needed($p_name, $p_value)
{
    if ($p_value != plugin_config_get($p_name)) {
        plugin_config_set($p_name, $p_value);
    }
}

$t_redirect_url = plugin_page( 'config_page', true );
layout_page_header( null, $t_redirect_url );
layout_page_begin();



config_set_if_needed('assigned_templates', gpc_get('assigned_templates'));
config_set_if_needed('niimblueBaseUrl', gpc_get_string('niimblueBaseUrl'));
config_set_if_needed('branding', gpc_get_string('branding'));
config_set_if_needed('hotline', gpc_get_string('hotline'));
config_set_if_needed('githubApiUrl', gpc_get_string('githubApiUrl'));
config_set_if_needed('githubRawBaseUrl', gpc_get_string('githubRawBaseUrl'));
config_set_if_needed('githubToken', gpc_get_string('githubToken'));

form_security_purge('imatic_print_labels_config');
html_operation_successful($t_redirect_url);
layout_page_end();