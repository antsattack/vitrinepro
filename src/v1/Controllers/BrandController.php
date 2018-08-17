<?php
namespace App\v1\Controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use App\Models\Entity\Brand;

/**
 * Controller v1
 */
class BrandController {

    /**
     * Container Class
     * @var [object]
     */
    private $container;

    /**
     * Undocumented function
     * @param [object] $container
     */
    public function __construct($container) {
        $this->container = $container;
    }
    
    /**
     * Listagem
     * @param [type] $request
     * @param [type] $response
     * @param [type] $args
     * @return Response
     */
    public function listBrand($request, $response, $args) {
        
        $category_id = (int) $args['category_id'];
        $category_id = ($category_id) ? $category_id : 0;

        $entityManager = $this->container->get('em');
        
        $dql = "
            SELECT 
                b.id AS id,
                b.name AS name
            FROM 
                App\Models\Entity\Brand b
                JOIN b.category c
            WHERE 
                c.id = $category_id
            ORDER BY
                b.name
        ";
        $query = $entityManager->createQuery($dql);
        $brands_temp = $query->getResult();

        $brands = [];

        $i = 0;
        foreach($brands_temp AS $item){
            $brands[$i]['id'] = (int) $item['id'];
            $brands[$i]['name'] = $item['name'];
            $i++;
        }

        $return = $response->withJson($brands, 200)
            ->withHeader('Content-type', 'application/json');
        return $return;
    }
}
