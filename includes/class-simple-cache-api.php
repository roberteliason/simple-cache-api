<?php

Class Simple_Cache_API {

	/**
	 * @var Simple_Cache_Implementation $caching_method
	 */
	private $caching_method;


	public function __construct() {
		$this->negotiate_caching_method();
	}


	/**
	 * Retrieve list of implemented caching methods
	 *
	 * @return array $caching_methods
	 */
	public function get_implemented_caching_methods() {
		$caching_methods = array();

		return $caching_methods;
	}


	/**
	 * @return Simple_Cache_Implementation
	 */
	public function get_caching_method() {
		if ( isset( $this->caching_method ) ) {
			return $this->caching_method;
		}
		return false;
	}


	/**
	 * Test all preferred caching methods and return the first that is available
	 */
	private function negotiate_caching_method() {
		$caching_methods = $this->get_implemented_caching_methods();

		foreach( $caching_methods as $method ) {
			if ( true === $method->is_available ) {
				return $method;
			}
		}

		return false;
	}


	/**
	 * Set the caching method implementation
	 *
	 * @param Simple_Cache_Implementation $caching_method
	 *
	 * @return bool
	 */
	public function set_caching_method( $caching_method ) {
		$implementations = class_implements( $caching_method );
		if ( in_array( 'Simple_Cache_Implementation', $implementations ) ) {
			$this->caching_method = $caching_method;
			return true;
		}
		return false;
	}


	/**
	 * Get cached object from chosen cache implementation
	 * If caching is unavailable, return result of callback
	 *
	 * @param $cache_key
	 * @param $callback
	 * @param $ttl
	 *
	 * @return mixed
	 */
	public function get( $cache_key, $callback, $ttl ) {
		if ( false === $this->get_caching_method() && function_exists( $callback ) ) {
			return call_user_func( $callback );
		}

		return $this->get_caching_method()->get( $cache_key, $callback, $ttl );
	}
}
