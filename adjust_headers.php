<?php
function wpsc_adjust_headers_function(){
$headers = get_option('wpsc_tickets_headers');

if($headers == ''){
	$headers = wpsc_tickets_get_headers();
}else{
$count = count($headers);
$headers2 = wpsc_tickets_get_headers();
	if($count < count($headers2)){
		$headers = $headers2;
	}
}
//exit('<pre>'.print_r($headers, true).'</pre>');
?>
<style type='text/css'>
table th{
padding:15px 15px 5px 0;
}
</style>
	<div id="outer">
<form action='' method='post'>
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
	<br />
</tr>
<?php
}
?>
<input type='hidden' value='wpsc_tickets_adjust_headers' name='wpsc_tickets_action' />
<input type='submit' value='Submit' name='wpsc_tickets_submit' />
</table>
</form>
</div>
<?php
}
?>