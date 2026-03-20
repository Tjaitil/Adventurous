<?php

namespace App\Http\Controllers;

use App\Services\GuideService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class GuideController extends Controller
{
    protected GuideService $guideService;

    public function __construct(GuideService $guideService)
    {
        $this->guideService = $guideService;
    }

    public function index(Request $request): Response|JsonResponse
    {
        $categories = $this->guideService->getCategories();
        $categoryGuides = [];

        foreach ($categories as $category) {
            $categoryGuides[$category] = $this->guideService->listByCategory($category);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'categories' => $categories,
                'categoryGuides' => $categoryGuides,
            ]);
        }

        return Inertia::render('Guides/ShowGuides', [
            'categories' => $categories,
            'categoryGuides' => $categoryGuides,
        ]);
    }

    public function show(string $category, string $slug, Request $request): Response|JsonResponse
    {
        $guide = $this->guideService->getGuide($category, $slug);

        $wantsJson = $request->wantsJson();

        if (! $guide) {
            if ($wantsJson) {
                return response()->json(['error' => 'Guide not found'], 404);
            }
            abort(404, 'Guide not found');
        }

        if ($wantsJson) {
            return response()->json($guide);
        }

        return Inertia::render('Guides/ShowGuide', [
            'guide' => $guide,
            'category' => $category,
        ]);
    }
}
