<?php
namespace Trafficinc\Util;

/*
 * EasyCache v1.0
 *
 * By Ron Bailey
 * http://github.com/trafficinc
 *
 * Free to use and abuse under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 */

class EasyCache {

    // Path to cache folder (with trailing /)
    public $cache_path = 'cache/';

    // Length of time to cache a file (in seconds)
    public $cache_time = 3600;

    // Cache file extension
    public $cache_extension = '.cache';

    // This is just a functionality wrapper function
    public function get_data($label, $url) {
        if ($data = $this->get_cache($label)) {
            return $data;
        } else {
            $data = $this->do_request($url);
            $this->set_cache($label, $data);
            return $data;
        }
    }

    public function set_cache($label, $data) {
        file_put_contents($this->cache_path . $this->safe_filename($label) . $this->cache_extension, $data);
    }

    public function get_cache($label) {
        if ($this->is_cached($label)) {
            $filename = $this->cache_path . $this->safe_filename($label) . $this->cache_extension;
            return file_get_contents($filename);
        }
        return false;
    }

    public function is_cached($label) {
        $filename = $this->cache_path . $this->safe_filename($label) . $this->cache_extension;
        if (file_exists($filename) && (filemtime($filename) + $this->cache_time >= time())) return true;
        return false;
    }
    // Retrieving data from url
    public function do_request($url) {
        if (function_exists("curl_init")) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            $content = curl_exec($ch);
            curl_close($ch);
            return $content;
        } else {
            return file_get_contents($url);
        }
    }
    // Validate filenames
    private function safe_filename($filename) {
        return preg_replace('/[^0-9a-z\.\_\-]/i', '', strtolower($filename));
    }

    public function clear($id = null) {
        if (isset($id)) {
            $currentFilePath = $this->cache_path.$id.''.$this->cache_extension;
            if (file_exists($currentFilePath)) {
                @unlink($currentFilePath);
                return true;
            } else {
                return false;
            }
        } else if ($this->delete_files($this->cache_path, TRUE)) {
            return true;
        } else {
            // Files must be writable or owned by the system in order to be deleted.
            return false;
        }
    }

    private function delete_files($path, $del_dir = FALSE, $htdocs = FALSE, $_level = 0) {
        // Trim the trailing slash
        $path = rtrim($path, '/\\');
        if (!$current_dir = @opendir($path)) {
            return FALSE;
        }
        while (FALSE !== ($filename = @readdir($current_dir))) {
            if ($filename !== '.' && $filename !== '..') {
                $filepath = $path . DIRECTORY_SEPARATOR . $filename;
                if (is_dir($filepath) && $filename[0] !== '.' && !is_link($filepath)) {
                    delete_files($filepath, $del_dir, $htdocs, $_level + 1);
                } elseif ($htdocs !== TRUE OR !preg_match('/^(\.htaccess|index\.(html|htm|php)|web\.config)$/i', $filename)) {
                    @unlink($filepath);
                }
            }
        }
        closedir($current_dir);
        return ($del_dir === TRUE && $_level > 0) ? @rmdir($path) : TRUE;
    }


}
