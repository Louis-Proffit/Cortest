<?php

namespace App\Form;

use App\Entity\EchelleEtalonnage;
use App\Form\Data\EchelleEtalonnageGaussienCreer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

class EchelleEtalonnageGaussienType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('mean', NumberType::class, [
                'label' => 'Moyenne',
            ])
            ->add('stdDev', NumberType::class, [
                'label' => 'Ecart Type',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EchelleEtalonnageGaussienCreer::class,
            'bounds_number' => 9, // default number of bounds
        ]);
    }

    public function submit(FormInterface $form, $data, $options)
    {
        $inputData = $form->getData();
        $actualValue = $this->calculateBounds($inputData['mean'], $inputData['stdDev'], $options['bounds_number']);

        $form->setData(['bounds' => $actualValue]);
    }

    private function calculateBounds(float $mean, float $stdDev, int $boundsNumber): array
    {
        $bounds = [];
        for ($i = 1; $i <= $boundsNumber; $i++) {
            $percentile = $i * 100 / ($boundsNumber + 1);
            $value = $this->inverse_normal_cdf($percentile, $mean, $stdDev);
            $bounds[] = $value;
        }
        return $bounds;
    }

    private function inverse_normal_cdf($p, $mu = 0, $sigma = 1) {
        // Constants
        $a1 = -39.6968302866538;
        $a2 = 220.946098424521;
        $a3 = -275.928510446969;
        $a4 = 138.357751867269;
        $a5 = -30.6647980661472;
        $a6 = 2.50662827745924;

        $t = sqrt(-2 * log($p));
        $x = ($t - ($a1 + ($a2 + ($a3 + ($a4 + ($a5 + $a6 * $t ** 2) * $t ** 2) * $t ** 2) * $t ** 2) * $t ** 2)
            / ($a6 + ($a5 + ($a4 + ($a3 + ($a2 + $a1 * $t ** 2) * $t ** 2) * $t ** 2) * $t ** 2) * $t ** 2));
        return $x * $sigma + $mu;
    }
}