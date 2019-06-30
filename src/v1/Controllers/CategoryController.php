<?php
namespace App\v1\Controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use App\Models\Entity\Category;

/**
 * Controller v1
 */
class CategoryController {

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
    public function listCategory($request, $response, $args) {
        
        $parent_id = (int) $args['parent_id'];
        $parent_id = ($parent_id) ? $parent_id : 0;

        $entityManager = $this->container->get('em');
        //$where = "p.id = c.id";
        //if ($parent_id){
            $where = "p.id = 0 AND c.id != 0";
        //}
        $dql = "
            SELECT 
            c.id AS id,
            c.name AS name,
            c.description AS description
            FROM 
                App\Models\Entity\Category c
                JOIN c.parent p
            WHERE 
                $where
            ORDER BY
                c.name
        ";
        $query = $entityManager->createQuery($dql);
        $categories_temp = $query->getResult();

        $categories = [];

        $i = 0;
        foreach($categories_temp AS $item){
            $categories[$i]['id'] = (int) $item['id'];
            $categories[$i]['name'] = $item['name'];
            $categories[$i]['description'] = $item['description'];
            $i++;
        }

        $return = $response->withJson($categories, 200)
            ->withHeader('Content-type', 'application/json');
        return $return;
    }

     
    /**
     * Cria uma categoria
     * @param [type] $request
     * @param [type] $response
     * @param [type] $args
     * @return Response
     */
    public function createCategory($request, $response, $args) {
        $params = (object) $request->getParams();
        $parent = ((int) $params->id_parent > 0) ? (int) $params->id_parent : 0;
        /**
         * Pega o Entity Manager do nosso Container
         */
        $entityManager = $this->container->get('em');
        $categoriesRepository = $entityManager->getRepository('App\Models\Entity\Category');
        $category_parent = $categoriesRepository->find($parent); 
        /**
         * Instância da nossa Entidade preenchida com nossos parametros do post
         */
        $category = (new Category())->setName($params->name)
        ->setName($params->name)
        ->setParent($category_parent);
        
        /**
         * Registra a criação da cor
         */
        $logger = $this->container->get('logger');
        $logger->info('Category Created!', $category->getValues());

        /**
         * Persiste a entidade no banco de dados
         */
        $entityManager->merge($category);
        $entityManager->flush();
        $return = $response->withJson($category, 201)
            ->withHeader('Content-type', 'application/json');
        return $return;
    }

    /**
     * Exibe as informações de uma categoria 
     * @param [type] $request
     * @param [type] $response
     * @param [type] $args
     * @return Response
     */
    public function viewCategory($request, $response, $args) {

        $id = (int) $args['id'];

        $entityManager = $this->container->get('em');
        $categoriesRepository = $entityManager->getRepository('App\Models\Entity\Category');
        $category = $categoriesRepository->find($id); 

        /**
         * Verifica se existe uma categoria com a ID informada
         */
        if (!$category) {
            $logger = $this->container->get('logger');
            $logger->warning("Category {$id} Not Found");
            throw new \Exception("Category not Found", 404);
        }    

        $return = $response->withJson($category, 200)
            ->withHeader('Content-type', 'application/json');
        return $return;   
    }

    /**
     * Atualiza uma categoria
     * @param [type] $request
     * @param [type] $response
     * @param [type] $args
     * @return Response
     */
    public function updateCategory($request, $response, $args) {

        $id = (int) $args['id'];
        //$id = (int) $request->getParam('id');

        /**
         * Encontra a categoria no Banco
         */ 
        $entityManager = $this->container->get('em');
        $categoriesRepository = $entityManager->getRepository('App\Models\Entity\Category');
        $category = $categoriesRepository->find($id);   

        /**
         * Verifica se existe uma categoria com a ID informada
         */
        if (!$category) {
            $logger = $this->container->get('logger');
            $logger->warning("Category {$id} Not Found");
            throw new \Exception("Category not Found", 404);
        }  

        /**
         * Atualiza e Persiste a category com os parâmetros recebidos no request
         */
        $category->setName($request->getParam('name'));

        /**
         * Persiste a entidade no banco de dados
         */
        $entityManager->persist($category);
        $entityManager->flush();        
        
        $return = $response->withJson($category, 200)
            ->withHeader('Content-type', 'application/json');
        return $return;
    }

    /**
     * Deleta uma category
     * @param [type] $request
     * @param [type] $response
     * @param [type] $args
     * @return Response
     */
    public function deleteCategory($request, $response, $args) {

        $id = (int) $args['id'];
        //$id = (int) $request->getParam('id');

        /**
         * Encontra a category no Banco
         */ 
        $entityManager = $this->container->get('em');
        $categoriesRepository = $entityManager->getRepository('App\Models\Entity\Category');
        $category = $categoriesRepository->find($id);   

        /**
         * Verifica se existe uma category com a ID informada
         */
        if (!$category) {
            $logger = $this->container->get('logger');
            $logger->warning("Category {$id} Not Found");
            throw new \Exception("Category {$id} not Found", 404);
        }

        /**
         * Remove a entidade
         */
        $entityManager->remove($category);
        $entityManager->flush();

        $return = $response->withJson(['msg' => "Deleting the category {$id}"], 204)
            ->withHeader('Content-type', 'application/json');
        return $return;
    }
}
