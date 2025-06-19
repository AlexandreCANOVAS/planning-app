    public function updateMonthlyCalendar(Request $request)
    {
        $user = auth()->user();
        $data = $request->validate([
            'employe_id' => 'required|exists:employes,id',
            'mois' => 'required|integer|min:1|max:12',
            'annee' => 'required|integer',
            'plannings' => 'required|array'
        ]);

        $employe = Employe::where('id', $data['employe_id'])
            ->where('societe_id', $user->societe_id)
            ->firstOrFail();

        DB::beginTransaction();
        try {
            // Supprimer tous les plannings existants pour ce mois et cet employé
            Planning::where('employe_id', $employe->id)
                ->where('societe_id', $user->societe_id)
                ->whereYear('date', $data['annee'])
                ->whereMonth('date', $data['mois'])
                ->delete();

            // Créer les nouveaux plannings
            foreach ($data['plannings'] as $date => $planning) {
                if ($planning['type_horaire'] === 'simple') {
                    // Créer un planning simple pour la journée
                    Planning::create([
                        'employe_id' => $employe->id,
                        'societe_id' => $user->societe_id,
                        'lieu_id' => $planning['lieu_id'],
                        'date' => $date,
                        'periode' => 'journee',
                        'heure_debut' => $planning['horaires']['debut'],
                        'heure_fin' => $planning['horaires']['fin'],
                        'heures_travaillees' => $this->calculerHeuresTravaillees($planning['horaires']['debut'], $planning['horaires']['fin'])
                    ]);
                } else {
                    // Créer deux plannings pour la journée (matin et après-midi)
                    Planning::create([
                        'employe_id' => $employe->id,
                        'societe_id' => $user->societe_id,
                        'lieu_id' => $planning['lieu_id'],
                        'date' => $date,
                        'periode' => 'matin',
                        'heure_debut' => $planning['horaires']['debut_matin'],
                        'heure_fin' => $planning['horaires']['fin_matin'],
                        'heures_travaillees' => $this->calculerHeuresTravaillees($planning['horaires']['debut_matin'], $planning['horaires']['fin_matin'])
                    ]);

                    Planning::create([
                        'employe_id' => $employe->id,
                        'societe_id' => $user->societe_id,
                        'lieu_id' => $planning['lieu_id'],
                        'date' => $date,
                        'periode' => 'apres-midi',
                        'heure_debut' => $planning['horaires']['debut_aprem'],
                        'heure_fin' => $planning['horaires']['fin_aprem'],
                        'heures_travaillees' => $this->calculerHeuresTravaillees($planning['horaires']['debut_aprem'], $planning['horaires']['fin_aprem'])
                    ]);
                }
            }

            DB::commit();
            
            // Envoyer une notification à l'employé concerné
            if ($employe->user) {
                // Récupérer le premier planning mis à jour pour la notification et l'email
                $updatedPlanning = Planning::where('employe_id', $employe->id)
                    ->whereYear('date', $data['annee'])
                    ->whereMonth('date', $data['mois'])
                    ->first();
                
                // Notification dans l'application
                $employe->user->notify(new PlanningUpdatedNotification(
                    $updatedPlanning,
                    [
                        'message' => 'Votre planning a été mis à jour',
                        'dates' => implode(', ', array_keys($data['plannings']))
                    ]
                ));
                
                // Envoi d'un email
                if ($employe->user->email && $updatedPlanning) {
                    Mail::to($employe->user->email)
                        ->queue(new PlanningUpdated($updatedPlanning, $employe->user));
                }
            }
            
            return response()->json(['message' => 'Planning modifié avec succès']);
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Erreur lors de la modification du planning: ' . $e->getMessage());
            return response()->json(['error' => 'Une erreur est survenue lors de la modification'], 500);
        }
    }
