<?php

require './vendor/autoload.php';

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Psr7Middlewares\Middleware\TrailingSlash;
use Monolog\Logger;
use Firebase\JWT\JWT;

define(BDPSWD, getenv("BDPSWD"));
define(ENVIR, getenv("ENVIR"));

/**
 * Configurações
 */
$configs = [
    'settings' => [
        'displayErrorDetails' => true,
    ],
    'appsettings' => [
        'prefix' => 'ssc',
        'url' => 'http://images.antsattack.com'
    ]
];

/**
 * Container Resources do Slim.
 * Aqui dentro dele vamos carregar todas as dependências
 * da nossa aplicação que vão ser consumidas durante a execução
 * da nossa API
 */
$container = new \Slim\Container($configs);


/**
 * Converte os Exceptions Genéricas dentro da Aplicação em respostas JSON
 */
$container['errorHandler'] = function ($container) {
    return function ($request, $response, $exception) use ($container) {
        $statusCode = $exception->getCode() ? $exception->getCode() : 500;
        return $container['response']->withStatus($statusCode)
            ->withHeader('Content-Type', 'Application/json')
            ->withJson(["message" => $exception->getMessage()], $statusCode);
    };
};

/**
 * Converte os Exceptions de Erros 405 - Not Allowed
 */
$container['notAllowedHandler'] = function ($container) {
    return function ($request, $response, $methods) use ($container) {
        return $container['response']
            ->withStatus(405)
            ->withHeader('Allow', implode(', ', $methods))
            ->withHeader('Content-Type', 'Application/json')
            ->withHeader("Access-Control-Allow-Methods", implode(",", $methods))
            ->withJson(["message" => "Method not Allowed; Method must be one of: " . implode(', ', $methods)], 405);
    };
};

/**
 * Converte os Exceptions de Erros 404 - Not Found
 */
$container['notFoundHandler'] = function ($container) {
    return function ($request, $response) use ($container) {
        return $container['response']
            ->withStatus(404)
            ->withHeader('Content-Type', 'Application/json')
            ->withJson(['message' => 'Page not found']);
    };
};

/**
 * Serviço de Logging em Arquivo
 */
$container['logger'] = function($container) {
    $logger = new Monolog\Logger('books-microservice');
    $logfile = __DIR__ . '/log/books-microservice.log';
    $stream = new Monolog\Handler\StreamHandler($logfile, Monolog\Logger::DEBUG);
    $fingersCrossed = new Monolog\Handler\FingersCrossedHandler(
        $stream, Monolog\Logger::INFO);
    $logger->pushHandler($fingersCrossed);
    
    return $logger;
};

$isDevMode = true;

/**
 * Diretório de Entidades e Metadata do Doctrine
 */
$config = Setup::createAnnotationMetadataConfiguration(array(__DIR__."/src/Models/Entity"), $isDevMode);

/**
 * Array de configurações da nossa conexão com o banco
 */
/*
$conn = array(
    'driver' => 'pdo_sqlite',
    'path' => __DIR__ . '/db.sqlite',
);
*/

$cnf['local']['user'] = 'root';
$cnf['local']['pwd'] = 'root';
$cnf['local']['host'] = '127.0.0.1';
$cnf['local']['port'] = '8889';

$cnf['aws']['user'] = 'vitrinepro';
$cnf['aws']['pwd'] = BDPSWD;
$cnf['aws']['host'] = 'vitrinepro.cyur6u9cx6vc.sa-east-1.rds.amazonaws.com';
$cnf['aws']['port'] = '3306';

$env = ENVIR;
$env = "aws";

$conn = array(
    'dbname' => 'vitrinepro',
    'user' => $cnf[$env]['user'],
    'password' => $cnf[$env]['pwd'],
    'host' => $cnf[$env]['host'],
    'port' => $cnf[$env]['port'],
    'driver' => 'pdo_mysql',
    'charset'  => 'utf8',
    'driverOptions' => array(
        1002 => 'SET NAMES utf8'
    )
);


/**
 * Instância do Entity Manager
 */
$entityManager = EntityManager::create($conn, $config);


/**
 * Coloca o Entity manager dentro do container com o nome de em (Entity Manager)
 */
$container['em'] = $entityManager;

/**
 * Token do nosso JWT
 */
$container['secretkey'] = "secretloko";

/**
 * Application Instance
 */
$app = new \Slim\App($container);

$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});


$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization, X-Token')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});

/*
$app->add(new Tuupola\Middleware\CorsMiddleware([
    "origin" => ["*"],
    "methods" => ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'OPTIONS'],
    "headers.allow" => ['X-Requested-With', 'Content-Type', 'Accept', 'Origin', 'Authorization', 'X-Token'],
    "headers.expose" => [],
    "credentials" => true,
    "cache" => 0,
]));
*/
/**
 * @Middleware Tratamento da / do Request 
 * true - Adiciona a / no final da URL
 * false - Remove a / no final da URL
 */
$app->add(new TrailingSlash(false));

/**
 * Auth básica HTTP
 */
//$app->add(new \Slim\Middleware\HttpBasicAuthentication([
    /**
     * Usuários existentes
     */
   // "users" => [
    //    "root" => "toor"
    //],
    /**
     * Blacklist - Deixa todas liberadas e só protege as dentro do array
     */
    //"path" => ["/auth"],

    /**
     * Whitelist - Protege todas as rotas e só libera as de dentro do array
     */
    //"passthrough" => ["/auth/liberada", "/admin/ping"],
//]));


/**
 * Auth básica do JWT
 * Whitelist - Bloqueia tudo, e só libera os
 * itens dentro do "passthrough"
 */
$app->add(new \Slim\Middleware\JwtAuthentication([
    "regexp" => "/(.*)/",
    "header" => "X-Token",
    "path" => "/",
    "relaxed" => ["localhost","ec2-18-231-100-176.sa-east-1.compute.amazonaws.com"],
    "passthrough" => ["/auth", "/v1/auth"],
    "realm" => "Protected",
    "secret" => $container['secretkey']
]));

/**
 * Proxys confiáveis
 */
$trustedProxies = ['0.0.0.0', '127.0.0.1'];
$app->add(new RKA\Middleware\SchemeAndHost($trustedProxies));

