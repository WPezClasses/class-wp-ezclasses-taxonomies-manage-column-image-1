<?php
/** 
 * Using the WordPress plugin WPCustom Category Image? This class adds the image to a tax's listing; The ezWay. 
 *
 * TODO
 *
 * PHP version 5.3
 *
 * LICENSE: TODO
 *
 * @package WPezClasses
 * @author Mark Simchock <mark.simchock@alchemyunited.com>
 * @since 0.5.1
 * @license TODO
 */
 
/**
 * == Change Log == 
 *
 * -- 0.5.0 - Mon 26 Jan 2015
 *
 * ---- Pop the champagne!
 */
 
/**
 * == TODO == 
 *
 *
 */

// No WP? Die! Now!!
if (!defined('ABSPATH')) {
	header( 'HTTP/1.0 403 Forbidden' );
    die();
}

if ( ! class_exists('Class_WP_ezClasses_Taxonomies_Manage_Column_Image_1') ) {
  class Class_WP_ezClasses_Taxonomies_Manage_Column_Image_1 extends Class_WP_ezClasses_Master_Singleton{
  
    private $_version;
	private $_url;
	private	$_path;
	private $_path_parent;
	private $_basename;
	private $_file;
  
    protected $_arr_init;
	  
	public function __construct() {
	  parent::__construct();
	}
		
	/**
	 *
	 */
	public function ez__construct(){
	
	  $this->_str_action = 'init';
	  $this->_int_priority = 100;
	
	  $this->setup();
	  
	  /**
	   * IMPORTANT: we want to delay things a bit to be sure that any custom taxonomies have already been added
	   */
	  add_action( $this->_str_action, array($this, 'wp_add_action') );
	  
	}
	
	
	public function wp_add_action(){

	  $arr_ez_defaults = $this->ez_defaults();
	  
	  $arr_todo = $this->manage_column_image_todo();
	  
	  $this->_arr_init = WPezHelpers::ez_array_merge(array($arr_ez_defaults, $arr_todo));
	  
	  $this->manage_column_image_do();	
	
	}
	
	
	/**
	 * currently not in use
	 */
	protected function setup(){
	
	  $this->_version = '0.5.0';
	  $this->_url = plugin_dir_url( __FILE__ );
	  $this->_path = plugin_dir_path( __FILE__ );
	  $this->_path_parent = dirname($this->_path);
	  $this->_basename = plugin_basename( __FILE__ );
	  $this->_file = __FILE__ ;
	}
	
	/**
	 * currently not in use
	 */
	protected function ez_defaults(){
	
	  $arr_defaults = array(
	  
	  	'active'			 					=> true,
		'active_true'							=> true,	// use the active true "filtering"
		'filters'								=> false, 	// currently NA
		'arr_arg_validation'					=> false, 	// currently NA
		);
	
	  return $arr_defaults;
	}
	
	
	/**
	 * These are the basic blanks you need to fill in
	 */

	protected function manage_column_image_todo(){
	
	  $arr_todo = array(
	    
		'include'				=> $this->include_taxonomies(),
		'exclude'				=> $this->exclude_taxonomies(),
		'column_header'			=> 'Image',
		'image_size'			=> 'thumbnail',
		'no_image'				=> 'No Image',
		'image_option_prefix'	=> 'categoryimage_',
	
	  );
	  
	  return  $arr_todo;
	}
	
	/**
	 * associative array. pseudo code .e.g., $arr_include['taxonomy'] => bool
	 */
	protected function include_taxonomies(){
	
	  return array();
	}
	
	/**
	 * associative array. pseudo code .e.g., $arr_exclude['taxonomy'] => bool
	 *
	 * or return an empty array. 
	 */
	protected function exclude_taxonomies(){
	
	  return array();
	}
	
	
	/**
	 *
	 */
	protected function manage_column_image_do(){
	
	  $arr_init = $this->_arr_init;
	
	  $arr_ins = $arr_init['include'];
	  $arr_exs = $arr_init['exclude'];
	  
	  // start w/ using all the taxonomies
	  $arr_taxs = get_taxonomies(array(), 'objects');		  
	  	
	  foreach ($arr_taxs as $str_tax => $obj_value){
	  
	    // if it's an exclude then skip it
	    if ( isset($arr_exs[$str_tax]) && $arr_exs[$str_tax] === true ){
		  continue;
		}
		
		// in "ins" is empty OR if there are "ins" and the tax is an ins...
		if ( empty($arr_ins) || ( ! empty($arr_ins) && is_array($arr_ins) && isset($arr_ins[$str_tax]) && $arr_ins[$str_tax] === true ) ){
		  add_filter( 'manage_edit-' . $str_tax . '_columns', array($this, 'add_taxonomy_column') );
	      add_filter( 'manage_' . $str_tax . '_custom_column', array($this, 'add_taxonomy_column_image'), 10, 3 );
		}
	  }
	}
	
	/**
	 * all the column
	 */
	public function add_taxonomy_column( $columns ) {
	  $new_column = array();
	  // cb = checkbox
	  $new_columns['cb'] = $columns['cb'];
	  $new_columns['ez_tax_img'] = $this->_arr_init['column_header'];
	  unset( $columns['cb'] );
	  return array_merge( $new_columns, $columns );
	}
	
	/**
	 * add the image to the added column
	 */
	function add_taxonomy_column_image( $columns, $column, $int_term_id ) {
	  
	  if ( $column == 'ez_tax_img' ){
	  
	     $int_get_post_thumbnail_id = get_option($this->_arr_init['image_option_prefix'] . $int_term_id);
		 if ($int_get_post_thumbnail_id !== false){
		
		   $str_get_the_post_thumbnail = wp_get_attachment_image( $int_get_post_thumbnail_id, $this->_arr_init['image_size']);
		   if ( $str_get_the_post_thumbnail === false ){
		 
		 	  $columns = '<span>' . $this->_arr_init['no_image'] . ' </span>';
		   } else {
		 
		 	  $columns = '<span>' . $str_get_the_post_thumbnail . '</span>';
		   }
		 } else {
		   $columns = '<span>' . $this->_arr_init['no_image'] . ' </span>';
		 }
	    return $columns;
	  }
	}
	
  }
} 
