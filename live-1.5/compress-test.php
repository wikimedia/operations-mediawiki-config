<?php
/*
function ob_content_length( $s ) {
	if ( $_SERVER['SERVER_PROTOCOL'] == 'HTTP/1.0' ) {
		header('Content-Length: ' . strlen($s));
	}
	return $s;
}*/

function my_ob_gzhandler( $s ) {
	if ( !headers_sent() ) {
		$tokens = preg_split( '/[,; ]/', $_SERVER['HTTP_ACCEPT_ENCODING'] );
		if ( in_array( 'gzip', $tokens ) ) {
			header( 'Content-Encoding: gzip' );
			$s = gzencode( $s, 3 );

			# Set vary header
			$headers = headers_list();
			$foundVary = false;
			foreach ( $headers as $header ) {
				if ( substr( $header, 0, 5 ) == 'Vary:' ) {
					$foundVary == true;
				}
				
			}
			if ( !$foundVary ) {
				header( 'Vary: Accept-Encoding' );
			}
		}
		if ( $_SERVER['SERVER_PROTOCOL'] == 'HTTP/1.0' ) {
			header('Content-Length: ' . strlen($s));
		}
	}
	return $s;
}

#ob_start('ob_content_length');
#ob_start('ob_gzhandler');
ob_start( 'my_ob_gzhandler' );

print str_repeat('a',10000);
?>
