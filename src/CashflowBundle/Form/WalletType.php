<?php
/**
 * Created by PhpStorm.
 * User: stolarz
 * Date: 01.06.16
 * Time: 13:40
 */

namespace CashflowBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class WalletType.
 *
 * @package AppBundle\Form
 * @author Tomasz Chojna
 */
class WalletType extends AbstractType
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
            'hidden',
            array('mapped' => false)
        );
        if (isset($options['validation_groups'])
            && count($options['validation_groups'])
            && !in_array('tag-delete', $options['validation_groups'])
        ) {
            $builder->add(
                'name',
                'text',
                array(
                    'label' => 'Nazwa portfela',
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
                'category',
                'entity',
                array(
                    'label' => 'Kategoria',
                    'class' => 'CashflowBundle:WalletCategory',
                    /*'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('w')
                            ->orderBy('u.username', 'ASC');
                    },*/
                    'property' => 'name',
                    'attr'=> array('class'=>'form-control')

                )
            );
            $builder->add(
                'Zapisz',
                'submit',
                array(
                    'label' => 'Zapisz',
                    'attr'=> array('class'=>'btn btn-success btn-save')

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
                'data_class' => 'CashflowBundle\Entity\Wallet',
                'validation_groups' => 'wallet-default',
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
        return 'wallet_form';
    }
}
