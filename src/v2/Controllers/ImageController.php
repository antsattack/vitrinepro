<?php
namespace App\v2\Controllers;

define(AKANTSATTACK, getenv("AKANTSATTACK"));
define(SKANTSATTACK, getenv("SKANTSATTACK"));

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Aws\S3\S3Client;
use App\Models\Entity\Image;
use App\Models\Entity\Product;


/**
 * Controller v2
 */
class ImageController {

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
     * Listagem de Imagens
     * @param [type] $request
     * @param [type] $response
     * @param [type] $args
     * @return Response
     */
    public function listImage($request, $response, $args) {

        $product_id = (int) $args['product_id'];
        $product_id = ($product_id) ? $product_id : 0;

        $entityManager = $this->container->get('em');
        /*$imagesRepository = $entityManager->getRepository('App\Models\Entity\Image');
        $images = $imagesRepository->findBy(array('product' => $product_id));
        $return = $response->withJson($images, 200)
            ->withHeader('Content-type', 'application/json');
        return $return;*/
        $query = $entityManager->createQuery("
            SELECT 
                i.id AS image_id,
                i.prefix AS prefix,
                p.id AS product_id
            FROM 
                App\Models\Entity\Image i
                JOIN i.product p
            WHERE 
                p.id = $product_id
            ORDER BY
                i.id
        ");
        $images_temp = $query->getResult();

        $images = [];

        $i = 0;
        foreach($images_temp AS $item){
            $key_image = "image_".$item['image_id'];
            $images[$i]['id'] = (int) $item['image_id'];
            $images[$i]['url'] = "http://images.antsattack.com/".$item['prefix']."/".$item['product_id']."_".$item['image_id'].".jpg";
            $i++;
        }

        $return = $response->withJson($images, 200)
            ->withHeader('Content-type', 'application/json');
        return $return;
    }

    /**
     * Cria uma imagem
     * @param [type] $request
     * @param [type] $response
     * @param [type] $args
     * @return Response
     */
    public function createImage($request, $response, $args) {
        $params = (object) $request->getParams();
        $appsettings = $this->container->get('appsettings');
        $prefix = $appsettings["prefix"];
        $url = $appsettings["url"]."/".$prefix."/";

        /**
         * Pega o Entity Manager do nosso Container
         */
        $entityManager = $this->container->get('em');
        $entityManager->getConnection()->beginTransaction();
        try{
            /**
             * Instância da nossa Entidade preenchida com nossos parametros do post
             */
            $product = (new Product())->setId($params->product);
            $image = (new Image())->setPrefix("ssc")
                ->setProduct($product)
                ->setMain($params->main);

            $files = $request->getUploadedFiles();

            if (!isset($files)) {
                throw new \Exception("File not uploaded", 1);
            }

            $file = $files['file'];

            //foreach ($files AS $file){

            /*if (($file->getSize() > 2097152)){
                throw new \Exception("Arquivo da imagem deve ter menos que 2MB de tamanho.", 2);
            }*/

            /**
             * Persiste a entidade no banco de dados
             */
            $attachedImage = $entityManager->merge($image);
            $entityManager->flush();

            $novoId = $attachedImage->getId();

            if (!($novoId > 0)) {
                throw new \Exception("Não persistiu", 1);
            }

            $name = $prefix."/".$params->product."_".$novoId.".jpg";

            //$tmpname = $file->file;

            /**
             * reduzir arquivo
             */
            /*$info = getimagesize($tmpname);

            if ($info['mime'] == 'image/jpeg'){
                $newImage = imagecreatefromjpeg($tmpname);
            }
            elseif ($info['mime'] == 'image/gif'){
                $newImage = imagecreatefromgif($tmpname);
            }
            elseif ($info['mime'] == 'image/png'){
                $newImage = imagecreatefrompng($tmpname);
            }*/
            $newImage = imagecreatefromstring($image);
            $reduced = "/tmp/temp.jpg";

            $largura = 500;
            
            $largura_original = imagesX($newImage);
            $altura_original = imagesY($newImage);

            if ($altura_original > $largura_original){
                $altura_nova = (int) ($altura_original * $largura)/$largura_original;
                $imgReduced = imagecreatetruecolor($largura,$altura_nova);

                imagecopyresampled($imgReduced, $newImage, 0, 0, 0, 0, $largura, $altura_nova, $largura_original,  $altura_original);
                $desloc = ($altura_nova/2)-($largura/2);

                //crop
                $size = min(imagesx($imgReduced), imagesy($imgReduced));
                $imgCropped = imagecrop($imgReduced, ['x' => 0, 'y' => $desloc, 'width' => $size, 'height' => $size]);

            } else{
                $largura_nova = (int) ($largura_original * $largura)/$altura_original;
                $imgReduced = imagecreatetruecolor($largura_nova,$largura);

                imagecopyresampled($imgReduced, $newImage, 0, 0, 0, 0, $largura_nova, $largura, $largura_original,  $altura_original);
                $desloc = ($largura_nova/2)-($largura/2);

                //crop
                $size = min(imagesx($imgReduced), imagesy($imgReduced));
                $imgCropped = imagecrop($imgReduced, ['x' => $desloc, 'y' => 0, 'width' => $size, 'height' => $size]);
            }

            imagejpeg($imgCropped, $reduced, 100);
            imagedestroy($imgCropped);
            imagedestroy($imgReduced);
            chmod($reduced,0777);

            /**
             * cria o objeto do cliente S3, necessita passar as credenciais da AWS
             */ 
            $clientS3 = S3Client::factory(array(
                'key' => AKANTSATTACK,
                'secret' => SKANTSATTACK
            ));
            $clientS3->setRegion('sa-east-1');

            /**
             * método putObject envia os dados pro bucket selecionado
             */
            $resp = $clientS3->putObject(array(
                'Bucket' => "images.antsattack.com",
                'Key' => $name,
                'SourceFile' => $reduced
            ));

            unlink($reduced);
            //}
            $ret = "http://images.antsattack.com/".$name;

            $entityManager->getConnection()->commit();

            $return = $response->withJson($ret, 201)
                ->withHeader('Content-type', 'application/json');

        } catch(\Exception $e){
            $entityManager->getConnection()->rollBack();
            $return = $response->withJson($e->getMessage(), 500)
                ->withHeader('Content-type', 'application/json');
        }
        return $return;
    }
    
    /**
     * Torna uma imagem a principal
     * @param [type] $request
     * @param [type] $response
     * @param [type] $args
     * @return Response
     */
    public function setMainImage($request, $response, $args) {
        $product_id = (int) $args['product_id'];
        $product_id = ($product_id) ? $product_id : 0;
        //$product = (new Product())->setId($product_id);

        $params = (object) $request->getParams();
        $entityManager = $this->container->get('em');
        $entityManager->getConnection()->beginTransaction();
        try {

            $qb = $entityManager->createQueryBuilder();
            $q = $qb->update('App\Models\Entity\Image', 'i')
                    ->set('i.main', 0)
                    ->where('i.product = (:product_id)')
                    ->setParameter('product_id', $product_id)
                    ->getQuery();
            $p = $q->execute();

            $qb = $entityManager->createQueryBuilder();
            $q = $qb->update('App\Models\Entity\Image', 'i')
                    ->set('i.main', 1)
                    ->where('i.id = ?1')
                    ->setParameter(1, $params->image_id)
                    ->getQuery();
            $p = $q->execute();

            $entityManager->getConnection()->commit();

            $return = $response->withJson(1, 201)
                    ->withHeader('Content-type', 'application/json');
        } catch(\Exception $e){
            $entityManager->getConnection()->rollBack();
            $return = $response->withJson($e->getMessage(), 500)
                ->withHeader('Content-type', 'application/json');
        }
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
