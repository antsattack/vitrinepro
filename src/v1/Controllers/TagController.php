<?php
namespace App\v1\Controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use App\Models\Entity\Tag;

/**
 * Controller v1
 */
class TagController {

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
    public function listTagByCategory($request, $response, $args) {
        
        $category_id = (int) $args['category_id'];
        $category_id = ($category_id) ? $category_id : 0;

        $entityManager = $this->container->get('em');
        
        $dql = "
            SELECT 
                t.id AS id,
                t.name AS name
            FROM 
                App\Models\Entity\Tag t
                JOIN t.category c
            WHERE 
                c.id = $category_id
            ORDER BY
                t.name
        ";
        $query = $entityManager->createQuery($dql);
        $tags_temp = $query->getResult();

        $tags = [];

        $i = 0;
        foreach($tags_temp AS $item){
            $tags[$i]['id'] = (int) $item['id'];
            $tags[$i]['name'] = $item['name'];
            $i++;
        }

        $return = $response->withJson($tags, 200)
            ->withHeader('Content-type', 'application/json');
        return $return;
    }
}
