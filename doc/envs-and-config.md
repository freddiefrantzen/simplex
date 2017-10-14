Environments and Configuration
==============================

Simplex relies on environment variables for ad-hoc configuration. If you prefer, you can include a `.env` file in the 
root directory. 

`SIMPLEX_ENV` is used to determine the current runtime environment while `DEBUG_MODE` is used to determine whether or 
not to run in debug mode. Running in debug mode will enable the debug error handler. See [here](caching.md) for 
information on how to enable and disable caching.

You can define your own environments simply by placing a file inside the root configuration directory and naming it
`config_[your-env-name]`. To run in this environment modify the value of `SIMPLEX_ENV`.

Environments all inherit from the base environment (which should be be used for production). Config and service 
definitions will be merged recursively. Settings in the base configuration file override setting in module configuration
files. Settings in environment configuration files will override settings in the base configuration file and in module 
configuration files. 
