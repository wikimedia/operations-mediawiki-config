<?php
/**
 * Reduced from
 * - https://github.com/perftools/xhgui/blob/0.20.5/src/Saver/PdoSaver.php#L28
 * - https://github.com/perftools/xhgui/blob/0.20.5/src/Db/PdoRepository.php#L215
 *
 * Copyright 2013 XHGui by Mark Story & Paul Reinheimer.
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace Wikimedia\MWConfig;

use PDO;

class XhguiSaverPdo {
	public const INSERT_DML = <<<SQL

INSERT INTO %s (
  id,
  profile,
  url,
  SERVER,
  GET,
  ENV,
  simple_url,
  request_ts,
  request_ts_micro,
  request_date,
  main_wt,
  main_ct,
  main_cpu,
  main_mu,
  main_pmu
) VALUES (
  :id,
  :profile,
  :url,
  :SERVER,
  :GET,
  :ENV,
  :simple_url,
  :request_ts,
  :request_ts_micro,
  :request_date,
  :main_wt,
  :main_ct,
  :main_cpu,
  :main_mu,
  :main_pmu
);

SQL;

	/** @var PDO */
	private $pdo;
	/** @var string */
	private $table;

	/**
	 * @param PDO $pdo
	 * @param string $table
	 */
	public function __construct( PDO $pdo, $table ) {
		$this->pdo = $pdo;
		$this->table = $table;
	}

	/**
	 * @param array $data
	 */
	public function save( array $data ): void {
		$main = $data['profile']['main()'];

		$stmt = $this->pdo->prepare( sprintf( self::INSERT_DML, $this->table ) );

		$stmt->execute( [
			'id' => self::generateId(),
			'profile' => json_encode( $data['profile'] ),
			'url' => $data['meta']['url'],
			'SERVER' => json_encode( $data['meta']['SERVER'] ),
			'GET' => json_encode( $data['meta']['get'] ),
			'ENV' => json_encode( $data['meta']['env'] ),
			'simple_url' => $data['meta']['simple_url'],
			'request_ts' => $data['meta']['request_ts_micro']['sec'],
			'request_ts_micro' => "{$data['meta']['request_ts_micro']['sec']}.{$data['meta']['request_ts_micro']['usec']}",
			'request_date' => date( 'Y-m-d', $data['meta']['request_ts_micro']['sec'] ),
			'main_wt' => $main['wt'],
			'main_ct' => $main['ct'],
			'main_cpu' => $main['cpu'],
			'main_mu' => $main['mu'],
			'main_pmu' => $main['pmu'],
		] );
	}

	/**
	 * Return an new ObjectId-like string, where its first 8
	 * characters encode the current unix timestamp and the
	 * next 16 are random.
	 *
	 * @see http://php.net/manual/en/mongodb-bson-objectid.construct.php
	 * @return string
	 */
	private static function generateId(): string {
		return dechex( time() ) . bin2hex( random_bytes( 8 ) );
	}
}
