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
use App\Models\Entity\Attribute;
use App\Models\Entity\Datasheet;
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

        //$token = $request->getHeaderLine('X-Token');
        //$data = JWT::decode($token, $this->container->get('secretkey'), array('HS512'));
        /*if (!$data->userid){
            return $response->withStatus(401);
        }*/

        $entityManager = $this->container->get('em');
        $query = $entityManager->createQuery("
            SELECT 
                p.id AS product_id,
                p.title AS title,
                i.id AS image,
                p.description AS description
            FROM 
                App\Models\Entity\Product p
                LEFT JOIN App\Models\Entity\Image i WITH i.product = p.id AND i.main = 1
            ORDER BY
                p.id
        ");
        $products_temp = $query->getResult();

        $products = [];

        $i = 0;
        foreach($products_temp AS $item){
            $products[$i]['id'] = (int) $item['product_id'];
            $products[$i]['title'] = $item['title'];
            $products[$i]['image'] = $item['image'];
            $products[$i]['description'] = $item['description'];
            $i++;
        }

        $return = $response->withJson($products, 200)
            ->withHeader('Content-type', 'application/json');
        return $return;
    }

    /**
     * Listagem por usuário
     * @param [type] $request
     * @param [type] $response
     * @param [type] $args
     * @return Response
     */
    public function listProductByUser($request, $response, $args) {

        $entityManager = $this->container->get('em');
        $user_id = (int) $args['user_id'];
        $query = $entityManager->createQuery("
            SELECT 
                p.id AS product_id,
                p.title AS title,
                i.id AS image,
                c.id AS category,
                pa.id AS parent_category,
                b.id AS brand,
                p.description AS description,
                p.model AS model,
                p.price AS price,
                p.new AS new
            FROM 
                App\Models\Entity\Product p
                LEFT JOIN App\Models\Entity\Category c WITH p.category = c.id
                LEFT JOIN App\Models\Entity\Category pa WITH c.parent = pa.id
                LEFT JOIN App\Models\Entity\Brand b WITH p.brand = b.id
                LEFT JOIN App\Models\Entity\Image i WITH i.product = p.id AND i.main = 1
            WHERE
                p.seller = $user_id
            ORDER BY
                p.id
        ");
        $products_temp = $query->getResult();

        

        $products = [];

        $i = 0;
        foreach($products_temp AS $item){
            $product_id = (int) $item['product_id'];
            $products[$i]['id'] = $product_id;
            $products[$i]['title'] = $item['title'];
            $products[$i]['category'] = $item['category'];
            $products[$i]['parent_category'] = $item['parent_category'];
            $products[$i]['image'] = $item['image'];
            $products[$i]['description'] = $item['description'];
            $products[$i]['brand'] = $item['brand'];
            $products[$i]['model'] = $item['model'];
            $products[$i]['price'] = $item['price'];
            $products[$i]['new'] = $item['new'];
            $query = $entityManager->createQuery("
            SELECT 
                t.id
            FROM 
                App\Models\Entity\Tag t
                JOIN t.product p
            WHERE
                p.id = $product_id
            ORDER BY
                t.id
            ");
            $tags = $query->getResult();
            $tagsList = array();
            foreach($tags AS $tag) $tagsList[] = $tag["id"];

            $products[$i]['tag'] = $tagsList;
            $i++;
        }

        $return = $response->withJson($products, 200)
            ->withHeader('Content-type', 'application/json');
        return $return;
    }

    /**
     * Ver
     * @param [type] $request
     * @param [type] $response
     * @param [type] $args
     * @return Response
     */
    public function viewProduct($request, $response, $args) {
        $product_id = (int) $args['id'];
        $product_id = ($product_id) ? $product_id : 0;
        $entityManager = $this->container->get('em');
        $product = $entityManager->find('App\Models\Entity\Product', $product_id);

        $prdObj = new \stdClass();
        $prdObj->id = $product->id;
        $prdObj->title = $product->title;
        $prdObj->description = $product->description;
        $prdObj->model = $product->model;
        $prdObj->price = (float) $product->price;
        $prdObj->new = $product->new;
        $prdObj->quantity = $product->quantity;
        $prdObj->brand = $product->brand->id;
        $prdObj->category = $product->category->id;
        $prdObj->currency = $product->currency->id;
        $prdObj->seller = $product->seller->id;

        $query = $entityManager->createQuery("
            SELECT 
                t.id
            FROM 
                App\Models\Entity\Tag t
                JOIN t.product p
            WHERE
                p.id = $product_id
            ORDER BY
                t.id
        ");
        $tags = $query->getResult();
        $tagsList = array();
        foreach($tags AS $tag) $tagsList[] = $tag["id"];

        $query = $entityManager->createQuery("
            SELECT 
                d.attribute AS id,
                d.value
            FROM 
                App\Models\Entity\Datasheet d
            WHERE
                d.product = $product_id
        ");
        $datasheet = $query->getResult();
        $datasheetList = array();
        foreach($datasheet AS $item) {
            $datasheetList[] = array(
                "id" => (int) $item["id"],
                "value" => $item["value"]
            );
        }
        $prdObj->datasheet = $datasheetList;
        $prdObj->tags = $tagsList;

        $return = $response->withJson($prdObj, 200)
            ->withHeader('Content-type', 'application/json');
        return $return;
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
        if (!$data->id){
            return $response->withStatus(401);
        }
        $seller = (new User())->setId($data->id);
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
        $entityManager->getConnection()->beginTransaction();
        try {
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
                //$listTags = array();
                $product->getTag()->clear();
                foreach($params->tag AS $tag_id){
                    //$listTags[] = $entityManager->find('App\Models\Entity\Tag', $tag);
                    $tag = $entityManager->find('App\Models\Entity\Tag', $tag_id);
                    //if(!$product->getTag()->contains($tag)){
                        $product->getTag()->add($tag);
                    //}
                }
            }

            if (count($params->datasheet)) {
                $listAttrs = array();
                foreach($params->datasheet AS $datasheet){
                    $listAttrs[] = $entityManager->find('App\Models\Entity\Attribute', $datasheet['id']);
                }
                $product->setAttribute($listAttrs);
            }

            /**
             * Persiste a entidade no banco de dados
             */
            $entityManager->persist($product);
            $entityManager->flush();

            foreach($params->datasheet AS $datasheet){
                $entityDatasheet = $entityManager->find('App\Models\Entity\Datasheet', array(
                    "product" => $product_id,
                    "attribute" => $datasheet["id"]
                ));
                $entityDatasheet->setValue($datasheet["value"]);
                $entityManager->persist($entityDatasheet);
            }

            $entityManager->flush();

            $return_val = $product->getId();

            $entityManager->getConnection()->commit();

            $return = $response->withJson($return_val, 201)
                ->withHeader('Content-type', 'application/json');

        } catch(\Exception $e){
            $entityManager->getConnection()->rollBack();
            $return = $response->withJson($e->getMessage(), 500)
                ->withHeader('Content-type', 'application/json');
        }
        return $return;
    } 
}
