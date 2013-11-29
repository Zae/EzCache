<?php

class ezCache_Disk extends ezCache {

	protected $_cachefile;
	protected $_memory;
	protected $_stats = array('hit' => 0, 'miss' => 0);
	protected $_dirty = false;

	protected static $_default_config = array(
		'cachefile' => '_cache.dat',
		'cachefolder' => WP_CONTENT_DIR
	);

	public function dump() {
		echo '<pre>';
		print_r($this->_stats);
		echo '</pre>';

		echo '<pre>';
		print_r($this->_non_persistent_groups);
		echo '</pre>';

		$this->_memory->dump();
	}

	public function __construct($config) {
		$this->_config = array_merge(self::$_default_config, $config);
		$this->_cachefile = $this->_config['cachefolder'].DIRECTORY_SEPARATOR.$this->_config['cachefile'];

		$this->init();
	}

	public function __destruct() {
		$this->close();
	}

	public function init() {
		$cache = @file_get_contents($this->_cachefile);
		$this->_memory = @unserialize($cache);

		$dirty = FALSE;
		if (!$this->_memory) {
			$dirty = TRUE;
			$this->_memory = new ezCache_Memory();
		}
		return !!($this->_memory);
	}

	public function close() {
		if ($this->_dirty) {
			$cache = serialize($this->_memory);
			return file_put_contents($this->_cachefile, $cache, LOCK_EX);
		} else {
			return true;
		}
	}

	public function delete($key, $group = 'default') {
		$this->_dirty = TRUE;
		return $this->_memory->delete($key, $group);
	}

	public function exists($key, $group = 'default') {
		return $this->_memory->exists($key, $group);
	}

	public function flush() {
		$this->_dirty = TRUE;
		return $this->_memory->flush();
	}

	public function get($key, $group = 'default', $force = false, &$found = null) {
		return  $this->_memory->get($key, $group, $force, $found);
	}

	public function set($key, $data, $group = 'default', $expire = 0) {
		$this->_dirty = TRUE;
		return $this->_memory->set($key, $data, $group, $expire);
	}

	public function add($key, $data, $group = 'default', $expire = 0) {
		return $this->set($key, $data, $group, $expire);
	}

	public function replace($key, $data, $group = 'default', $expire = 0) {
		return $this->set($key, $data, $group, $expire);
	}

	public function incr($key, $offset = 1, $group = 'default') {
		return false;
	}

	public function decr($key, $offset = 1, $group = 'default') {
		return false;
	}

	public function reset() {
		$this->_dirty = TRUE;
		return $this->_memory->reset();
	}

	public function add_global_groups($groups) {
		return (parent::add_global_groups($groups) && $this->_memory->add_global_groups($groups));
	}

}
