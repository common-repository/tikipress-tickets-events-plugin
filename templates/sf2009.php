<?php
// Add a page, but only on a multiple of 6
if ( $counter == 1 || ( $counter % 8 == 1 ) ) {
	$pdf->AddPage('L', 'Legal');
	$counter = 1;
}
// Set the co-ordinates for all items in each of the badges
switch ( $counter ) {
	case 1:
		$background_x = 0;
		$background_y = 0;
		$avatar_x = 0.22;
		$avatar_y = 1.11;
		$text_x = 0.96;
		$text_y = 1.28;
		$line1_x = 0.22;
		$line1_y = 1.9;
		$infotext_x = 0.35;
		$infotext_y = 2;
		$line2_x = 0.22;
		$line2_y = 2.76;
		$years_x = 0.65;
		$years_y = 3.05;
		$typebox_x = 0;
		$typebox_y = 3.4;
	break;
	case 2:
		$background_x = 3;
		$background_y = 0;
		$avatar_x = 3.22;
		$avatar_y = 1.11;
		$text_x = 3.96;
		$text_y = 1.28;
		$line1_x = 3.22;
		$line1_y = 1.9;
		$infotext_x = 3.35;
		$infotext_y = 2;
		$line2_x = 3.22;
		$line2_y = 2.76;
		$years_x = 3.65;
		$years_y = 3.05;
		$typebox_x = 3;
		$typebox_y = 3.4;
	break;
	case 3:
		$background_x = 6;
		$background_y = 0;
		$avatar_x = 6.22;
		$avatar_y = 1.11;
		$text_x = 6.96;
		$text_y = 1.28;
		$line1_x = 6.22;
		$line1_y = 1.9;
		$infotext_x = 6.35;
		$infotext_y = 2;
		$line2_x = 6.22;
		$line2_y = 2.76;
		$years_x = 6.65;
		$years_y = 3.05;
		$typebox_x = 6;
		$typebox_y = 3.4;
	break;
	case 4:
		$background_x = 9;
		$background_y = 0;
		$avatar_x = 9.22;
		$avatar_y = 1.11;
		$text_x = 9.96;
		$text_y = 1.28;
		$line1_x = 9.22;
		$line1_y = 1.9;
		$infotext_x = 9.35;
		$infotext_y = 2;
		$line2_x = 9.22;
		$line2_y = 2.76;
		$years_x = 9.65;
		$years_y = 3.05;
		$typebox_x = 9;
		$typebox_y = 3.4;
	break;
	case 5:
		$background_x = 0;
		$background_y = 3.9;
		$avatar_x = 0.22;
		$avatar_y = 5;
		$text_x = 0.96;
		$text_y = 5.2;
		$line1_x = 0.22;
		$line1_y = 5.8;
		$infotext_x = 0.35;
		$infotext_y = 5.9;
		$line2_x = 0.22;
		$line2_y = 6.66;
		$years_x = 0.65;
		$years_y = 6.95;
		$typebox_x = 0;
		$typebox_y = 7.3;
	break;
	case 6:
		$background_x = 3;
		$background_y = 3.9;
		$avatar_x = 3.22;
		$avatar_y = 5;
		$text_x = 3.96;
		$text_y = 5.2;
		$line1_x = 3.22;
		$line1_y = 5.8;
		$infotext_x = 3.35;
		$infotext_y = 5.9;
		$line2_x = 3.22;
		$line2_y = 6.66;
		$years_x = 3.65;
		$years_y = 6.95;
		$typebox_x = 3;
		$typebox_y = 7.3;
	break;
	case 7:
		$background_x = 6;
		$background_y = 3.9;
		$avatar_x = 6.22;
		$avatar_y = 5;
		$text_x = 6.96;
		$text_y = 5.2;
		$line1_x = 6.22;
		$line1_y = 5.8;
		$infotext_x = 6.35;
		$infotext_y = 5.9;
		$line2_x = 6.22;
		$line2_y = 6.66;
		$years_x = 6.65;
		$years_y = 6.95;
		$typebox_x = 6;
		$typebox_y = 7.3;
	break;
	case 8:
		$background_x = 9;
		$background_y = 3.9;
		$avatar_x = 9.22;
		$avatar_y = 5;
		$text_x = 9.96;
		$text_y = 5.2;
		$line1_x = 9.22;
		$line1_y = 5.8;
		$infotext_x = 9.35;
		$infotext_y = 5.9;
		$line2_x = 9.22;
		$line2_y = 6.66;
		$years_x = 9.65;
		$years_y = 6.95;
		$typebox_x = 9;
		$typebox_y = 7.3;
	break;
}
?>