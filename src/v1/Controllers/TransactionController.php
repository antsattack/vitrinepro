<?php
namespace App\v1\Controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

//use App\Models\Entity\Transactionformat;

/**
 * Controller v1
 */
class TransactionController {

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
     * Listagem de Transações
     * @param [type] $request
     * @param [type] $response
     * @param [type] $args
     * @return Response
     */
    public function listTransaction($request, $response, $args) {
        $entityManager = $this->container->get('em');
        //$transactionsRepository = $entityManager->getRepository('App\Models\Entity\Transaction');
        //$transactions = $transactionsRepository->findAll();
        $query = $entityManager->createQuery("
            SELECT 
                t.id AS transaction_id,
                t.register AS transaction_register,
                p.id AS product_id,
                p.title AS product_title,
                p.price AS price,
                c.id AS category_id,
                c.name AS category,
                pc.name AS parent_category,
                gpc.name AS granparent_category,
                se.description AS seller,
                se.id AS seller_id,
                bu.description AS buyer,
                bu.id AS buyer_id,
                sh.uf AS from,
                pa.uf AS to
            FROM 
                App\Models\Entity\Shoppingcart s
                JOIN s.transaction t
                JOIN s.product p
                JOIN p.category c
                JOIN c.parent pc
                JOIN pc.parent gpc
                JOIN p.seller se
                JOIN s.user bu
                JOIN se.addressShipping sh
                JOIN bu.addressPayment pa
            ORDER BY
                t.id
        ");
        $transactions_temp = $query->getResult();

        $transactions = [];

        foreach($transactions_temp AS $item){
            $key_transact = "transaction_".$item['transaction_id'];
            $transactions[$key_transact]['transaction_id'] = $item['transaction_id'];
            $transactions[$key_transact]['transaction_register'] = $item['transaction_register'];

            //category
            if ($item['granparent_category'] != $item['parent_category']){
                $category = $item['granparent_category']." > ".$item['parent_category']." > ".$item['category'];
            } elseif ($item['parent_category']!=$item['category']){
                $category = $item['parent_category']." > ".$item['category'];
            } else{
                $category = $item['category'];
            }

            //products
            $transactions[$key_transact]['products'][] = array (
                'product_id' => $item['product_id'],
                'product_title' => $item['product_title'],
                'price' => $item['price'],
                'category_id' => $item['category_id'],
                'category' => $category,
                'seller_id' => $item['seller_id'],
                'seller' => $item['seller'],
                'from' => $item['from']
            );
            
            $transactions[$key_transact]['buyer_id'] = $item['buyer_id'];
            $transactions[$key_transact]['buyer'] = $item['buyer'];
            $transactions[$key_transact]['to'] = $item['to'];
        }

        $return = $response->withJson($transactions, 200)
            ->withHeader('Content-type', 'application/json');
        return $return;
    }
    
    /**
     * Exibe as informações de uma transacao 
     * @param [type] $request
     * @param [type] $response
     * @param [type] $args
     * @return Response
     */
    public function viewTransaction($request, $response, $args) {

        $id = (int) $args['id'];

        $entityManager = $this->container->get('em');
        $transactionsRepository = $entityManager->getRepository('App\Models\Entity\Transaction');
        $transaction = $transactionsRepository->find($id); 

        /**
         * Verifica se existe uma transacao com a ID informada
         */
        if (!$transaction) {
            $logger = $this->container->get('logger');
            $logger->warning("Transaction {$id} Not Found");
            throw new \Exception("Transaction not Found", 404);
        }    

        $return = $response->withJson($transaction, 200)
            ->withHeader('Content-type', 'application/json');
        return $return;   
    }
    
}
