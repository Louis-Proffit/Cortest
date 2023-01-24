<?php

namespace App\Core\Correcteur\ExpressionLanguage\Environment;

use App\Core\Correcteur\ExpressionLanguage\CortestExpressionLanguage;
use ArrayAccess;
use ArrayObject;
use BadMethodCallException;
use Exception;

class CortestExpressionEnvironment implements ArrayAccess
{
    const REPONSES = "reponses";
    const COMPILE_ENVIRONMENT = array(self::REPONSES);

    private CortestExpressionLanguage $cortest_expression_language;
    private array $scores;
    private array $reponses;
    private array $running;
    private array $echelles;

    /**
     * @param string[] $echelles
     * @param int[] $reponses
     * @param CortestExpressionLanguage $cortest_expression_language
     */
    public function __construct(array $echelles, array $reponses, CortestExpressionLanguage $cortest_expression_language)
    {
        $this->cortest_expression_language = $cortest_expression_language;

        $this->reponses = $reponses;
        $this->scores = [];
        $this->echelles = $echelles;
        $this->running = array();

        foreach ($echelles as $nom => $_) {
            $this->running[$nom] = false;
        }
    }

    public function get_score(string $nom_echelle): float
    {
        if (!key_exists($nom_echelle, $this->scores)) {
            if ($this->running[$nom_echelle]) {
                throw new BadMethodCallException("Référence circulaire");
            } else {
                $this->running[$nom_echelle] = true;
                $result = $this->cortest_expression_language->evaluateCortest($this->echelles[$nom_echelle], $this);
                $this->scores[$nom_echelle] = $result;
                $this->running[$nom_echelle] = false;
            }
        }

        return $this->scores[$nom_echelle];
    }

    public function &offsetGet(mixed $key): mixed
    {
        if (strcmp($key, "reponses") == 0) {
            return $this->reponses;
        }

        $split = explode("_____", $key);

        if (count($split) == 2) {
            $nom_echelle = $split[1];

            if (strcmp($split[0], "echelle") == 0) {
                $score = $this->get_score($nom_echelle);
                return $score;
            }
        }

        throw new Exception();
    }

    public function offsetExists(mixed $offset): bool
    {
        if (strcmp($offset, "reponses")) {
            return true;
        } else if (str_starts_with("echelle_____", $offset)) {
            return true;
        }
        return false;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        // TODO: Implement offsetSet() method.
    }

    public function offsetUnset(mixed $offset): void
    {
        // TODO: Implement offsetUnset() method.
    }

    public function get_names()
    {
        $result = ["reponses"];
        foreach ($this->echelles as $echelle => $expression) {
            $result[] = "echelle_____" . $echelle;
        }

        return $result;
    }

    public function get_reponses(){
        return array('reponses' => $this->reponses);
    }
}