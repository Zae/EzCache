<?php
include ABSPATH.'wp-content/plugins/'.'EzCache'.DIRECTORY_SEPARATOR.'ezCache.php';

if (!class_exists('ezCache_Factory')) {
	require_once ABSPATH.'wp-includes/cache.php';
	trigger_error('class ezCacheFactory not found, plugin files are missing?');
}

if (!function_exists('wp_cache_add')) {

	function wp_cache_add($key, $data, $group = '', $expire = 0) {
		global $wp_object_cache;

		return $wp_object_cache->add($key, $data, $group, $expire);
	}

}

if (!function_exists('wp_cache_close')) {

	function wp_cache_close() {
		global $wp_object_cache;

		return $wp_object_cache->close();
	}

}

if (!function_exists('wp_cache_decr')) {

	function wp_cache_decr($key, $offset = 1, $group = '') {
		global $wp_object_cache;

		return $wp_object_cache->decr($key, $offset, $group);
	}

}

if (!function_exists('wp_cache_delete')) {

	function wp_cache_delete($key, $group = '') {
		global $wp_object_cache;

		return $wp_object_cache->delete($key, $group);
	}

}

if (!function_exists('wp_cache_flush')) {

	function wp_cache_flush() {
		global $wp_object_cache;

		return $wp_object_cache->flush();
	}

}

if (!function_exists('wp_cache_get')) {

	function wp_cache_get($key, $group = '', $force = false, &$found = null) {
		global $wp_object_cache;

		return $wp_object_cache->get($key, $group, $force, $found);
	}

}

if (!function_exists('wp_cache_incr')) {

	function wp_cache_incr($key, $offset = 1, $group = '') {
		global $wp_object_cache;

		return $wp_object_cache->incr($key, $offset, $group);
	}

}

if (!function_exists('wp_cache_init')) {

	function wp_cache_init() {
		$GLOBALS['wp_object_cache'] = ezCache_Factory::getCache();
		var_dump($GLOBALS['wp_object_cache']);
	}

}

if (!function_exists('wp_cache_replace')) {

	function wp_cache_replace($key, $data, $group = '', $expire = 0) {
		global $wp_object_cache;

		return $wp_object_cache->replace($key, $data, $group, $expire);
	}

}

if (!function_exists('wp_cache_set')) {

	function wp_cache_set($key, $data, $group = '', $expire = 0) {
		global $wp_object_cache;

		return $wp_object_cache->set($key, $data, $group, $expire);
	}

}

if (!function_exists('wp_cache_switch_to_blog')) {

	function wp_cache_switch_to_blog($blog_id) {
		return true;
	}

}

if (!function_exists('wp_cache_add_global_groups')) {

	function wp_cache_add_global_groups($groups) {
		return;
	}

}

if (!function_exists('wp_cache_add_non_persistent_groups')) {

	function wp_cache_add_non_persistent_groups($groups) {
		// Default cache doesn't persist so nothing to do here.
		return;
	}

}

if (!function_exists('wp_cache_reset')) {

	function wp_cache_reset() {
		_deprecated_function(__FUNCTION__, '3.5');

		global $wp_object_cache;

		return $wp_object_cache->reset();
	}

}