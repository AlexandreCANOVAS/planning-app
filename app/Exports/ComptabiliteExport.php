<?php

namespace App\Exports;

use Illuminate\Support\Collection;

class ComptabiliteExport
{
    protected $recapMensuel;
    protected $plannings;
    protected $employe;
    protected $mois;
    protected $annee;

    public function __construct($recapMensuel, $plannings, $employe, $mois, $annee)
    {
        $this->recapMensuel = $recapMensuel;
        $this->plannings = $plannings;
        $this->employe = $employe;
        $this->mois = $mois;
        $this->annee = $annee;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $data = collect();
        
        // Récapitulatif des heures
        $data->push([
            'Récapitulatif des heures pour ' . $this->employe->nom . ' ' . $this->employe->prenom,
            '',
            '',
            '',
            '',
            '',
        ]);
        
        $data->push([
            'Mois',
            $this->mois . '/' . $this->annee,
            '',
            '',
            '',
            '',
        ]);
        
        $data->push([
            'Heures totales',
            number_format($this->recapMensuel['total_heures'], 2) . 'h',
            '',
            '',
            '',
            '',
        ]);
        
        $data->push([
            'Heures supplémentaires 25%',
            number_format($this->recapMensuel['heures_25'], 2) . 'h',
            '',
            '',
            '',
            '',
        ]);
        
        $data->push([
            'Heures supplémentaires 50%',
            number_format($this->recapMensuel['heures_50'], 2) . 'h',
            '',
            '',
            '',
            '',
        ]);
        
        $data->push([
            'Heures de nuit (21h-06h)',
            number_format($this->recapMensuel['heures_nuit'], 2) . 'h',
            '',
            '',
            '',
            '',
        ]);
        
        $data->push([
            'Heures dimanche',
            number_format($this->recapMensuel['heures_dimanche'], 2) . 'h',
            '',
            '',
            '',
            '',
        ]);
        
        $data->push([
            'Heures jours fériés',
            number_format($this->recapMensuel['heures_jours_feries'], 2) . 'h',
            '',
            '',
            '',
            '',
        ]);
        
        $data->push([
            'Absences',
            $this->recapMensuel['absences'] . ' jour(s)',
            '',
            '',
            '',
            '',
        ]);
        
        // Ligne vide pour séparer
        $data->push(['', '', '', '', '', '']);
        
        // Détail des plannings par semaine
        $data->push(['Détail des plannings par semaine', '', '', '', '', '']);
        
        // En-têtes pour les plannings détaillés
        $data->push(['Semaine', 'Date', 'Heures travaillées', 'Début', 'Fin', 'Type']);
        
        // Regrouper les plannings par semaine
        $planningsParSemaine = $this->plannings->groupBy(function ($planning) {
            return \Carbon\Carbon::parse($planning->date)->weekOfYear;
        });
        
        foreach ($planningsParSemaine as $semaine => $planningsSemaine) {
            $premierPlanning = true;
            
            foreach ($planningsSemaine as $planning) {
                $date = \Carbon\Carbon::parse($planning->date);
                $jourSemaine = $date->translatedFormat('l');
                $dateFormatee = $date->format('d/m/Y');
                
                $type = '';
                if ($date->isDayOfWeek(0)) {
                    $type = 'Dimanche';
                } elseif ($this->estJourFerie($date)) {
                    $type = 'Jour férié';
                }
                
                $heureDebut = !empty($planning->heure_debut) ? \Carbon\Carbon::parse($planning->heure_debut)->format('H:i') : '';
                $heureFin = !empty($planning->heure_fin) ? \Carbon\Carbon::parse($planning->heure_fin)->format('H:i') : '';
                
                $data->push([
                    $premierPlanning ? 'Semaine ' . $semaine : '',
                    $jourSemaine . ' ' . $dateFormatee,
                    number_format($planning->heures_travaillees, 2) . 'h',
                    $heureDebut,
                    $heureFin,
                    $type
                ]);
                
                $premierPlanning = false;
            }
            
            // Ligne vide entre les semaines
            $data->push(['', '', '', '', '', '']);
        }
        
        return $data;
    }
    
    public function headings(): array
    {
        return [
            'Catégorie',
            'Valeur',
            '',
            '',
            '',
            '',
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            2 => ['font' => ['bold' => true]],
            3 => ['font' => ['bold' => true]],
            4 => ['font' => ['bold' => true]],
            5 => ['font' => ['bold' => true]],
            6 => ['font' => ['bold' => true]],
            7 => ['font' => ['bold' => true]],
            8 => ['font' => ['bold' => true]],
            9 => ['font' => ['bold' => true]],
            11 => ['font' => ['bold' => true, 'size' => 12]],
            12 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'color' => ['rgb' => 'EEEEEE']]],
        ];
    }
    
    public function title(): string
    {
        return 'Comptabilité ' . $this->mois . '/' . $this->annee;
    }
    
    /**
     * Vérifie si une date est un jour férié
     */
    private function estJourFerie($date)
    {
        // Liste des jours fériés en France pour l'année en cours
        $joursFeries = [
            // Jour de l'an
            $this->annee . '-01-01',
            // Lundi de Pâques (à calculer)
            // Fête du Travail
            $this->annee . '-05-01',
            // Victoire 1945
            $this->annee . '-05-08',
            // Ascension (à calculer)
            // Lundi de Pentecôte (à calculer)
            // Fête Nationale
            $this->annee . '-07-14',
            // Assomption
            $this->annee . '-08-15',
            // Toussaint
            $this->annee . '-11-01',
            // Armistice
            $this->annee . '-11-11',
            // Noël
            $this->annee . '-12-25',
        ];
        
        return in_array($date->format('Y-m-d'), $joursFeries);
    }
}
