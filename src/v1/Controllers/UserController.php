<?php
namespace App\v1\Controllers;

use App\Models\Entity\User;
use Firebase\JWT\JWT;
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Controller v1 de cores
 */
class UserController
{

    /**
     * Container Class
     * @var [object]
     */
    private $container;

    /**
     * Undocumented function
     * @param [object] $container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Listagem
     * @param [type] $request
     * @param [type] $response
     * @param [type] $args
     * @return Response
     */
    public function listUser($request, $response, $args)
    {
        $entityManager = $this->container->get('em');
        $usersRepository = $entityManager->getRepository('App\Models\Entity\User');
        $users = $usersRepository->findAll();
        $return = $response->withJson($users, 200)
            ->withHeader('Content-type', 'application/json');
        return $return;
    }

    /**
     * Listagem favoritos
     * @param [type] $request
     * @param [type] $response
     * @param [type] $args
     * @return Response
     */
    public function listFavorites($request, $response, $args)
    {
        $id = (int) $args['id'];
        $id = ($id) ? $id : 0;

        $entityManager = $this->container->get('em');
        $usersRepository = $entityManager->getRepository('App\Models\Entity\User');
        $user = $usersRepository->find($id);

        $query = $entityManager->createQuery("
            SELECT 
                p.id,
                p.title,
                p.description,
                CONCAT('R$', p.price) AS price,
                CONCAT('https://s3-sa-east-1.amazonaws.com/img.rankforms.com/ssc/', p.id, '_', i.id, '.jpg') AS image
            FROM 
                App\Models\Entity\Product p
                JOIN p.user u
                LEFT JOIN App\Models\Entity\Image i WITH i.product = p AND i.main = 1
            WHERE
                u.id = $id
        ");
        $favorites = $query->getResult();

        $return = $response->withJson($favorites, 200)
            ->withHeader('Content-type', 'application/json');
        return $return;
    }

    /**
     * Listagem vendedores
     * @param [type] $request
     * @param [type] $response
     * @param [type] $args
     * @return Response
     */
    public function listSellers($request, $response, $args)
    {
        $entityManager = $this->container->get('em');
        $usersRepository = $entityManager->getRepository('App\Models\Entity\User');
        $users = $usersRepository->findBy(array('exclusion'=> null, 'plan'=> 1));
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
    public function createUser($request, $response, $args)
    {
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
        if ($params->password == $params->passwdagain) {
            $passwdsEquals = true;
        } else {
            $returnVal["error"][] = "Senhas não coincidem.";
        }
        ////////
        $query = $entityManager->createQuery("SELECT 1 FROM App\Models\Entity\User u WHERE u.email = '$params->email'");
        $usersResult = $query->getResult();
        if (count($usersResult) == 0) {
            $emailAlreadyRegistered = false;
        } else {
            $returnVal["error"][] = "E-mail já cadastrado.";
        }
        /////////
        if (filter_var($params->email, FILTER_VALIDATE_EMAIL)) {
            $validEmail = true;
        } else {
            $returnVal["error"][] = "E-mail inválido.";
        }
        /////////
        if (strlen($params->name) > 0) {
            $hasName = true;
        } else {
            $returnVal["error"][] = "Um nome deve ser preenchido.";
        }
        /////////
        $validation = (object) $returnVal;
        if ($passwdsEquals && !$emailAlreadyRegistered && $validEmail && $hasName) {
            /**
             * Instância da nossa Entidade preenchida com nossos parametros do post
             */
            $user = (new User())->setName($params->name)
                ->setEmail($params->email)
                ->setPasswd(password_hash($params->password, PASSWORD_DEFAULT));
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

            $token = array(
                "id" => $user->id,
                "name" => $user->name,
                "email" => $user->email,
            );

            $jwt = JWT::encode($token, $key, 'HS512');

            return $response->withJson(["token" => $jwt], 200)
                ->withHeader('Content-type', 'application/json');
        } else {
            return $response->withJson($validation, 201)
                ->withHeader('Content-type', 'application/json');
        }
    }

    /**
     * Retorna usuário pelo email
     * @param [type] $request
     * @param [type] $response
     * @param [type] $args
     * @return Response
     */
    public function getUserByEmail($request, $response, $args)
    {

        $email = (strlen($args['email'])) ? $args['email'] : "";

        $entityManager = $this->container->get('em');
        //$usersRepository = $entityManager->getRepository('App\Models\Entity\User');
        //$user = $usersRepository->find($id);
        $query = $entityManager->createQuery("
            SELECT
                u.id,
                u.name,
                u.email
            FROM
                App\Models\Entity\User u
            WHERE
                u.email = '$email'
        ");
        $userResponse = $query->getResult();

        /**
         * Verifica se existe um item com a ID informada
         */
        if (!count($userResponse)) {
            $logger = $this->container->get('logger');
            $logger->warning("User {$email} Not Found");
            throw new \Exception("User not Found", 404);
        }

        $return = $response->withJson($userResponse, 200)
            ->withHeader('Content-type', 'application/json');
        return $return;
    }

    /**
     * Exibe as informações de um item
     * @param [type] $request
     * @param [type] $response
     * @param [type] $args
     * @return Response
     */
    public function viewUser($request, $response, $args)
    {

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

        $ret = $user;
        $ret->passwd = null;

        $return = $response->withJson($ret, 200)
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
    public function updateUser($request, $response, $args)
    {

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

        if ($user->indication != $request->getParam('indication') && strlen($user->indication)) {
            throw new \Exception("Indicação já realizada", 409);
        }

        /**
         * Atualiza e Persiste  item com os parâmetros recebidos no request
         */
        $user->setName($request->getParam('name'))
            ->setIndication($request->getParam('indication'));

        /**
         * Persiste a entidade no banco de dados
         */
        $entityManager->persist($user);
        $entityManager->flush();

        $ret = $user;
        $ret->passwd = null;

        $return = $response->withJson($ret, 200)
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
    public function deleteUser($request, $response, $args)
    {

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
