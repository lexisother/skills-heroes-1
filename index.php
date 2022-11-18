<?php
require __DIR__ . "/vendor/autoload.php";

use Bramus\Router\Router;
use Dotenv\Dotenv;
use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Events\Dispatcher;
use Whoops\Handler\JsonResponseHandler;
use Whoops\Run;

// Make sure all data is sent as JSON.
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: *");
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    return 0;
}

// Load the config file.
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Set up the error handler
$whoops = new Run();
$whoops->pushHandler(new JsonResponseHandler());
$whoops->register();

#region Illuminate setup {{{
// Create our database capsule.
$capsule = new Capsule();
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => $_ENV['DB_HOST'] ?? 'localhost',
    'database' => $_ENV['DB_NAME'] ?? 'skills-heroes',
    'username' => $_ENV['DB_USER'] ?? 'root',
    'password' => $_ENV['DB_PASS'] ?? 'root',
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
]);
$capsule->setEventDispatcher(new Dispatcher(new Container()));
$capsule->setAsGlobal();
$Schema = $capsule->schema();

// Table creations {{{
if (!$Schema->hasTable('docenten')) {
    $Schema->create('docenten', function (Blueprint $table) {
        $table->id();
        $table->string('first_name', 50);
        $table->string('last_name', 50);
        $table->integer('age');
        $table->string('class', 50);
        $table->string('email', 100)->unique();
        $table->string('phone', 10)->unique();
        $table->string('work_days', 50);
        $table->timestamps();
    });
}
// }}}

$capsule->bootEloquent();
#endregion }}}

// App container, used for registering services or container bindings.
$container = app();

// Initialize our router and use the controller classes from the \App\Controllers namespace.
$router = new Router();
$router->setNamespace("\App\Controllers");

$router->all("/health", 'HealthController@getHealth');

$router->get("/teachers", 'TeacherController@fetchTeacher');
$router->post("/teachers", 'TeacherController@createTeacher');
$router->patch("/teachers/(\d+)", 'TeacherController@editTeacher');
$router->delete("/teachers/(\d+)", 'TeacherController@deleteTeacher');

// Kick off the router and start responding to requests!
$router->run();
