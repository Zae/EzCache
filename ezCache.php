<?php
/*  Copyright 2013  Ezra Pool  (email : ezra@tsdme.nl)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * Plugin Name: ezCache
 * Plugin URI: 
 * Description: Object Caching for Wordpress
 * Version: 0.0.1
 * Author: Ezra Pool <ezra@tsdme.nl>
 * Author URI: tsdme.nl
 * License: GPL 2
 */

class ezCacheLoader {
	public static function autoload($className) {
		$className = ltrim($className, '\\');
		$fileName = '';
		$namespace = '';

		if ($lastNsPos = strripos($className, '\\')) {
			$namespace = substr($className, 0, $lastNsPos);
			$className = substr($className, $lastNsPos + 1);
			$fileName = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
		}

		$fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
		$fileName = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . $fileName);

		if (file_exists($fileName)) {
			require $fileName;
		}
	}
}

set_include_path(__DIR__);
spl_autoload_register(array('ezCacheLoader', 'autoload'), false);

abstract class ezCache implements iezCache {
	protected $_config;
}
