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
		add_action ('digimember_purchase', array($this,'digitest_purchase'), 10, 4);
		add_filter('wplms_course_product_metabox',array($this,'select_digimember_product'),99);
		add_filter('custom_meta_box_type',array($this,'meta_digimember_product'),10,5);
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

	function select_digimember_product($settings){
		if ( !in_array( 'digimember/digimember.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			$settings[]=array( // Text Input
				'label'	=> __('Connected Course','wplms-digi'), // <label>
				'desc'	=> __('Connect this question to a course','wplms-digi'), // description
				'id'	=> $prefix.'question_course', // field id and name
				'type'	=> 'digimember_product', // type of field
			);
		}
		return $settings;
	}

	function meta_digimember_product($type,$meta,$id,$desc,$post_type){

		if($type == 'digimember_product'){
			$options = array();
			global $wpdb;
			$table_name = $wpdb->prefix.'digimember_product';
			$products = $wpdb->get_results("SELECT id, name FROM $table_name LIMIT 0,99");
			if(!empty($poducts)){
				foreach($products as $product){
					$options[] = array('value'=>$product->id,'label'=>$product->name);
				}
			}
			echo '<select name="' . $id . '" id="' . $id . '" class="select">';
            if($meta == '' || !isset($meta)){$meta=$std;}
			foreach ( $options as $option )
				echo '<option' . selected( esc_attr( $meta ), $option['value'], false ) . ' value="' . $option['value'] . '">' . $option['label'] . '</option>';
			echo '</select><br />' . $desc;
		}
		return $type;
	}
	function digitest_purchase ($user_id, $product_id, $order_id, $reason){
	    switch ($reason)
	    {
	        case 'order_paid':
	        // handle paid orders here
	        break;

	        case 'order_cancelled':
	        // trade canceled payment here
	        break;

	        case 'payment_missing':
	        // handle missing rebilling payment here
	        break;
	    }
	}

}
Wplms_Digimember_Init::init();