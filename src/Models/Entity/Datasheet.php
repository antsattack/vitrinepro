<?php



namespace App\Models\Entity;


/**
 * @Table(name="datasheet", indexes={@Index(name="fk_product_has_attribute_attribute1_idx", columns={"attribute_id"}), @Index(name="fk_product_has_attribute_product1_idx", columns={"product_id"})})
 * @Entity
 */
class Datasheet
{
    /**
     * @var string|null
     *
     * @Column(name="product_id", type="string", length=255, nullable=true)
     */
    public $product;

    /**
     * @var string|null
     *
     * @Column(name="attribute_id", type="string", length=255, nullable=true)
     */
    public $attribute;

    /**
     * @var string|null
     *
     * @Column(name="value", type="string", length=255, nullable=true)
     */
    public $value;

    public function __construct($product, $attribute)
    {
        $this->product = $product;
        $this->attribute = $attribute;
    }

    /**
     * Get the value of value
     *
     * @return  string|null
     */ 
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set the value of value
     *
     * @param  string|null  $value
     *
     * @return  self
     */ 
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

}