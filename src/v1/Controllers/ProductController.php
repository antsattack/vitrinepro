<?php
namespace App\v1\Controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use App\Models\Entity\Product;
use App\Models\Entity\Currency;
use App\Models\Entity\User;


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

        $seller_id = (int) $args['seller_id'];
        $seller_id = ($seller_id) ? $seller_id : 0;

        $entityManager = $this->container->get('em');
        $query = $entityManager->createQuery("
            SELECT 
                p.id AS product_id
            FROM 
                App\Models\Entity\Product p
                JOIN p.seller s
            WHERE 
                s.id = $seller_id
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
        $seller = (new User())->setId($params->seller_id);
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
        $entityManager->merge($product);
        $entityManager->flush();
        $return = $response->withJson(1, 201)
            ->withHeader('Content-type', 'application/json');
        return $return;
    }

    /**
     * Deleta uma imagem
     * @param [type] $request
     * @param [type] $response
     * @param [type] $args
     * @return Response
     */
    public function deleteImage($request, $response, $args) {

        $id = (int) $args['id'];

        /**
         * Encontra no Banco
         */ 
        $entityManager = $this->container->get('em');
        $imagesRepository = $entityManager->getRepository('App\Models\Entity\Image');
        $image = $imagesRepository->find($id);

        /**
         * Verifica se existe
         */
        if (!$image) {
            $logger = $this->container->get('logger');
            $logger->warning("Image {$id} Not Found");
            throw new \Exception("Image not Found", 404);
        }

        /**
         * Remove a imagem do S3
         */
        $clientS3 = S3Client::factory(array(
            'key' => AKANTSATTACK,
            'secret' => SKANTSATTACK
        ));
        $clientS3->setRegion('sa-east-1');

        $name = $image->prefix."/".$image->product->id."_".$image->id.".jpg";

        $resp = $clientS3->deleteObject(array(
            'Bucket' => "images.antsattack.com",
            'Key'    => $name,
            'RequestPayer' => 'requester',
        ));

        /**
         * Remove a entidade
         */
        $entityManager->remove($image);
        $entityManager->flush();

        $return = $response->withJson(['msg' => "Deleting the image {$id}"], 204)
            ->withHeader('Content-type', 'application/json');
        return $return;
    }
}
