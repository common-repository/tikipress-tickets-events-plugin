<?php
/**
 * WP eCommerce view tickets page functions
 *
 * This is the display code for the wpsc-ticket-log page
 *
 * @package wp-e-commerce
 * @since 3.7
 */

function wpsc_display_ticket_logs(){
	global $wpdb;
?>
	<div class="wrap">
		<?php //screen_icon(); ?>
		<h2><?php echo wp_specialchars( 'Ticket Details' ); ?> </h2>
	</div>
	<?php
	
	$categories = wpsc_tickets_get_categories();
	?>
	<div class="metabox-holder" style="width: 95%;">
		<div class='postbox'>
			<h3 class='hndle'>Ticket Category</h3>
			<div class='inside'>
				<?php if(get_option('wpsc_ticket_module_category') == ''){?>
				<form action='' method='post'>
					<p><?php _e('Please select a category you want to use for Ticketing purposes or <a href="?page=wpsc-edit-groups" title="">create one</a>', 'wpsc'); ?></p>
					<label for='wpsc_ticket_category'><?php _e('Category','wpsc'); ?> :</label>
					<select id='wpsc_ticket_category' name='wpsc_ticket_category'>
					<?php
						foreach((array)$categories as $cat){
							echo "<option value='".$cat['id']."'>".$cat['name']."</option>";
						}
					
					?>
					</select>
					<input type='submit' value='Submit' name='wpsc_ticket_category_submit' />
				</form>
				<?php }else{ ?>
				
				<p> The category <strong><?php echo wpsc_ticket_get_category_name(); ?></strong> is being used for the ticketing module.</p>
				
				<?php } ?>
			</div>
		</div>
	</div>
	<div class="metabox-holder" style="width: 95%;">
		<div class='postbox'>
			<h3 class='hndle'><?php _e('At a Glance','wpsc'); ?></h3>
	   		 <div class='inside'> 
			<?php wpsc_tickets_specs(); ?>
			</div>
		</div>
	</div>
	<div class="metabox-holder" style="width: 95%;">
		<div class='postbox'>
			<h3 class='hndle'><?php _e('Mail-list','wpsc'); ?></h3>
	   		 <div class='inside'> 
			<?php $users = wpsc_get_ticket_content(false, true); ?>
			<p>
			<?php foreach($users as $user_details){
				if($user_details[2] != ''){
					echo $user_details[2].' , ';
				}
			
			} ?>
			</p>
			</div>
		</div>
	</div>
	<div class="metabox-holder" style="width: 95%;">
		<div class='postbox'>
			<h3 class='hndle'><?php _e('Stats','wpsc'); ?></h3>
	   		 <div class='inside'> 
		   		<?php echo wpsc_tickets_meal_stats($users); ?>
				<?php echo wpsc_tickets_tee_size($users); ?>
				<?php echo wpsc_tickets_using_wp($users); ?>
				<div style='clear:both'></div>
				<?php echo wpsc_tickets_blogs_manage($users); ?>
				<?php echo wpsc_tickets_which_sessions($users); ?>
				<?php echo wpsc_tickets_best_describe($users); ?>
				<div style='clear:both'></div>
				<?php echo wpsc_tickets_make_money($users); ?>
				<div style='clear:both'></div>
			</div>
		</div>
	</div>
	
	<?php
	$headers = wpsc_tickets_get_headers();
//	exit('<pre>'.print_r($headers,true).'</pre>');
		if (!isset($_GET['wpsc_ticket_purchlog'])) {
		 register_column_headers('display-ticketing-details', $headers); 

		 // Need to check if notes column exists in DB and plugin version? ?>
		 
	<div style='clear:both;'></div>
		<form method='post' action=''>
		<table class="widefat fixed page" style="width:95%;" cellspacing="0">
			<thead>
				<tr>
			<?php print_column_headers('display-ticketing-details'); ?>
				</tr>
			</thead>
		
			<tfoot>
				<tr>
			<?php print_column_headers('display-ticketing-details', false); ?>
				</tr>
			</tfoot>
		
			<tbody>
			<?php echo wpsc_get_ticket_content(false,false); ?>
			</tbody>
		</table>
		</form>
	
	<p><a href='<?php echo get_option('siteurl');?>/wp-admin/admin.php?page=wpsc-ticket-logs&wpsc_admin_action=display_ticket_pdf' title='generate PDF'>Generate PDF</a></p>
	<p><a onclick="jQuery('#adjust_headers').show();" href='#' title='PDF Settings'>Edit PDF Settings</a></p>
<?php // ADJUST PDF HEADERS PST META BOX ?>
<div class="metabox-holder" style="width:95%;display:none;" id='adjust_headers'>
	<div class='postbox'>
		<h3 class='hndle'>Adjust PDF Headers</h3>
		<form action='' method='post'>
		<div class='inside'>
			<table>
			<tr><th>Include in PDF</th><th>Column Name</th><th>Column Size</th></tr>
			<?php 
			
			foreach($headers as $key => $header){
				if(is_array($header)){
					$values = $header;
					$header = str_replace('_', ' ',$key);
				}
				//exit($key.'<pre>'.print_r($header, true).'</pre>');
				?>
				
				<tr>
					<td><input type='checkbox' <?php if($values['checkbox'] == 'on'){echo 'checked="checked"';}?> name='<?php echo $header; ?>[checkbox]' id='<?php echo $header; ?>_checkbox'  /></td>
					<td><label for='<?php echo $header; ?>[checkbox]'><?php echo $header; ?></label></td>
					<td><input type='text' value='<?php if(isset($values)){echo $values['size'];}?>' name='<?php echo $header; ?>[size]' id='<?php echo $header; ?>_size'  /></td>
				</tr>
				<?php
			}
			?>
			
			</table>
			<input type='hidden' value='wpsc_tickets_adjust_headers' name='wpsc_tickets_action' />
			<input type='submit' value='Submit' name='wpsc_tickets_submit' />
			</form>
		</div>
	</div>
</div>
	<p><a href='<?php echo get_option('siteurl');?>/wp-admin/admin.php?page=wpsc-ticket-logs&wpsc_admin_action=display_ticket_badges' title='generate Badges'>Generate Badges</a></p>

	<?php }elseif( is_numeric($_GET['wpsc_ticket_purchlog'])){
			$purchlog = (int)$_GET['wpsc_ticket_purchlog'];
			//check_admin_referer('wpsc_ticket_'.$purchlog);
			$columns = wpsc_tickets_get_headers();
			register_column_headers('display-ticketing-details', $columns); 

			?>
			<div class="metabox-holder" style="width: 95%;">
				<div class='postbox'>
					<h3 class='hndle'><?php _e('Edit Details','wpsc'); ?></h3>
	   				 <div class='inside'> 
	   		 		<form action='' method='post'>
	   		 		<?php
						if ( function_exists('wp_nonce_field') ){
							wp_nonce_field('wpsc-tickets-submit-edit_' . $purchlog);
						}
					?>
	   		 		<table class="widefat page fixed" style="width:95%;" cellspacing="0">
						<thead>
							<tr>
						<?php print_column_headers('display-ticketing-details'); ?>
							</tr>
						</thead>
					
						<tfoot>
							<tr>
						<?php print_column_headers('display-ticketing-details', false); ?>
							</tr>
						</tfoot>
					
						<tbody>
						 <?php echo wpsc_get_ticket_form_fields($purchlog); ?>
						</tbody>
					</table>	
					<input type='hidden' value='<?php echo $purchlog; ?>' name='wpsc_edit_purchlog_id' />   								<br />
	   		 		<input type='hidden' value='wpsc_edit_ticket_data' name='wpsc_tickets_action' />
					<input type='submit' value='Update Details' name='submit' class='button-primary' />
   		 			</form>
					</div>
				</div>
			</div>

			<?php
			
		  }
}
?>