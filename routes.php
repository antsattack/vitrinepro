<?php

/**
 * Grupo dos enpoints iniciados por v1
 */
$app->group('/v1', function() {

    /**
     * Dentro de v1, o recurso /products
     */
    $this->group('/products', function() {
        $this->post('', '\App\v1\Controllers\ProductController:createProduct');
        $this->patch('/{id:[0-9]+}', '\App\v1\Controllers\ProductController:updateProduct');
    });

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
     * Dentro de v1, o recurso /images
     */
    $this->group('/images', function() {
        $this->get('/product/{product_id:[0-9]+}', '\App\v1\Controllers\ImageController:listImage');
        $this->patch('/main/product/{product_id:[0-9]+}', '\App\v1\Controllers\ImageController:setMainImage');
        $this->post('', '\App\v1\Controllers\ImageController:createImage');
        $this->delete('/{id:[0-9]+}', '\App\v1\Controllers\ImageController:deleteImage');
    });

    /**
     * Dentro de v1, o recurso /categories
     */
    $this->group('/categories', function() {
        $this->get('', '\App\v1\Controllers\CategoryController:listCategory');
        $this->get('/{parent_id:[0-9]+}', '\App\v1\Controllers\CategoryController:listCategory');
    });

    /**
     * Dentro de v1, o recurso /brands
     */
    $this->group('/brands', function() {
        $this->get('/category/{category_id:[0-9]+}', '\App\v1\Controllers\BrandController:listBrand');
    });

    /**
     * Dentro de v1, o recurso /tags
     */
    $this->group('/tags', function() {
        $this->get('/category/{category_id:[0-9]+}', '\App\v1\Controllers\TagController:listTagByCategory');
    });

    /**
     * Dentro de v1, o recurso /transactions
     */
    $this->group('/transactions', function() {
        $this->get('', '\App\v1\Controllers\TransactionController:listTransaction');
    });

    /**
     * Dentro de v1, o recurso /advisors
     */
    $this->group('/advisors', function() {
        $this->get('/{id:[0-9]+}', '\App\v1\Controllers\AdvisorController:listAdvisor');
    });

    /**
     * Dentro de v1, o recurso /auth
     */
    $this->group('/auth', function() {
        $this->post('', \App\v1\Controllers\AuthController::class);
    });
});

// Catch-all route to serve a 404 Not Found page if none of the routes match
// NOTE: make sure this route is defined last
$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function($req, $res) {
    $handler = $this->notFoundHandler; // handle using the default Slim page not found handler
    return $handler($req, $res);
});