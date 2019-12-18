<?php
namespace App\v1\Controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use App\Models\Entity\Shoppingcart;

/**
 * Controller v1
 */
class ShoppingcartController {

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
    public function listShoppingcart($request, $response, $args) {
        
        $user_id = (int) $args['user_id'];
        $user_id = ($user_id) ? $user_id : 0;

        $entityManager = $this->container->get('em');

        $dql = "
            SELECT 
                p.id AS id,
                p.title AS title,
                p.description AS description,
                CONCAT('R$', p.price) AS price,
                CONCAT('https://s3-sa-east-1.amazonaws.com/img.rankforms.com/ssc/', p.id, '_', i.id, '.jpg') AS image
            FROM 
                App\Models\Entity\Shoppingcart s
                JOIN s.user u
                JOIN App\Models\Entity\Image i ON i.product = s.product
                JOIN s.product p
                JOIN s.transaction t
                JOIN t.transactionstatus a
            WHERE 
                u.id = $user_id
                AND a.id = 1
        ";
        $query = $entityManager->createQuery($dql);
        $items_temp = $query->getResult();

        $items = [];

        $i = 0;
        foreach($items_temp AS $item){
            $items[$i]['id'] = (int) $item['id'];
            $items[$i]['title'] = $item['title'];
            $items[$i]['price'] = $item['price'];
            $items[$i]['description'] = $item['description'];
            $i++;
        }

        $return = $response->withJson($items, 200)
            ->withHeader('Content-type', 'application/json');
        return $return;
    }

     
    /**
     * Cria um item
     * @param [type] $request
     * @param [type] $response
     * @param [type] $args
     * @return Response
     */
    public function createShoppingcart($request, $response, $args) {
        $params = (object) $request->getParams();
        $parent = ((int) $params->id_parent > 0) ? (int) $params->id_parent : 0;
        /**
         * Pega o Entity Manager do nosso Container
         */
        $entityManager = $this->container->get('em');
        $categoriesRepository = $entityManager->getRepository('App\Models\Entity\Shoppingcart');
        $Shoppingcart_parent = $categoriesRepository->find($parent); 
        /**
         * Instância da nossa Entidade preenchida com nossos parametros do post
         */
        $Shoppingcart = (new Shoppingcart())->setName($params->name)
        ->setName($params->name)
        ->setParent($Shoppingcart_parent);
        
        /**
         * Registra a criação da categoria
         */
        $logger = $this->container->get('logger');
        $logger->info('Shoppingcart Created!', $Shoppingcart->getValues());

        /**
         * Persiste a entidade no banco de dados
         */
        $cat = $entityManager->merge($Shoppingcart);
        $entityManager->flush();
        $return = $response->withJson($cat->getId(), 201)
            ->withHeader('Content-type', 'application/json');
        return $return;
    }

    /**
     * Exibe as informações de um item
     * @param [type] $request
     * @param [type] $response
     * @param [type] $args
     * @return Response
     */
    public function viewShoppingcart($request, $response, $args) {

        $id = (int) $args['id'];

        $entityManager = $this->container->get('em');
        $categoriesRepository = $entityManager->getRepository('App\Models\Entity\Shoppingcart');
        $Shoppingcart = $categoriesRepository->find($id); 

        /**
         * Verifica se existe uma categoria com a ID informada
         */
        if (!$Shoppingcart) {
            $logger = $this->container->get('logger');
            $logger->warning("Shoppingcart {$id} Not Found");
            throw new \Exception("Shoppingcart not Found", 404);
        }    

        $return = $response->withJson($Shoppingcart, 200)
            ->withHeader('Content-type', 'application/json');
        return $return;   
    }

    /**
     * Atualiza um item
     * @param [type] $request
     * @param [type] $response
     * @param [type] $args
     * @return Response
     */
    public function updateShoppingcart($request, $response, $args) {

        //$id = (int) $args['id'];
        $id = (int) $request->getParam('id');

        /**
         * Encontra a categoria no Banco
         */ 
        $entityManager = $this->container->get('em');
        $categoriesRepository = $entityManager->getRepository('App\Models\Entity\Shoppingcart');
        $Shoppingcart = $categoriesRepository->find($id);   

        /**
         * Verifica se existe uma categoria com a ID informada
         */
        if (!$Shoppingcart) {
            $logger = $this->container->get('logger');
            $logger->warning("Shoppingcart {$id} Not Found");
            throw new \Exception("Shoppingcart not Found", 404);
        }  

        /**
         * Atualiza e Persiste a Shoppingcart com os parâmetros recebidos no request
         */
        $Shoppingcart->setName($request->getParam('name'));

        /**
         * Persiste a entidade no banco de dados
         */
        $entityManager->persist($Shoppingcart);
        $entityManager->flush();        
        
        $return = $response->withJson($Shoppingcart, 200)
            ->withHeader('Content-type', 'application/json');
        return $return;
    }

    /**
     * Deleta uma Shoppingcart
     * @param [type] $request
     * @param [type] $response
     * @param [type] $args
     * @return Response
     */
    public function deleteShoppingcart($request, $response, $args) {

        $id = (int) $args['id'];
        //$id = (int) $request->getParam('id');

        /**
         * Encontra a Shoppingcart no Banco
         */ 
        $entityManager = $this->container->get('em');
        $categoriesRepository = $entityManager->getRepository('App\Models\Entity\Shoppingcart');
        $Shoppingcart = $categoriesRepository->find($id);   

        /**
         * Verifica se existe uma Shoppingcart com a ID informada
         */
        if (!$Shoppingcart) {
            $logger = $this->container->get('logger');
            $logger->warning("Shoppingcart {$id} Not Found");
            throw new \Exception("Shoppingcart {$id} not Found", 404);
        }

        /**
         * Remove a entidade
         */
        try {
            $entityManager->remove($Shoppingcart);
            $entityManager->flush();

            $return = $response->withJson(['msg' => "Deleting the Shoppingcart {$id}"], 204)
                ->withHeader('Content-type', 'application/json');
            return $return;
        } catch (\Exception $e) {
            $return = $response->withJson("Categoria não pode ser excluída", 202)
            ->withHeader('Content-type', 'application/json');
            return $return;
        }
    }
}
