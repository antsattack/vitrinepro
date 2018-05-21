<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Paymentdata
 *
 * @ORM\Table(name="paymentdata", indexes={@ORM\Index(name="fk_payment_data_user2_idx", columns={"user_id"})})
 * @ORM\Entity
 */
class Paymentdata
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int|null
     *
     * @ORM\Column(name="creditcard_number", type="integer", nullable=true)
     */
    private $creditcardNumber;

    /**
     * @var int|null
     *
     * @ORM\Column(name="security_code", type="integer", nullable=true)
     */
    private $securityCode;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="expiring", type="datetime", nullable=true)
     */
    private $expiring;

    /**
     * @var int|null
     *
     * @ORM\Column(name="paypal", type="integer", nullable=true)
     */
    private $paypal;

    /**
     * @var int|null
     *
     * @ORM\Column(name="pagseguro", type="integer", nullable=true)
     */
    private $pagseguro;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="register", type="datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $register = 'CURRENT_TIMESTAMP';

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="exclusion", type="datetime", nullable=true)
     */
    private $exclusion;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;


}
