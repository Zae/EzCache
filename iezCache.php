<?php

interface iezCache {

	public function __construct($config);

	public function init();

	public function close();

	public function add($key, $data, $group = 'default', $expire = 0);

	public function set($key, $data, $group = 'default', $expire = 0);

	public function get($key, $group = 'default', $force = false, &$found = null);

	public function delete($key, $group = 'default');

	public function replace($key, $data, $group = 'default', $expire = 0);

	public function exists($key, $group);

	public function incr($key, $offset = 1, $group = 'default');

	public function decr($key, $offset = 1, $group = 'default');

//	public function switch_to_blog( $blog_id );
	public function add_global_groups($groups);
	public function add_non_persistent_groups($groups);

	public function reset();

	public function flush();
}