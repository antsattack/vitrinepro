<?php



namespace App\Models\Entity;

/**
 * Shoppingcart
 *
 * @Table(name="shoppingcart", indexes={@Index(name="fk_product_has_user_user1_idx", columns={"user_id"}), @Index(name="fk_product_has_user_product1_idx", columns={"product_id"}), @Index(name="fk_shoppingcart_transaction1_idx", columns={"transaction_id"})})
 * @Entity
 */
class Shoppingcart
{
    /**
     * @var \App\Models\Entity\Product
     *
     * @Id
     * @GeneratedValue(strategy="NONE")
     * @OneToOne(targetEntity="App\Models\Entity\Product")
     * @JoinColumns({
     *   @JoinColumn(name="product_id", referencedColumnName="id")
     * })
     */
    public $product;

    /**
     * @var \App\Models\Entity\User
     *
     * @Id
     * @GeneratedValue(strategy="NONE")
     * @OneToOne(targetEntity="App\Models\Entity\User")
     * @JoinColumns({
     *   @JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    public $user;

    /**
     * @var \App\Models\Entity\Transaction
     *
     * @ManyToOne(targetEntity="App\Models\Entity\Transaction")
     * @JoinColumns({
     *   @JoinColumn(name="transaction_id", referencedColumnName="id")
     * })
     */
    public $transaction;



    /**
     * Get the value of product
     *
     * @return  \Models\Entity\Product
     */ 
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Get the value of user
     *
     * @return  \Models\Entity\User
     */ 
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Get the value of transaction
     *
     * @return  \Models\Entity\Transaction
     */ 
    public function getTransaction()
    {
        return $this->transaction;
    }
}
