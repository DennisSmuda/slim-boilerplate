<?php
if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $file = __DIR__ . $_SERVER['REQUEST_URI'];
    if (is_file($file)) {
        return false;
    }
}

require __DIR__ . '/../vendor/autoload.php';

session_start();

// Instantiate the app
$settings = require __DIR__ . '/../src/settings.php';
$app = new \Slim\App($settings);

// Set up dependencies
require __DIR__ . '/../src/dependencies.php';

// Register middleware
require __DIR__ . '/../src/middleware.php';

// Twig Container for Application
$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig('./templates/', [
        'cache' => false
    ]);
    $view->addExtension(new \Slim\Views\TwigExtension(
        $container['router'],
        $container['request']->getUri()
    ));

    return $view;
};


$app->get('/', function($request, $response, $args) {
  return $this->view->render($response, 'index.twig');
});

$app->get('/somepage', function($request, $response, $args) {
  return $this->view->render($response, 'somepage.twig');
});




// Run app
$app->run();
