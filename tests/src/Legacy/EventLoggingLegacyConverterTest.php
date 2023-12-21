<?php

require_once __DIR__ . '/../../../src/Legacy/EventLoggingLegacyConverter.php';

use Wikimedia\MWConfig\Legacy\EventLoggingLegacyConverter;

/**
 * @covers \Wikimedia\MWConfig\Legacy\EventLoggingLegacyConverter
 */
class EventLoggingLegacyConverterTest extends PHPUnit\Framework\TestCase {

	private const RECV_FROM = 'host123.domain.net';
	private const USER_AGENT = 'test user agent';

	public function testFromHttpRequest() {
		$expected = [
			'recvFrom' => self::RECV_FROM,
			'meta' => [
				'stream' => 'eventlogging_MediaWikiPingback'
			],
			'$schema' => '/analytics/legacy/mediawikipingback/1.0.0',
			'http' => [
				'request_headers' => [
					'user-agent' => self::USER_AGENT
				]
			],
			'schema' => 'MediaWikiPingback',
			'revision' => 15781718,
			'wiki' => 'dummy',
			'event' => [
				'database' => 'mysql',
				'MediaWiki' => '1.31.1',
				'PHP' => '7.4.33',
				'OS' => 'Linux 4.4.400-icpu-097',
				'arch' => 64,
				'machine' => 'x86_64',
				'serverSoftware' => 'Apache',
			],
		];

		$_server = [
			'QUERY_STRING' => '?%7B%22schema%22%3A%22MediaWikiPingback%22%2C%22revision%22%3A15781718%2C%22wiki%22%3A%22dummy%22%2C%22event%22%3A%7B%22database%22%3A%22mysql%22%2C%22MediaWiki%22%3A%221.31.1%22%2C%22PHP%22%3A%227.4.33%22%2C%22OS%22%3A%22Linux%5Cu00204.4.400-icpu-097%22%2C%22arch%22%3A64%2C%22machine%22%3A%22x86_64%22%2C%22serverSoftware%22%3A%22Apache%22%7D%7D;',
			'REMOTE_HOST' => self::RECV_FROM,
			'HTTP_USER_AGENT' => self::USER_AGENT
		];

		$convertedEvent = EventLoggingLegacyConverter::fromHttpRequest( $_server );

		// client_dt (legacy) and dt should be the same.
		$this->assertEquals( $convertedEvent['dt'], $convertedEvent['client_dt'] );

		// Assert only that non-deterministic values are set, and then unset them.
		$this->assertIsString( $convertedEvent['uuid'] );
		$this->assertIsString( $convertedEvent['dt'] );
		$this->assertIsString( $convertedEvent['client_dt'] );
		unset( $convertedEvent['uuid'] );
		unset( $convertedEvent['dt'] );
		unset( $convertedEvent['client_dt'] );

		$this->assertEquals(
			$expected,
			$convertedEvent
		);
	}

	public function testFromHttpRequestEmptyQuery() {
		$_server = [
			'QUERY_STRING' => '',
			'REMOTE_HOST' => self::RECV_FROM,
			'HTTP_USER_AGENT' => self::USER_AGENT
		];

		$this->expectException( JsonException::class );

		EventLoggingLegacyConverter::fromHttpRequest( $_server );
	}

	public function testFromHttpRequestTruncatedData() {
		$_server = [
			'QUERY_STRING' => '?%7B%22schema%22%3A%22MediaWikiPingback',
			'REMOTE_HOST' => self::RECV_FROM,
			'HTTP_USER_AGENT' => self::USER_AGENT
		];

		$this->expectException( JsonException::class );
		EventLoggingLegacyConverter::fromHttpRequest( $_server );
	}

	public function testFromHttpRequestBadEncoding() {
		$_server = [
			'QUERY_STRING' => '?%%22schema%22%3A%22MediaWikiPingback%22%2C%22revision%22%3A15781718%2C%22wiki%22%3A%22dummy%22%2C%22event%22%3A%7B%22database%22%3A%22mysql%22%2C%22MediaWiki%22%3A%221.31.1%22%2C%22PHP%22%3A%227.4.33%22%2C%22OS%22%3A%22Linux%5Cu00204.4.400-icpu-097%22%2C%22arch%22%3A64%2C%22machine%22%3A%22x86_64%22%2C%22serverSoftware%22%3A%22Apache%22%7D%7D;',
			'REMOTE_HOST' => self::RECV_FROM,
			'HTTP_USER_AGENT' => self::USER_AGENT
		];

		$this->expectException( JsonException::class );
		EventLoggingLegacyConverter::fromHttpRequest( $_server );
	}

	/**
	 * Should convert legacy EventLogging event to event platform event
	 */
	public function testConvertEvent() {
		$input = [
			'meta' => [
				'stream' => 'eventlogging_MediaWikiPingback'
			],
			'schema' => 'MediaWikiPingback',
			'revision' => 15781718,
			'wiki' => 'dummy',
			'event' => [
				'database' => 'mysql',
				'MediaWiki' => '1.31.1',
				'PHP' => '7.4.33',
				'OS' => 'Linux 4.4.400-icpu-097',
				'arch' => 64,
				'machine' => 'x86_64',
				'serverSoftware' => 'Apache',
			],
		];

		$dtString = "2023-12-27T12:00:03.003";
		$recvFrom = 'host123.domain.net';
		$userAgent = 'test user agent';
		$expected = $input;
		$expected['$schema'] = '/analytics/legacy/mediawikipingback/1.0.0';
		$expected['dt'] = $dtString . 'Z';
		$expected['client_dt'] = $expected['dt'];
		$expected['recvFrom'] = $recvFrom;
		$expected['http'] = [
			'request_headers' => [
				'user-agent' => $userAgent
			]
		];

		$actual = EventLoggingLegacyConverter::convertEvent(
			$input,
			new DateTime( $dtString ),
			$recvFrom,
			$userAgent
		);
		// uuid is random, so just assert that it is a string.
		$this->assertIsString( $actual['uuid'], 'uuid' );
		unset( $actual['uuid'] );
		$this->assertEquals( $expected, $actual, 'converted event' );
	}

	/**
	 * Should convert DateTime to ISO-8601 string with zulu TZ
	 */
	public function testDateTimeString() {
		$dtString = "2023-12-27T12:00:03.003";
		$input = new DateTime( $dtString );
		$expected = $dtString . 'Z';
		$actual = EventLoggingLegacyConverter::dateTimeString( $input );
		$this->assertEquals( $expected, $actual, 'date time string' );
	}

	/**
	 * Should get legacy eventlogging stream name from schema name
	 */
	public function testGetStreamName() {
		$input = 'Test';
		$expected = 'eventlogging_Test';
		$actual = EventLoggingLegacyConverter::getStreamName( $input );
		$this->assertEquals( $expected, $actual, 'stream name' );
	}

	/**
	 * Should get legacy eventlogging schema URI from schema name
	 */
	public function testGetSchemaUri() {
		$input = 'Test';
		$expected = '/analytics/legacy/test/1.2.0';
		$actual = EventLoggingLegacyConverter::getSchemaUri( $input );
		$this->assertEquals( $expected, $actual, 'schema uri' );
	}

	public function testGetSchemaUriUnsupportedSchemaName() {
		$this->expectException( UnexpectedValueException::class );
		$input = 'NotSupportedSchemaNameTest';
		EventLoggingLegacyConverter::getSchemaUri( $input );
	}

	/**
	 * Should decode a JSON url encoded "qson" string to a PHP assoc array
	 */
	public function testDecodeQson() {
		$input = '?%7B%22schema%22%3A%22MediaWikiPingback%22%2C%22revision%22%3A15781718%2C%22wiki%22%3A%22dummy%22%2C%22event%22%3A%7B%22database%22%3A%22mysql%22%2C%22MediaWiki%22%3A%221.31.1%22%2C%22PHP%22%3A%227.4.33%22%2C%22OS%22%3A%22Linux%5Cu00204.4.400-icpu-097%22%2C%22arch%22%3A64%2C%22machine%22%3A%22x86_64%22%2C%22serverSoftware%22%3A%22Apache%22%7D%7D;';
		$expected = [
			'schema' => 'MediaWikiPingback',
			'revision' => 15781718,
			'wiki' => 'dummy',
			'event' => [
				'database' => 'mysql',
				'MediaWiki' => '1.31.1',
				'PHP' => '7.4.33',
				'OS' => 'Linux 4.4.400-icpu-097',
				'arch' => 64,
				'machine' => 'x86_64',
				'serverSoftware' => 'Apache',
			],
		];
		$actual = EventLoggingLegacyConverter::decodeQson( $input );
		$this->assertEquals( $expected, $actual, 'urldecoded and parsed json' );
	}

}
