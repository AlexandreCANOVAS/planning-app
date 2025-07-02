<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FichePaie extends Model
{
    use HasFactory;

    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'fiches_paie';

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employe_id',
        'mois',
        'salaire_base',
        'heures_normales',
        'heures_sup_25',
        'heures_sup_50',
        'heures_nuit',
        'heures_dimanche',
        'heures_jours_feries',
        'montant_heures_normales',
        'montant_heures_sup_25',
        'montant_heures_sup_50',
        'montant_heures_nuit',
        'montant_heures_dimanche',
        'montant_heures_jours_feries',
        'prime_transport',
        'prime_anciennete',
        'prime_performance',
        'autres_primes',
        'indemnites_repas',
        'salaire_brut',
        'cotisations_salariales',
        'cotisations_patronales',
        'impot_revenu',
        'salaire_net',
        'salaire_net_a_payer',
        'commentaires',
        'statut',
        'date_validation',
        'date_publication',
        // Nouveaux champs ajoutés
        'convention_collective',
        'siret',
        'matricule',
        'securite_sociale',
        'emploi',
        'categorie',
        'niveau',
        'echelon_coefficient',
        'anciennete_date',
        'entree_date',
        'conges_payes',
        'periode_conges',
        'absences',
        'heures_dimanche_ferie',
        'montant_heures_dimanche_ferie',
        'cotisation_maladie',
        'complementaire_sante',
        'assurance_chomage',
        'retraite_securite_sociale',
        'retraite_complementaire',
        'prevoyance',
        'autres_contributions',
        'csg_deductible',
        'csg_non_deductible',
        'crds',
        'exonerations_allegements',
        'avantages_nature',
        'prime_panier',
        'prime_habillage',
        'acompte',
        'total_cotisations',
        'montant_net_social',
        'net_a_payer_avant_impot',
        'impot_preleve_source',
        'total_cotisations_patronales',
        'cumul_brut',
        'cumul_imposable',
        'cumul_net',
        'cumul_cotisations_patronales',
    ];

    /**
     * Les attributs qui doivent être convertis.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'salaire_base' => 'decimal:2',
        'heures_normales' => 'decimal:2',
        'heures_sup_25' => 'decimal:2',
        'heures_sup_50' => 'decimal:2',
        'heures_nuit' => 'decimal:2',
        'heures_dimanche' => 'decimal:2',
        'heures_jours_feries' => 'decimal:2',
        'montant_heures_normales' => 'decimal:2',
        'montant_heures_sup_25' => 'decimal:2',
        'montant_heures_sup_50' => 'decimal:2',
        'montant_heures_nuit' => 'decimal:2',
        'montant_heures_dimanche' => 'decimal:2',
        'montant_heures_jours_feries' => 'decimal:2',
        'prime_transport' => 'decimal:2',
        'prime_anciennete' => 'decimal:2',
        'prime_performance' => 'decimal:2',
        'autres_primes' => 'decimal:2',
        'indemnites_repas' => 'decimal:2',
        'salaire_brut' => 'decimal:2',
        'cotisations_salariales' => 'decimal:2',
        'cotisations_patronales' => 'decimal:2',
        'impot_revenu' => 'decimal:2',
        'salaire_net' => 'decimal:2',
        'salaire_net_a_payer' => 'decimal:2',
        'date_validation' => 'datetime',
        'date_publication' => 'datetime',
        // Nouveaux champs ajoutés
        'anciennete_date' => 'date',
        'entree_date' => 'date',
        'conges_payes' => 'decimal:2',
        'absences' => 'decimal:2',
        'heures_dimanche_ferie' => 'decimal:2',
        'montant_heures_dimanche_ferie' => 'decimal:2',
        'cotisation_maladie' => 'decimal:2',
        'complementaire_sante' => 'decimal:2',
        'assurance_chomage' => 'decimal:2',
        'retraite_securite_sociale' => 'decimal:2',
        'retraite_complementaire' => 'decimal:2',
        'prevoyance' => 'decimal:2',
        'autres_contributions' => 'decimal:2',
        'csg_deductible' => 'decimal:2',
        'csg_non_deductible' => 'decimal:2',
        'crds' => 'decimal:2',
        'exonerations_allegements' => 'decimal:2',
        'avantages_nature' => 'decimal:2',
        'prime_panier' => 'decimal:2',
        'prime_habillage' => 'decimal:2',
        'acompte' => 'decimal:2',
        'total_cotisations' => 'decimal:2',
        'montant_net_social' => 'decimal:2',
        'net_a_payer_avant_impot' => 'decimal:2',
        'impot_preleve_source' => 'decimal:2',
        'total_cotisations_patronales' => 'decimal:2',
        'cumul_brut' => 'decimal:2',
        'cumul_imposable' => 'decimal:2',
        'cumul_net' => 'decimal:2',
        'cumul_cotisations_patronales' => 'decimal:2',
    ];

    /**
     * Relation avec l'employé
     */
    public function employe(): BelongsTo
    {
        return $this->belongsTo(Employe::class);
    }

    /**
     * Obtenir le mois formaté pour l'affichage
     */
    public function getMoisFormateAttribute(): string
    {
        $date = \Carbon\Carbon::createFromFormat('Y-m', $this->mois);
        return $date->locale('fr')->isoFormat('MMMM YYYY');
    }

    /**
     * Obtenir l'année de la fiche de paie
     */
    public function getAnneeAttribute(): string
    {
        return substr($this->mois, 0, 4);
    }

    /**
     * Obtenir le mois (numérique) de la fiche de paie
     */
    public function getMoisNumeriqueAttribute(): string
    {
        return substr($this->mois, 5, 2);
    }
}
