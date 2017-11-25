Caching
=======

Set `ENABLE_CACHE=1` to enable caching. By default, the following components will be cached:
 
* The serializer
* The validator
* Route annotations
* ORM annotations and proxies

To enable container compilation set `COMPILE_CONTAINER=1`.

It's recommended to disable caching while developing.
