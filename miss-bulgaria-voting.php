<?php
/**
 * Plugin Name: Miss Bulgaria Voting
 * Plugin URI: https://github.com/EmmadHussain37/Miss-Bulgaria-Voting
 * Description: A complete WordPress voting platform for beauty pageants with candidate management, free voting, paid Stripe voting, leaderboard rankings, and admin statistics.
 * Version: 1.0.0
 * Author: Emmad Hussain
 * Author URI: https://github.com/EmmadHussain37
 * License: GPL-2.0+
 * Text Domain: miss-bulgaria-voting
 */

if (!defined('ABSPATH')) {
    exit;
}


/*
|--------------------------------------------------------------------------
| Plugin Constants
|--------------------------------------------------------------------------
*/

define('MBV_VERSION', '1.0.5');

define(
    'MBV_PATH',
    plugin_dir_path(__FILE__)
);

define(
    'MBV_URL',
    plugin_dir_url(__FILE__)
);

define(
    'MBV_FILE',
    __FILE__
);


/*
|--------------------------------------------------------------------------
| Autoload Classes
|--------------------------------------------------------------------------
*/

spl_autoload_register(function($class){


    if(strpos($class,'MBV_') !== 0){
        return;
    }


    $class = str_replace(
        'MBV_',
        '',
        $class
    );


    $class = strtolower(
        str_replace(
            '_',
            '-',
            $class
        )
    );


    $paths = array(

        'includes/class-',
        'includes/post-types/class-',
        'includes/database/class-',
        'includes/payments/class-',
        'includes/api/class-',
        'includes/helpers/class-',
        'includes/admin/class-',
        'frontend/class-'

    );



    foreach($paths as $path){


        $file = MBV_PATH . $path . $class . '.php';



        if(file_exists($file)){


            require_once $file;

            return;

        }


    }


});

/*
|--------------------------------------------------------------------------
| Activation / Deactivation
|--------------------------------------------------------------------------
*/


register_activation_hook(
    __FILE__,
    array('MBV_Activator', 'activate')
);


register_deactivation_hook(
    __FILE__,
    array('MBV_Deactivator', 'deactivate')
);



/*
|--------------------------------------------------------------------------
| Start Plugin
|--------------------------------------------------------------------------
*/

function mbv_start_plugin()
{

    require_once MBV_PATH . 'includes/class-plugin.php';

    MBV_Plugin::instance();

}


add_action(
    'plugins_loaded',
    'mbv_start_plugin'
);