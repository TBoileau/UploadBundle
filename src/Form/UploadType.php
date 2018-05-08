<?php

namespace TBoileau\UploadBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class UploadType
 * @package TBoileau\UploadBundle\Form
 * @author Thomas Boileau <t-boileau@email.com>
 */
class UploadType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars["attr"] = ($view->vars["attr"] ?? []) +  [
            "data-max-size" => $options["max_size"],
            "data-mime-types" => json_encode($options["mime_types"]),
            "data-image" => json_encode($options["image"])
        ];
        $view->vars["max_size"] = $options["max_size"];
        $view->vars["image"] = $options["image"];
        $view->vars["mime_types"] = $options["mime_types"];
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired([
            "max_size", "image", "mime_types"
        ]);

        $resolver->setDefaults([
            "max_size" => -1,
            "image" => [
                "min_ratio" => null,
                "max_ratio" => null,
                "min_height" => null,
                "max_height" => null,
                "min_width" => null,
                "max_width" => null
            ],
            "mime_types" => []
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return HiddenType::class;
    }
}