<?php

Interface Simple_Cache_Implementation {

	/**
	 * Test if caching method is available for use
	 *
	 * @return boolean
	 */
	public function is_available();


	/**
	 * Test if caching method supports TTL
	 *
	 * @return mixed
	 */
	public function supports_ttl();


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
	public function get( $cache_key, $callback, $ttl = 0 );


	/**
	 * Put object to cache using callback, if ttl is supported set that as well
	 *
	 * @param $cache_key
	 * @param $object
	 * @param int $ttl
	 *
	 * @return bool
	 */
	public function set( $cache_key, $object, $ttl = 0 );


	/**
	 * Delete cached object
	 *
	 * @param $cache_key
	 *
	 * @return bool
	 */
	public function flush( $cache_key );
}