<?php



namespace App\Models\Entity;

/**
 * Transaction
 *
 * @Table(name="transaction", indexes={@Index(name="fk_transaction_transactionstatus1_idx", columns={"transactionstatus_id"}), @Index(name="fk_transaction_transactionformat1_idx", columns={"transactionformat_id"})})
 * @Entity
 */
class Transaction
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
     * @var \DateTime|null
     *
     * @Column(name="register", type="string", nullable=true, options={"default"="CURRENT_TIMESTAMP"})
     */
    public $register = 'CURRENT_TIMESTAMP';

    /**
     * @var \App\Models\Entity\Transactionformat
     *
     * @ManyToOne(targetEntity="App\Models\Entity\Transactionformat")
     * @JoinColumns({
     *   @JoinColumn(name="transactionformat_id", referencedColumnName="id")
     * })
     */
    public $transactionformat;

    /**
     * @var \App\Models\Entity\Transactionstatus
     *
     * @ManyToOne(targetEntity="App\Models\Entity\Transactionstatus")
     * @JoinColumns({
     *   @JoinColumn(name="transactionstatus_id", referencedColumnName="id")
     * })
     */
    public $transactionstatus;



    /**
     * Get the value of id
     *
     * @return  int
     */ 
    public function getId()
    {
        return $this->id;
    }
}
