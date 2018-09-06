<?php
namespace App\v1\Controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Firebase\JWT\JWT;
use App\Models\Entity\Product;
use App\Models\Entity\Currency;
use App\Models\Entity\User;
use App\Models\Entity\Category;
use App\Models\Entity\Brand;
use App\Models\Entity\Tag;
use \Doctrine\Common\Collections\Collection;


/**
 * Controller v1
 */
class ProductController {

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
    public function listProduct($request, $response, $args) {

        $token = $request->getHeaderLine('X-Token');
        $data = JWT::decode($token, $this->container->get('secretkey'), array('HS512'));
        if (!$data->userid){
            return $response->withStatus(401);
        }

        $entityManager = $this->container->get('em');
        $query = $entityManager->createQuery("
            SELECT 
                p.id AS product_id
            FROM 
                App\Models\Entity\Product p
                JOIN p.seller s
            WHERE 
                s.id = $data->userid
            ORDER BY
                p.id
        ");
        /*$images_temp = $query->getResult();

        $images = [];

        $i = 0;
        foreach($images_temp AS $item){
            $images[$i]['id'] = (int) $item['image_id'];
            $images[$i]['url'] = "http://images.antsattack.com/".$item['prefix']."/".$item['product_id']."_".$item['image_id'].".jpg";
            $i++;
        }

        $return = $response->withJson($images, 200)
            ->withHeader('Content-type', 'application/json');
        return $return;*/
    }

    /**
     * Cria
     * @param [type] $request
     * @param [type] $response
     * @param [type] $args
     * @return Response
     */
    public function createProduct($request, $response, $args) {
        $params = (object) $request->getParams();
        $currency = (new Currency())->setId(1);
        $token = $request->getHeaderLine('X-Token');
        $data = JWT::decode($token, $this->container->get('secretkey'), array('HS512'));
        if (!$data->userid){
            return $response->withStatus(401);
        }
        $seller = (new User())->setId($data->userid);
        /**
         * Pega o Entity Manager do nosso Container
         */
        $entityManager = $this->container->get('em');
        /**
         * Instância da nossa Entidade preenchida com nossos parametros do post
         */
        $product = (new Product())->setTitle($params->title)
            ->setDescription($params->description)
            ->setCurrency($currency)
            ->setSeller($seller);
        
        /**
         * Registra a criação
         */
        $logger = $this->container->get('logger');
        $logger->info('Product Created!', $product->getValues());

        /**
         * Persiste a entidade no banco de dados
         */
        $prd = $entityManager->merge($product);
        $entityManager->flush();
        $return = $response->withJson($prd->getId(), 201)
            ->withHeader('Content-type', 'application/json');
        return $return;
    }

    /**
     * Atualiza
     * @param [type] $request
     * @param [type] $response
     * @param [type] $args
     * @return Response
     */
    public function updateProduct($request, $response, $args) {
        $product_id = (int) $args['id'];
        $product_id = ($product_id) ? $product_id : 0;

        $params = (object) $request->getParams();

        $entityManager = $this->container->get('em');
        $product = $entityManager->find('App\Models\Entity\Product', $product_id);

        if (strlen($params->title)) {
            $product->setTitle($params->title);
        }

        if (strlen($params->description)) {
            $product->setDescription($params->description);
        }

        if ($params->category > 0) {
            //$category = (new Category())->setId($params->category);
            $category = $entityManager->find('App\Models\Entity\Category', $params->category);
            $product->setCategory($category);
        }

        if ($params->brand > 0) {
            //$brand = (new Brand())->setId($params->brand);
            $brand = $entityManager->find('App\Models\Entity\Brand', $params->brand);
            $product->setBrand($brand);
        }

        if (strlen($params->model)) {
            $product->setModel($params->model);
        }

        if (strlen($params->price)) {
            $product->setPrice($params->price);
        }

        if (strlen($params->new)) {
            $product->setNew($params->new);
        }

        if (strlen($params->quantity)) {
            $product->setQuantity($params->quantity);
        }

        if (count($params->tag)) {
            $listTags = array();
            foreach($params->tag AS $tag){
                $listTags[] = $entityManager->find('App\Models\Entity\Tag', $tag);
            }
            $product->setTag($listTags);
        }

        /**
         * Persiste a entidade no banco de dados
         */
        $entityManager->persist($product);
        $entityManager->flush();
        $return = $response->withJson($product->getId(), 201)
            ->withHeader('Content-type', 'application/json');
        return $return;
    } 
}
