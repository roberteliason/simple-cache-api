<?php

/**
 * Class Simple_Cache_API
 *
 * @todo Convert to singleton
 */
Class Simple_Cache_API {

	/**
	 * @var Simple_Cache_Implementation $caching_method
	 */
	private $caching_method;


	public function __construct() {
		if ( defined( 'CACHE_API_METHOD' ) ) {
			$implementations = $this->get_implemented_caching_methods();
			if ( true === array_key_exists( CACHE_API_METHOD, $implementations ) ) {
				$this->set_caching_method( $implementations[ CACHE_API_METHOD ] );
				return;
			}
		}

		$this->negotiate_caching_method();
	}


	/**
	 * Retrieve list of implemented caching methods
	 *
	 * @return array $caching_methods
	 */
	public function get_implemented_caching_methods() {
		$caching_methods = array();

		foreach( get_declared_classes() as $class_name ) {
			if ( in_array( 'Simple_Cache_Implementation', class_implements( $class_name ) ) ) {
				$class = new $class_name;
				$caching_methods[ $class->get_name() ] = $class;
			}
		}

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
			if ( true === $method->is_available() ) {
				$this->set_caching_method( $method );
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
		$caching_method = $this->get_caching_method();

		if ( false === $caching_method || ( false !== $caching_method && ! $caching_method->is_available() ) ) {
			if ( function_exists( $callback ) ) {
				return call_user_func( $callback );
			}

		} else {
			$result = $this->get_caching_method()->get( $cache_key, $callback, $ttl );
			if ( false === $result && function_exists( $callback ) ) {
				return call_user_func( $callback );
			} else {
				return $result;
			}

		}

		return false;
	}


	/**
	 * Flush the cache for an object.
	 *
	 * @param $cache_key
	 *
	 * @return mixed
	 */
	public function flush( $cache_key ) {
		return $this->get_caching_method()->flush( $cache_key );
	}

}
