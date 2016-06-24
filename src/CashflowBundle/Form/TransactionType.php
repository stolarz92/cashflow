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
    /**
     * User
     * @var User
     */
    private $user;

    /**
     * TransactionType constructor.
     * @param User $user
     */
    public function __construct(User $user)
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
        $userRoles = $this->user->getRoles();
        $userRole = $userRoles[0];

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
                    'label' => 'Nazwa transakcji',
                    'required' => true,
                    'max_length' => 128,
                    'attr'=> array('class'=>'form-control')

                )
            );
            $builder->add(
                'description',
                'textarea',
                array(
                    'label' => 'Opis',
                    'required' => false,
                    'max_length' => 500,
                    'attr'=> array('class'=>'form-control')

                )
            );
            $builder->add(
                'amount',
                'money',
                array(
                    'label' => 'Kwota',
                    'required' => true,
                    'currency' => 'PLN',
                    'attr'=> array('class'=>'form-control')

                )
            );
            if ($userRole === 'ROLE_ADMIN') {
                $builder->add(
                    'wallet',
                    'entity',
                    array(
                        'label' => 'Wybierz portfel',
                        'class' => 'CashflowBundle:Wallet',
                        'property' => 'name',
                        'attr'=> array('class'=>'form-control')

                    )
                );
            } else {
                $builder->add(
                    'wallet',
                    'entity',
                    array(
                        'label' => 'Wybierz portfel',
                        'class' => 'CashflowBundle:Wallet',
                        'query_builder' => function (Wallet $er) use ($user) {
                            return $er->createQueryBuilder('w')
                                ->where('w.user = :user')
                                ->setParameter('user', $user);
                        },
                        'property' => 'name',
                        'attr'=> array('class'=>'form-control')
                    )
                );
            }
            $builder->add(
                'category',
                'entity',
                array(
                    'label' => 'Kategoria',
                    'class' => 'CashflowBundle:TransactionCategory',
                    'property' => 'name',
                    'attr'=> array('class'=>'form-control')
                )
            );
            $builder->add(
                'date',
                'date',
                array(
                    'label' => 'Data',
                    'widget' => 'single_text',
                    'format' => 'yyyy-MM-dd',
                    'attr' => array('class' => 'js-datepicker form-control'),

                )
            );
            $builder->add(
                'Zapisz',
                'submit',
                array(
                    'label' => 'Zapisz',
                    'attr' => array('class' => 'btn btn-success btn-save')
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
