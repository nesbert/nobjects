# UPGRADING-1.0.0

## Removed classes

The following classes were deprecated in 0.8.0 and removed in 1.0.0.

 * NObjects\Cipher
 * NObjects\Cache\Memcache
 * NObjects\Cache\Memcache\Cluster
 * NObjects\Cache\Memcache\Data
 * NObjects\Cache\Memcache\Session
 
The following classes were deprecated in 0.8.0 with provided alternatives.

| 0.8.0           | 1.0.0               |
|-----------------|---------------------|
| NObjects\Object | NObjects\Nobject    |
| NObjects\String | NObjects\StringUtil |

## Caching
 
* NObjects\Cache\Apc 

Support has been removed for caching based on the Apcu extension. APC cache storage now requires Apcu.

