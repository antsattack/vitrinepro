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
    public function listMessage($request, $response, $args) {

        $user_id = (int) $args['user_id'];
        $user_id = ($user_id) ? $user_id : 0;

        date_default_timezone_set("America/Sao_Paulo");

        $entityManager = $this->container->get('em');

        $query = $entityManager->createQuery("
            SELECT 
                m.id,
                s.id AS idsender,
                s.name AS sender,
                r.id AS idrecipient,
                r.name AS recipient,
                m.message,
                m.register,
                m.readed
            FROM 
                App\Models\Entity\Message m
                LEFT JOIN m.sender s
                LEFT JOIN m.recipient r
            WHERE
                s.id = $user_id
                OR r.id = $user_id
            ORDER BY
                m.register DESC
        ");
        $items = $query->getResult();

        $listItems = [];
        $array_others = [];
        $list_badge = [];
        $temp = [];
        $badge = 0;
        for($i = 0; $i < count($items); $i++){
            if ($items[$i]["idsender"] != $user_id && $items[$i]["readed"] != 1){
                $list_badge[$items[$i]["idsender"]]++;
            }
            if ($items[$i]["idrecipient"] != $user_id && $items[$i]["readed"] != 1){
                $list_badge[$items[$i]["idrecipient"]]++;
            }
        }
        for($i = 0; $i < count($items); $i++){
            $now = date_create();
            $diff = ($items[$i]["register"])->diff($now);
            $minutes = $diff->i + ($diff->h * 60) + ($diff->d * 1440) + ($diff->m * 44640) + ($diff->y * 525600);
            if($minutes == 0){
                $diff_text = "agora";
            } elseif ($minutes < 60){
                if ($diff->i > 1) {
                    $diff_text = $diff->i . " minutos";
                } else {
                    $diff_text = $diff->i . " minuto";
                }
            } elseif($minutes < 1440) {
                if ($diff->h > 1) {
                    $diff_text = $diff->h . " horas";
                } else {
                    $diff_text = $diff->h . " hora";
                }
            } elseif($minutes < 44640) {
                if ($diff->d > 1) {
                    $diff_text = $diff->d . " dias";
                } else {
                    $diff_text = $diff->d . " dia";
                }
            } elseif($minutes < 525600) {
                if ($diff->m > 1) {
                    $diff_text = $diff->m . " meses";
                } else {
                    $diff_text = $diff->m . " mês";
                }
            } else {
                if ($diff->y > 1) {
                    $diff_text = $diff->y . " anos";
                } else {
                    $diff_text = $diff->y . " ano";
                }
            }
            if (!in_array($items[$i]["idsender"], $array_others) && $items[$i]["idsender"] != $user_id){
                $array_others[] = $items[$i]["idsender"];
                $temp["id"] = $items[$i]["idsender"];
                $temp["name"] = $items[$i]["sender"];
                $temp["register"] = $diff_text;
                $temp["image"] = "http://img.rankforms.com/ssc/".$items[$i]["idsender"].".jpg";
                // $temp["badge"] = $list_badge[$items[$i]["idsender"]];
                $badge ++;
            }
            if (!in_array($items[$i]["idrecipient"], $array_others) && $items[$i]["idrecipient"] != $user_id){
                $array_others[] = $items[$i]["idrecipient"];
                $temp["id"] = $items[$i]["idrecipient"];
                $temp["name"] = $items[$i]["recipient"];
                $temp["register"] = $diff_text;
                $temp["image"] = "http://img.rankforms.com/ssc/".$items[$i]["idrecipient"].".jpg";
                // $temp["badge"] = $list_badge[$items[$i]["idrecipient"]];
                $badge ++;
            }
            if ($temp != []){
                $temp["badge"] = $badge;
                $listItems[] = $temp;
                $badge = 0;
            }
            $temp = [];
        }

        $return = $response->withJson($listItems, 200)
            ->withHeader('Content-type', 'application/json');
        return $return;        
    }

    /**
     * Listagem
     * @param [type] $request
     * @param [type] $response
     * @param [type] $args
     * @return Response
     */
    public function listConversation($request, $response, $args) {

        $user_id = (int) $args['user_id'];
        $user_id = ($user_id) ? $user_id : 0;

        $self_id = (int) $args['self_id'];
        $self_id = ($self_id) ? $self_id : 0;

        date_default_timezone_set("America/Sao_Paulo");

        $entityManager = $this->container->get('em');

        $query = $entityManager->createQuery("
            SELECT 
                m.id,
                s.id AS idsender,
                s.name AS sender,
                r.id AS idrecipient,
                r.name AS recipient,
                m.message,
                m.register,
                m.readed
            FROM 
                App\Models\Entity\Message m
                LEFT JOIN m.sender s
                LEFT JOIN m.recipient r
            WHERE
                (s.id = $user_id AND r.id = $self_id)
            	OR
            	(s.id = $self_id AND r.id = $user_id)
            ORDER BY
                m.register ASC
        ");
        $items = $query->getResult();

        $listItems = [];
        $temp = [];
        
        for($i = 0; $i < count($items); $i++){
            $now = date_create();
            $diff = ($items[$i]["register"])->diff($now);
            $minutes = $diff->i + ($diff->h * 60) + ($diff->d * 1440) + ($diff->m * 44640) + ($diff->y * 525600);
            if($minutes == 0){
                $diff_text = "agora";
            } elseif ($minutes < 60){
                if ($diff->i > 1) {
                    $diff_text = $diff->i . " minutos";
                } else {
                    $diff_text = $diff->i . " minuto";
                }
            } elseif($minutes < 1440) {
                if ($diff->h > 1) {
                    $diff_text = $diff->h . " horas";
                } else {
                    $diff_text = $diff->h . " hora";
                }
            } elseif($minutes < 44640) {
                if ($diff->d > 1) {
                    $diff_text = $diff->d . " dias";
                } else {
                    $diff_text = $diff->d . " dia";
                }
            } elseif($minutes < 525600) {
                if ($diff->m > 1) {
                    $diff_text = $diff->m . " meses";
                } else {
                    $diff_text = $diff->m . " mês";
                }
            } else {
                if ($diff->y > 1) {
                    $diff_text = $diff->y . " anos";
                } else {
                    $diff_text = $diff->y . " ano";
                }
            }

            $temp["id"] = $items[$i]["id"];
            $temp["idsender"] = $items[$i]["idsender"];
            $temp["idrecipient"] = $items[$i]["idrecipient"];
            $temp["sender"] = $items[$i]["sender"];
            $temp["recipient"] = $items[$i]["recipient"];
            $temp["message"] = nl2br($items[$i]["message"]);
            $temp["register"] = $items[$i]["register"];
            $temp["register"] = $diff_text;
            $temp["image"] = "http://img.rankforms.com/ssc/".$items[$i]["idsender"].".jpg";

            if ($self_id === $items[$i]["idsender"]){
                $temp["mine"] = true;
            } else {
                $temp["mine"] = false;
            }

            if ($temp != []){
                $listItems[] = $temp;
            }
            $temp = [];
        }

        $return = $response->withJson($listItems, 200)
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
    public function createMessage($request, $response, $args) {
        $params = (object) $request->getParams();
        
        $entityManager = $this->container->get('em');

        $sender = $entityManager->find('App\Models\Entity\User', $params->sender);
        $recipient = $entityManager->find('App\Models\Entity\User', $params->recipient);
        
        date_default_timezone_set("America/Sao_Paulo");

        $message = (new Message())->setType(1)
            ->setReaded(0)
            ->setRegister(date_create())
            ->setSender($sender)
            ->setRecipient($recipient)  
            ->setMessage($params->message);
        
        $logger = $this->container->get('logger');
        $logger->info('Message Created!', $message->getValues());

        /**
         * Persiste a entidade no banco de dados
         */
        $entityManager->persist($message);
        $entityManager->flush();
        $return = $response->withJson($message->id, 201)
            ->withHeader('Content-type', 'application/json');
        return $return;
    }

}
