<?php



namespace App\Models\Entity;

/**
 * Paymentdata
 *
 * @Table(name="paymentdata", indexes={@Index(name="fk_payment_data_user2_idx", columns={"user_id"})})
 * @Entity
 */
class Paymentdata
{
    /**
     * @var int
     *
     * @Column(name="id", type="integer", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    public $id;

    /**
     * @var int|null
     *
     * @Column(name="creditcard_number", type="integer", nullable=true)
     */
    public $creditcardNumber;

    /**
     * @var int|null
     *
     * @Column(name="security_code", type="integer", nullable=true)
     */
    public $securityCode;

    /**
     * @var \DateTime|null
     *
     * @Column(name="expiring", type="datetime", nullable=true)
     */
    public $expiring;

    /**
     * @var int|null
     *
     * @Column(name="paypal", type="integer", nullable=true)
     */
    public $paypal;

    /**
     * @var int|null
     *
     * @Column(name="pagseguro", type="integer", nullable=true)
     */
    public $pagseguro;

    /**
     * @var \DateTime|null
     *
     * @Column(name="register", type="datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP"})
     */
    public $register = 'CURRENT_TIMESTAMP';

    /**
     * @var \DateTime|null
     *
     * @Column(name="exclusion", type="datetime", nullable=true)
     */
    public $exclusion;

    /**
     * @var \App\Models\Entity\User
     *
     * @ManyToOne(targetEntity="App\Models\Entity\User")
     * @JoinColumns({
     *   @JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    public $user;


}
