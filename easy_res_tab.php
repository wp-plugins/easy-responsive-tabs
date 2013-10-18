<?php
/*
  Plugin Name: Easy Responsive Tabs
  Plugin URI: http://www.oscitasthemes.com
  Description: Make bootstrap tabs res.
  Version: 1.1
  Author: oscitas
  Author URI: http://www.oscitasthemes.com
  License: Under the GPL v2 or later
 */
define('ERT_VERSION', '1.0');
define('ERT_BASE_URL', plugins_url('',__FILE__));
define('ERT_ASSETS_URL', ERT_BASE_URL . '/assets/');
define('ERT_BASE_DIR_LONG', dirname(__FILE__));
class easyResponsiveTabs {
	private $resjs_path;
	private $rescss_path;
	private $plugin_name;

	function __construct(){
		$pluginmenu=explode('/',plugin_basename(__FILE__));
		$this->plugin_name=$pluginmenu[0];
		$this->resjs_path='js/bootstrap-tabdrop.js';
		$this->rescss_path='css/tabdrop.css';

		add_action('init',array($this,'ert_tab_shortcode'));
		if(!apply_filters('plugin_oscitas_theme_check',false)){
			add_action('admin_menu', array($this, 'ert_register_admin_menu'));
			add_filter( "plugin_action_links_".plugin_basename( __FILE__ ), array($this, 'osc_ert_settings_link' ));
			add_action('admin_enqueue_scripts', array($this, 'ert_admin_scripts'));
			add_action('wp_enqueue_scripts', array($this, 'ert_enqueue_scripts'));
		}
		add_shortcode('restabs', array($this,'ert_theme_tabs'));
		add_shortcode('restab', array($this,'ert_theme_tab'));

	}

	public function ert_activate_plugin(){
		$isSet=apply_filters('ert_custom_option',false);
		if (!$isSet) {
			update_option( 'ERT_BOOTSTRAP_JS_LOCATION', 1 );
			update_option( 'ERT_BOOTSTRAP_CSS_LOCATION', 1 );
		}
	}

	public function ert_deactivate_plugin(){
		$isSet=apply_filters('ert_custom_option',false);
		if (!$isSet) {
			delete_option( 'ERT_BOOTSTRAP_JS_LOCATION' );
			delete_option( 'ERT_BOOTSTRAP_CSS_LOCATION');
		}
	}

	public function ert_register_admin_menu(){
		$isSet=apply_filters('ert_custom_option',false);
		if (!$isSet) {
			add_menu_page('ERT Settings', ' ERT Settings', 'manage_options', $this->plugin_name,array( $this,'osc_ebs_setting_page' ), ERT_ASSETS_URL.'images/menu_icon.png');
		}
	}

	public function osc_ert_settings_link( $links ) {
		$isSet=apply_filters('ert_custom_option',false);
		if (!$isSet) {
			$settings_link = '<a href="admin.php?page='.$this->plugin_name.'">Settings</a>';
			array_push( $links, $settings_link );
		}
		return $links;
	}


	public function osc_ebs_setting_page(){
		include 'files/ert_settings.php';
	}

	public function ert_tab_shortcode(){

		if (!current_user_can('edit_posts') && !current_user_can('edit_pages'))
			return;

		if (get_user_option('rich_editing') == 'true') {
			add_filter("mce_external_plugins", array( $this,"osc_add_ert_plugin"));
			add_filter('mce_buttons_3', array( $this,'osc_register_ert_button'));
		}
	}

	public function osc_register_ert_button($buttons) {
		$buttons[]='oscitasrestabs';
		return $buttons;
	}

	public function osc_add_ert_plugin($plugin_array) {
		$plugin_array['oscitasrestabs']=plugins_url('/assets/js/tabs_plugin.js', __FILE__);
		return $plugin_array;
	}

	public function ert_theme_tabs($params, $content = null) {
		global $_ert_restabs;
		extract(shortcode_atts(array(
					'id' => count($_ert_restabs),
					'class' => '',
					'pills' =>'',
					'position'=>'',
					'text'=>'',
					'icon'=>''


				), $params));
		$_ert_restabs[$id] = array();
		do_shortcode($content);
		if($icon=='true'){
			$text='<i class="res_tab_icon"></i>'.$text;
		}
		if($pills=='nav-pills'){
			$navclass='nav-pills';
		}
		else{
			$navclass='nav-tabs';
		}
		if($position=='tabs-below'){
			$scontent = '<div
    class="tab-content">' . implode('', $_ert_restabs[$id]['panes']) . '</div><ul class="nav osc-res-nav '.$navclass.'" id="oscitas-restabs-' . $id . '">' . implode('', $_ert_restabs[$id]['tabs']) . '</ul>';
		} else{
			$scontent = '<ul class="nav osc-res-nav '.$navclass.'" id="oscitas-restabs-' . $id . '">' . implode('', $_ert_restabs[$id]['tabs']) . '</ul><div
    class="tab-content">' . implode('', $_ert_restabs[$id]['panes']) . '</div>';
		}

		if (trim($scontent) != "") {
			$output = '<div class="tabbable '.$class.' '.$position.'">' . $scontent;
			$output .= '</div>';
			$output.="<script>
        jQuery(document).ready(function() {
            jQuery('.osc-res-nav').tabdrop({
            'text': '".$text."'
            });
        });
    </script>";
			return $output;
		} else {
			return "";
		}

	}


	public function ert_theme_tab($params, $content = null) {
		global $_ert_restabs;
		extract(shortcode_atts(array(
					'title' => 'title',
					'active' => '',
				), $params));

		$index = count($_ert_restabs) - 1;
		if (!isset($_ert_restabs[$index]['tabs'])) {
			$_ert_restabs[$index]['tabs'] = array();
		}
		$pane_id = 'ert-pane-' . $index . '-' .  count($_ert_restabs[$index]['tabs']);
		$_ert_restabs[$index]['tabs'][] = '<li class="' . $active . '"><a href="#' . $pane_id . '" data-toggle="tab">' . $title
			. '</a></li>';
		$_ert_restabs[$index]['panes'][] = '<div class="tab-pane ' . $active . '" id="'
			. $pane_id . '">'
			. do_shortcode
			(trim($content)) . '</div>';
	}
	public function ert_enqueue_scripts(){
		wp_enqueue_script('jquery');
		$isSet=apply_filters('ert_custom_option',false);
		if (!$isSet) {
			$ertjs = get_option( 'ERT_BOOTSTRAP_JS_LOCATION', 1 );
			$ertcss = get_option( 'ERT_BOOTSTRAP_CSS_LOCATION', 1 );
			if($ertcss==1){
				if (!apply_filters('ert_bootstrap_css_url',false)) {
					wp_enqueue_style('bootstrap_tab',ERT_ASSETS_URL.'css/bootstrap_tab.min.css');
					wp_enqueue_style('bootstrap_dropdown',ERT_ASSETS_URL.'css/bootstrap_dropdown.min.css');
				}
				else{
					wp_enqueue_style('ertbootstrap', apply_filters('ert_bootstrap_css_url',false));
				}
			}
			if($ertjs==1){
				if (!apply_filters('ert_bootstrap_js_url',false)) {
					wp_enqueue_script('bootstrap_dropdown',ERT_ASSETS_URL.'js/bootstrap-dropdown.js',array('jquery'),ERT_VERSION,true);
					wp_enqueue_script('bootstrap_tab',ERT_ASSETS_URL.'js/bootstrap-tab.js',array('jquery'),ERT_VERSION,true);}
				else{
					wp_enqueue_script('ertbootstrap', apply_filters('ert_bootstrap_js_url',false),array('jquery'),ERT_VERSION,true);
				}

			}
		}
		wp_enqueue_script('ert_tab_js',ERT_ASSETS_URL.$this->resjs_path,array('jquery'),ERT_VERSION,true);
		wp_enqueue_style('ert_tab_css',ERT_ASSETS_URL.$this->rescss_path);
		wp_enqueue_style('ert_tab_icon_css',ERT_ASSETS_URL.'css/res_tab_icon.css');

	}

	public function ert_admin_scripts(){
		global $pagenow;
		if ('post-new.php' == $pagenow || 'post.php' == $pagenow) {
			wp_enqueue_script('jquery');
			if (!apply_filters('ert_custom_bootstrap_admin_css',false)) {
				wp_enqueue_style('bootstrap_admin', ERT_ASSETS_URL.'css/bootstrap_admin.min.css');
			}
			wp_enqueue_style('ert_tab_icon_css',ERT_ASSETS_URL.'css/res_tab_icon.css');
		}
	}
}
$ertrestab= new easyResponsiveTabs();
register_activation_hook(__FILE__, array($ertrestab,'ert_activate_plugin'));
register_deactivation_hook(__FILE__, array($ertrestab,'ert_deactivate_plugin'));
