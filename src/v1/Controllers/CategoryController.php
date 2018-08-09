<?php
namespace App\v1\Controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use App\Models\Entity\Category;

/**
 * Controller v1
 */
class CategoryController {

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
    public function listCategory($request, $response, $args) {
        
        $parent_id = (int) $args['parent_id'];
        $parent_id = ($parent_id) ? $parent_id : 0;

        $entityManager = $this->container->get('em');
        $where = "p.id = c.id";
        if ($parent_id){
            $where = "p.id = $parent_id AND c.id != $parent_id";
        }
        $dql = "
            SELECT 
            c.id AS id,
            c.name AS name,
            c.description AS description
            FROM 
                App\Models\Entity\Category c
                JOIN c.parent p
            WHERE 
                $where
            ORDER BY
                c.id
        ";
        $query = $entityManager->createQuery($dql);
        $categories_temp = $query->getResult();

        $categories = [];

        $i = 0;
        foreach($categories_temp AS $item){
            $categories[$i]['id'] = (int) $item['id'];
            $categories[$i]['name'] = $item['name'];
            $categories[$i]['description'] = $item['description'];
            $i++;
        }

        $return = $response->withJson($categories, 200)
            ->withHeader('Content-type', 'application/json');
        return $return;
    }
}
