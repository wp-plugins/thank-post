<?php

/*
Options Page - Thank Post
-Screen Options

*/
class ThankPost_Options {

	function ThankPost_Options() {
		add_filter('screen_layout_columns', array(&$this, 'on_screen_layout_columns'), 10, 2);
		add_action('admin_menu', array(&$this, 'on_admin_menu')); 
		add_action('admin_post_save_thankpost', array(&$this, 'thankpost_save'));
	}
	function on_screen_layout_columns($columns, $screen) {
		if ($screen == $this->pagehook) {
			$columns[$this->pagehook] = 2;
		}
		return $columns;
	}
	
	function on_admin_menu() {
		$path = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
		$img = $path."/thanktiny.png";
		$this->pagehook = add_options_page('ThankPost Options', "<img style='float:left;' src='$img' />ThankPost", 'manage_options',"thankpost_options", array(&$this, 'options_page'));
		add_action('load-'.$this->pagehook, array(&$this, 'loading_page'));
	}
	function loading_page() {
	
		wp_enqueue_script('common');
		wp_enqueue_script('wp-lists');
		wp_enqueue_script('postbox');
		add_meta_box('thankpost-sidebox-1', 'ThankPost - Settings', array(&$this, 'thankpost_sidebox'), $this->pagehook, 'side', 'core');
		add_meta_box('thankpost-contentbox-2', 'ThankPost - Statistics', array(&$this, 'thankpost_content1'), $this->pagehook, 'normal', 'core');
		add_meta_box('thankpost-sidebox-2', 'ThankPost - Manual', array(&$this, 'thankpost_sidebox_2'), $this->pagehook, 'side', 'core');
		add_meta_box('thankpost-sidebox-3', 'ThankPost - Image', array(&$this, 'thankpost_sidebox_3'), $this->pagehook, 'side', 'core');
	}
	
	function options_page() {
		
		global $screen_layout_columns;
		
		if(isset($_POST['update'])) {
			$error = 0;
				extract($_POST);
			$design_before = ($design_before);
			update_option('ThankPost_design_before',$design_before);
			update_option('ThankPost_design_after',$design_after);
			update_option('ThankPost_location',$location);
			update_option('ThankPost_actions',$jquery_actions);
			
			
			
			

			if($image == ""  ) {
				
		  	$img = "";
				
			}else{
				
				$img = $image;
			}
			update_option('ThankPost_image',$img);
			
			
			
			if($tinyimage == ""  ) {
				
		  	$img = "";
				
			}else{
				
				$img = $tinyimage;
			}
			update_option('ThankPost_tinyimage',$img);
		
			
			
	
	
	
		  if(isset($_POST['front'])) {
			$front = "yes";
			 }else{
			 $front = "no";	
			}
			 if(isset($_POST['page'])) {
			$page = "yes";
			 }else{
			 $page = "no";	
			}
			

     update_option('Thankpost_show_front',$front);
     update_option('Thankpost_show_page',$page);
   
			?>
			<div class="updated" style="background-color:lightblue;border-color:blue;"><p><strong><?php _e('Settings Saved!', 'thank-post_saved' ); ?></strong></p></div>
			<?php
			
		
		}
		
		
		add_meta_box('thankpost-contentbox-1', 'ThankPost - Design', array(&$this, 'thankpost_content2'), $this->pagehook, 'normal', 'core');
	
	
		?>
		<div id="thankpost" class="wrap">
		<?php screen_icon('options-general'); ?>
		<h2>ThankPost Options</h2>
		<form action="" method="post">
			<?php wp_nonce_field('thankpost'); ?>
			<?php wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false ); ?>
			<?php wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false ); ?>
			<div id="poststuff" class="metabox-holder<?php echo 2 == $screen_layout_columns ? ' has-right-sidebar' : ''; ?>">
				<div id="side-info-column" class="inner-sidebar">
					<?php do_meta_boxes($this->pagehook, 'side', null); ?>
				</div>
				<div id="post-body" class="has-sidebar">
					<div id="post-body-content" class="has-sidebar-content">
						<?php do_meta_boxes($this->pagehook, 'normal', null); ?>
							<input type="submit" value="Save Changes" class="button-primary" name="update"/>	
						</p>
					</div>
				</div>
			</div>	
		</form>
		</div>
	<script type="text/javascript">
		//<![CDATA[
		jQuery(document).ready( function($) {
			// close postboxes that should be closed
			$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
			// postboxes setup
			postboxes.add_postbox_toggles('<?php echo $this->pagehook; ?>');
		});
		//]]>
	</script>
		
				
		<?php
	}

	function thankpost_save() {

		if ( !current_user_can('manage_options') )
			wp_die( __('Failure') );			
		check_admin_referer('thankpost');
		wp_redirect($_POST['_wp_http_referer']);		
	}

/*

Contents :

*/

	function thankpost_sidebox() {
		
?>
		<table>
				<tr><td>Front Posts</td><td><input name="front" type="checkbox" <? echo (get_option("Thankpost_show_front") == "yes" )? "checked=checked" : "";?>  ></td></tr>
				<tr><td>Pages</td><td><input name="page" type="checkbox" <? echo (get_option("Thankpost_show_page") == "yes" )? "checked=checked" : "";?>  ></td></tr>
			</table>
			
			<p>Select location style</p>
				<select name="location">
					<option <? echo (get_option("ThankPost_location") == "Append" )? "selected=selected" : "";?>>Append</option>
					<option  <? echo (get_option("ThankPost_location") == "Prepend" )? "selected=selected" : "";?>>Prepend</option>
					<option  <? echo (get_option("ThankPost_location") == "Inside Post" )? "selected=selected" : "";?>>Inside Post</option>
					<option  <? echo (get_option("ThankPost_location") == "Theme" )? "selected=selected" : "";?>>Theme</option>
				</select>

		
		<?php
	}
function thankpost_content1() {
	
	?>
	<table class="widefat page fixed" cellspacing="0">
	<thead>
	<tr>
	<th scope="col" id="cb" class="manage-column column-cb check-column" style=""></th>

	<th scope="col" id="title" class="manage-column column-title" style="">Title</th>
	<th scope="col" id="author" class="manage-column column-author" style=""></th>

	<th scope="col" id="date" class="manage-column column-date" style="">Thanks</th>
	</tr>
	</thead>

	<tfoot>

	<tr>
	<th scope="col"  class="manage-column column-cb check-column" style=""></th>
	<th scope="col"  class="manage-column column-title" style="">Title</th>
	<th scope="col"  class="manage-column column-author" style=""></th>

	<th scope="col"  class="manage-column column-date" style="">Thanks</th>
	</tr>

	</tfoot>

	<tbody>
	<p>Ordered by the last <b>5</b> posts that were thanked lately.</p>
	<?php
	global $wpdb;
	$arr = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."thank  ORDER BY id DESC LIMIT 5  ",ARRAY_A);
	function get_thanks ( $id ) {
		global $wpdb;
		$num = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM ".$wpdb->prefix."thank WHERE post_id='$id'"));
		return $num;
	
		
	}
	
foreach ( $arr as $ar ) {
	?>
		
	
<tr id="page-2" class="alternate iedit">
		<th scope="row" class="check-column"></th>
				<td class="post-title page-title column-title">
				
				<strong><a class="row-title" href="<?php echo get_permalink($ar['post_id']); ?>" ><?php echo get_the_title($ar['post_id']); ?></a></strong></td>
				<td><div class="post-com-count-wrapper"></div></td>
					
		<td><?php echo get_thanks($ar['post_id']) ?></td>
</tr>

<?php  } ?>



		</tbody>
</table>

<?php global $wpdb; $num = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM ".$wpdb->prefix."thank ")); ?>
<p><h3>Up to now your blog was thanked <?php echo $num; ?> time(s)!</h3></p> 

	<?php
}
	function thankpost_content2() {
		
		?>
				
				<small>%thank% - number of thanks</small>
				<p>Design before thank was made:</p>
				<textarea name="design_before" style="width: 80%; height: 25x; width:500px;"><?php echo stripslashes(get_option('ThankPost_design_before')); ?></textarea>
				<p>Design after thank was made:</p>
				<textarea name="design_after" style="width: 80%; height: 25x; width:500px;"><?php echo stripslashes(get_option('ThankPost_design_after')); ?></textarea>		
				<p>Jquery Actions after thanked</p>
				<textarea name="jquery_actions" style="width: 80%; height: 25x; width:500px;"><?php echo stripslashes(get_option('ThankPost_actions')); ?></textarea>
		<?php
		
	}
	
	function thankpost_sidebox_2 () {
		
		
		?>
		
		
		<p><b>Thank You for using ThankPost.</b><br/><font color="green">Consider Joining the Code'em Group, simple mail to this :ch-td@hotmail.com</font><br/><br/> See the really really fast menual <a href="http://www.icode.it.tc/94-2/" target="_blank"><b>here!</b></a>
	</p>
	<p>Your current Version is : <b><?php echo stripslashes(get_option('ThankPost_version')); ?></b></p>
		
		<?php
		
		
		
	}
	
	function thankpost_sidebox_3 () {
		
		
		?>
		<p>In order to change the default image near the ThankPost Text do the following:</p>
		<p>Upload an image to <a href="media-new.php">here</a> , and afterwards copy the <b>link</b> to here.</p>
		<input type="text" name="image" size="40" value="<?php echo stripslashes(get_option('ThankPost_image')); ?>" >
		<p><b>Leave Blank if you wish to remain the default image!!</b></p>
		
		<p>In order to change the default <b>Tiny Image</b> that is used for putting the thanks count on top of the post.</p>
		<p>Change the image here:(Very Recommened Size:16x16)</p>
		<input type="text" name="tinyimage" size="40" value="<?php echo stripslashes(get_option('ThankPost_tinyimage')); ?>">
	  <p><b>Leave Blank if you wish to remain the default image!!</b></p>
		
		<p>A picture that doesn't really exists will result in not showing anything.</p>
		
		<?php
		
		
		
		
	}
	
	
	
	

}

$ThankPost_Options = new ThankPost_Options();

?>