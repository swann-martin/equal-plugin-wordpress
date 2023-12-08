<?
/*
* Plugin Name:       equal
* Plugin URI:        https://yesbabylon.com
* Description:       Dashboard to use eQual.
* Version:           1.0.0
* Requires PHP:      7.4
* Author:            YesBabylon
* License URI:       https://www.gnu.org/licenses/gpl-2.0.html
* Text Domain:       yb-lms
*/


/**
 * Init eQual framework by using file eq.lib.php
 */
function eq_init()
{
    if (!function_exists('eQual::init')) {
        // try to include eQual framework
        $eq_bootstrap = '/var/www/html/eq.lib.php';
        // $eq_bootstrap = dirname(__FILE__) . '/../../../../../eq.lib.php';

        if (file_exists($eq_bootstrap)) {
            if ((include_once($eq_bootstrap)) === false) {
                die('missing mandatory dependency');
            }
        }

        if (!is_callable('equal\services\Container::getInstance')) {
            die('missing mandatory declaration for equal\services\Container::getInstance');
        }

        $context = equal\services\Container::getInstance()->get('context');

        if (!$context) {
            die('unable to retrieve mandatory dependency');
        }
        // make sure the original context holds the header of the original HTTP response (set by WORDPRESS)
        // so that it can be restored afterwards
        $context->getHttpResponse();
    }
    if (!is_callable('eQual::run')) {
        throw new Exception('unable to load eQual dependencies');
    }
}


/**
 * Add style from the css file
 */
function twentytwentyfour_enqueue_style()
{
    wp_enqueue_style('twentytwentyfour-child',  plugin_dir_url(__FILE__) . 'css/style.css', [], false, 'all');
}

add_action('wp_enqueue_scripts', 'twentytwentyfour_enqueue_style');

add_action('admin_enqueue_scripts', function ($hook) {
    $path = plugin_dir_url(__FILE__) . 'js/equal.bundle.js'; // copy of 'js/equal.bundle.js in /var/www/html/public/assets/js/equal.bundle.js
    // $project_js = plugin_dir_url(__FILE__) . 'js/task.js'; //
    wp_enqueue_style('yb_lms_fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css', [], false);
    wp_enqueue_style('yb_lms_material.fonts', 'https://fonts.googleapis.com/css?family=Roboto:300,400,500,600,700,400italic|Roboto+Mono:400,500|Material+Icons|Google+Material+Icons', [], false);
    wp_enqueue_style('yb_lms_material', 'https://unpkg.com/material-components-web@12.0.0/dist/material-components-web.min.css', [], false);
    wp_enqueue_style('yb_lms_jquery.ui', 'https://code.jquery.com/ui/1.12.0/themes/smoothness/jquery-ui.css', [], false);
    wp_enqueue_style('yb_lms_jquery.daterange', 'https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css', [], false);
    // wp_enqueue_script('yb_lms_eq_lib', $path, $project_js); // n'existe pas
    wp_enqueue_script('yb_lms_eq_lib', $path); //
});

add_action('admin_menu', function () {
    // equal logo in uri base64
    $logo = "data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBzdGFuZGFsb25lPSJubyI/Pgo8IURPQ1RZUEUgc3ZnIFBVQkxJQyAiLS8vVzNDLy9EVEQgU1ZHIDIwMDEwOTA0Ly9FTiIKICJodHRwOi8vd3d3LnczLm9yZy9UUi8yMDAxL1JFQy1TVkctMjAwMTA5MDQvRFREL3N2ZzEwLmR0ZCI+CjxzdmcgdmVyc2lvbj0iMS4wIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciCiB3aWR0aD0iMzAwLjAwMDAwMHB0IiBoZWlnaHQ9IjMwMC4wMDAwMDBwdCIgdmlld0JveD0iMCAwIDMwMC4wMDAwMDAgMzAwLjAwMDAwMCIKIHByZXNlcnZlQXNwZWN0UmF0aW89InhNaWRZTWlkIG1lZXQiPgoKPGcgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMC4wMDAwMDAsMzAwLjAwMDAwMCkgc2NhbGUoMC4xMDAwMDAsLTAuMTAwMDAwKSIKZmlsbD0iIzAwMDAwMCIgc3Ryb2tlPSJub25lIj4KPHBhdGggZD0iTTExMCAxNjcwIGwwIC05MjAgNjA1IDAgNjA1IDAgMCAtMTY1IDAgLTE2NSAxNjUgMCAxNjUgMCAwIDE2NSAwCjE2NSA2MDUgMCA2MDUgMCAwIDkyMCAwIDkyMCAtMTM3NSAwIC0xMzc1IDAgMCAtOTIweiBtMjQyMCAwIGwwIC01OTAgLTQ0MCAwCi00NDAgMCAwIDE2NSAwIDE2NSAtMTY1IDAgLTE2NSAwIDAgLTE2NSAwIC0xNjUgLTQ0MCAwIC00NDAgMCAwIDU5MCAwIDU5MAoxMDQ1IDAgMTA0NSAwIDAgLTU5MHoiLz4KPC9nPgo8L3N2Zz4K";
    $slug = 'equal';
    add_menu_page(__('eQual', 'equal'), __('eQual', 'equal'), 'manage_options', $slug, 'Menu::showEqualPage', $logo);
});


/**
 * Display a menu dashboard
 */
class Menu
{

    /**
     * Display the dashboard
     */
    public static function showEqualPage()
    {
        echo <<<EOT
        <script>

        const menu = [
            {
                name: 'Employees',
                description: '',
                icon: 'person',
                type: 'parent',
                children: [
                    {
                        name: 'Employees',
                        description: 'Users of eQual',
                        icon: 'person',
                        type: 'entry',
                        entity: 'projectFlow\\\\Employee',
                        target: 'list.default'
                    },
                    // {
                    //     name: 'Groups',
                    //     description: '',
                    //     icon: 'group',
                    //     type: 'entry',
                    //     entity: 'core\\\\Group',
                    //     target: 'list.default'
                    // }
                ]
            },
            {
                name: 'Projects',
                description: '',
                icon: 'school',
                type: 'parent',
                children: [
                    {
                        name: 'Projects',
                        description: '',
                        icon: '',
                        type: 'entry',
                        entity: 'projectFlow\\\\Project',
                        target: 'list.default'
                    }
                ]
            }
        ];

        $(document).ready(function() {
            var eq = new eQ('eq-listener');
            eq.loadMenu(menu);
            var context = {
                entity:     'projectFlow\\\\Employee',
                type:       'list',
                name:       'default',
                domain:     [],
                mode:       'view',
                lang:		'en'
            };

            const queryString = window.location.search;
            const urlParams = new URLSearchParams(queryString);

            // overload environment lang if set in URL
            if(urlParams.has('lang')) {
                context['lang'] = urlParams.get('lang');
            }

            eq.open(context);


        });
        </script>
        <div id="sb-menu" style="height: 30px;"></div>
        <div id="sb-lang" style="position: absolute;top: 10px;right: 20px;"></div>
        <div id="sb-container" style="margin-top: 20px;"></div>
        EOT;
    }
}

function equal_run_shortcode()
{

    try {
        eq_init();

        // gets projects from eQual
        $projects = array_reverse(projectFlow\Project::search()->read(['id', 'name', 'description', 'status', 'budget', 'date', 'employees_ids' => ['id', 'name'], 'client_id' => ['id', 'name'], 'startdate'])->get(true));
        echo '<div class="tasks_title">' . '<h2>' . esc_html("The eQual Flow Projects") . '</h2>' . '</div>';
        echo '<div class="tasks">';

        foreach ($projects as $project_id => $project) {
            echo '<div class="task ' . ($project['status'] == 'approved' ? 'task_green' : 'task_red') . '"' . ' data-id="' . $project_id . '">'
                . '<div class="task_name ' . ($project['status'] == 'draft' ? 'task_name_green' : 'task_name_red') . '" ' . '> Project : ' . esc_html($project['name']) . '</div>';

            echo '<div>Start Date - ' . date('d/m/Y', $project['startdate']) . '</div>';

            echo '<div class="task_description">';
            echo '<div class="cutoff-container">';
            echo '<p class="cutoff-text">Project description : ' . $project['description'] . '</p>';
            echo '</div>';
            echo '</div>';
            echo '<div>' . '<p><strong>Client :</strong> ' . $project['client_id']['name'] . '</p></div>';
            if (count($project['employees_ids']) > 0) {
                echo '<div>' . '<p><strong>Employee :</strong> ';
                foreach ($project['employees_ids'] as $employee) {
                    echo '<span>';
                    echo ($employee['name'] ? $employee['name'] . "," : "");
                    echo ' </span>';
                }
            };
            // echo '<div>' . $project['status'] == 'draft' ? 'U+1F603' : '😛' . '</div>';
            echo '</div></p></div>';
        }
        // End tasks div.
        echo '</div>';

        // End container
        echo '</div>';
    } catch (Exception $e) {
        $output = "une erreur est survenue : " . $e->getMessage();
    }


    return $output;
}

add_shortcode('equal_projects', 'equal_run_shortcode');
