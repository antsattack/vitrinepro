<?php
namespace App\v1\Controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use App\Models\Entity\User;

/**
 * Controller v1 de cores
 */
class UserController {

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
    public function listUser($request, $response, $args) {
        $entityManager = $this->container->get('em');
        $usersRepository = $entityManager->getRepository('App\Models\Entity\User');
        $users = $usersRepository->findAll();
        $return = $response->withJson($users, 200)
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
    public function createUser($request, $response, $args) {
        $params = (object) $request->getParams();
        /**
         * Pega o Entity Manager do nosso Container
         */
        $entityManager = $this->container->get('em');
        /**
         * Realiza verificações
         */
        $passwdsEquals = false;
        $emailAlreadyRegistered = true;
        $validEmail = false;
        $hasName = false;
        $returnVal = [];
        /////////
        if ($params->password == $params->passwdagain){
            $passwdsEquals = true;
        } else{
            $returnVal["error"][] = "Senhas não coincidem.";
        }
        ////////
        $query = $entityManager->createQuery("SELECT 1 FROM App\Models\Entity\User u WHERE u.email = '$params->email'");
        $usersResult = $query->getResult();
        if (count($usersResult)==0){
            $emailAlreadyRegistered = false;
        } else{
            $returnVal["error"][] = "E-mail já cadastrado.";
        }
        /////////
        if (filter_var($params->email, FILTER_VALIDATE_EMAIL)){
            $validEmail = true;
        } else{
            $returnVal["error"][] = "E-mail inválido.";
        }
        /////////
        if (strlen($params->name)>0){
            $hasName = true;
        } else{
            $returnVal["error"][] = "Um nome deve ser preenchido.";
        }
        /////////
        $validation = (object) $returnVal;
        if($passwdsEquals && !$emailAlreadyRegistered && $validEmail && $hasName){
            /**
             * Instância da nossa Entidade preenchida com nossos parametros do post
             */
            $user = (new User())->setName($params->name)
                ->setEmail($params->email)
                ->setPassword(password_hash($params->password, PASSWORD_DEFAULT));
            /**
             * Registra a criação
             */
            $logger = $this->container->get('logger');
            $logger->info('User Created!', $user->getValues());

            /**
             * Persiste a entidade no banco de dados
             */
            $entityManager->persist($user);
            $entityManager->flush();
            return $response->withJson($user, 201)
                ->withHeader('Content-type', 'application/json');
        } else {
            return $response->withJson($validation, 201)
                ->withHeader('Content-type', 'application/json');
        }
    }

    /**
     * Exibe as informações de um item
     * @param [type] $request
     * @param [type] $response
     * @param [type] $args
     * @return Response
     */
    public function viewUser($request, $response, $args) {

        $id = (int) $args['id'];

        $entityManager = $this->container->get('em');
        $usersRepository = $entityManager->getRepository('App\Models\Entity\User');
        $user = $usersRepository->find($id); 

        /**
         * Verifica se existe um item com a ID informada
         */
        if (!$user) {
            $logger = $this->container->get('logger');
            $logger->warning("User {$id} Not Found");
            throw new \Exception("User not Found", 404);
        }    

        $return = $response->withJson($user, 200)
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
    public function updateUser($request, $response, $args) {

        $id = (int) $args['id'];

        /**
         * Encontra item no Banco
         */ 
        $entityManager = $this->container->get('em');
        $usersRepository = $entityManager->getRepository('App\Models\Entity\User');
        $user = $usersRepository->find($id);   

        /**
         * Verifica se existe um item com a ID informada
         */
        if (!$user) {
            $logger = $this->container->get('logger');
            $logger->warning("User {$id} Not Found");
            throw new \Exception("User not Found", 404);
        }  

        /**
         * Atualiza e Persiste  item com os parâmetros recebidos no request
         */
        $user->setName($request->getParam('name'))
            ->setHexadecimal($request->getParam('hexadecimal'));

        /**
         * Persiste a entidade no banco de dados
         */
        $entityManager->persist($user);
        $entityManager->flush();        
        
        $return = $response->withJson($user, 200)
            ->withHeader('Content-type', 'application/json');
        return $return;
    }

    /**
     * Deleta um item
     * @param [type] $request
     * @param [type] $response
     * @param [type] $args
     * @return Response
     */
    public function deleteUser($request, $response, $args) {

        $id = (int) $args['id'];

        /**
         * Encontra item no Banco
         */ 
        $entityManager = $this->container->get('em');
        $usersRepository = $entityManager->getRepository('App\Models\Entity\User');
        $user = $usersRepository->find($id);   

        /**
         * Verifica se existe um livro com a ID informada
         */
        if (!$user) {
            $logger = $this->container->get('logger');
            $logger->warning("User {$id} Not Found");
            throw new \Exception("User not Found", 404);
        }

        /**
         * Remove a entidade
         */
        $entityManager->remove($user);
        $entityManager->flush();

        $return = $response->withJson(['msg' => "Deleting the user {$id}"], 204)
            ->withHeader('Content-type', 'application/json');
        return $return;
    }
    
}
