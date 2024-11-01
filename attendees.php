<?php 
function wpsc_attendees_page(){

?>
<style type='text/css'>
 div.attendee {
border-bottom:1px solid #DFDFDF;
float:left;
font-size:12px;
height:50px;
list-style-image:none;
list-style-position:outside;
list-style-type:none;
margin:0;
padding:4px 0;
width:50%;
}
div.attendee img {
-moz-background-clip:border;
-moz-background-inline-policy:continuous;
-moz-background-origin:padding;
background:#F7F7F7 none repeat scroll 0 0;
border:1px solid #DFDFDF;
float:left;
margin-right:6px;
vertical-align:middle;
}
div.attendee h4 {
margin-bottom:4px;
margin-top:0;
}

</style>


		
		<p class="lead">These folks have signed up already. <a href="http://en.gravatar.com/">Get a Gravatar</a> to have your picture show up by your name.</p>

<?php
	$users = wpsc_get_ticket_content(false,true);
//if(get_option('show_avatars')){ echo 'site uses gravatars'; }else{ echo 'no gravatars allowed';}
	foreach((array)$users as $user){
//	exit('<pre>'.print_r($user, true).'</pre>');
		if($user[0] != '' && $user[1] != ''){
			$attendees .="<div class='attendee'>";
			$gravatar = get_avatar($user[2], 42, WP_CONTENT_URL.'/plugins/'.WPSC_TICKETS_FOLDER.'/images/default.png' );
			if($user[4] != ''){
				if(substr($user[4],0,7) != 'http://'){
					//exit('yes is required');
					$user[4] = 'http://'.$user[4];
				}
				$attendees .= "<h4><a href='".$user[4]."' rel='nofollow'> ".$gravatar.$user[0].' '.$user[1].'</a></h4>';
			}else{
				$attendees .= $gravatar."<h4>".$user[0].' '.$user[1].'</h4>';
			}
			if( false && $user[5] != '' ) { // disabling Company field
				$attendees .= '<p>'.$user[5].'</p>';
			}

			if ( !empty( $user[4] ) ) {
				$parts = parse_url( $user[4] );
				$host = $parts['host'];
				$host = strtolower( $host );
				$host = str_replace( 'www.', '', $host );
				if ( strlen( $host ) > 22 )
					$host = substr( $host, 0, 22 ) . '&#8230;';
				$attendees .= "<p><a href='{$user[4]}' rel='nofollow' style='text-decoration: none; color: #aaa;'>$host</a></p>";
			}
	
			$attendees .='</div>';
		}
	}

?>

<p><?php echo number_format( wpsc_get_ticket_attendees()); ?> attendees total.</p>
<br clear="all" />
<?php echo $attendees; ?>
<br clear="all" />
<?php

}
?>
