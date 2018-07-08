<?php
/**
 * WPLMS Digimember Init
 *
 * @author 		VibeThemes
 * @category 	Initialise
 * @package 	Wplms-digimember/Includes
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Wplms_Digimember_Init{


	public static $instance;
    
    public static function init(){

        if ( is_null( self::$instance ) )
            self::$instance = new Wplms_Digimember_Init();
        return self::$instance;
    }

	private function __construct(){

		add_action('admin_notices',array($this,'check_digimember_plugin'));

	}


	function check_digimember_plugin(){
		if ( !in_array( 'digimember/digimember.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    
			?>
		    <div class="notice notice-warning is-dismissible">
		        <p><?php _ex( 'Please install Digimember plugin.', 'notice on admin screen','wplms-digi' ); ?></p>
		    </div>
		    <?php
		}
	}

}
Wplms_Digimember_Init::init();