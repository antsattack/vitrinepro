<?php
namespace App\v1\Controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use App\Models\Entity\Color;

/**
 * Controller v1 de cores
 */
class ColorController {

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
     * Listagem de Cores
     * @param [type] $request
     * @param [type] $response
     * @param [type] $args
     * @return Response
     */
    public function listColor($request, $response, $args) {
        $entityManager = $this->container->get('em');
        $colorsRepository = $entityManager->getRepository('App\Models\Entity\Color');
        $colors = $colorsRepository->findAll();
        $return = $response->withJson($colors, 200)
            ->withHeader('Content-type', 'application/json');
        return $return;        
    }
    
    /**
     * Cria uma cor
     * @param [type] $request
     * @param [type] $response
     * @param [type] $args
     * @return Response
     */
    public function createColor($request, $response, $args) {
        $params = (object) $request->getParams();
        /**
         * Pega o Entity Manager do nosso Container
         */
        $entityManager = $this->container->get('em');
        /**
         * Instância da nossa Entidade preenchida com nossos parametros do post
         */
        $color = (new Color())->setName($params->name)
            ->setHexadecimal($params->hexadecimal);
        
        /**
         * Registra a criação da cor
         */
        $logger = $this->container->get('logger');
        $logger->info('Color Created!', $color->getValues());

        /**
         * Persiste a entidade no banco de dados
         */
        $entityManager->persist($color);
        $entityManager->flush();
        $return = $response->withJson($color, 201)
            ->withHeader('Content-type', 'application/json');
        return $return;
    }

    /**
     * Exibe as informações de uma cor 
     * @param [type] $request
     * @param [type] $response
     * @param [type] $args
     * @return Response
     */
    public function viewColor($request, $response, $args) {

        $id = (int) $args['id'];

        $entityManager = $this->container->get('em');
        $colorsRepository = $entityManager->getRepository('App\Models\Entity\Color');
        $color = $colorsRepository->find($id); 

        /**
         * Verifica se existe uma cor com a ID informada
         */
        if (!$color) {
            $logger = $this->container->get('logger');
            $logger->warning("Color {$id} Not Found");
            throw new \Exception("Color not Found", 404);
        }    

        $return = $response->withJson($color, 200)
            ->withHeader('Content-type', 'application/json');
        return $return;   
    }

    /**
     * Atualiza uma cor
     * @param [type] $request
     * @param [type] $response
     * @param [type] $args
     * @return Response
     */
    public function updateColor($request, $response, $args) {

        $id = (int) $args['id'];

        /**
         * Encontra a cor no Banco
         */ 
        $entityManager = $this->container->get('em');
        $colorsRepository = $entityManager->getRepository('App\Models\Entity\Color');
        $color = $colorsRepository->find($id);   

        /**
         * Verifica se existe uma cor com a ID informada
         */
        if (!$color) {
            $logger = $this->container->get('logger');
            $logger->warning("Color {$id} Not Found");
            throw new \Exception("Color not Found", 404);
        }  

        /**
         * Atualiza e Persiste a cor com os parâmetros recebidos no request
         */
        $color->setName($request->getParam('name'))
            ->setHexadecimal($request->getParam('hexadecimal'));

        /**
         * Persiste a entidade no banco de dados
         */
        $entityManager->persist($color);
        $entityManager->flush();        
        
        $return = $response->withJson($color, 200)
            ->withHeader('Content-type', 'application/json');
        return $return;
    }

    /**
     * Deleta uma cor
     * @param [type] $request
     * @param [type] $response
     * @param [type] $args
     * @return Response
     */
    public function deleteColor($request, $response, $args) {

        $id = (int) $args['id'];

        /**
         * Encontra a cor no Banco
         */ 
        $entityManager = $this->container->get('em');
        $colorsRepository = $entityManager->getRepository('App\Models\Entity\Color');
        $color = $colorsRepository->find($id);   

        /**
         * Verifica se existe um livro com a ID informada
         */
        if (!$color) {
            $logger = $this->container->get('logger');
            $logger->warning("Color {$id} Not Found");
            throw new \Exception("Color not Found", 404);
        }

        /**
         * Remove a entidade
         */
        $entityManager->remove($color);
        $entityManager->flush();

        $return = $response->withJson(['msg' => "Deleting the color {$id}"], 204)
            ->withHeader('Content-type', 'application/json');
        return $return;
    }
    
}
