<?php
namespace App\v1\Controllers;

define(ACCESS_KEY, "AKIAIMUUC5SC4SYPQPRQ");
define(SECRET_KEY, "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");

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Aws\S3\S3Client;
use App\Models\Entity\Image;
use App\Models\Entity\Product;


/**
 * Controller v1
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

        $entityManager = $this->container->get('em');
        $imagesRepository = $entityManager->getRepository('App\Models\Entity\Image');
        $images = $imagesRepository->findBy(array('product' => $product_id));
        $return = $response->withJson($images, 200)
            ->withHeader('Content-type', 'application/json');
        return $return;
    }

    /**
     * Exibe as informações de uma imagem 
     * @param [type] $request
     * @param [type] $response
     * @param [type] $args
     * @return Response
     */
    public function viewImage($request, $response, $args) {

        $id = (int) $args['id'];

        $appsettings = $this->container->get('appsettings');
        $prefix = $appsettings["prefix"];
        $url = $appsettings["url"]."/".$prefix."/";

        $entityManager = $this->container->get('em');
        $ImagesRepository = $entityManager->getRepository('App\Models\Entity\Image');
        $Image = $ImagesRepository->find($id);

        /**
         * Verifica se existe uma imagem com a ID informada
         */
        /*
        if (!$Image) {
            $logger = $this->container->get('logger');
            $logger->warning("Image {$id} Not Found");
            throw new \Exception("Image not Found", 404);
        }    

        $return = $response->withJson($Image2, 200)
            ->withHeader('Content-type', 'application/json');
        */

        $return = $response->withJson($url, 200)
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

            $files = $files['file'];

            foreach ($files AS $file){
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

                $tmpname = $file->file;

                /**
                 * reduzir arquivo
                 */
                $info = getimagesize($tmpname);

                if ($info['mime'] == 'image/jpeg'){
                    $newImage = imagecreatefromjpeg($tmpname);
                }
                elseif ($info['mime'] == 'image/gif'){
                    $newImage = imagecreatefromgif($tmpname);
                }
                elseif ($info['mime'] == 'image/png'){
                    $newImage = imagecreatefrompng($tmpname);
                }
                $reduced = "temp.jpg";
                
                $largura_original = imagesX($newImage);
                $altura_original = imagesY($newImage);
                
                $altura_nova = (int) ($altura_original * 640)/$largura_original;
                $imgReduced = imagecreatetruecolor(640,$altura_nova);

                imagecopyresampled($imgReduced, $newImage, 0, 0, 0, 0, 640, $altura_nova, $largura_original,  $altura_original);

                imagejpeg($imgReduced, $reduced, 100);
 
                /**
                 * cria o objeto do cliente S3, necessita passar as credenciais da AWS
                 */ 
                $clientS3 = S3Client::factory(array(
                    'key' => ACCESS_KEY,
                    'secret' => SECRET_KEY
                ));
                $clientS3->setRegion('sa-east-1');

                /**
                 * método putObject envia os dados pro bucket selecionado
                 */
                $resp = $clientS3->putObject(array(
                    'Bucket' => "images.antsattack.com",
                    'Key'    => $name,
                    'SourceFile' => $reduced,
                ));

                unlink($reduced);
            }
            $ret = 1;

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
}
