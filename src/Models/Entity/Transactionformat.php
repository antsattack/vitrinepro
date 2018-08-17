<?php



namespace App\Models\Entity;

/**
 * Transactionformat
 *
 * @Table(name="transactionformat")
 * @Entity
 */
class Transactionformat
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
     * @Column(name="icon", type="string", length=45, nullable=true)
     */
    public $icon;


}
