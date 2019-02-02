<?php
namespace App\v1\Controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Firebase\JWT\JWT;
use App\Models\Entity\User;

/**
 * Controller de Autenticação
 */
class AuthController {

    /**
     * Container
     * @var object s
     */
   protected $container;
   
   /**
    * Undocumented function
    * @param ContainerInterface $container
    */
   public function __construct($container) {
       $this->container = $container;
   }
   
   /**
    * Invokable Method
    * @param Request $request
    * @param Response $response
    * @param [type] $args
    * @return void
    */
   public function __invoke(Request $request, Response $response, $args) {

    $params = (object) $request->getParams();
    /**
     * JWT Key
     */
    $key = $this->container->get("secretkey");

    $entityManager = $this->container->get('em');
    $usersRepository = $entityManager->getRepository('App\Models\Entity\User');
    $users = $usersRepository->findBy(array('email' => $params->user));
    $user = $users[0];

    if (password_verify($params->passwd, $user->passwd)){
        $token = array(
            "id" => $user->id,
            "name" => $user->name,
            "email" => $user->email
        );

        $jwt = JWT::encode($token, $key, 'HS512');

        return $response->withJson(["auth-jwt" => $jwt], 200)
            ->withHeader('Content-type', 'application/json');
    } else{
        return $response->withStatus(401);
    }

   }
}