<?php
namespace AppBundle\Form;

use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PostType extends AbstractType {
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->add('title',TextType::class, array("label" => "Title"));
		$builder->add('content', CKEditorType::class,
			array(
				'config_name' => 'user_config',
			)
		);
		$builder->add('enabled', null, array("label" => "Enabled"));
		$builder->add('comment', null, array("label" => "Enabled comments"));
		$builder->add('tags', null, array("label" => "Tags (Keywords)"));
		$builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
			$article = $event->getData();
			$form = $event->getForm();
			if ($article and null !== $article->getId()) {
				$form->add("file", null, array("label" => "", "required" => false));
				$form->add('save', SubmitType::class, array("label" => "SAVE"));

			} else {
				$form->add("file", null, array("label" => "", "required" => true));
				$form->add('save', SubmitType::class, array("label" => "ADD"));
			}
		});
	}
    public function getBlockPrefix()
    {
        return 'post';
    }
}
?>