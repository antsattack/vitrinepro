<?php
namespace App\v1\Controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use App\Models\Entity\Advisor;

class AdvisorController {

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
    public function listAdvisor($request, $response, $args) {
        $id = (int) $args['id'];
        if ($id==1){
            $advisors = array(
                'step1'=>'Crie um novo produto adicionando primeiramento as informações mais básicas. (* campos obrigatórios)',
                'step2'=>'Adicione fotos ao seu produto para que as pessoas possam verificar o real estado de conservação.',
                'step3'=>'Classifique seu produto para que ele possa ser mais facilmente encontrado pela busca.',
                'step4'=>'Selecione alguns rótulos que tenham relevância para identificar seu produto.',
                'step5'=>'Atribua as principais características ao seu produto.',
                'step6'=>'Pronto... Produto cadastro! Não esqueça de liberar sua visualização!'
            );
        } else{
            $advisors = array();
        }
        $return = $response->withJson($advisors, 200)
            ->withHeader('Content-type', 'application/json');
        return $return;
    }
}
