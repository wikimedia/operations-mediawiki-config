<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.
$wgJobQueueAggregator = array(
	'class'       => 'JobQueueAggregatorRedis',
	'redisServer' => '10.0.12.1', # mc1
	'redisConfig' => array(
		'connectTimeout' => 1,
		'serializer'     => 'igbinary'
	)
);
# vim: set sts=4 sw=4 et :
