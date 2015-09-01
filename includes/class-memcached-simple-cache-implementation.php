<?php

/**
 * Class Memcached_Simple_Cache_Implementation
 */
final class Memcached_Simple_Cache_Implementation implements Simple_Cache_Implementation {

	/**
	 * @var string $host Host IP for memcached.
	 */
	private $host = '127.0.0.1';

	/**
	 * @var string $port Port to connect to memcached.
	 */
	private $port = '11211';

	/**
	 * @var object Memcache object instance.
	 */
	private $memcached = null;


	/**
	 * Allow overriding of the default host and ports.
	 */
	public function __constructor() {
		if ( defined( 'SCAPI_MEMCACHED_HOST' ) ) {
			$this->host = SCAPI_MEMCACHED_HOST;
		}
		if ( defined( 'SCAPI_MEMCACHED_PORT' ) ) {
			$this->port = SCAPI_MEMCACHED_PORT;
		}
	}


	/**
	 * Get the memache server instance.
	 */
	private function get_memcache_server() {
		if ( null === $this->memcached ) {
			$this->memcached = new Memcache;
			$this->memcached->connect( $this->host, $this->port );
		}

		return $this->memcached;
	}


	/**
	 * Return uniquely identifiable name of implementation, eg 'Memcached'
	 *
	 * @return string
	 */
	public function get_name() {
		return 'MEMCACHED';
	}


	/**
	 * Test if memcached class exists and if it's possible
	 * to connect to the server.
	 *
	 * @return boolean
	 */
	public function is_available() {
		if ( ! class_exists( 'Memcache' ) ) {
			return false;
		}

		try {
			$memcached = new Memcache;
			$memcached->connect( $this->host, $this->port );

		} catch( Exception $e ) {
			error_log( $e->getMessage() ); // Maybe log the error?
			return false;
		}

		return true;
	}


	/**
	 * Test if caching method supports TTL
	 *
	 * @return mixed
	 */
	public function supports_ttl() {
		return true;
	}


	/**
	 * Fetch the cached object
	 * If the object is not cached, use callback to retrieve object and put it to cache
	 *
	 * @param $cache_key
	 * @param $callback
	 * @param int $ttl
	 *
	 * @return mixed      object if found, false if not
	 */
	public function get( $cache_key, $callback, $ttl = 0 ) {
		if ( ! $this->is_available() ) {
			return false;
		}

		$object = $this->get_memcache_server()->get( $cache_key );

		if ( false === $object ) {
			if ( function_exists( $callback ) ) {
				$object = call_user_func( $callback );
				if ( false !== $object ) {
					$result = $this->set( $cache_key, $object, $ttl );  // what happens to $result?
				}
			}
		}

		return $object;
	}


	/**
	 * Put object to cache using callback, if ttl is supported set that as well
	 *
	 * @param $cache_key
	 * @param $object
	 * @param int $ttl
	 *
	 * @return bool
	 */
	public function set( $cache_key, $object, $ttl = 0 ) {
		if ( ! $this->is_available() ) {
			return false;
		}

		$compression = 0;
		$compression = apply_filters( 'scapi_memcache_compression', $compression );

		return $this->get_memcache_server()->set( $cache_key, $object, $compression, $ttl );
	}


	/**
	 * Delete cached object
	 *
	 * @param $cache_key
	 *
	 * @return bool
	 */
	public function flush( $cache_key ) {
		if ( ! $this->is_available() ) {
			return false;
		}

		return $this->get_memcache_server()->flush( $cache_key );
	}
}
