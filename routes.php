<?php

/**
 * Grupo dos enpoints iniciados por v1
 */
$app->group('/v1', function() {

    /**
     * Dentro de v1, o recurso /colors
     */
    $this->group('/colors', function() {
        $this->get('', '\App\v1\Controllers\ColorController:listColor');
        $this->post('', '\App\v1\Controllers\ColorController:createColor');
        
        /**
         * Validando se tem um integer no final da URL
         */
        $this->get('/{id:[0-9]+}', '\App\v1\Controllers\ColorController:viewColor');
        $this->put('/{id:[0-9]+}', '\App\v1\Controllers\ColorController:updateColor');
        $this->delete('/{id:[0-9]+}', '\App\v1\Controllers\ColorController:deleteColor');
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
