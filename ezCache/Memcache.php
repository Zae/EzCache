<?php

class ezCache_Memcache extends ezCache {

	protected $_memcache;
	protected $_memory;
	protected $_stats = array('hit' => 0, 'miss' => 0);

	public function dump() {
		echo '<pre>';
		print_r($this->_stats);
		echo '</pre>';

		$this->_memory->dump();
	}

	public function __construct($config) {
		$this->_config = $config;

		$this->init();
	}

	public function __destruct() {
		$this->close();
	}

	public function init() {
		$this->_memcache = new Memcache();
		$this->_memcache->connect('localhost', 11211);

		$this->_memory = new ezCache_Memory();

		return !!($this->_memcache && $this->_memory);
	}

	public function close() {
		$this->_memory->close();
		return $this->_memcache->close();
	}

	public function delete($key, $group = 'default') {
		global $blog_id;

		$group = self::_sanitizeGroup($group);
		$b_id = $this->_sanitizeBlogId($blog_id, $group);

		$this->_memory->delete($key, $group);
		return $this->_memcache->delete($this->key($b_id, $key, $group));
	}

	public function exists($key, $group) {
		global $blog_id;

		$group = self::_sanitizeGroup($group);
		$b_id = $this->_sanitizeBlogId($blog_id, $group);

		return ($this->_memory->exists($key, $group) || $this->_memcache->get($this->key($b_id, $key, $group)) !== FALSE);
	}

	public function flush() {
		$this->_memory->flush();
		return $this->_memcache->flush();
	}

	public function get($key, $group = 'default', $force = false, &$found = null) {
		global $blog_id;

		$group = self::_sanitizeGroup($group);

		$data = $this->_memory->get($key, $group, $force);

		if ($data === FALSE) {
			$b_id = $this->_sanitizeBlogId($blog_id, $group);

			$data = $this->_memcache->get($this->key($b_id, $key, $group));
			$this->_memory->set($key, $data, $group, 0);

			if ($data !== FALSE) {
				$this->_stats['hit'] ++;
			} else {
				$this->_stats['miss'] ++;
			}
		}

		$found = $data !== FALSE;
		return $data;
	}

	public function set($key, $data, $group = 'default', $expire = 0) {
		global $blog_id;

		if ($expire === 0) {
			$expire = 86400;
		}

		$group = self::_sanitizeGroup($group);
		$b_id = $this->_sanitizeBlogId($blog_id, $group);

		$m = $this->_memory->set($key, $data, $group, $expire);
		if (!in_array($group, $this->_non_persistent_groups, TRUE)) {
			return $this->_memcache->set($this->key($b_id, $key, $group), $data, NULL, $expire);
		}
		return $m;
	}

	public function add($key, $data, $group = 'default', $expire = 0) {
		global $blog_id;

		if ($expire === 0) {
			$expire = 86400;
		}

		$group = self::_sanitizeGroup($group);
		$b_id = $this->_sanitizeBlogId($blog_id, $group);

		$m = $this->_memory->add($key, $data, $group, $expire);
		if (!in_array($group, $this->_non_persistent_groups, TRUE)) {
			return $this->_memcache->add($this->key($b_id, $key, $group), $data, NULL, $expire);
		}
		return $m;
	}

	public function replace($key, $data, $group = 'default', $expire = 0) {
		global $blog_id;

		if ($expire === 0) {
			$expire = 86400;
		}

		$group = self::_sanitizeGroup($group);
		$b_id = $this->_sanitizeBlogId($blog_id, $group);

		$m = $this->_memory->replace($key, $data, $group, $expire);
		if (!in_array($group, $this->_non_persistent_groups, TRUE)) {
			return $this->_memcache->replace($this->key($b_id, $key, $group), $data, NULL, $expire);
		}
		return $m;
	}

	public function incr($key, $offset = 1, $group = 'default') {
		global $blog_id;

		$group = self::_sanitizeGroup($group);
		$b_id = $this->_sanitizeBlogId($blog_id, $group);

		return $this->_memcache->increment($this->key($b_id, $key, $group), $offset);
	}

	public function decr($key, $offset = 1, $group = 'default') {
		global $blog_id;

		$group = self::_sanitizeGroup($group);
		$b_id = $this->_sanitizeBlogId($blog_id, $group);

		return $this->_memcache->decrement($this->key($b_id, $key, $group), $offset);
	}

	public function reset() {
		return $this->_memory->reset();
	}

	public function add_global_groups($groups) {
		return (parent::add_global_groups($groups) && $this->_memory->add_global_groups($groups));
	}

	protected function key($blog_id, $key, $group = 'default') {
		return sprintf('%s:%s:%s', $blog_id, $group, $key);
	}

}
