<?php

namespace App\Entity;

use App\Repository\ReponseCandidatRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Type;

#[ORM\Entity(repositoryClass: ReponseCandidatRepository::class)]
class ReponseCandidat
{

    public const INDEX_HOMME = 1;
    public const INDEX_FEMME = 2;

    const TYPE_E = "E";
    const TYPE_I = "I";
    const TYPE_R = "R";
    const TYPE_S = "S";
    const TYPES = [self::TYPE_E, self::TYPE_I, self::TYPE_R, self::TYPE_S];
    const CHAMPS_EXPORT = ["Nom"=>0, "Prenom"=>1, "Nom de jeune fille"=>2, "Niveau scolaire"=>3,
        "Date de naissance"=>4, "Sexe"=>5, "Réservé"=>6, "Autre 1"=>7, "Autre 2"=>8, "Code barre"=>9];
    const NOMBRE_CHAMPS_EXPORT = 10;
    const OPTIONS_SEXE = ['Homme' => self::INDEX_HOMME, 'Femme' => self::INDEX_FEMME];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[ORM\ManyToOne(targetEntity: Session::class, inversedBy: 'reponses_candidats')]
    public Session $session;

    #[All([
        new Type("int")
    ])]
    #[ORM\Column]
    public array $reponses;

    #[Length(max: 15)]
    #[ORM\Column]
    public string $nom;

    #[Length(max: 11)]
    #[ORM\Column]
    public string $prenom;

    #[Length(max: 12)]
    #[ORM\Column]
    public string $nom_jeune_fille;

    #[ORM\ManyToOne(targetEntity: NiveauScolaire::class)]
    public NiveauScolaire $niveau_scolaire;

    #[ORM\Column]
    public DateTime $date_de_naissance;

    #[Choice(choices: [self::INDEX_HOMME, self::INDEX_FEMME])]
    #[ORM\Column]
    public int $sexe;

    #[Length(max: 3)]
    #[ORM\Column]
    public string $reserve;

    #[Length(max: 4)]
    #[ORM\Column]
    public string $autre_1;

    #[Length(max: 6)]
    #[ORM\Column]
    public string $autre_2;

    #[ORM\Column]
    public string $code_barre;

    #[Choice(choices: self::TYPES)]
    #[ORM\Column]
    public string $eirs;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    public ?array $raw;

    /**
     * @param int $id
     * @param Session $session
     * @param array $reponses
     * @param string $nom
     * @param string $prenom
     * @param string $nom_jeune_fille
     * @param NiveauScolaire $niveau_scolaire
     * @param DateTime $date_de_naissance
     * @param int $sexe
     * @param string $reserve
     * @param string $autre_1
     * @param string $autre_2
     * @param string $code_barre
     * @param string $eirs
     * @param array|null $raw
     */
    public function __construct(int $id, Session $session, array $reponses, string $nom, string $prenom, string $nom_jeune_fille, NiveauScolaire $niveau_scolaire, DateTime $date_de_naissance, int $sexe, string $reserve, string $autre_1, string $autre_2, string $code_barre, string $eirs, ?array $raw)
    {
        $this->id = $id;
        $this->session = $session;
        $this->reponses = $reponses;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->nom_jeune_fille = $nom_jeune_fille;
        $this->niveau_scolaire = $niveau_scolaire;
        $this->date_de_naissance = $date_de_naissance;
        $this->sexe = $sexe;
        $this->reserve = $reserve;
        $this->autre_1 = $autre_1;
        $this->autre_2 = $autre_2;
        $this->code_barre = $code_barre;
        $this->eirs = $eirs;
        $this->raw = $raw;
    }


}
