# PHP Easy Cache
An easy way to cache 3rd party API calls with PHP.

## Install - Composer
```json
{
    "require": {
        "trafficinc/php-easycache": "~1.0"
    }
}
```
Then include composer autoloader
```php
require 'vendor/autoload.php';
$cache = new Trafficinc\Util\EasyCache();
```

## Usage
```php
$cache = new TRafficinc\Util\EasyCache();
$cache->cache_path = 'cache/';
$cache->cache_time = 3600;

if($data = $cache->get_cache('label')){
	$data = json_decode($data);
} else {
	$data = $cache->do_request('https://jsonplaceholder.typicode.com/posts/1');
	$cache->set_cache('label', $data);
	$data = json_decode($data);
}

print_r($data);
```
## Clear the Cache
```php
// clear single cache file by id
$cache->clear('label');
// clear whole cache
$cache->clear();
```
