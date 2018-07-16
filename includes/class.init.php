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
		if ( !in_array( 'digimember/digimember.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) )  || (function_exists('is_plugin_active') && is_plugin_active('digimember/digimember.php')) ){
			$settings[]=array( // Text Input
				'label'	=> __('Connected Digimember product','wplms-digi'), // <label>
				'desc'	=> __('Connect this question to a course','wplms-digi'), // description
				'id'	=> 'vibe_digimember_product', // field id and name
				'type'	=> 'digimember_product', // type of field
			);
		}
		return $settings;
	}

	function meta_digimember_product($type,$meta,$id,$desc,$post_type){

		if($type == 'digimember_product'){
			$options = array(array('value'=>'','label'=>_x('Select','select a product','wplms-digi')));
			global $wpdb;
			$table_name = $wpdb->prefix.'digimember_product';
			$products = $wpdb->get_results("SELECT id, name FROM $table_name LIMIT 0,99");
			
			if(!empty($products)){
				foreach($products as $product){
					$options[] = array('value'=>$product->id,'label'=>$product->name);
				}
			}
			
			echo '<select name="' . $id . '" id="' . $id . '" class="select">';
            if($meta == '' || !isset($meta)){$meta=$std;}
            if(!empty($options))
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
	        	global $wpdb;
	        	$courses = $wpdb->get_results("SELECT meta_value FROM $wpdb->postmeta where meta_key = 'vibe_digimember_product'");
	        	if(!empty($courses)){
	        		foreach($courses as $course){
	        			$course_id = $course->meta_value;
	        			bp_course_add_user_to_course($user_id,$course_id);
	        		}
	        	}

	        	/* COMMISSION CALCULATION
	        	if(function_exists('vibe_get_option'))
			      $instructor_commission = vibe_get_option('instructor_commission');
			    
			    if($instructor_commission == 0)
			      		return;
			      	
			    if(!isset($instructor_commission) || !$instructor_commission)
			      $instructor_commission = 70;

			    $commissions = get_option('instructor_commissions');
				foreach($commission_array as $item_id=>$commission_item){

					foreach($commission_item['course'] as $course_id){
						
						if(count($commission_item['instructor'][$course_id]) > 1){     // Multiple instructors
							
							$calculated_commission_base=round(($commission_item['total']*($instructor_commission/100)/count($commission_item['instructor'][$course_id])),0); // Default Slit equal propertion

							foreach($commission_item['instructor'][$course_id] as $instructor){
								if(empty($commissions[$course_id][$instructor]) && !is_numeric($commissions[$course_id][$instructor])){
									$calculated_commission_base = round(($commission_item['total']*$instructor_commission/100),2);
								}else{
									$calculated_commission_base = round(($commission_item['total']*$commissions[$course_id][$instructor]/100),2);
								}
								$calculated_commission_base = apply_filters('wplms_calculated_commission_base',$calculated_commission_base,$instructor);

		                        bp_course_record_instructor_commission($instructor,$calculated_commission_base,$course_id,array('origin'=>'woocommerce','order_id'=>$order_id,'item_id'=>$item_id));
		                        
							}
						}else{
							if(is_array($commission_item['instructor'][$course_id]))                                    // Single Instructor
								$instructor=$commission_item['instructor'][$course_id][0];
							else
								$instructor=$commission_item['instructor'][$course_id]; 
							
							if(isset($commissions[$course_id][$instructor]) && is_numeric($commissions[$course_id][$instructor]))
								$calculated_commission_base = round(($commission_item['total']*$commissions[$course_id][$instructor]/100),2);
							else
								$calculated_commission_base = round(($commission_item['total']*$instructor_commission/100),2);

							$calculated_commission_base = apply_filters('wplms_calculated_commission_base',$calculated_commission_base,$instructor);

		                    bp_course_record_instructor_commission($instructor,$calculated_commission_base,$course_id,array('origin'=>'woocommerce','order_id'=>$order_id,'item_id'=>$item_id));
						}   
					}

				} // End Commissions_array 
				*/
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