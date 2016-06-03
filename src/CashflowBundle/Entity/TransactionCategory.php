<?php
/**
 * Created by PhpStorm.
 * User: stolarz
 * Date: 03.06.16
 * Time: 20:21
 */

namespace CashflowBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class TransactionCategory
 * @package CashflowBundle\Entity
 * @author Radosław Stolarski
 * @ORM\Table(name="transaction_categories")
 * @ORM\Entity(repositoryClass="CashflowBundle\Repository\TransactionCategory")
 */
class TransactionCategory
{
    /**
     * @ORM\Id
     * @ORM\Column(
     *     type="integer",
     *     nullable=false,
     *     options={
     *          "unsigned"=true
     *     }
     * )
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\Column(
     *     name="name",
     *     type="string",
     *     length=128,
     *     nullable=false
     * )
     */
    private $name;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return TransactionCategory
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }
}
