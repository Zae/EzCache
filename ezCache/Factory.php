<?php

class ezCache_Factory {

	public static function getCache($config = NULL) {

		if (extension_loaded("redis")) {
			return new ezCache_Redis($config);
		}
		if (extension_loaded("memcached")) {
			return new ezCache_Memcached($config);
		}
		if (extension_loaded("memcache")) {
			return new ezCache_Memcache($config);
		}
		if (extension_loaded("apc")) {
			return new ezCache_APC($config);
		}
		if (extension_loaded("wincache")) {
			return new ezCache_Wincache($config);
		}

		return new ezCache_Memory();
	}

	public function __construct() {
		throw new Exception('This is a factory, use ::getCache');
	}

}
