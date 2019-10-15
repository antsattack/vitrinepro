<?php
namespace App\v1\Controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

//use App\Models\Entity\Attribute;
use App\Models\Entity\Color;

/**
 * Controller v1
 */
class DatasheetController {

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
    public function listDatasheetByCategory($request, $response, $args) {
        
        $category_id = (int) $args['category_id'];
        $category_id = ($category_id) ? $category_id : 0;

        $entityManager = $this->container->get('em');
        $colorsRepository = $entityManager->getRepository('App\Models\Entity\Color');
        $colors = $colorsRepository->findAll();
        
        $dql = "
            SELECT 
                a.id AS id,
                a.name AS name,
                a.description AS values,
                a.unit AS unit
            FROM 
                App\Models\Entity\Attribute a
                JOIN a.category c
            WHERE 
                c.id = $category_id
            ORDER BY
                a.name
        ";
        $query = $entityManager->createQuery($dql);
        $datasheets_temp = $query->getResult();

        $datasheets = [];

        $i = 0;
        foreach($datasheets_temp AS $item){
            $datasheets[$i]['id'] = (int) $item['id'];
            $datasheets[$i]['name'] = $item['name'];
            if ($item['unit']=="color"){
                $datasheets[$i]['values'] = $colors;
            } else {
                $datasheets[$i]['values'] = explode(";",$item['values']);
            }
            $i++;
        }

        $return = $response->withJson($datasheets, 200)
            ->withHeader('Content-type', 'application/json');
        return $return;
    }

    /**
     * Listagem
     * @param [type] $request
     * @param [type] $response
     * @param [type] $args
     * @return Response
     */
    public function listDatasheetByProduct($request, $response, $args) {
        
        $product_id = (int) $args['product_id'];
        $product_id = ($product_id) ? $product_id : 0;

        $entityManager = $this->container->get('em');
        
        $dql = "
            SELECT 
                a.id AS id,
                a.name AS name,
                a.description AS values,
                a.unit AS unit,
                aa.value AS value
            FROM 
                App\Models\Entity\Product p
                JOIN p.attribute aa
                JOIN p.category c
                JOIN c.attribute a
            WHERE 
                p.id = $product_id
                AND a.category = p.category
            ORDER BY
                a.name
        ";
        $query = $entityManager->createQuery($dql);
        $datasheets_temp = $query->getResult();

        $datasheets = [];

        $i = 0;
        foreach($datasheets_temp AS $item){
            $datasheets[$i]['id'] = (int) $item['id'];
            $datasheets[$i]['name'] = $item['name'];
            if ($item['unit']=="color"){
                $datasheets[$i]['values'] = $colors;
            } else {
                $datasheets[$i]['values'] = explode(";",$item['values']);
            }
            $datasheets[$i]['value'] = $item['value'];
            $i++;
        }

        $return = $response->withJson($datasheets, 200)
            ->withHeader('Content-type', 'application/json');
        return $return;
    }
}
