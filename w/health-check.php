<?php
echo "PHP interpreter running\n";

# Work out the number of physical processors

if ( !file_exists ( '/proc/cpuinfo' ) ) {
	echo "Can't get CPU information, as procfs isn't mounted or /proc/cpuinfo isn't available on this system.\n";
	exit;
}

$cpuinfo = file_get_contents( '/proc/cpuinfo' );

# Parse cpuinfo
$processors = explode( "\n\n", $cpuinfo );
$ids = array();
foreach ( $processors as $i => $processor ) {
	if ( trim( $processor ) == '' ) {
		continue;
	}
	$lines = explode( "\n", $processor );
	$props = array();
	foreach ( $lines as $line ) {
		list( $name, $value ) = array_map( 'trim', explode( ':', $line, 2 ) );
		$props[$name] = $value;
	}

	if ( isset( $props['physical id'] ) ) {
		$id = $props['physical id'];
	} else {
		$id = $props['processor'];
	}
	if ( isset( $props['core id'] ) ) {
		$id .= ',' . $props['core id'];
	}
	$ids[$id] = true;
}
$count = count( $ids );
echo "CPU count: $count\n";

if ( $count > 10 ) {
	$weight = 10;
} elseif ( $count <= 1 ) {
	$weight = 1;
} else {
	$weight = $count;
}

echo "Request weight: $weight\n";
