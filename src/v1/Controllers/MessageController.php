<?php
namespace App\v1\Controllers;

use App\Models\Entity\Message;
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Controller v1 message
 */
class MessageController
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
    public function listMessage($request, $response, $args)
    {
        $entityManager = $this->container->get('em');
        $messageRepository = $entityManager->getRepository('App\Models\Entity\Message');
        $messages = $messageRepository->findAll();
        $return = $response->withJson($messages, 200)
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

        /**
         * Atualiza e Persiste  item com os parâmetros recebidos no request
         */
        $user->setName($request->getParam('name'))
            ->setEmail($request->getParam('email'));

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
