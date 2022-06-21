<?php

/**
 * Vendimia bootstrap script.
 */

define ('Vendimia\\PROJECT_PATH', __DIR__);

// Project environment
define ('Vendimia\\ENVIRONMENT',
    $_ENV['VENDIMIA_ENVIRONMENT'] ?? 'development');

// Base URL path
define ('Vendimia\\WEB_ROOT',
    $_ENV['VENDIMIA_WEB_ROOT'] ?? '/');

define ('Vendimia\\PUBLIC_PATH',
    $_ENV['VENDIMIA_PUBLIC_PATH'] ?? __DIR__ . '/public');

define ('Vendimia\\PUBLIC_URL',
    $_ENV['VENDIMIA_PUBLIC_URL'] ?? Vendimia\WEB_ROOT . '/public');

// Composer class autoloader
$autoloader = require __DIR__ . '/vendor/autoload.php';

// '.env' file loader for sensitive parameters
Dotenv\Dotenv::createImmutable(__DIR__)->safeLoad();

// Object repository
$object = new Vendimia\ObjectManager\ObjectManager;

// Config class for interact the configuration
$config = $object->build(Vendimia\Core\Config::class);
$config->addFile(Vendimia\PROJECT_PATH . '/config/default.php');

// Create a constant with the debug value
define ('Vendimia\\DEBUG', $config->get("debug", false));

// Save project info in the object repository
$project_info = $object->build(Vendimia\Core\ProjectInfo::class);

// By default, CLI exception handler are used, in case there is an error
// at initializing phase.
set_exception_handler([Vendimia\Core\ExceptionHandler\Cli::class, 'handle']);

// Treat all error as exceptions
set_error_handler(function($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

// Build and save the Vendimia resource locator class
$resource_locator = $object->save(
    $object->new(Vendimia\Core\ResourceLocator::class),
    Vendimia\Interface\Path\ResourceLocatorInterface::class,
);

// Session manager
$object->bind(
    Vendimia\Session\Driver\DriverInterface::class,
    $config->session['driver']
);
$session = $object->build(Vendimia\Session\SessionManager::class);

// CSRF token
$object->build(Vendimia\Core\Csrf::class);

// Database
if ($config->database) {
    Vendimia\Database\Setup::init(
        $connector = new $config->database['connector'](
            ...$config->database['options']
        ),
    );

    // Save the connector in the object repository
    $object->save(
        $connector, Vendimia\Database\Driver\ConnectorInterface::class
    );
}

// Error and exception handlers
set_exception_handler([Vendimia\Core\ExceptionHandler\Web::class, 'handle']);
set_error_handler(function($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});


// ===========================================================================//
//                       VENDIMIA INITIALIZATION COMPLETE                     //
// ===========================================================================//

if (PHP_SAPI == 'cli') {
    return;
}

// If the request ACCEPTs a application/json, o content-type is application/json,
// use the Json exception handler
if (str_contains(strtolower($request->getHeaderLine('accept')), 'application/json') ||
    $request->getHeaderLine('content-type') == 'application/json') {
    set_exception_handler([Vendimia\Core\ExceptionHandler\Json::class, 'handle']);
} else {
    set_exception_handler([Vendimia\Core\ExceptionHandler\Web::class, 'handle']);
}


// Create and save the HTTP request in the object repository
$request = $object->save(Vendimia\Http\Request::fromPHP());

if (PHP_SAPI == 'cli-server') {
    $path = trim($request->getUri()->getPath(), ' /');

    // Si la ruta pública está dentro de Vendimia\WEB_ROOT, y es la solicitada,
    // retornamos false para que sea procesado directo por cli-server
    if (str_starts_with(Vendimia\PUBLIC_URL, Vendimia\WEB_ROOT)) {
        $public_url_path = trim(substr(Vendimia\PUBLIC_URL, strlen(Vendimia\WEB_ROOT)), '/');

        if (str_starts_with($path, $public_url_path)) {
            return false;
        }
    }
}


$middleware_queue = $object->new(
    Vendimia\Middleware\QueueHandler::class
);
$middleware_queue->setQueue($config->middleware);
$response = $middleware_queue->handle($request);

$response->send();