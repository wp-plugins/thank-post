<?php
/*
Plugin Name: Thank Post
Plugin URI: http://icode.it.tc
Description: The "Thank Post" simply shows another approach to gratitude the author for the hard work. It allows 1 thank per ip and uses ajax. Meaning there is not thank spam.
Version: 1.2
Author: Nulled_Icode
Author URI: http://icode.it.tc
License:  GPL2
*/
if(!class_exists("Thank_Post")){

Class Thank_Post {

	var $table = "thank";



	function init () {
	
		  
		add_filter( 'the_content', array(&$this, 'add_thank_content') );
		add_filter('wp_head',array(&$this, 'head_js'));
		add_action('wp_ajax_myajax-submit',array(&$this, 'callback_ajax'));
		add_action('wp_ajax_nopriv_myajax-submit',array(&$this, 'callback_ajax'));
		wp_enqueue_script( 'jquery');
		add_action('admin_menu', array(&$this,'ThankPost_menu'));

	}

	/*
	* OPTIONS PAGE
	* version 1.0
	*
	*
	*/
	function ThankPost_menu () {

		add_options_page('ThankPost', 'ThankPost', 'manage_options', 'thank-post', array(&$this,'ThankPost_page') );

	}

	function ThankPost_page () {


		if (!current_user_can('manage_options'))  {
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}
		  //Default 
		 



		if(isset($_POST['update'])) {

			extract($_POST);
			$design_before = ($design_before);
			update_option('ThankPost_design_before',$design_before);
			update_option('ThankPost_design_after',$design_after);
			update_option('ThankPost_location',$location);
	
		  if(isset($_POST['front'])) {
			$front = "yes";
			 }else{
			 $front = "no";	
			}

     update_option('Thankpost_show_front',$front);
     
     
			?>
			<div class="updated"><p><strong><?php _e('settings saved.', 'thank-post_saved' ); ?></strong></p></div>
			<?php

		}
		?>
		<div class="wrap">

			<form method="post" action="">
				<h2>Thank Post Options</h2>
				<h3>Thank Post Design</h3>
				<small>%thank% - number of thanks</small>
				<p>Design before thank was made:</p>
				<textarea name="design_before" style="width: 80%; height: 25x; width:500px;"><?php echo stripslashes(get_option('ThankPost_design_before')); ?></textarea>
				<p>Design after thank was made:</p>
				<textarea name="design_after" style="width: 80%; height: 25x; width:500px;"><?php echo stripslashes(get_option('ThankPost_design_after')); ?></textarea>
					<h3>Show - Single Post / Front Posts</h3>
					<p>Disabled For now, problems..</p>
			<table>
				<tr><td>Front Posts</td><td><input type="checkbox" disabled=disabled <? echo (get_option("Thankpost_show_front") == "yes" )? "checked=checked" : "";?>  ></td></tr>
			</table>
				<h3>Location</h3>
				<p>Select to Append Or Prepend to set the location</p>
				<select name="location">
					<option <? echo (get_option("ThankPost_location") == "Append" )? "selected=selected" : "";?>>Append</option>
					<option  <? echo (get_option("ThankPost_location") == "Prepend" )? "selected=selected" : "";?>>Prepend</option>
					<option  <? echo (get_option("ThankPost_location") == "Inside Post" )? "selected=selected" : "";?>>Inside Post</option>
					<option  <? echo (get_option("ThankPost_location") == "Theme" )? "selected=selected" : "";?>>Theme</option>
				</select>
			<div  class="updated" >	<p id="sticky-span">
				<ul>
				<li><strong>Append</strong> - the thanks goes after the content..nobreaking.</li>
				<li><strong>Prepend</strong> - the thanks goes before the content..nobreaking.</li>
				<li><strong>Inside Post</strong> - Is a very special selection, You need to implent this [thanks] in the post.. which allows you to implent in each post different the location, and also when it's not selected it will not show [thanks] anywhere. <b><u>Use the thank button in the editor to implent [thanks] but be aware that deleting this plugin while using [thanks] will lead to the remains of [thanks] in the posts.so don't use it unless you have other solution.</u></b></li>
				<li><strong>Theme</strong> - This requires to edit the theme directory files such as single.php & index.php in order to implent this $thx->thank_theme(); in php tags... anywhere.</li>
				</ul>
				</p>
				</div>
				<div class="submit">
				<input type="submit" name="update" value="<?php _e('Update Settings', 'ThankPost_submit') ?>" /></div>
			</form>
		</div>

		<?



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
	
	
	function thank_theme (){
		
		$thanks = $this->get_thanks();
		$content = "";

		$path = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
		$img = "thank.png";
		$imgpath = $path."/".$img;

		$before = stripslashes(get_option("ThankPost_design_before"));
		$after = stripslashes(get_option("ThankPost_design_after"));

		$before = str_replace("%thank%",$thanks,$before);
		$after = str_replace("%thank%",$thanks,$after);
		$loc = get_option("ThankPost_location");
    $front = get_option("Thankpost_show_front");
	 

if(is_single() && $loc == "Theme" ) {
		if($this->check_ip() ) {
   
			$basic = "<table><tr><td> <img src=\"$imgpath\"> </td><td><span id=\"thank\"><a id=\"test\" href=\"#\"  >$before</span></td></tr></table>";
		 
    		echo $basic;

		}elseif (!$this->check_ip() ){
    
			$basic = "<table><tr><td> <img src=\"$imgpath\"></td><td><span id=\"thank\">$after</span></td></tr></table>";
      
 
      echo $basic;


	}
		
	}
		
		
		
		
	}

	function add_thank_content ( $content ) {

		$thanks = $this->get_thanks();

		$path = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
		$img = "thank.png";
		$imgpath = $path."/".$img;

		$before = stripslashes(get_option("ThankPost_design_before"));
		$after = stripslashes(get_option("ThankPost_design_after"));

		$before = str_replace("%thank%",$thanks,$before);
		$after = str_replace("%thank%",$thanks,$after);
		$loc = get_option("ThankPost_location");
    $front = get_option("Thankpost_show_front");
	 
  if($loc != "Theme"  ){

		if($this->check_ip() ) {
   
			$basic = "<table><tr><td> <img src=\"$imgpath\"> </td><td><span id=\"thank\"><a id=\"test\" href=\"#\"  >$before</span></td></tr></table>";
			if($loc == "Append") { 
			 
				 return str_replace('[thanks]','',$content).$basic;
				  }
			elseif ($loc == "Inside Post"  ) {
				 
				return str_replace("[thanks]",$basic,$content); 
				
				}
    	else{
    	
    		return $basic.str_replace('[thanks]','',$content);
    		
    		}

		}elseif (!$this->check_ip() ){
    
			$basic = "<table><tr><td> <img src=\"$imgpath\"></td><td><span id=\"thank\">$after</span></td></tr></table>";
			if($loc == "Append") {  
		
				return  str_replace('[thanks]','',$content).$basic; 
				}
			elseif ($loc == "Inside Post" ) {
			
				return str_replace("[thanks]",$basic,$content); 
				}
			else {
	
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
		
jQuery(document).ready(function($) {
$("#test").click(function(){
	var data = {
		action: 'myajax-submit',
                postID : <? echo $post->ID ?>,
		            thanks : $("#thank").html()
	};

	
	jQuery.post("<?php echo admin_url('admin-ajax.php'); ?> ", data, function(response) {
		$("#thank").html(response);
	});
});
});

		</script>


		<?php

	}

	function callback_ajax () {
		global $wpdb;
		$postid = $_POST['postID'];
		$thx = $_POST['thanks'];
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