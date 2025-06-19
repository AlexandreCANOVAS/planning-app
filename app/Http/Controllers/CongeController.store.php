    public function store(Request $request)
    {
        $validated = $request->validate([
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'motif' => 'required|string',
        ]);

        $employe = auth()->user()->employe;
        $conge = $employe->conges()->create([
            'date_debut' => $validated['date_debut'],
            'date_fin' => $validated['date_fin'],
            'motif' => $validated['motif'],
            'statut' => 'en_attente',
        ]);

        try {
            broadcast(new CongeRequested($conge))->toOthers();
            
            // Envoyer un email aux gestionnaires
            $managers = \App\Models\User::whereHas('roles', function($query) {
                $query->where('name', 'employeur');
            })->where('societe_id', $employe->societe_id)->get();
            
            foreach ($managers as $manager) {
                Mail::to($manager->email)
                    ->queue(new CongeRequestedMail($conge, $manager));
            }
        } catch (\Exception $e) {
            \Log::error('Erreur de broadcast ou d\'envoi d\'email: ' . $e->getMessage());
        }

        return redirect()->route('conges.index')
            ->with('success', 'Votre demande de congé a été envoyée.');
    }
