<?php
/**
 * Slack Integration
 * Copyright (C) Karim Ratib (karim.ratib@gmail.com)
 *
 * Slack Integration is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License 2
 * as published by the Free Software Foundation.
 *
 * Slack Integration is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Slack Integration; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA
 * or see http://www.gnu.org/licenses/.
 */

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

$basicAuthUser = gpc_get_string('basicAuthUsername');
$basicAuthPass = gpc_get_string('basicAuthPassword');
if ($basicAuthUser && $basicAuthPass) {
    config_set_if_needed('basicAuth', [
        'username' => $basicAuthUser,
        'password' => $basicAuthPass
    ]);
} else {
    config_set_if_needed('basicAuth', []);
}

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