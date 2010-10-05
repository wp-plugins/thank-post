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
		$this->pagehook = add_options_page('ThankPost Options', "ThankPost", 'manage_options',"thankpost_options", array(&$this, 'options_page'));
		add_action('load-'.$this->pagehook, array(&$this, 'loading_page'));
	}
	function loading_page() {
	
		wp_enqueue_script('common');
		wp_enqueue_script('wp-lists');
		wp_enqueue_script('postbox');
		add_meta_box('thankpost-sidebox-1', 'ThankPost - Settings', array(&$this, 'thankpost_sidebox'), $this->pagehook, 'side', 'core');
		add_meta_box('thankpost-contentbox-2', 'ThankPost - Statistics', array(&$this, 'thankpost_content1'), $this->pagehook, 'normal', 'core');
	}
	
	function options_page() {
		
		global $screen_layout_columns;
		
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
			<div  class="updated" >	<p >
				<ul>
				<li><strong>Append</strong> - the thanks goes after the content..nobreaking.</li>
				<li><strong>Prepend</strong> - the thanks goes before the content..nobreaking.</li>
				<li><strong>Inside Post</strong> - Is a very special selection, You need to implent this [thanks] in the post.. which allows you to implent in each post different  location, and also when it's not selected it will not show [thanks] anywhere. <b><u>Use the thank button in the editor to implent [thanks] but be aware that deleting this plugin while using [thanks] will lead to the remains of [thanks] in the posts.so don't use it unless you have other solution.</u></b></li>
				<li><strong>Theme</strong> - This requires to edit the theme directory files such as single.php & index.php in order to implent this <pre>$thx->thank_theme(); - Shows the text .</pre>  and <pre>$thx->thank_stat(); - Shows the number of thanks that were made in the post including image.</pre> in php tags... anywhere.</li>
				</ul>
				</p>
				</div>
				
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
	<p>Order by last 5 posts that have +1 thank(s).</p>
	<?php
	global $wpdb;
	$arr = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."thank LIMIT 5 ",ARRAY_A);
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
					<strong><a class="row-title" href="<?php echo get_permalink($ar['post_id']); ?>" ><?php echo get_the_title($ar['post_id']); ?></a></strong>
				<td><div class="post-com-count-wrapper"></td>
		<td><?php echo get_thanks($ar['post_id']) ?></td>
</tr>

<?php } ?>




		</tbody>
</table>

	<?php
}
	function thankpost_content2() {
		
		?>
				
				<small>%thank% - number of thanks</small>
				<p>Design before thank was made:</p>
				<textarea name="design_before" style="width: 80%; height: 25x; width:500px;"><?php echo stripslashes(get_option('ThankPost_design_before')); ?></textarea>
				<p>Design after thank was made:</p>
				<textarea name="design_after" style="width: 80%; height: 25x; width:500px;"><?php echo stripslashes(get_option('ThankPost_design_after')); ?></textarea>		
		<?php
	}

}

$ThankPost_Options = new ThankPost_Options();

?>