<?php
/*
Plugin Name: Simple Cache API
Version: 0.1-alpha
Description: Abstraction of caching that makes it agnostic of implementation
Author: Robert Eliason
Author URI: https://twitter.com/eliason_robert
Plugin URI: TBA
Text Domain: simple-cache-api
Domain Path: /languages
*/

include_once( 'includes/class-simple-cache-api.php' );
include_once( 'includes/interface-simple-cache-implementation.php' );
include_once( 'includes/class-transient-simple-cache-implementation.php' );