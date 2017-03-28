Environments and Configuration
==============================

Simplex relies on environment variables for ad-hoc configuration. If you prefer, you can include a `.env` file in the 
root directory. 

There are a couple of variables that have special meaning: `FRAMEWORK_ENV` is used to determine the current runtime 
environment while `FRAMEWORK_DEBUG` is used to determine whether or not to run in debug mode. 

You can define your own environments simply by placing a file inside the root configuration directory and naming it
`config_[your-env-name]`.

Environments all inherit from the base environment (which should be be used for production). Note that any keys you 
define in your environments will completely overwrite the base environment (currently, settings will not be merged).
