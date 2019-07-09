<?php
namespace App\v1\Controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use App\Models\Entity\Attribute;

/**
 * Controller v1
 */
class AttributeController {

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
    public function listAttribute($request, $response, $args) {
        
        $category_id = (int) $args['category_id'];
        $category_id = ($category_id) ? $category_id : 0;

        $entityManager = $this->container->get('em');
        
        $dql = "
            SELECT 
                a.id AS id,
                a.name AS name,
                a.description AS description
            FROM 
                App\Models\Entity\Attribute a
                JOIN b.category c
            WHERE 
                c.id = $category_id
            ORDER BY
                a.name
        ";
        $query = $entityManager->createQuery($dql);
        $attributes_temp = $query->getResult();

        $attributes = [];

        $i = 0;
        foreach($attributes_temp AS $item){
            $attributes[$i]['id'] = (int) $item['id'];
            $attributes[$i]['name'] = $item['name'];
            $attributes[$i]['description'] = $item['description'];
            $i++;
        }

        $return = $response->withJson($attributes, 200)
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
    public function createAttribute($request, $response, $args) {
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
        $attribute = (new Attribute())->setName($params->name)
        ->setDescription($params->description)
        ->setCategory($category_parent);
        
        /**
         * Registra a criação da entidade
         */
        $logger = $this->container->get('logger');
        $logger->info('Attribute Created!', $attribute->getValues());

        /**
         * Persiste a entidade no banco de dados
         */
        $attr = $entityManager->merge($attribute);
        $entityManager->flush();
        $return = $response->withJson($attr->getId(), 201)
            ->withHeader('Content-type', 'application/json');
        return $return;
    }

    /**
     * Atualiza uma attribute
     * @param [type] $request
     * @param [type] $response
     * @param [type] $args
     * @return Response
     */
    public function updateAttribute($request, $response, $args) {

        $id = (int) $args['id'];
        //$id = (int) $request->getParam('id');

        /**
         * Encontra a attribute no Banco
         */ 
        $entityManager = $this->container->get('em');
        $attributesRepository = $entityManager->getRepository('App\Models\Entity\Attribute');
        $attribute = $attributesRepository->find($id);   

        /**
         * Verifica se existe uma attribute com a ID informada
         */
        if (!$attribute) {
            $logger = $this->container->get('logger');
            $logger->warning("Attribute {$id} Not Found");
            throw new \Exception("Attribute not Found", 404);
        }  

        /**
         * Atualiza e Persiste a attribute com os parâmetros recebidos no request
         */
        $attribute->setName($request->getParam('name'))
        ->setDescription($request->getParam('description'));

        /**
         * Persiste a entidade no banco de dados
         */
        $entityManager->persist($attribute);
        $entityManager->flush();        
        
        $return = $response->withJson($attribute, 200)
            ->withHeader('Content-type', 'application/json');
        return $return;
    }

    /**
     * Deleta uma attribute
     * @param [type] $request
     * @param [type] $response
     * @param [type] $args
     * @return Response
     */
    public function deleteAttribute($request, $response, $args) {

        $id = (int) $args['id'];
        //$id = (int) $request->getParam('id');

        /**
         * Encontra a attribute no Banco
         */ 
        $entityManager = $this->container->get('em');
        $attributesRepository = $entityManager->getRepository('App\Models\Entity\Attribute');
        $attribute = $attributesRepository->find($id);   

        /**
         * Verifica se existe uma attribute com a ID informada
         */
        if (!$attribute) {
            $logger = $this->container->get('logger');
            $logger->warning("Attribute {$id} Not Found");
            throw new \Exception("Attribute {$id} not Found", 404);
        }

        /**
         * Remove a entidade
         */
        try {
            $entityManager->remove($attribute);
            $entityManager->flush();

            $return = $response->withJson(['msg' => "Deleting the attribute {$id}"], 204)
                ->withHeader('Content-type', 'application/json');
            return $return;
        } catch (\Exception $e) {
            $return = $response->withJson("Atributo não pode ser excluído", 202)
            ->withHeader('Content-type', 'application/json');
            return $return;
        }
    }
}
