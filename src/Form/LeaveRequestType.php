<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\LeaveRequest;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LeaveRequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reason')
            ->add('date_from')
            ->add('date_to');

	if ($options['admin']) {
	    $builder->add('is_approved');
	}
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => LeaveRequest::class,
	    'admin' => false,
        ]);
    }
}
