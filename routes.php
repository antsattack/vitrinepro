<?php

/**
 * Grupo dos enpoints iniciados por v1
 */
$app->group('/v1', function() {

    /**
     * Dentro de v1, o recurso /book
     */
    $this->group('/book', function() {
        $this->get('', '\App\v1\Controllers\BookController:listBook');
        $this->post('', '\App\v1\Controllers\BookController:createBook');
        
        /**
         * Validando se tem um integer no final da URL
         */
        $this->get('/{id:[0-9]+}', '\App\v1\Controllers\BookController:viewBook');
        $this->put('/{id:[0-9]+}', '\App\v1\Controllers\BookController:updateBook');
        $this->delete('/{id:[0-9]+}', '\App\v1\Controllers\BookController:deleteBook');
    });

    /**
     * Dentro de v1, o recurso /auth
     */
    $this->group('/auth', function() {
        $this->get('', \App\v1\Controllers\AuthController::class);
    });
});

// Catch-all route to serve a 404 Not Found page if none of the routes match
// NOTE: make sure this route is defined last
$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function($req, $res) {
    $handler = $this->notFoundHandler; // handle using the default Slim page not found handler
    return $handler($req, $res);
});
