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
 * Class TransactionCategoryType.
 *
 * @package CashflowBundle\Form
 * @author Radosław Stolarski
 */
class ChangeRoleType extends AbstractType
{

    /**
     * Form builder.
     *
     * @param FormBuilderInterface $builder Form builder
     * @param array $options Form options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $roles = array(
            'ROLE_USER'        => 'User',
            'ROLE_ADMIN'     => 'Admin',
        );

        $builder->add(
            'role',
            'choice',
            array(
                'label'   => 'Role',
                'choices' => $roles,
                'attr'    => array(
                    'class' => "form-control"
                )
            )
        );

        $builder->add(
            'Zmień rolę',
            'submit',
            array(
                'label' => 'Zapisz',
                'attr' => array('class' => 'btn btn-success btn-save')
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
            array()
        );
    }

    /**
     * Getter for form name.
     *
     * @return string Form name
     */
    public function getName()
    {
        return 'change_role_form';
    }
}
