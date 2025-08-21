<?php
access_ensure_global_level(config_get('manage_plugin_threshold'));

layout_page_header(plugin_lang_get('title'));

layout_page_begin('manage_overview_page.php');

print_manage_menu('manage_plugin_page.php');

?>

<?php

$channels = [];

$projects = project_get_all_rows();
$assigned_templates = plugin_config_get('assigned_templates', []);

$templates_dir = __DIR__ . '/../templates';
$template_files = glob($templates_dir . '/*.json');

$templates = plugin_get()->getTemplatesNamesFromGithub();


?>

    <div class="col-md-12 col-xs-12">
        <div class="space-10"></div>
        <div class="form-container">
            <form action="<?php echo plugin_page('config') ?>" method="post">
                <?php echo form_security_field('imatic_print_labels_config') ?>
                <div class="widget-box widget-color-blue2">
                    <div class="widget-header widget-header-small">
                        <h4 class="widget-title lighter">
                            <i class="ace-icon fa fa-exchange"></i>
                            Configuration
                        </h4>
                    </div>
                    <div class="widget-body">
                        <div class="widget-main no-padding">
                            <div class="table-responsive">

                                <div class="widget-header widget-header-small">
                                    <h4 class="widget-title lighter">
                                        <i class="ace-icon fa fa-cog"></i>
                                        General settings
                                    </h4>
                                </div>

                                <table class="table table-bordered table-condensed table-striped">
                                    <tr>
                                        <th class="category width-40">
                                            Niimblue base url
                                        </th>
                                        <td>
                                            <input type="text" name="niimblueBaseUrl" class="input-sm form-control"
                                                   value="<?php echo plugin_config_get('niimblueBaseUrl', 'http://localhost:5173') ?>"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="category width-40">
                                            Branding
                                        </th>
                                        <td>
                                            <input type="text" name="branding" class="input-sm form-control"
                                                   value="<?php echo plugin_config_get('branding', 'www.imatic.cz') ?>"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="category width-40">
                                            Hotline number
                                        </th>
                                        <td>
                                            <input type="text" name="hotline" class="input-sm form-control"
                                                   value="<?php echo plugin_config_get('hotline', '+420 944 162 732') ?>"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="category width-40">
                                            GitHub API URL
                                        </th>
                                        <td>
                                            <input type="text" name="githubApiUrl" class="input-sm form-control"
                                                   value="<?php echo plugin_config_get('githubApiUrl') ?>"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="category width-40">
                                            GitHub Raw Base URL
                                        </th>
                                        <td>
                                            <input type="text" name="githubRawBaseUrl" class="input-sm form-control"
                                                   value="<?php echo plugin_config_get('githubRawBaseUrl') ?>"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="category width-40">
                                            GitHub Token
                                        </th>
                                        <td>
                                            <input type="password" name="githubToken" class="input-sm form-control"
                                                   value="<?php echo string_attribute(plugin_config_get('githubToken')) ?>"/>
                                        </td>
                                    </tr>
<!--                                    TODO: COMMENTED FOR NOW  -->
<!--                                    <tr>-->
<!--                                        <th class="category width-40">-->
<!--                                            Default template-->
<!--                                        </th>-->
<!--                                        <td>-->
<!--                                            <select name="defaultTemplate" class="input-sm form-control">-->
<!--                                                <option value="-1"></option>-->
<!--                                                --><?php //foreach ($templates as $template) {
//                                                    $selected = (plugin_config_get('defaultTemplate') == $template) ? 'selected' : '';
//                                                    echo '<option value="' . $template . '" ' . $selected . '>' . string_display_line($template) . '</option>';
//                                                } ?>
<!--                                            </select>-->
<!--                                        </td>-->
<!--                                    </tr>-->
                                </table>
                                <div class="widget-header widget-header-small">
                                    <h4 class="widget-title lighter">
                                        <i class="ace-icon fa fa-exchange"></i>
                                        Assign templates to projects
                                    </h4>
                                </div>
                                <div class="space-2"></div>

                                <?php
                                if (count($templates) == 0) {
                                    echo '<div class="alert alert-warning">No templates found. Please add templates in the plugin directory.</div>';
                                } elseif (count($projects) == 0) {
                                    echo '<div class="alert alert-warning">No projects found. Please create projects in MantisBT.</div>';
                                }
                                ?>


                                <table class="table table-bordered table-condensed table-striped">
                                    <?php
                                    foreach ($projects as $key => $project) {
                                        ?>
                                        <tr>
                                            <th style="width: 35px;">
                                                <input type="checkbox" name="assigned_projects[]"
                                                       value="<?php echo (int)$project['id'] ?>">
                                            </th>
                                            <th class=" category width-40">
                                                <?php echo string_display_line($project['name']) ?>
                                            </th>
                                            <td>
                                                <?php
                                                $defaultTemplate = plugin_config_get('defaultTemplate') ?? '-1';
                                                ?>

                                                <select name="assigned_templates[<?php echo (int)$project['id'] ?>]"
                                                        class="input-sm form-control">
                                                    <option value="-1"></option>
                                                    <?php foreach ($templates as $template) {
                                                        $selected = isset($assigned_templates[$project['id']]) && $assigned_templates[$project['id']] != -1
                                                            ? (($assigned_templates[$project['id']] == $template) ? 'selected' : '')
                                                            : (($template == $defaultTemplate) ? 'selected' : '');

                                                        echo '<option value="' . $template . '" ' . $selected . '>' . string_display_line($template) . '</option>';
                                                    } ?>
                                                </select>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </table>
                                <div class="widget-toolbox padding-8 clearfix">
                                    <div class="form-inline pull-left">
                                        <label class="inline">
                                            <input class="ace check_all input-sm" type="checkbox"
                                                   id="assign_template_select_all" name="assign_template_select_all"
                                                   value="all"><span
                                                    class="lbl padding-6"><?php echo lang_get('select_all') ?> </span>
                                        </label>
                                        <select name="bulk_assign_template"
                                                class="input-sm form-control">
                                            <option value="-1"></option>
                                            <?php foreach ($templates as $template) {
                                                echo '<option value="' . $template . '">' . string_display_line($template) . '</option>';
                                            } ?>
                                        </select>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="widget-toolbox padding-8 clearfix">
                            <input type="submit" class="btn btn-primary btn-white btn-round" value="Save"/>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>


<?php
layout_page_end();
