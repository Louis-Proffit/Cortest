<?php

namespace App\Constraint;

use App\Core\ScoreBrut\ExpressionLanguage\CortestExpressionLanguage;
use App\Core\ScoreBrut\ExpressionLanguage\Environment\CortestCompilationEnvironment;
use App\Entity\EchelleCorrecteur;
use Symfony\Component\ExpressionLanguage\SyntaxError;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use TypeError;

class CompilableValidator extends ConstraintValidator
{

    public function __construct(
        private readonly CortestExpressionLanguage $cortest_expression_language
    )
    {
    }

    /**
     * @param string $value
     * @param Constraint $constraint
     * @return void
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof Compilable) {
            throw new UnexpectedTypeException($constraint, Compilable::class);
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, "string");
        }

        /** @var EchelleCorrecteur $echelle */
        $echelle = $this->context->getObject();

        if (!$echelle instanceof EchelleCorrecteur) {
            throw new UnexpectedTypeException($echelle, EchelleCorrecteur::class);
        }

        $compile_environment = new CortestCompilationEnvironment(types: $echelle->correcteur->get_echelle_types());

        try {
            $this->cortest_expression_language->cortestCompile(
                expression: $value,
                type: $echelle->echelle->type,
                environment: $compile_environment);
        } catch (SyntaxError|TypeError $e) {
            $this->context->addViolation("Erreur de syntaxe " . $e->getMessage());
        }
    }
}
