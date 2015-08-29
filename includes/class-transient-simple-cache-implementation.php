<?php

/**
 * Class Transient_Simple_Cache_Implementation
 */
Class Transient_Simple_Cache_Implementation implements Simple_Cache_Implementation {

	/**
	 * Return uniquely identifiable name of implementation, eg 'TRANSIENT'
	 *
	 * @return string
	 */
	public function get_name() {
		return 'TRANSIENT';
	}


	/**
	 * Test if caching method is available for use
	 *
	 * @return boolean
	 */
	public function is_available() {
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
		$cached_object = get_transient( $cache_key );

		if ( false === $cached_object ) {
			$object = call_user_func( $callback );
			if ( false !== $object ) {
				$this->set( $cache_key, $object, $ttl );
			}
			return $object;
		}

		return $cached_object;
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
		return set_transient( $cache_key, $object, $ttl );
	}


	/**
	 * Delete cached object
	 *
	 * @param $cache_key
	 *
	 * @return bool
	 */
	public function flush( $cache_key ) {
		return delete_transient( $cache_key );
	}
}