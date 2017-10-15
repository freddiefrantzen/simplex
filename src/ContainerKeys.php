<?php declare(strict_types=1);

namespace Simplex;

interface ContainerKeys
{
    const ENABLE_CACHE = 'enable_cache';

    const DEBUG_MODE = 'debug_mode';

    const EDITOR = 'editor';

    const MODULES = 'modules';

    const MIDDLEWARE = 'middleware';

    const CONTROLLER_DEPENDENCIES = 'controller_dependencies';

    const ROUTE_COLLECTION = 'route_collection';

    const CONSOLE_COMMANDS = 'console_commands';

    const CONSOLE_HELPER_SET = 'console_helper_set';
}
