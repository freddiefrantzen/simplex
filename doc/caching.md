Caching
=======

Set `ENABLE_CACHE=1` to enable caching. By default, the following components will be cached:
 
* The serializer
* The validator
* Route annotations
* ORM annotations and proxies

To enable container compilation set `COMPILE_CONTAINER=1`.

It's recommended to disable caching while developing.

The default cache directory is `/cache`. If you want to specify a different directory define a constant 
`CACHE_DIRECTORY` with the value of the desired cache directory path. If you do not define `CACHE_DIRECTORY` it will be 
defined automatically and can be used in dependency injection definitions to reference the cache directory.
