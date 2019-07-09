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

    /**
     * Add
     * @param [type] $request
     * @param [type] $response
     * @param [type] $args
     * @return Response
     */
    public function createBrand($request, $response, $args) {
        $params = (object) $request->getParams();
        //$parent = ((int) $params->id_parent > 0) ? (int) $params->id_parent : 0;
        $parent = (int) $args['category_id'];
        /**
         * Pega o Entity Manager do nosso Container
         */
        $entityManager = $this->container->get('em');
        $categoriesRepository = $entityManager->getRepository('App\Models\Entity\Category');
        $category_parent = $categoriesRepository->find($parent); 
        /**
         * Instância da nossa Entidade preenchida com nossos parametros do post
         */
        $brand = (new Brand())->setName($params->name)
        ->setCategory($category_parent);
        
        /**
         * Registra a criação da categoria
         */
        $logger = $this->container->get('logger');
        $logger->info('Brand Created!', $brand->getValues());

        /**
         * Persiste a entidade no banco de dados
         */
        $bd = $entityManager->merge($brand);
        $entityManager->flush();
        $return = $response->withJson($bd->getId(), 201)
            ->withHeader('Content-type', 'application/json');
        return $return;
    }

    /**
     * Atualiza uma brand
     * @param [type] $request
     * @param [type] $response
     * @param [type] $args
     * @return Response
     */
    public function updateBrand($request, $response, $args) {

        $id = (int) $args['id'];
        //$id = (int) $request->getParam('id');

        /**
         * Encontra a brand no Banco
         */ 
        $entityManager = $this->container->get('em');
        $brandsRepository = $entityManager->getRepository('App\Models\Entity\Brand');
        $brand = $brandsRepository->find($id);   

        /**
         * Verifica se existe uma brand com a ID informada
         */
        if (!$brand) {
            $logger = $this->container->get('logger');
            $logger->warning("Brand {$id} Not Found");
            throw new \Exception("Brand not Found", 404);
        }  

        /**
         * Atualiza e Persiste a brand com os parâmetros recebidos no request
         */
        $brand->setName($request->getParam('name'));

        /**
         * Persiste a entidade no banco de dados
         */
        $entityManager->persist($brand);
        $entityManager->flush();        
        
        $return = $response->withJson($brand, 200)
            ->withHeader('Content-type', 'application/json');
        return $return;
    }

    /**
     * Deleta uma brand
     * @param [type] $request
     * @param [type] $response
     * @param [type] $args
     * @return Response
     */
    public function deleteBrand($request, $response, $args) {

        $id = (int) $args['id'];
        //$id = (int) $request->getParam('id');

        /**
         * Encontra a brand no Banco
         */ 
        $entityManager = $this->container->get('em');
        $brandsRepository = $entityManager->getRepository('App\Models\Entity\Brand');
        $brand = $brandsRepository->find($id);   

        /**
         * Verifica se existe uma brand com a ID informada
         */
        if (!$brand) {
            $logger = $this->container->get('logger');
            $logger->warning("Brand {$id} Not Found");
            throw new \Exception("Brand {$id} not Found", 404);
        }

        /**
         * Remove a entidade
         */
        try {
            $entityManager->remove($brand);
            $entityManager->flush();

            $return = $response->withJson(['msg' => "Deleting the brand {$id}"], 204)
                ->withHeader('Content-type', 'application/json');
            return $return;
        } catch (\Exception $e) {
            $return = $response->withJson("Marca não pode ser excluída", 202)
            ->withHeader('Content-type', 'application/json');
            return $return;
        }
    }
}
