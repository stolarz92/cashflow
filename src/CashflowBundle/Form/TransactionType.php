<?php
/**
 * Created by PhpStorm.
 * User: stolarz
 * Date: 01.06.16
 * Time: 17:07
 */

namespace CashflowBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class TransactionType.
 *
 * @package CashflowBundle\Form
 * @author Radosław Stolarski
 */
class TransactionType extends AbstractType
{
    /**
     * Form builder.
     *
     * @param FormBuilderInterface $builder Form builder
     * @param array $options Form options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'id',
            'hidden'
        );
        $builder->add(
            'name',
            'text',
            array(
                'label' => 'Transaction name',
                'required' => true,
                'max_length' => 128,
            )
        );
        $builder->add(
            'description',
            'text',
            array(
                'label' => 'Description',
                'required' => false,
                'max_length' => 500
            )
        );
        $builder->add(
            'amount',
            'money',
            array(
                'label' => 'Amount',
                'required' => true,
                'currency' => 'PLN'
            )
        );
        $builder->add(
            'wallet',
            'entity',
            array(
                'class' => 'CashflowBundle:Wallet',
                /*'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('w')
                        ->orderBy('u.username', 'ASC');
                },*/
                'property' => 'name'
            )
        );
        $builder->add(
            'category',
            'entity',
            array(
                'class' => 'CashflowBundle:TransactionCategory',
                /*'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('w')
                        ->orderBy('u.username', 'ASC');
                },*/
                'property' => 'name'
            )
        );
        $builder->add(
            'save',
            'submit',
            array(
                'label' => 'Save'
            )
        );
    }

    /**
     * Sets default options for form.
     *
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'CashflowBundle\Entity\Transaction'
            )
        );
    }

    /**
     * Getter for form name.
     *
     * @return string Form name
     */
    public function getName()
    {
        return 'transaction_form';
    }
}