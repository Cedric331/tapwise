<?php

namespace App\Http\Controllers;

use App\Http\Requests\BarSettingsRequest;
use App\Models\Bar;
use App\Support\RecommendationQuestions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class BarSettingsController extends Controller
{
    /**
     * Show the bar settings page.
     */
    public function edit(Bar $bar): Response
    {
        $this->authorize('updateSettings', $bar);

        return Inertia::render('Bars/Settings', [
            'bar' => $bar,
            'recommendationQuestions' => RecommendationQuestions::all(),
            'selectedRecommendationQuestions' => RecommendationQuestions::normalizeSelected(
                $bar->recommendation_questions
            ),
        ]);
    }

    /**
     * Update the bar settings.
     */
    public function update(BarSettingsRequest $request, Bar $bar): RedirectResponse
    {
        $this->authorize('updateSettings', $bar);

        $data = $request->validated();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($bar->logo_path) {
                Storage::disk('public')->delete($bar->logo_path);
            }

            $path = $request->file('logo')->store('bar-logos', 'public');
            $data['logo_path'] = $path;
        } else {
            unset($data['logo']);
        }

        $bar->update($data);

        return redirect()->route('bars.settings.edit', $bar)
            ->with('success', 'Paramètres mis à jour avec succès.');
    }
}
