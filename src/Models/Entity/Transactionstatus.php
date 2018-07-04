<?php



namespace App\Models\Entity;

/**
 * Transactionstatus
 *
 * @Table(name="transactionstatus")
 * @Entity
 */
class Transactionstatus
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
     * @var string|null
     *
     * @Column(name="title", type="string", length=45, nullable=true)
     */
    public $title;

    /**
     * @var string|null
     *
     * @Column(name="order", type="string", length=45, nullable=true)
     */
    public $order;

    /**
     * @var string|null
     *
     * @Column(name="style", type="string", length=45, nullable=true)
     */
    public $style;


}
