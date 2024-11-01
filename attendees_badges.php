<?php
$attendees = array();
$attendees_org = wpsc_get_ticket_content(false,true);


$count = 0;
//exit('<pre>'.print_r($attendees_org,true).'</pre>');
//remove underscores and add descriptive keys to the attendees array
/*


$attendees_org = Array
(
     Array
        (
            'first_name' => 'aaaa',
            'last_name' => 'aaaaa',
            'email' => 'AAAA@aaa.com',
            'twitter' => 'aaaa',
            'blog' => 'aaaa',
            'company' => 'aaaa',
            'tsize' => 'Mens_Large',
            'vegetarian' => 'Vegan',
            'how_long' => '6 mnths',
            'job_title' => 'BeginnerDev',
        ),

    Array
        (
            'first_name' => 'bbbb',
            'last_name' => 'bbbb',
            'email' => 'bbb@bb.bb',
            'twitter' => 'bbb',
            'blog' => 'bbbb',
            'company' => 'bbbb',
            'tsize' => 'Mens_Large',
            'vegetarian' => 'Vegetarian',
            'how_long' => '6-12 mnths',
            'job_title' => 'BeginnerDev',
        ),

	Array
        (
            'first_name' => 'Jeffry',
            'last_name' => 'Ghazally',
            'email' => 'jghazally@gmail.com',
            'twitter' => 'jghazally',
            'blog' => 'screamingcodemonkey.com',
            'company' => 'Screaming Code Monkey',
            'tsize' => 'Mens_Large',
            'vegetarian' => 'Vegan',
            'how_long' => '6 mnths',
            'job_title' => 'BeginnerDev',
        ),

    Array
        (
            'first_name' => 'skdjfhkjhsdfg',
            'last_name' => 'kjsdhgfdjshg',
            'email' => 'ksjdhgsjdhfg@lskjdfhkjlhsdf.sdf',
            'twitter' => 'sdklfjhksdjh',
            'blog' => 'kljsdhflksdfjh',
            'company' => 'klsjdfhklsdfhj',
            'tsize' => 'Mens_X_Large',
            'vegetarian' => 'Gluten_Free',
            'how_long' => '1',
            'job_title' => 'CMS',
        ),

    Array
        (
            'first_name' => 'skdjhflksdjh',
            'last_name' => 'kljhkljhjklhjkkljhj',
            'email' => 'ljkhhjlkkhljhjkhjklhjk@lkjhhjk.asd',
            'twitter' => 'ksdjhfk',
            'blog' => 'sdkjfdsjkhg',
            'company' => 'skdjhfgj',
            'tsize' => 'Womens_X_Large',
            'vegetarian' => 'Gluten_Free',
            'how_long' => '4',
            'job_title' => 'opensource',
        ),

    Array
        (
            'first_name' => 'lksdjhfkljsdhfkljh',
            'last_name' => 'klsjdhfkjsdhfjkhg',
            'email' => 'ssjkdfh@jdklhsdkfj.sdf',
            'twitter' => 'sjdhgfsjdfkhg',
            'blog' => 'jksdhfgdksjfhg',
            'company' => 'kjdhsfgsfdjhg',
            'tsize' => 'Mens_X_Large',
            'vegetarian' => 'Gluten_Free',
            'how_long' => '1',
            'job_title' => 'CMS',
        ),

    Array
        (
            'first_name' => 'aslkdjh',
            'last_name' => 'sdlkfjh',
            'email' => 'slkdfjh@skldjfh.asd',
            'twitter' => 's;dlkj',
            'blog' => 'askjhaslkjdh',
            'company' => 'slkdjfhklsdhfkl',
            'tsize' => 'Mens_Medium',
            'vegetarian' => 'Vegan',
            'how_long' => '6 mnths',
            'job_title' => 'BeginnerDev',
        ),

    Array
        (
            'first_name' => 'aslkdjh',
            'last_name' => 'sdlkfjh',
            'email' => 'slkdfjh@skldjfh.asd',
            'twitter' => 's;dlkj',
            'blog' => 'askjhaslkjdh',
            'company' => 'slkdjfhklsdhfkl',
            'tsize' => 'Mens_Medium',
            'vegetarian' => 'Vegan',
            'how_long' => '6 mnths',
            'job_title' => 'BeginnerDev',
        )
);

//exit('<pre>'.print_r($attendees_org,true).'</pre>');
*/

foreach($attendees_org as $attendee){

	if ( !empty( $attendee[0] ) ){
//echo('<pre>'.print_r($attendee,true).'</pre>');
		$attendees[$count]['first_name'] = $attendee[0];
		$attendees[$count]['last_name'] = $attendee[1];
		$attendees[$count]['email'] = $attendee[2];
		$attendees[$count]['twitter'] = $attendee[3];
		$attendees[$count]['blog'] = $attendee[4];
		$attendees[$count]['company'] = $attendee[5];
		
		if ( 'malesmall' == $attendee[6] || 'Mens_Small' == $attendee[6] ) 
			$attendee[6] = 'MS';

		else if ( 'femalesmall' == $attendee[6] || 'Womens_Small' == $attendee[6] ) 
			$attendee[6] = 'FS';
			
		else if ( 'malemedium' == $attendee[6] || 'Mens_Medium' == $attendee[6] ) 
			$attendee[6] = 'MM';

		else if ( 'femalemedium' == $attendee[6] || 'Womens_Medium' == $attendee[6] ) 
			$attendee[6] = 'FM';
				
		else if ( 'malelarge' == $attendee[6] || 'Mens_Large' == $attendee[6] ) 
			$attendee[6] = 'ML';

		else if ( 'femalelarge' == $attendee[6] || 'Womens_Large' == $attendee[6]) 
			$attendee[6] = 'FL';

		else if ( 'maleextralarge' == $attendee[6] || 'Mens_X_Large' == $attendee[6] ) 
			$attendee[6] = 'MXL';

		else if ( 'femaleextralarge' == $attendee[6] || 'Womens_X_Large' == $attendee[6] ) 
			$attendee[6] = 'FXL';
			
		else if ( 'maleextraextralarge' == $attendee[6] || 'Mens_XX_Large' == $attendee[6] ) 
			$attendee[6] = 'MXXL';

		else if ( 'femaleextraextralarge' == $attendee[6] || 'Womens_XX_Large' == $attendee[6] ) 
			$attendee[6] = 'FXXL';
										
		$attendees[$count]['tsize'] = $attendee[6];
	
		if ( 'Vegetarian' == $attendee[7] )
			$attendee[7] = 'V';
	
		else if ( 'Kosher' == $attendee[7] )
			$attendee[7] = 'K';

		else if ( 'Vegan' == $attendee[7] )
			$attendee[7] = 'G';

		else if ( 'none' == $attendee[7] || '-1' == $attendee[7] )
			$attendee[7] = '';
											
		$attendees[$count]['vegetarian'] = $attendee[7];
		$attendees[$count]['how_long'] = $attendee[8];
		
		if ( $attendee[11] == 'Core_Contributer' )
			$attendees[$count]['job_title'] = 'Core Contributor';

		else if ( $attendee[11] == 'Theme_Developer' || $attendee[11] == 'Theme_Designer_(no_coding)' )
			$attendees[$count]['job_title'] = 'Themes';

		else if ( $attendee[11] == 'Personal_Blogger' || $attendee[11] == 'Corporate_Blogger' )
			$attendees[$count]['job_title'] = 'Blogger';

		else if ( $attendee[11] == 'BuddyPress/MU' )
			$attendees[$count]['job_title'] = 'BuddyPress';
			
		else if ( $attendee[11] == 'Plugin_Developer' )
			$attendees[$count]['job_title'] = 'Plugins';

		else if ( $attendee[11] == 'Open_Source_Community' )
			$attendees[$count]['job_title'] = 'O.S. Community';

		else if ( $attendee[11] == 'System_Admin' )
			$attendees[$count]['job_title'] = 'I.T. Professional';

		else if ( $attendee[11] == 'speaker' )
			$attendees[$count]['job_title'] = 'Speaker';

		else if ( $attendee[11] == 'sponsor' )
			$attendees[$count]['job_title'] = 'Sponsor';

		else if ( $attendee[11] == 'volunteer' )
			$attendees[$count]['job_title'] = 'Volunteer';

		else
			$attendees[$count]['job_title'] = 'WordPress Fan';

			switch($attendee[8]){
				case 'less_than_6_mnths':
				$attendees[$count]['how_long'] = '6 mnths';
				break;
				case '6-12mnths':
				$attendees[$count]['how_long'] = '6-12 mnths';
				break;
				case '1yr':
				$attendees[$count]['how_long'] = '1';
				break;
				case '2yrs':
				$attendees[$count]['how_long'] = '2';
				break;
				case '3yrs':
				$attendees[$count]['how_long'] = '3';
				break;
				case '4yrs':
				$attendees[$count]['how_long'] = '4';
				break;
				case '5yrs':
				$attendees[$count]['how_long'] = '5';
				break;
				case '6yrs':
				$attendees[$count]['how_long'] = '6';
				break;
				case'never':
				case '-1':
				$attendees[$count]['how_long'] = 'new';
				break;
				case '7':
				case '7yrs':
				$attendees[$count]['how_long'] = '7';
				break;
				default:
				$attendees[$count]['how_long'] = '0';
				break;
			} //switch


	$count++;
	}

}
