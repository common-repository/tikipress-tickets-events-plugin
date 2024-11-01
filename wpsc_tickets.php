<?php
/*
Plugin Name:WPSC Tickets Module
Plugin URI: http://www.getshopped.org
Description: A Wp-e-Commerce module that allows ticketing Functionality
Version: 0.1
Author: Instinct
Author URI: http://www.getshopped.org/e-commerce/tickets
*/


/* These are the main WPSC tickets page functions */
define('WPSC_TICKETS_FOLDER', dirname(plugin_basename(__FILE__)));
/* Checks to see whether the Tickets Form Fields Set is in the Checkout Form Sets, If its not adds it to the array. */
//update_option('wpsc_checkout_form_sets', array('Default Checkout Forms'));
$checkout_form_sets = get_option('wpsc_checkout_form_sets');
if(!in_array('Ticket Form Fields',$checkout_form_sets)){
	global $wpdb;
	$checkout_form_sets[] = 'Ticket Form Fields';
	update_option('wpsc_checkout_form_sets', $checkout_form_sets);
	if(!empty($wpdb->prefix)) {
	  $wp_table_prefix = $wpdb->prefix;
	} else if(!empty($table_prefix)) {
	  $wp_table_prefix = $table_prefix;
	}
	 $sql ="ALTER TABLE `".$wp_table_prefix."wpsc_checkout_forms` CHANGE `options` `options` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL";
	 $wpdb->query($sql);
	insert_new_ticket_forms();
}

if(isset($_GET['wpsc_tickets_test'])){
	global $wpdb;
	//wpsc_clear_stock_claims();
	//$sql = "DELETE FROM `".$wp_table_prefix."wpsc_checkout_forms` ";
	//$wpdb->query($sql);
}

function insert_new_ticket_forms(){
	global $wpdb;
	$checkout_set = wpsc_get_ticket_checkout_set();
	if(!empty($wpdb->prefix)) {
	  $wp_table_prefix = $wpdb->prefix;
	} else if(!empty($table_prefix)) {
	  $wp_table_prefix = $table_prefix;
	}
	$form_names = array('Ticket Holder','First Name', 'Last Name', 'E-mail','Twitter ID','Blog URL','Company Name','T-Shirt Size','Meal Restrictions','How long have you been using WordPress','How many WordPress blogs do you manage','Which sessions are you most likely to attend','How would you describe yourself (if more than one, choose the one that will influence your choice of sessions at this WordCamp)', 'Do you make money from your WordPress Website');
	foreach($form_names as $formname){
		switch($formname){
			case 'T-Shirt Size':
				$default_forms[$formname]['type'] = 'select';
				$default_forms[$formname][] = array('Mens Small','Mens Medium','Mens Large','Mens X Large','Womens Small','Womens Medium','Womens Large','Womens X Large');
			break;
			case 'Meal Restrictions':
				$default_forms[$formname]['type'] = 'select';
				$default_forms[$formname][] = array('none','Vegan','Vegetarian','Gluten Free');
			break;
			case 'How long have you been using WordPress':
				$default_forms[$formname]['type'] = 'select';
				$default_forms[$formname][] = array('never', 'less than 6 mnths', '6-12mnths', '1yr', '2yrs', '3yrs', '4yrs', '5yrs', '6yrs');
			break;
			case 'How many WordPress blogs do you manage':
				$default_forms[$formname]['type'] = 'select';
				$default_forms[$formname][] = array('none', '1 - 2', '3 - 5', '6 - 10', '11 - 20', '20 +');
			break;
			case 'Which sessions are you most likely to attend':
				$default_forms[$formname]['type'] = 'select';
				$default_forms[$formname][] = array('Blogger', 'BeginnerDev', 'AdvancedDev', 'CMS', 'Academic', 'BuddyPress', 'opensource');
			break;
			case 'How would you describe yourself (if more than one, choose the one that will influence your choice of sessions at this WordCamp)':
				$default_forms[$formname]['type'] = 'select';
				$default_forms[$formname][] = array('Personal Blogger', 'Corporate Blogger', 'Plugin Developer', 'Theme Developer', 'Theme Designer (no coding)', 'System Admin', 'Core Contributer', 'Forum Moderator', 'BuddyPress/MU', 'Open Source Community');
			break;
			case 'Do you make money from your WordPress Website':
				$default_forms[$formname]['type'] = 'select';
				$default_forms[$formname][] = array('None', 'Day Job', 'Support', 'Custom Development', 'Hosting', 'Ads', 'E-Commerce', 'Themes', 'Plugins');
			break;
			case 'Ticket Holder':
				$default_forms[$formname]['type'] = 'heading';
				$default_forms[$formname][] = '';
			break;
			default:
				$default_forms[$formname]['type'] = 'text';
				$default_forms[$formname][] = '';
			break;
		}				
	}
    $max_order_sql = 'SELECT MAX(`order`) FROM `'.$wp_table_prefix.'wpsc_checkout_forms'.'` WHERE `active` = "1";';
    $orderstart = $wpdb->get_var($max_order_sql);
//	exit($orderstart.'SELECT MAX(`order`) FROM `'.$wp_table_prefix.'wpsc_checkout_forms'.'` WHERE `active` = "1";');
	foreach($default_forms as $default_set_name => $options){
		$sql = "INSERT INTO `".$wp_table_prefix."wpsc_checkout_forms` ( `name`, `type`, `mandatory`, `display_log`, `default`, `active`, `order` , `unique_name`, `checkout_set`) VALUES ( '".$default_set_name."', '".$options['type']."', '1', '0', '', '1','".$orderstart."','','".$checkout_set."');";
		$orderstart++;
		$wpdb->query($sql);
		if(is_array($options[0])){
			$form_fields = array();
			foreach($options[0] as $array){

					$label = str_replace(' ','_',$array);
					$form_fields[$array] = $label;

			}
						//exit($sql.'<pre>'.print_r($form_fields, true).'</pre>');
			$form_fields = maybe_serialize($form_fields);
			$sql = "UPDATE `".$wp_table_prefix."wpsc_checkout_forms` SET `options`='".$form_fields."' WHERE id=".$wpdb->insert_id;

			$wpdb->query($sql);
		}
	}

}


function wpsc_tickets_init_method() {
   wp_enqueue_script('thickbox');
   wp_enqueue_style('thickbox');
//   	remove_action('admin_head', 'wpsc_tickets_init_method');
}

add_action('admin_init', 'wpsc_tickets_init_method');

/**
 * Description Function Pulls out the 'Id' for the Tickets Form Field used in the wpsc_checkout_form_fields table
 * @access public
 *
 * @return int checkout_set id
 */
 function wpsc_get_ticket_checkout_set(){
	$checkout_form_sets = get_option('wpsc_checkout_form_sets');
	foreach($checkout_form_sets as $key => $value){
		if($value == 'Ticket Form Fields'){
			$checkout_set = $key;
			break;
		}
	}
	return $checkout_set;
}

function wpsc_ticket_checkoutfields(){
	global $wpdb, $wpsc_cart, $wpsc_checkout;
	$count = 0;
	foreach($wpsc_cart->cart_items as $cartitem){
		$categoriesIDs = $wpdb->get_col("SELECT category_id FROM `".WPSC_TABLE_ITEM_CATEGORY_ASSOC."` WHERE product_id=".$cartitem->product_id);

		foreach((array)$categoriesIDs as $catid){
			if($catid == get_option('wpsc_ticket_module_category')){
				$count  += $cartitem->quantity;
			}
		}
		
	}
	return $count;
}


/**
 * Description Finds and returns the product ids associated with tickets
 * @access public
 *
 * @return string, either a single product_id or a string of product ids seperated by commas (for use in SQL statements)
 */
function wpsc_ticket_product_ids(){
	global $wpdb;
	$id = get_option('wpsc_ticket_module_category');
	$sql = "SELECT `product_id` FROM `".WPSC_TABLE_ITEM_CATEGORY_ASSOC."` WHERE `category_id`=".$id;

	$product_ids = $wpdb->get_col($sql);
//	exit('<pre>'.print_r($product_ids,true).'</pre>');
	if(is_array($product_ids)){
		$product_ids = implode(' , ', $product_ids);
	}
	return $product_ids;
}


/**
 * Description finds and returns the 'headings' for tables
 * @access public
 *
 * @param boolean forPDF true or false depending on whether the return value is for PDF or not
 * @return either numeric or associative array depending on the use
 */
function wpsc_tickets_get_headers($forPDF = false){
	global $wpdb;
	$columns = array();
	$checkout_set = wpsc_get_ticket_checkout_set();
	$sql = "SELECT `name` FROM `".WPSC_TABLE_CHECKOUT_FORMS."` WHERE `active`='1' AND `type` != 'heading' AND `checkout_set`='".$checkout_set."' ORDER BY `order`";
	$headers = $wpdb->get_col($sql);
	if(!$forPDF){



		$limit = 32;
		foreach($headers as $header){
			if(strlen($header) > $limit){
				$header = substr($header, 0, $limit).'..';
			}
			$header_shortened = str_replace(' ', '',$header);

			$columns[$header_shortened] = $header;
		}
		$columns['edit'] = 'Edit';
	//	exit('<pre>'.print_r($columns, true).'</pre>');
		return $columns;
	}else{
		return $headers;
	}

}


/**
 * Description Calculates and returns the Sum of products bought
 * @access public
 *
 * @param int id used to limit the search to a certain product id
 * @return int numebr of tickets sold
 */
function wpsc_tickets_sold($id){
	global $wpdb;
	$sql = 'SELECT SUM(`'.WPSC_TABLE_CART_CONTENTS.'`.`quantity`) FROM `'.WPSC_TABLE_CART_CONTENTS.'` LEFT JOIN `'.WPSC_TABLE_PURCHASE_LOGS.'` ON `'.WPSC_TABLE_CART_CONTENTS.'`.`purchaseid` = `'.WPSC_TABLE_PURCHASE_LOGS.'`.`id` WHERE `'.WPSC_TABLE_PURCHASE_LOGS.'`.`processed` >1 AND `'.WPSC_TABLE_PURCHASE_LOGS.'`.`processed` < 5  AND `'.WPSC_TABLE_CART_CONTENTS.'`.`prodid`='.$id;
	$num = $wpdb->get_var($sql);
	if($num != null){
		return $num;
	}else{
		return 0;
	}
}


function wpsc_get_ticket_attendees(){
	global $wpdb;
	$sql = "SELECT `id` FROM `".WPSC_TABLE_CHECKOUT_FORMS."` WHERE `active`='1' AND `type` !='heading' AND `checkout_set`='".wpsc_get_ticket_checkout_set()."' ORDER BY `order` LIMIT 1";
	$id = $wpdb->get_var($sql);
	//exit($id);
	$sql = "SELECT `".WPSC_TABLE_PURCHASE_LOGS."`.`id` FROM `".WPSC_TABLE_PURCHASE_LOGS."` WHERE `".WPSC_TABLE_PURCHASE_LOGS."`.`processed` >1 AND `".WPSC_TABLE_PURCHASE_LOGS."`.`processed` < 5 ";
	$Purchids = $wpdb->get_col($sql);
	foreach($Purchids as $purch){
			$sql = "SELECT COUNT(*) FROM `".WPSC_TABLE_SUBMITED_FORM_DATA."` WHERE `".WPSC_TABLE_SUBMITED_FORM_DATA."`.`log_id` = '".$purch."' AND `".WPSC_TABLE_SUBMITED_FORM_DATA."`.`form_id` ='".$id."'";
			$count = $wpdb->get_var($sql);
			$counts += $count;
	}
	return $counts;
//	exit('Attendees'.$counts);

}

/**
 * Description Get ticket content gets the stored ticket 'attendees information' and either displays it in a tables <tr> or
 * prints out a PDF.
 * @access public
 *
 * @param boolean isPDF used to switch between printing a table or a pdf
 * @return either $output string for table data or a PDF.
 */
function wpsc_get_ticket_content($isPdf = false, $returnArray = false){
	global $wpdb;
	$count = 0;
	$isfirstrun = true;
	$headers = get_option('wpsc_tickets_headers');
	$no_settings = false;
	if(empty($headers) ||  $isPdf == false){
		$headers = wpsc_tickets_get_headers(true);
		$no_settings = true;
	}
	if(!$no_settings  && $isPdf == true){
		foreach($headers as $key => $value){
			if($value['checkbox'] == 'on'){
				$string .= '"'.str_replace('_', ' ',$key).'",';
			}
		}
		$string = substr($string, 0, -1);
		$sql = "SELECT `id` FROM `".WPSC_TABLE_CHECKOUT_FORMS."` WHERE `active`='1' AND `type` !='heading' AND `checkout_set`='".wpsc_get_ticket_checkout_set()."' AND `name` IN (".$string.") ORDER BY `order`";
	}else{
		$sql = "SELECT `id` FROM `".WPSC_TABLE_CHECKOUT_FORMS."` WHERE `active`='1' AND `type` !='heading' AND `checkout_set`='".wpsc_get_ticket_checkout_set()."' ORDER BY `order`";
	}
//	exit($sql.'<pre>'.print_r($headers,true).'</pre>');
	$ids_array = $wpdb->get_col($sql);
	$form_count = count($ids_array);
	$ids = implode(',',$ids_array);
	if(isset($_GET['wpsc_ticket_filter'])){
		$filter = $wpdb->escape($_GET['wpsc_ticket_filter']);
	}
	if(isset($filter) && $filter != 'all'){
		$sql = "SELECT `id` FROM `".WPSC_TABLE_PURCHASE_LOGS."` WHERE `processed` > 1 AND `".WPSC_TABLE_PURCHASE_LOGS."`.`processed` < 5  AND `discount_data` ='".$filter."'";
	}else{
		$sql = "SELECT `".WPSC_TABLE_PURCHASE_LOGS."`.`id` FROM `".WPSC_TABLE_PURCHASE_LOGS."` WHERE `".WPSC_TABLE_PURCHASE_LOGS."`.`processed` >1 AND `".WPSC_TABLE_PURCHASE_LOGS."`.`processed` < 5 ";

	}
	$Purchids = $wpdb->get_col($sql);
	$counts = 0;
	
	foreach($Purchids as $purch){
		foreach($ids_array as $id){
			$sql = "SELECT `".WPSC_TABLE_SUBMITED_FORM_DATA."`.`value`,`".WPSC_TABLE_SUBMITED_FORM_DATA."`.`id`,`".WPSC_TABLE_SUBMITED_FORM_DATA."`.`log_id` FROM `".WPSC_TABLE_SUBMITED_FORM_DATA."` WHERE `".WPSC_TABLE_SUBMITED_FORM_DATA."`.`log_id` = '".$purch."' AND `".WPSC_TABLE_SUBMITED_FORM_DATA."`.`form_id` ='".$id."' ORDER BY `id`";
			$values[$id] = $wpdb->get_results($sql, ARRAY_N);
		}
		
		$rows = count($values[$ids_array[0]]);
		$i = 0;
		$x = 0;

		foreach($values as $value){
			if(count($value) > 1){
				foreach($value as $v){
					if($x <= count($value) && !empty($v[0])){
						$array[$counts+$x][] = $v[0];
					}
					$x++;
				}
				$x = 0;
			}elseif(!empty($value[0][0])){
				$array[$counts][] = $value[0][0];
			}
		}
		$counts++;
	}

	foreach((array)$array as $a){
		//sort($a);
		$numValues = count($a);
		$numTicketFields = count(wpsc_tickets_get_headers());
		$x = 0;
		$j = 0;
		if(count($a) > count(wpsc_tickets_get_headers())){
			$numTickets = $numValues/$numTicketFields;
			while($j <= $numTickets){
			 	$newarray[strtolower($a[($x+1)]).'_'.uniqid()] = array_slice($a, $x,($numTicketFields-1));
			 	$x += ($numTicketFields-1);
			 	$j++;
			}
		}else{
			$newarray[strtolower($a[1]).'_'.uniqid()] = $a;
		}
	}
	$newarray = (array)$newarray;
	ksort($newarray);
	
	if($returnArray){
		return $newarray;
	}elseif($isPdf){
			if($isfirstrun){
				require_once(WPSC_FILE_PATH."/wpsc-includes/fpdf/mc_table.php");
				$pdf=new PDF_MC_Table('L');
				$pdf->AddPage();
				$pdf->SetFont('Arial','',18);
				$pdf->Cell(30,10,'Ticketing Info');
				$pdf->Ln();

				$pdf->SetFont('Arial','',11);

				$pdf->SetFillColor(255,0,0);
				$pdf->SetTextColor(255);


				$isfirstrun = false;
				$fill = true;

				if($no_settings){
					$num_columns = count($headers);
					$length = 0;
					foreach($headers as $column){
							$length += strlen($column);
					}
					$equal_length = floor($length/$num_columns);

					$i = 0;

					while($i < $num_columns){
						$widths[] =$equal_length;
						$i++;
					}
					$pdf->SetWidths($widths);
					$pdf->Row($headers, true);
				}else{
					foreach($headers as $key => $value){
						if($value['checkbox'] == 'on'){
							$titles[] = str_replace('_', ' ',$key);
							if($value['size'] > 0){
								$widths[] = $value['size'];
							}else{
								$widths[] = 30;
							}
						}
					}
					//exit('<pre>'.print_r($widths,true).'</pre>');
					$pdf->SetWidths($widths);
					$pdf->Row($titles, true);
				}

			$pdf->SetTextColor(0);
			$pdf->SetFillColor(224,235,255);
			$i = 0;
				foreach($newarray as $array){
					$array = maybe_unserialize($array);
					$pdf->Row($array, $fill);
	   		  	  $fill = !$fill;
				}
			
			}
		$pdf->Output();
		exit();
	}else{
		foreach((array)$newarray as $array){

			$x = 0;
			$output .="<tr>";
			foreach($array as $key => $info){
				    $info = stripslashes($info);
					$info = maybe_unserialize($info);
					if(is_array($info)){
						$array = array();
						foreach($info as $i){
							if($i != '-1'){
								$array[] = $i;
							}else{
								//$array[] = '';
							}
						}
						$info = implode(',',$array );
					}elseif($info == '-1'){
						$info = 'NA';
					}
					if($key == 2){
						$sql = 'SELECT `a`.`log_id` FROM `'.WPSC_TABLE_SUBMITED_FORM_DATA.'` as `a` LEFT JOIN  `'.WPSC_TABLE_SUBMITED_FORM_DATA.'` as `b` ON `a`.`log_id`= `b`.`log_id` LEFT JOIN `'.WPSC_TABLE_SUBMITED_FORM_DATA.'` as `c`  ON `b`.`log_id`= `c`.`log_id` WHERE `a`.`value`="'.$info.'" AND `b`.`value`="'.$array[$key+1].'" AND `c`.`value`="'.$array[$key+2].'" LIMIT 1';
	//					echo $sql;
						$logid = $wpdb->get_var($sql);
					}

						$output .= "<td>".$info."</td>";
						$x++;
				if($x == (count(wpsc_tickets_get_headers())-1)){

					$sendback = add_query_arg('wpsc_ticket_purchlog',$logid);
					$sendback = ( function_exists('wp_nonce_url') ) ? wp_nonce_url($sendback, 'wpsc_ticket_' . $logid) : $sendback;

					$output .="<td><a href='$sendback'>Edit</a></td>";
					$output .= "</tr><tr>";
					$x = 0;
				}

			}
			$output .="</tr>";

		}
		$i++;
		return $output;
	}
}


/**
 * Description Calls the get ticket content function with the PDF argument to display the PDF
 * @access public
 *
 * @return none
 */
function wpsc_display_ticket_pdf(){
	wpsc_get_ticket_content(true,false);
}

/**
 * Description get quantities left calculates the stock remaining (if any products use stock remaining) as well as
 * displaying the information folowed by the amounts sold
 * @access public
 *
 * @return $output html li items
 */
function wpsc_get_quantities_left(){
	global $wpdb;
	$ids = wpsc_ticket_product_ids();
	$sql = 'SELECT `id`,`name`,`quantity` FROM `'.WPSC_TABLE_PRODUCT_LIST.'` WHERE `quantity_limited` = "1" AND `id` IN('.$ids.')';
	$products_data = $wpdb->get_results($sql, ARRAY_A);
	if(is_array($products_data)){
		foreach($products_data as $product_data){
			$output .= '<li>'.$product_data['name'].' : '.$product_data['quantity'].' left ('.wpsc_tickets_sold($product_data['id']).' Sold)</li>';
		}
	}else{
		$sql = 'SELECT `id`,`name`,`quantity` FROM `'.WPSC_TABLE_PRODUCT_LIST.'` WHERE `id` IN('.$ids.')';
		$products_data = $wpdb->get_results($sql, ARRAY_A);
		$output .= "<li>No tickets have limited quantities</li>";
		foreach((array)$products_data as $product){
			$output .="<li>".$product['name'].' : '.wpsc_tickets_sold($product['id'])." Sold</li>";
		}
	}
	return $output;
}

/*
 * Description generates the badges PDF for printing
 * @access public
 *
 * @return PDF download
 *
 */
function wpsc_display_ticket_badges(){
	global $wpdb;

@ini_set('log_errors','on');
@ini_set('display_errors','on');

require_once(WPSC_FILE_PATH."/wpsc-includes/fpdf/mc_table.php");
require_once('functions.php');

@ini_set( 'memory_limit', '128M' );
@ini_set( 'max_input_time', '240' );

// Set up the new PDF object
$pdf = new FPDF( 'L', 'in', 'Legal' );

// Remove page margins.
$pdf->SetMargins(0, 0);

// Disable auto page breaks.
$pdf->SetAutoPageBreak(0);

require_once( 'attendees_badges.php' );
// Set up badge counter
$counter = 1;
//exit('Attendees'.count($attendees).'<pre>'.print_r($attendees, true).'</pre>');
// Loop through each attendee and create a badge for them
//exit('<pre>'.print_r($attendees,true).'</pre>WHAT THE DUs?');
for ( $i = 0; $i < count($attendees); $i++ ) {
//for ( $i = 0; $i < 2; $i++ ) {
		
		// Set the text color to black.
		$pdf->SetTextColor(223,125,80);

		// Grab the template file that will be used for the badge page layout
		require('templates/sf2010.php');
		// Download and store the gravatar for use, FPDF does not support gravatar formatted image links
		$grav_file_raw = WP_CONTENT_DIR.'/blogs.dir/42/files/temp/' . $attendees[$i]['first_name'] . '-' . rand();
		$grav_url = 'http://www.gravatar.com/avatar/' . md5($attendees[$i]['email']) . '?s=512&default=http%3A%2F%2F2010.sf.wordcamp.org%2Fblank.jpg';
	//	exit('Data from Gravatar '.$grav_url);
		$grav_data = get_file_by_curl( $grav_url, $grav_file_raw );

		// Check if the image is a png, if it is, convert it, otherwise add a JPG extension to the raw filename
		if ( !$grav_file = pngtojpg($grav_file_raw) ) {
			$grav_file_extension = get_image_extension($grav_file_raw);
			$grav_file = $grav_file_raw . $grav_file_extension;
			rename( $grav_file_raw, $grav_file );
		}
		// Add the background image for the badge to the page
		$back_path = WP_CONTENT_DIR.'/plugins/'.WPSC_TICKETS_FOLDER.'/images/badgelogo.jpg';
		$pdf->image($back_path, $background_x, $background_y, 2.8, 1.23);

		//set all images to the man.jpg for testing

		$pdf->image($grav_file, $avatar_x, $avatar_y, 0.6, 0.6);
		$pdf->SetDrawColor(187,187,187);
		$pdf->Rect($avatar_x - 0.02, $avatar_y - 0.02, 0.64, 0.64);

		// Set the co-ordinates, font, and text for the first name
		$pdf->SetXY($text_x, $text_y);
		$pdf->SetFont('helvetica','b',28);
		$pdf->MultiCell(0, 0,ucwords(stripslashes($attendees[$i]['first_name'])),0,'L');

		// Set the co-ordinates, font, and text for the last name
		$pdf->SetXY($text_x, $text_y + 0.35);
		$pdf->SetFont('helvetica','',18);
		$pdf->SetTextColor(112,205,223);
		$pdf->MultiCell(0, 0,stripslashes(ucwords($attendees[$i]['last_name'])),0,'L');

		// Remove http:// from blog URL's and also remove ending slashes
		$attendees[$i]['blog'] = str_replace('http://', '', $attendees[$i]['blog']);
		$attendees[$i]['blog'] = str_replace('www.', '', $attendees[$i]['blog']);

		if ( $attendees[$i]['blog'] ) {
			if ( $attendees[$i]['blog'][strlen($attendees[$i]['blog']) - 1] == '/' )
				$attendees[$i]['blog'][strlen($attendees[$i]['blog']) - 1] = '';
		}

		$pdf->SetXY($infotext_x, $infotext_y);
		$pdf->SetFont('helvetica','',10);
		$pdf->SetTextColor(99,100,102);

		if ( stripslashes($attendees[$i]['company']) ) {
			$pdf->SetFont('helvetica','b',11);
			$pdf->MultiCell( 2.4, 0.21, stripslashes($attendees[$i]['company']), 0, 'L' );
			$infotext_y += 0.23;
		}

		if ( stripslashes($attendees[$i]['blog']) ) {
			$pdf->SetXY($infotext_x, $infotext_y);
			$pdf->SetFont('helvetica','',10);
			$pdf->MultiCell( 2.4, 0.21, stripslashes($attendees[$i]['blog']), 0, 'L' );
			$infotext_y += 0.23;
		}

		if ( stripslashes($attendees[$i]['twitter']) && strlen($attendees[$i]['twitter']) > 1 ) {
			$pdf->SetXY($infotext_x, $infotext_y);
			$pdf->SetFont('helvetica','',10);
			$pdf->MultiCell( 2.4, 0.21, '@' . str_replace( '@', '', str_replace( 'twitter.com/', '', stripslashes( $attendees[$i]['twitter'] ) ) ), 0, 'L' );
		}

		$pdf->SetTextColor(223,125,80);
		$pdf->SetXY($years_x, $years_y);

		if ( $attendees[$i]['how_long'] ) {
			if ( $attendees[$i]['how_long'] == 'new' ) {
				$pdf->SetXY($years_x + 0.21, $years_y);
			}else if($attendees[$i]['how_long'] == '6 mnths'){
				$pdf->SetFont('helvetica','',20);
				$pdf->MultiCell(2, 0, ucwords(stripslashes('6')));
				$pdf->SetXY($years_x + 0.21, $years_y);
				$pdf->SetFont('helvetica','',12);
			}else if($attendees[$i]['how_long'] == '6-12 mnths'){
				$pdf->SetFont('helvetica','',20);
				$pdf->MultiCell(2, 0, ucwords(stripslashes('12')));
				$pdf->SetXY($years_x + 0.31, $years_y);
				$pdf->SetFont('helvetica','',12);
			}else {
				$pdf->SetFont('helvetica','',20);
				$pdf->MultiCell(2, 0, ucwords(stripslashes($attendees[$i]['how_long'])));
				$pdf->SetXY($years_x + 0.21, $years_y);
				$pdf->SetFont('helvetica','',12);
			}

			if ( $attendees[$i]['how_long'] == '1' )
				$years_with = "YEAR";
			else if ( $attendees[$i]['how_long'] == 'new' )
				$years_with = "NEWBIE";
			else if ($attendees[$i]['how_long'] == '6 mnths' || $attendees[$i]['how_long'] == '6-12 mnths')
				$years_with = 'MONTHS';
			else
				$years_with = 'YEARS';

 			$pdf->SetFont('helvetica','', 6);
			$pdf->SetTextColor(112,205,223);
			$pdf->MultiCell(2, 0, $years_with);
		}

		$pdf->SetFont('helvetica','', 6);
		$pdf->SetTextColor(175, 175, 175);
		$pdf->SetXY($typebox_x, $typebox_y - 0.35);
		$pdf->MultiCell(3, 0.55, $attendees[$i]['tsex'] . $attendees[$i]['tsize'] . $attendees[$i]['vegetarian'] . $attendees[$i]['sun'], 0, 'R' );

		// Grey: 186, 188, 191
		// Automattic: 6, 66, 108

		$pdf->SetFillColor( 95, 188, 208 );
	
		if ( false !== strpos( 'Speaker', $attendees[$i]['job_title'] ) || false !== strpos( 'Core Contributor', $attendees[$i]['job_title'] ) || false !== strpos( 'Sponsor', $attendees[$i]['job_title'] )  || false !== strpos( 'Organizer', $attendees[$i]['job_title'] ) || false !== strpos( 'Volunteer', $attendees[$i]['job_title'] ) )
			$pdf->SetFillColor( 225, 129, 69 );
		
		$pdf->Rect( $typebox_x, $typebox_y, 3, 0.5, 'F' );

		$pdf->SetTextColor(255, 255, 255);
		$pdf->SetXY($typebox_x, $typebox_y);
		$pdf->SetFont('helvetica','b', 12);
		$pdf->MultiCell( 3, 0.5, strtoupper($attendees[$i]['job_title']), 0, 'C' );

		/* Draw crop lines */
		$pdf->SetDrawColor(187,187,187);
		$counter++;
}

// Output the PDF file to the browser
$pdf->Output();
exit();


}
/**
 * Description get coupons used searches for all coupons used and calculates how many times they have been used
 * @access public
 *
 * @return $output html li items
 */
function wpsc_get_coupons_used(){
	global $wpdb;
	$sql = 'SELECT `coupon_code` FROM `'.WPSC_TABLE_COUPON_CODES.'` WHERE `active`=1';
	$coupon_names = $wpdb->get_col($sql);
	foreach($coupon_names as $coupon_name){
		$sql = 'SELECT count(*) FROM `'.WPSC_TABLE_PURCHASE_LOGS.'` WHERE `discount_data` ="'.$coupon_name.'"';
		$count = $wpdb->get_var($sql);
		if(is_null($count)){ $count = 0;}
		$output .= '<li>'.$coupon_name.' :<a href="'.add_query_arg('wpsc_ticket_filter', $coupon_name).'">'.$count.' times used</a>';
	}
	$output .= '<li><a href="'.remove_query_arg('wpsc_ticket_filter').'">Show All</a></li>';
	return $output;
}
if($_REQUEST['wpsc_admin_action']=='display_ticket_badges'){
add_action('init','wpsc_display_ticket_badges');
}
/**
 * Description gets all categories available used for populating a select box for chosing a ticket category
 * (on first setup of the ticket module)
 * @access public
 *
 * @return associative array $categories
 */
function wpsc_tickets_get_categories(){
	global $wpdb;
	$sql = "SELECT `id`, `name` FROM `".WPSC_TABLE_PRODUCT_CATEGORIES."` WHERE `active`='1'";
	$categories = $wpdb->get_results($sql, ARRAY_A);
	return $categories;
}

function wpsc_tickets_has_coupons(){
	global $wpdb;
	$sql = 'SELECT COUNT(*) FROM `'.WPSC_TABLE_COUPON_CODES.'` WHERE `active`=1';
	$coupons = $wpdb->get_var($sql);
	if($coupons > 0){
		return true;
	}else{
		return false;
	}

}

/**
 * Description ticket spec is the inner html for the 'At a Glance' meta box on the wpsc-ticket-logs wp-admin page
 * @access public
 *
 * @return none
 */
function wpsc_tickets_specs(){
?>
<div style='width:50%;float:left;'>
<h4><?php _e('Stock Remaining and Stock Sold','wpsc'); ?></h4>
<ul>
<?php echo wpsc_get_quantities_left(); ?>
</ul>
</div>

<div style='width:50%;float:left;'>
<h4><?php _e('Total Coupons Used','wpsc'); ?></h4>
<ul>
<?php if(wpsc_tickets_has_coupons()){
	 echo wpsc_get_coupons_used();
}else{?>
<li><?php _e('No coupons have been set.','wpsc'); ?></li>
<?php } ?>
</ul>
</div>

<div style='clear:both;'></div>
<p>Number of Attendees : <?php echo wpsc_get_ticket_attendees(); ?></p>
<div style='clear:both;'></div>
<?php

 }
if($_REQUEST['wpsc_admin_action'] == 'display_ticket_pdf') {
	add_action('admin_init', 'wpsc_display_ticket_pdf');
}


/**
 * Description ticket get category name returns the category name associated to the ticket module
 * @access public
 *
 * @return string $category_name
 */
function wpsc_ticket_get_category_name(){
	global $wpdb;
	$id = get_option('wpsc_ticket_module_category');
	$sql = "SELECT `name` FROM `".WPSC_TABLE_PRODUCT_CATEGORIES."` WHERE `id`=".$id;
	$category_name = $wpdb->get_var($sql);
	return $category_name;
}

/**
 * Description ticket set category updates the ticket module category option
 * @access public
 *
 * @return none
 */
function wpsc_ticket_set_category(){
	global $wpdb;
	$category = $wpdb->escape($_POST['wpsc_ticket_category']);
	if(isset($_POST['wpsc_ticket_category'])){
		update_option('wpsc_ticket_module_category',$category );
	}
}

if($_REQUEST['wpsc_ticket_category_submit']=='Submit'){
	add_action('admin_init','wpsc_ticket_set_category');
}


//Add admin tickets page
require_once("display-tickets.page.php");
require_once('adjust_headers.php');
function wpsc_add_tickets_page($page_hooks, $base_page) {
	$page_hooks[] =  add_submenu_page($base_page, __('-Tickets','wpsc'),  __('-Tickets','wpsc'), 7, 'wpsc-ticket-logs', 'wpsc_display_ticket_logs');
	return $page_hooks;
}
add_filter('wpsc_additional_pages', 'wpsc_add_tickets_page',10, 2);

function wpsc_adjust_headers() {

  wp_iframe('wpsc_adjust_headers_function');
  exit();
}


if ($_REQUEST['wpsc_tickets_action'] == 'adjust_headers') {
	add_action('admin_init','wpsc_adjust_headers');
}
function wpsc_tickets_adjust_headers_submit() {
//exit('<pre>'.print_r($_POST, true).'</pre>');
	foreach($_POST as $key =>$post){
	 if(is_array($post)){
	 	$headers[$key] = $post;
	 }
	}
//	$headers = serialize($headers);
	update_option('wpsc_tickets_headers' , $headers);
}


if ($_REQUEST['wpsc_tickets_action'] == 'wpsc_tickets_adjust_headers') {
	add_action('admin_init','wpsc_tickets_adjust_headers_submit');
}
function wpsc_edit_ticket_data_submit() {
	global $wpdb;

//exit('<pre>'.print_r($_POST, true).'</pre>');
	$purch_id = (int)$_POST['wpsc_edit_purchlog_id'];
	check_admin_referer('wpsc-tickets-submit-edit_' . $purch_id);
	foreach((array)$_POST['submit_form_id'] as $checkout_id=> $form_value){
		foreach((array)$form_value as $submit_id => $value){
			if(is_numeric($submit_id)){
				$sql = 'UPDATE `'.WPSC_TABLE_SUBMITED_FORM_DATA.'` SET `value`="'.stripslashes($value).'" WHERE `log_id`="'.$purch_id.'" AND `form_id`="'.$checkout_id.'" AND `id`="'.$submit_id.'"';
				//$update = $wpdb->update(WPSC_TABLE_SUBMITED_FORM_DATA,
				//	array('value'=>$value),
				//	array('log_id'=>$purch_id,'form_id'=>$checkout_id,'id'=>$submit_id));
				$wpdb->query($sql);
				//	echo('update=>'.$update.'<br /> Sql=>'.$sql.'<br />');
			}elseif($submit_id == 'new'){
				if(is_array($value)){
					foreach($value as $val){
						$wpdb->insert( WPSC_TABLE_SUBMITED_FORM_DATA,
							array( 'value' => $val,'log_id'=>$purch_id,'form_id'=>$checkout_id ),
							array( '%s','%d','%d'));
					}
				}else{
				$sql = "INSERT INTO `".WPSC_TABLE_SUBMITED_FORM_DATA."` (`value`, `log_id`,`form_id`) VALUES ('".$value."','".$purch_id."','".$checkout_id."')";
//			echo $sql.' <br />';
//				$wpdb->query($sql);
				$wpdb->insert( WPSC_TABLE_SUBMITED_FORM_DATA,
					array( 'value' => $value,'log_id'=>$purch_id,'form_id'=>$checkout_id ),
					array( '%s','%d','%d'));
				}

			}


		}

	}
	$sendback = wp_get_referer();
	$sendback = remove_query_arg('wpsc_ticket_purchlog',$sendback);

	wp_redirect($sendback);
}


if ($_REQUEST['wpsc_tickets_action'] == 'wpsc_edit_ticket_data') {
	add_action('admin_init','wpsc_edit_ticket_data_submit');
}

//for editting
function wpsc_get_ticket_form_fields($purchid){
	global $wpdb;
	$sql = "SELECT `id` FROM `".WPSC_TABLE_CHECKOUT_FORMS."` WHERE `active`='1' AND `type` !='heading' AND `checkout_set`='".wpsc_get_ticket_checkout_set()."' ORDER BY `order`";
	$ids_array = $wpdb->get_col($sql);
	$form_count = count($ids_array);
	$ids = implode(',',$ids_array);
	$i=0;


	foreach($ids_array as $id){
		$sql = "SELECT `".WPSC_TABLE_SUBMITED_FORM_DATA."`.`value`,`".WPSC_TABLE_SUBMITED_FORM_DATA."`.`id` FROM `".WPSC_TABLE_SUBMITED_FORM_DATA."` WHERE `".WPSC_TABLE_SUBMITED_FORM_DATA."`.`log_id` = '".$purchid."' AND `".WPSC_TABLE_SUBMITED_FORM_DATA."`.`form_id` ='".$id."' ORDER BY `id`";

		$values[$id] = $wpdb->get_results($sql, ARRAY_N);
	//	$values[$id]['name'] = $id['1'];
	}
	$rows = count(current($values));
	//exit('<pre>'.print_r($values, true).'</pre>');
	while($i < $rows){
		$output .="<tr>";
		foreach($ids_array as $id){
//			exit('<pre>'.print_r($values, true).'</pre>');
			foreach($values as $key => $v){
				if($id == $key){
				if($v[$i][0] == '' && $v[$i][1] == ''){
					$output .= "<td><input type='text' value='".$v[$i][0]."' name='submit_form_id[".$id."][new][]' size='14' /></td>";
				}else{

					$v[$i][0] = maybe_unserialize($v[$i][0]);
					if(is_array($v[$i][0])){
						$v[$i][0] = implode(',',$v[$i][0]);
					}
					$output .= "<td><input type='text' value='".$v[$i][0]."' name='submit_form_id[".$id."][".$v[$i][1]."]' size='14' /></td>";
				}
				}
			}
		}
		$output .="</tr>";
		$i++;
	}

	return $output;

}

//Shortcode for attendees page
function wpsc_ticket_attendees_shortcode($content = ''){
	if(preg_match("/\[wpsc_attendees_page\]/",$content,$matches)) {
		require_once('attendees.php');
		$output = wpsc_attendees_page();
		return preg_replace("/\[wpsc_attendees_page\]/",$output, $content);
	} else {
		return $content;
	}

}

add_filter('the_content', 'wpsc_ticket_attendees_shortcode');
function wpsc_tickets_which_sessions($users){
?>
<div style='width:30%;float:left;'>
			<?php
			$Blogger=0;$BeginnerDev=0;$AdvancedDev=0;$CMS=0;$Academic=0;$BuddyPress=0;$opensource=0;$misseSession=array();
			foreach($users as $user_details){
	$default_forms[$formname][] = array('Blogger', 'BeginnerDev', 'AdvancedDev', 'CMS', 'Academic', 'BuddyPress', 'opensource');
				if($user_details[10] != ''){
					$user_deets = maybe_unserialize($user_details[10]);
				if(!is_array($user_deets)){
					$user_deets = explode(',',$user_deets);
				}
				foreach($user_deets as $deets)
					switch($deets){
						case 'Blogger':
						$Blogger++;
						break;
						case 'BeginnerDev':
						$BeginnerDev++;
						break;
						case 'AdvancedDev':
						//case ' support':
						$AdvancedDev++;
						break;
						case 'CMS':
						$CMS++;
						break;
						case 'Academic':
						$Academic++;
						break;
						case 'BuddyPress':
						$BuddyPress++;
						break;
						case 'opensource':
						$opensource++;
						break;
						case '-1':
						break;
						case ' ':
						case '':
						break;
						default:
						$misseSession[] = $deets;
						break;

					}
				}

			} ?>


			<h4>Which session are you most likely to attend</h4>
			<ul>
				<li>Blogger: <?php echo $Blogger; ?></li>
				<li>BeginnerDev: <?php echo $BeginnerDev; ?></li>
				<li>AdvancedDev: <?php echo $AdvancedDev; ?></li>
				<li>CMS: <?php echo $CMS; ?></li>
				<li>Academic: <?php echo $Academic; ?></li>
				<li>BuddyPress: <?php echo $BuddyPress; ?></li>
				<li>opensource: <?php echo $opensource; ?></li>
				<li>Other: <?php echo implode(' , ', $misseSession); ?></li>
			</ul>

			</div>
			<?php
}

function wpsc_tickets_make_money($users){
?>
<div style='width:30%;float:left;'>
			<?php
			$noMoney=0;$dayjob=0;$support=0;$custom_dev=0;$hosting=0;$ads=0;$ecommerce=0;$themes=0;$plugins=0;$missedMoney=array();
			foreach($users as $user_details){

				if($user_details[12] != ''){
				$user_deets = maybe_unserialize($user_details[12]);
				//echo '<pre>'.print_r($user_deets, true).'</pre>';
				//break;
				if(!is_array($user_deets)){
					$user_deets = explode(',',$user_deets);
				}
				foreach($user_deets as $deets)
					switch($deets){
						case 'none':
						$noMoney++;
						break;
						case 'day_job':
						case 'Day_Job':
						$dayjob++;
						break;
						case 'support':
						case ' support':
						$support++;
						break;
						case 'custom_dev':
						case 'Custom_Dev':
						$custom_dev++;
						break;
						case 'hosting':
						$hosting++;
						break;
						case 'ads':
						$ads++;
						break;
						case 'ecommerce':
						$ecommerce++;
						break;
						case 'themes':
						$themes++;
						break;
						case '-1':
						break;
						case 'plugins':
						$plugins++;
						break;
						case ' ':
						case '':
						break;
						default:
						$missedMoney[] = $deets;
						break;

					}
				}

			} ?>


			<h4>Do you make money from WordPress</h4>
			<ul>
				<li>None: <?php echo $noMoney; ?></li>
				<li>Day job: <?php echo $dayjob; ?></li>
				<li>Support: <?php echo $support; ?></li>
				<li>Custom Development: <?php echo $custom_dev; ?></li>
				<li>Hosting: <?php echo $hosting; ?></li>
				<li>Ads: <?php echo $ads; ?></li>
				<li>E-Commerce: <?php echo $ecommerce; ?></li>
				<li>Themes: <?php echo $themes; ?></li>
				<li>Plugins: <?php echo $plugins; ?></li>
				<li>Other: <?php echo implode(' , ', $missedMoney); ?></li>
			</ul>

			</div>
			<?php
}
function wpsc_tickets_best_describe($users){
				$core=0; $theme_dev=0;$theme_des=0; $personal=0; $buddypress=0; $sysadmin=0;$corporate=0; $plugindev=0;
				$opensource=0; $forummod=0; $missedPerson=array();
				foreach($users as $user_details){
				if($user_details[11] != ''){
					switch($user_details[11]){
						case 'Core_Contributer':
						$core++;
						break;
						case 'Theme_Developer':
						$theme_dev++;
						break;
						case 'Theme_Designer_(no_coding)':
						$theme_des++;
						break;
						case 'Personal_Blogger':
						$personal++;
						break;
						case 'BuddyPress/MU':
						$buddypress++;
						break;
						case 'System_Admin':
						$sysadmin++;
						break;
						case 'Corporate_Blogger':
						$corporate++;
						break;
						case 'Plugin_Developer':
						$plugindev++;
						break;
						case 'Open_Source_Community':
						$opensource++;
						break;
						case 'Forum_Moderator':
						$forummod++;
						break;
						default:
						$missedPerson[] = $user_details[11];
						break;
					}
				}

			} ?>
			<div style='width:30%;float:left;'>
				<h4>How would you best describe yourself</h4>
				<ul>
					<li>Core Contributer: <?php echo $core; ?></li>
					<li>Theme Developer: <?php echo $theme_dev; ?></li>
					<li>Theme Designer: <?php echo $theme_des; ?></li>
					<li>Personal Blogger: <?php echo $personal; ?></li>
					<li>BuddyPress/MU: <?php echo $buddypress; ?></li>
					<li>System Admin: <?php echo $sysadmin; ?></li>
					<li>Corporate Blogger: <?php echo $corporate; ?></li>
					<li>Plugin Developer: <?php echo $plugindev; ?></li>
					<li>Open Source Community: <?php echo $opensource; ?></li>
					<li>Forum Moderator: <?php echo $forummod; ?></li>
					<li>Other: <?php echo implode(', ', $missedPerson); ?></li>
				</ul>
			</div>
			<?php
}

function wpsc_tickets_blogs_manage($users){
?>
<div style='width:30%;float:left;'>
			<?php
			$noblogs=0;$oneBlogs=0;$threeBlogs=0;$sixBlogs=0;$elevenBlogs=0;$twentyBlogs=0;$missedBlogs=array();
			foreach($users as $user_details){
				if($user_details[9] != ''){
					switch($user_details[9]){
						case 'none':
						case '-1':
						$noblogs++;
						break;
						case '1_-_2':
						$oneBlogs++;
						break;
						case '3_-_5':
						$threeBlogs++;
						break;
						case '6_-_10':
						$sixBlogs++;
						break;
						case '11_-_20':
						$elevenBlogs++;
						break;
						case '20_+':
						$twentyBlogs++;
						break;
						default:
						$missedBlogs[] = $user_details[9];
						break;

					}
				}

			} ?>


			<h4>How many WordPress Blogs Do You Manage</h4>
			<ul>
				<li>None: <?php echo $noblogs; ?></li>
				<li>1 to 2: <?php echo $oneBlogs; ?></li>
				<li>3 to 5: <?php echo $threeBlogs; ?></li>
				<li>6 to 10: <?php echo $sixBlogs; ?></li>
				<li>11 to 20: <?php echo $elevenBlogs; ?></li>
				<li>20 Plus: <?php echo $twentyBlogs; ?></li>

				<li>Other: <?php echo implode(' , ', $missedBlogs); ?></li>
			</ul>

			</div>
			<?php
}
function wpsc_tickets_using_wp($users){
?>

	<div style='width:40%;float:left;'>
			<h4>How long have you been using WordPress</h4>
			<?php
			$never=0;$less6 = 0;$a6to1=0;$a1plus=0; $a2plus=0;$a3plus=0; $a4plus=0;$a5plus=0;$a6plus=0;$a7plus=0;
			foreach((array)$users as $user_details){
				if($user_details[8] != ''){
					switch($user_details[8]){
						case 'less_than_6_mnths':
						$aless6++;
						break;
						case '6-12mnths':
						$a6to1++;
						break;
						case '1yr':
						$a1plus++;
						break;
						case '2yrs':
						$a2plus++;
						break;
						case '3yrs':
						$a3plus++;
						break;
						case '4yrs':
						$a4plus++;
						break;
						case '5yrs':
						$a5plus++;
						break;
						case '6yrs':
						$a6plus++;
						break;
						case'never':
						case '-1':
						$never++;
						break;
						case '7':
						case '7yrs':
						$a7plus++;
						break;
						default:
						$missedyrs[] = $user_details[8];
						break;
					} //switch
					} //if not empty
					} //foreach ?>
					<ul>
					<li>Never: <?php echo $never; ?></li>
					<li>Less than 6 Months: <?php echo $aless6; ?></li>
					<li>6 - 12 Months: <?php echo $a6to1; ?></li>
					<li>1 Year: <?php echo $a1plus; ?></li>
					<li>2 Years: <?php echo $a2plus; ?></li>
					<li>3 Years: <?php echo $a3plus; ?></li>
					<li>4 Years: <?php echo $a4plus; ?></li>
					<li>5 Years: <?php echo $a5plus; ?></li>
					<li>6 Years: <?php echo $a6plus; ?></li>
					<li>7 Years: <?php echo $a7plus; ?></li>

					<li>Other: <?php if(is_array($missedyrs)){ echo implode(',', $missedyrs);} ?></li>
				</ul>


			</div>
<?php
}

function wpsc_tickets_tee_size($users){
?>
	<div style='width:30%;float:left;'>
			<?php
			$malexxlarge=0; $malexlarge =0; $malelarge=0;$malemedium=0;$malesmall=0;
			$femalexlarge=0; $femalelarge =0;$femalemedium =0;$femalesmall =0;
			?>
			<?php foreach((array)$users as $user_details){
				if($user_details[6] != ''){
					switch($user_details[6]){
						case 'malesmall':
						case 'Mens_Small':
						$malesmall++;
						break;
						case 'Mens_Medium':
						case 'malemedium':
						$malemedium++;
						break;
						case 'Mens_Large':
						case 'malelarge':
						$malelarge++;
						break;
						case 'malexlarge':
						case 'Mens_X_Large':
						$malexlarge++;
						break;
						case 'Mens_XX_Large':
						case 'malexxlarge':
						$malexxlarge++;
						break;
						case 'femalesmall':
						case 'Womens_Small':
						$femalesmall++;
						break;
						case 'femalemedium':
						case 'Womens_Medium':
						$femalemedium++;
						break;
						case 'femalelarge':
						case 'Womens_Large':
						$femalelarge++;
						break;
						case 'femalexlarge':
						case 'Womens_X_Large':
						$femalexlarge++;
						break;
						case 'femalexxlarge':
						case 'Womens_XX_Large':
						$femalexxlarge++;
						break;

						default:
						$missedts[] = $user_details[6];
						break;
					} //switch
					} //if not empty
					} //foreach ?>
				<h4>T-Shirt Sizes</h4>
				<ul>
					<li><strong>Mens</strong></li>
					<li>Mens Small: <?php echo $malesmall; ?></li>
					<li>Mens Medium: <?php echo $malemedium; ?></li>
					<li>Mens Large: <?php echo $malelarge; ?></li>
					<li>Mens X Large: <?php echo $malexlarge; ?></li>
					<li>Mens XX Large: <?php echo $malexxlarge; ?></li>
					<li><strong>Womens</strong></li>
					<li>Womens Small: <?php echo $femalesmall; ?></li>
					<li>Womens Medium: <?php echo $femalemedium; ?></li>
					<li>Womens Large: <?php echo $femalelarge; ?></li>
					<li>Womens X Large: <?php echo $femalexlarge; ?></li>
					<li>Womens XX Large: <?php echo $femalexxlarge; ?></li>
					<li>Other: <?php if(is_array($missedts)){ echo implode(',', $missedts);} ?></li>
				</ul>

			</div>
<?php
}
function wpsc_tickets_meal_stats($users){
?>
 <div style='width:30%;float:left;'>
			<?php $none = 0; $vegetarian=0; $vegan=0; $gluten=0; $kosher=0; ?>
			<?php foreach($users as $user_details){
				if($user_details[7] != ''){
					switch($user_details[7]){
						case 'none':
						case '-1':
						$none++;
						break;
						case 'Vegetarian':
						$vegetarian++;
						break;
						case 'Vegan':
						$vegan++;
						break;
						case 'Gluten Free':
						case 'Gluten_Free':
						$gluten++;
						break;
						case 'Kosher':
						$kosher++;
						break;
						default:
						$missed[] = $user_details[7];
						break;

					}
				}

			} ?>

				<h4>Meal Restrictions</h4>
				<ul>
					<li>No Restrictions: <?php echo $none; ?></li>
					<li>Vegetarians: <?php echo $vegetarian; ?></li>
					<li>Vegan: <?php echo $vegan; ?></li>
					<li>Gluten Free: <?php echo $gluten; ?></li>
					<li>Kosher: <?php echo $kosher; ?></li>
					<li>Other: <?php if(is_array($missed)){ echo implode(',', $missed);} ?></li>
				</ul>
			</div>
	<?php
}
?>