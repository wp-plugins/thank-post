<?php
/*
Plugin Name: Thank Post
Plugin URI: http://icode.it.tc
Description: The "Thank Post" simply shows another approach to gratitude the author for the hard work. It allows 1 thank per ip and uses ajax. Meaning there is not thank spam.
Version: 2.6
Author: Nulled_Icode
Author URI: http://icode.it.tc
License:  GPL2
*/
if(!class_exists("Thank_Post")){

	Class Thank_Post {

		var $table = "thank";
    var $ver = 2.6;


		function init () {


			add_filter( 'the_content', array(&$this, 'add_thank_content') );
			add_filter('wp_head',array(&$this, 'head_js'));
			add_action('wp_ajax_myajax-submit',array(&$this, 'callback_ajax'));
			add_action('wp_ajax_nopriv_myajax-submit',array(&$this, 'callback_ajax'));
			add_action('wp_ajax_thankpost_thank',array(&$this, 'callback_ajax2'));
			wp_enqueue_script( 'jquery');
      add_filter('plugin_action_links', array(&$this, 'add_settings_link'), 10, 2 );

		}


		function queryit ( $query ) {
			global $wpdb;
			return str_replace("{table}",$wpdb->prefix.$this->table,$query);

		}
		function install () {
			global $wpdb;
			$table = $wpdb->prefix.$this->table;
			if($wpdb->get_var("SHOW TABLES LIKE '$table'") != $table) {

				$query = <<<END
				CREATE TABLE IF NOT EXISTS {table} (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`post_id` int(11) NOT NULL,
				`ip` varchar(100) NOT NULL,
				PRIMARY KEY (`id`)
				) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
END;

				$finalquery = $this->queryit($query);
				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
				dbDelta($finalquery);


			}

			// Default Settings!
			add_option('ThankPost_design_before',"This post was thanked %thank% time(s). Thank it ?");
			add_option('ThankPost_design_after',"This post was thanked %thank% time(s).");
			add_option('ThankPost_location',"Append");
			add_option('Thankpost_show_front',"no");
			add_option('ThankPost_show_page',"no");
			add_option('ThankPost_image',"");
			add_option('ThankPost_tinyimage',"");
			add_option('ThankPost_version',$this->ver);
			add_option('ThankPost_actions',"");
			
		}

		function check_ip () {
			global $wpdb,$post;
			$table = $wpdb->prefix.$this->table;
			$ip = $_SERVER['REMOTE_ADDR'];
			$num = 	$thanks = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE ip='$ip' AND post_id='$post->ID' "));

			if($num == 0 ) {

				return true;

			}else{

				return false;
			}
		}

		function get_thanks (){
			global $wpdb,$post;
			$table = $wpdb->prefix.$this->table;
			$thanks = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE post_id='$post->ID' "));

			return $thanks;


		}


function get_thanks_via_id($id){

		global $wpdb,$post;
			$table = $wpdb->prefix.$this->table;
			$thanks = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE post_id='$id' "));

			return $thanks;



}

		function add_settings_link($links, $file) {
			static $this_plugin;
			if (!$this_plugin) $this_plugin = plugin_basename(__FILE__);

			if ($file == $this_plugin){
				$settings_link = '<a href="options-general.php?page=thankpost_options">'.__("Settings", "thankspost_set").'</a>';
				array_unshift($links, $settings_link);
			}
			return $links;
		}


		function thank_stat() {
		
		$imgpath = stripcslashes(get_option('ThankPost_tinyimage'));
	if($imgpath == "") {
		
			$path = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
			$imgpath = $path."/thanktiny.png";
		
	}
			
			$thanks = $this->get_thanks();
			$img_style = "	background: url($imgpath) no-repeat;
			padding: 0 5px 0 18px;";
$front = get_option("Thankpost_show_front");
			$page = get_option("Thankpost_show_page");
    
	
		if(is_single() && !is_page() ) {
			
			echo "<span style=\"$img_style\">".$thanks."</span>";
		}elseif($front == "yes" && !is_single() && !is_page() ){
			
				echo "<span style=\"$img_style\">".$thanks."</span>";
		}elseif($page == "yes" && is_page() ){
				echo "<span style=\"$img_style\">".$thanks."</span>";
		}else{
			
			echo "";
		}
			
		

		}
		function thank_theme (){

			$thanks = $this->get_thanks();
			$content = "";


			
			$imgpath = stripcslashes(get_option('ThankPost_image'));
	if($imgpath == "") {
		
			$path = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
			$imgpath = $path."/thank.png";
		
	}

			$before = stripslashes(get_option("ThankPost_design_before"));
			$after = stripslashes(get_option("ThankPost_design_after"));

			$before = str_replace("%thank%",$thanks,$before);
			$after = str_replace("%thank%",$thanks,$after);
			$loc = get_option("ThankPost_location");
			$front = get_option("Thankpost_show_front");


			if($loc == "Theme"  ) {
				
			
				
				if($this->check_ip() ) {

					$basic = "<table><tr><td> <img src=\"$imgpath\"> </td><td><span id=\"thank\"><a id=\"test\" style=\"cursor:pointer\"  >$before</span></td></tr></table>";

					echo $basic;

				}elseif (!$this->check_ip() ){

					$basic = "<table><tr><td> <img src=\"$imgpath\"></td><td><span id=\"thank\">$after</span></td></tr></table>";


					echo $basic;


				}

			}




		}

		function add_thank_content ( $content ) {
			global $post;
			$thanks = $this->get_thanks();

		
	$imgpath = stripcslashes(get_option('ThankPost_image'));
	if($imgpath == "") {
		
			$path = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
			$imgpath = $path."/thank.png";
		
	}
	
	
	
			$before = stripslashes(get_option("ThankPost_design_before"));
			$after = stripslashes(get_option("ThankPost_design_after"));

			$before = str_replace("%thank%",$thanks,$before);
			$after = str_replace("%thank%",$thanks,$after);
			$loc = get_option("ThankPost_location");
			$front = get_option("Thankpost_show_front");
			$page = get_option("Thankpost_show_page");

			if($loc != "Theme"  ){

				if($this->check_ip() ) {

					$basic = "<table><tr><td> <img src=\"$imgpath\"> </td><td><span id=\"thank_$post->ID\"><a  class=\"$post->ID\" style=\"cursor:pointer\" onClick=\"dang($post->ID)\" >$before</span></td></tr></table>";
					if($loc == "Append") {
						if(($front == "no" && !is_single() )|| is_page() && $page == "no" ) { $basic = ""; }
						
						return str_replace('[thanks]','',$content).$basic;
					}
					elseif ($loc == "Inside Post"  ) {
						if(($front == "no" && !is_single() )|| is_page() && $page == "no" ) { $basic = ""; }
						return str_replace("[thanks]",$basic,$content);

					}
					else{
						if(($front == "no" && !is_single() )|| is_page() && $page == "no" ) { $basic = ""; }
						return $basic.str_replace('[thanks]','',$content);

					}

				}elseif (!$this->check_ip() ){

					$basic = "<table><tr><td> <img src=\"$imgpath\"></td><td><span id=\"thank\">$after</span></td></tr></table>";
					if($loc == "Append") {
						if(($front == "no" && !is_single() )|| is_page() && $page == "no" ) { $basic = ""; }
						return  str_replace('[thanks]','',$content).$basic;
					}
					elseif ($loc == "Inside Post" ) {
					if(($front == "no" && !is_single() )|| is_page() && $page == "no" ) { $basic = ""; }
						return str_replace("[thanks]",$basic,$content);
					}
					else {
					if(($front == "no" && !is_single() )|| is_page() && $page == "no" ) { $basic = ""; }
						return $basic.str_replace('[thanks]','',$content);

					}


				}


			}else{

				return str_replace('[thanks]','',$content);


			}

		}


		function head_js () {
			global $post;
			?>
			<script>
				function dang (postid) {
					jQuery(document).ready(function($) {


						var data = {
							action: 'myajax-submit',
							postID : postid,
							thanks : $("#thank_"+postid).html()
						};
						jQuery.post("<?php echo admin_url('admin-ajax.php'); ?> ", data, function(response) {
							$("#thank_"+postid).html(response);
							<?php echo stripslashes(get_option('ThankPost_actions')); ?>
						});

					});
				}
			</script>


			<?php

		}



		function callback_ajax () {
			global $wpdb;
			$postid = $_POST['postID'];
			$thx = $this->get_thanks_via_id($postid);
			$wpdb->insert($wpdb->prefix.$this->table,array("post_id"=>$postid,"ip"=>$_SERVER['REMOTE_ADDR']));
			$after = stripslashes(get_option("ThankPost_design_after"));
			$after = str_replace("%thank%",$thx+1,$after);
			echo $after;
			die();
		}



	}

}

$thx = new Thank_Post();
register_activation_hook(__FILE__, array(&$thx ,'install') );
add_action( 'init', array( &$thx, 'init' ) );

require_once('thankpost_options.php');

function thankpost_add_button($buttons)
{
	array_push($buttons, "separator", "thankpost");
	return $buttons;
}

function thankpost_register($plugin_array)
{
	$path = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
	$url = $path."/editor_plugin.js";

	$plugin_array['thankpost'] = $url;
	return $plugin_array;
}

add_filter('mce_external_plugins', "thankpost_register");
add_filter('mce_buttons', 'thankpost_add_button');







?>