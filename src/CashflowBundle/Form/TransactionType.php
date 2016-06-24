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
use CashflowBundle\Repository\Wallet;
use CashflowBundle\Entity\User;

/**
 * Class TransactionType.
 *
 * @package CashflowBundle\Form
 * @author Radosław Stolarski
 */
class TransactionType extends AbstractType
{
    private $user;

    public function __construct (User $user)
    {
        $this->user = $user;
    }
    /**
     * Form builder.
     *
     * @param FormBuilderInterface $builder Form builder
     * @param array $options Form options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $this->user;

        $builder->add(
            'id',
            'hidden',
            array('mapped' => false)
        );
        if (isset($options['validation_groups'])
            && count($options['validation_groups'])
            && !in_array('transaction-delete', $options['validation_groups'])
        ) {
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
                    'query_builder' => function (Wallet $er) use ($user) {
                        return $er->createQueryBuilder('w')
                            ->where('w.user = :user')
                            ->setParameter('user', $user);
                    },
                    'property' => 'name'
                )
            );
            $builder->add(
                'category',
                'entity',
                array(
                    'class' => 'CashflowBundle:TransactionCategory',
                    'property' => 'name'
                )
            );
            $builder->add(
                'date',
                'date',
                array(
                    'widget' => 'single_text',
                    'format' => 'yyyy-MM-dd',
                    'attr' => array('class' => 'js-datepicker')
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
                'data_class' => 'CashflowBundle\Entity\Transaction',
                'validation_groups' => 'transaction-default'
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