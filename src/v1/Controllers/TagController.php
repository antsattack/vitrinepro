<?php
namespace App\v1\Controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use App\Models\Entity\Tag;
use App\Models\Entity\ProductTag;

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

    /**
     * Add
     * @param [type] $request
     * @param [type] $response
     * @param [type] $args
     * @return Response
     */
    public function createTag($request, $response, $args) {
        $params = (object) $request->getParams();
        //$parent = ((int) $params->id_parent > 0) ? (int) $params->id_parent : 0;
        $parent = (int) $args['ctegory_id'];
        /**
         * Pega o Entity Manager do nosso Container
         */
        $entityManager = $this->container->get('em');
        $categoriesRepository = $entityManager->getRepository('App\Models\Entity\Tag');
        $category_parent = $categoriesRepository->find($parent); 
        /**
         * Instância da nossa Entidade preenchida com nossos parametros do post
         */
        $tag = (new Tag())->setName($params->name)
        ->setCategory($category_parent);
        
        /**
         * Registra a criação da categoria
         */
        $logger = $this->container->get('logger');
        $logger->info('Tag Created!', $tag->getValues());

        /**
         * Persiste a entidade no banco de dados
         */
        $tg = $entityManager->merge($tag);
        $entityManager->flush();
        $return = $response->withJson($tg->getId(), 201)
            ->withHeader('Content-type', 'application/json');
        return $return;
    }

    /**
     * Atualiza uma tag
     * @param [type] $request
     * @param [type] $response
     * @param [type] $args
     * @return Response
     */
    public function updateTag($request, $response, $args) {

        $id = (int) $args['id'];
        //$id = (int) $request->getParam('id');

        /**
         * Encontra a tag no Banco
         */ 
        $entityManager = $this->container->get('em');
        $tagsRepository = $entityManager->getRepository('App\Models\Entity\Tag');
        $tag = $tagsRepository->find($id);   

        /**
         * Verifica se existe uma categoria com a ID informada
         */
        if (!$tag) {
            $logger = $this->container->get('logger');
            $logger->warning("Tag {$id} Not Found");
            throw new \Exception("Tag not Found", 404);
        }  

        /**
         * Atualiza e Persiste a tag com os parâmetros recebidos no request
         */
        $tag->setName($request->getParam('name'));

        /**
         * Persiste a entidade no banco de dados
         */
        $entityManager->persist($tag);
        $entityManager->flush();        
        
        $return = $response->withJson($tag, 200)
            ->withHeader('Content-type', 'application/json');
        return $return;
    }

    /**
     * Deleta uma tag
     * @param [type] $request
     * @param [type] $response
     * @param [type] $args
     * @return Response
     */
    public function deleteTag($request, $response, $args) {

        $id = (int) $args['id'];
        //$id = (int) $request->getParam('id');

        /**
         * Encontra a tag no Banco
         */ 
        $entityManager = $this->container->get('em');
        $tagsRepository = $entityManager->getRepository('App\Models\Entity\Tag');
        $tag = $tagsRepository->find($id);   

        /**
         * Verifica se existe uma tag com a ID informada
         */
        if (!$tag) {
            $logger = $this->container->get('logger');
            $logger->warning("Tag {$id} Not Found");
            throw new \Exception("Tag {$id} not Found", 404);
        }

        /**
         * Remove a entidade
         */
        try {
            $entityManager->remove($tag);
            $entityManager->flush();

            $return = $response->withJson(['msg' => "Deleting the tag {$id}"], 204)
                ->withHeader('Content-type', 'application/json');
            return $return;
        } catch (\Exception $e) {
            $return = $response->withJson("Tag não pode ser excluída", 202)
            ->withHeader('Content-type', 'application/json');
            return $return;
        }
    }

    /**
     * Add
     * @param [type] $request
     * @param [type] $response
     * @param [type] $args
     * @return Response
     */
    /*public function addTagsToProduct($request, $response, $args) {
        
        $product_id = (int) $args['product_id'];
        $product_id = ($product_id) ? $product_id : 0;

        $entityManager = $this->container->get('em'); 
        
        $product = (new Product())->setTitle($params->title)
            ->setDescription($params->description)
            ->setCurrency($currency)
            ->setSeller($seller);
        
        
        $return = $response->withJson($tags, 200)
            ->withHeader('Content-type', 'application/json');
        return $return;
    }*/
}
