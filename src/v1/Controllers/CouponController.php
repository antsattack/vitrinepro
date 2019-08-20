<?php
namespace App\v1\Controllers;

use App\Models\Entity\Coupon;
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Controller v1
 */
class CouponController
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
     * Listagem de Cupons
     * @param [type] $request
     * @param [type] $response
     * @param [type] $args
     * @return Response
     */
    public function listCoupons($request, $response, $args)
    {
        $user_id = (int) $args['user_id'];

        $entityManager = $this->container->get('em');
        $couponsRepository = $entityManager->getRepository('App\Models\Entity\Coupon');
        $coupons = $couponsRepository->findBy(array('user' => $user_id));
        $return = $response->withJson($coupons, 200)
            ->withHeader('Content-type', 'application/json');
        return $return;
    }

    /**
     * Get coupon
     * @param [type] $request
     * @param [type] $response
     * @param [type] $args
     * @return Response
     */
    public function getCoupon($request, $response, $args)
    {
        $id = (int) $args['id'];

        $entityManager = $this->container->get('em');
        $usersRepository = $entityManager->getRepository('App\Models\Entity\User');
        $user = $usersRepository->find($id);

        $r = $user->email;

        $r = strtolower($r);

        $t = "";
        for ($i = 1; $i <= strlen($r); $i++) {
            $letter = $r[strlen($r) - $i] . '';
            $t .= ++$letter;
        }

        $orig = array("sc.npd.", "npd.", "ufo.", "@", ".");
        $dest = array("SHOPCAS", "SHOP", "CASA", "Z", "E");

        $t = str_replace($orig, $dest, $t);

        $return = $response->withJson($t, 200)
            ->withHeader('Content-type', 'application/json');
        return $return;
    }

    /**
     * Get coupon user
     * @param [type] $request
     * @param [type] $response
     * @param [type] $args
     * @return Response
     */
    public function getCouponUser($request, $response, $args)
    {
        $coupon = $args['coupon'];

        $r = $coupon . '';

        $t = "";
        for ($i = 1; $i <= strlen($r); $i++) {
            $letter = $r[strlen($r) - $i] . '';
            $t .= chr(ord($letter) - 1);
        }

        $orig = array("R@BONGR", "ONGR", "@R@B", "Y", "D");
        $dest = array(".com.br", ".com", ".net", "@", ".");

        $t = str_replace($orig, $dest, $t);

        $entityManager = $this->container->get('em');
        $query = $entityManager->createQuery("
            SELECT
                u.id,
                u.name
            FROM
                App\Models\Entity\User u
            WHERE
                u.email = '$t'
        ");
        $user = $query->getResult();

        $return = $response->withJson($user[0], 200)
            ->withHeader('Content-type', 'application/json');
        return $return;
    }

    /**
     * Cria um cupom de indicacao
     * @param [type] $request
     * @param [type] $response
     * @param [type] $args
     * @return Response
     */
    public function createIndicationCoupon($request, $response, $args)
    {
        $params = (object) $request->getParams();
        $indicator = $params->indicator;
        $indicated = (int) $args['user_id'];

        $title = "Prêmio por indicação";

        $entityManager = $this->container->get('em');
        $usersRepository = $entityManager->getRepository('App\Models\Entity\User');
        $userIndicated = $usersRepository->find($indicated);
        $userIndicator = $usersRepository->find($indicator);

        $description = $userIndicated->name . " foi uma das pessoas indicadas por você a realizar seu cadastro no Shopping na Sua Casa. Essa indicação rendeu esse cupom!";
        /**
         * Pega o Entity Manager do nosso Container
         */
        $entityManager = $this->container->get('em');
        /**
         * Instância da nossa Entidade preenchida com nossos parametros do post
         */
        $coupon = (new Coupon())->setTitle($title)
            ->setDescription($description)
            ->setValue('5,0')
            ->setUser($userIndicator);

        /**
         * Registra a criação do coupon
         */
        $logger = $this->container->get('logger');
        $logger->info('Coupon Created!', $coupon->getValues());

        /**
         * Persiste a entidade no banco de dados
         */
        $entityManager->persist($coupon);
        $entityManager->flush();
        $return = $response->withJson($coupon, 201)
            ->withHeader('Content-type', 'application/json');
        return $return;
    }
}
