<?php

/**
 * Grupo dos enpoints iniciados por v1
 */
$app->group('/v1', function() {

    $this->group('/advisors', function() {
        $this->get('/{id:[0-9]+}', '\App\v1\Controllers\AdvisorController:listAdvisor');
    });

    $this->group('/auth', function() {
        $this->post('', \App\v1\Controllers\AuthController::class);
    });

    $this->group('/brands', function() {
        $this->get('/category/{category_id:[0-9]+}', '\App\v1\Controllers\BrandController:listBrand');
        $this->post('/category/{category_id:[0-9]+}', '\App\v1\Controllers\BrandController:createBrand');
        $this->patch('/{id:[0-9]+}', '\App\v1\Controllers\BrandController:updateBrand');
        $this->delete('/{id:[0-9]+}', '\App\v1\Controllers\BrandController:deleteBrand');
    });

    $this->group('/attributes', function() {
        $this->get('/category/{category_id:[0-9]+}', '\App\v1\Controllers\AttributeController:listAttribute');
        $this->post('/category/{category_id:[0-9]+}', '\App\v1\Controllers\AttributeController:createAttribute');
        $this->patch('/{id:[0-9]+}', '\App\v1\Controllers\AttributeController:updateAttribute');
        $this->delete('/{id:[0-9]+}', '\App\v1\Controllers\AttributeController:deleteAttribute');
    });

    $this->group('/categories', function() {
        $this->get('', '\App\v1\Controllers\CategoryController:listCategory');
        $this->get('/{parent_id:[0-9]+}', '\App\v1\Controllers\CategoryController:listCategory');
        $this->post('', '\App\v1\Controllers\CategoryController:createCategory');
        $this->patch('', '\App\v1\Controllers\CategoryController:updateCategory');
        $this->delete('/{id:[0-9]+}', '\App\v1\Controllers\CategoryController:deleteCategory');
    });

    $this->group('/colors', function() {
        $this->get('', '\App\v1\Controllers\ColorController:listColor');
        $this->post('', '\App\v1\Controllers\ColorController:createColor');
        $this->get('/{id:[0-9]+}', '\App\v1\Controllers\ColorController:viewColor');
        $this->put('/{id:[0-9]+}', '\App\v1\Controllers\ColorController:updateColor');
        $this->delete('/{id:[0-9]+}', '\App\v1\Controllers\ColorController:deleteColor');
    });

    $this->group('/datasheets', function() {
        $this->get('/category/{category_id:[0-9]+}', '\App\v1\Controllers\DatasheetController:listDatasheetByCategory');
    });

    $this->group('/getuserbyemail', function() {
        $this->get('/{email:[a-zA-Z0-9_\-\@\.]+}', '\App\v1\Controllers\UserController:getUserByEmail');
    });

    $this->group('/images', function() {
        $this->get('/product/{product_id:[0-9]+}', '\App\v1\Controllers\ImageController:listImage');
        $this->patch('/main/product/{product_id:[0-9]+}', '\App\v1\Controllers\ImageController:setMainImage');
        $this->post('', '\App\v1\Controllers\ImageController:createImage');
        $this->delete('/{id:[0-9]+}', '\App\v1\Controllers\ImageController:deleteImage');
    });

    $this->group('/products', function() {
        $this->get('', '\App\v1\Controllers\ProductController:listProduct');
        $this->get('/{id:[0-9]+}', '\App\v1\Controllers\ProductController:viewProduct');
        $this->post('', '\App\v1\Controllers\ProductController:createProduct');
        $this->patch('/{id:[0-9]+}', '\App\v1\Controllers\ProductController:updateProduct');
    });

    $this->group('/tags', function() {
        $this->get('/category/{category_id:[0-9]+}', '\App\v1\Controllers\TagController:listTagByCategory');
        $this->post('/category/{category_id:[0-9]+}', '\App\v1\Controllers\TagController:createTag');
        $this->patch('/{id:[0-9]+}', '\App\v1\Controllers\TagController:updateTag');
        $this->delete('/{id:[0-9]+}', '\App\v1\Controllers\TagController:deleteTag');
    });

    $this->group('/transactions', function() {
        $this->get('', '\App\v1\Controllers\TransactionController:listTransaction');
    });

    $this->group('/usercreate', function() {
        $this->post('', '\App\v1\Controllers\UserController:createUser');
    });

    $this->group('/users', function() {
        $this->get('', '\App\v1\Controllers\UserController:listUser');
        $this->get('/{id:[0-9]+}', '\App\v1\Controllers\UserController:viewUser');
        $this->put('/{id:[0-9]+}', '\App\v1\Controllers\UserController:updateUser');
        $this->delete('/{id:[0-9]+}', '\App\v1\Controllers\UserController:deleteUser');
    });
});

/**
 * Grupo dos enpoints iniciados por v2
 */
$app->group('/v2', function() {
    $this->group('/images', function() {
        $this->post('', '\App\v2\Controllers\ImageController:createImage');
    });
});

// Catch-all route to serve a 404 Not Found page if none of the routes match
// NOTE: make sure this route is defined last
$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function($req, $res) {
    $handler = $this->notFoundHandler; // handle using the default Slim page not found handler
    return $handler($req, $res);
});