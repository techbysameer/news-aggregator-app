<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Services\UserPreferenceService;
use App\Http\Controllers\API\BaseController;
use App\Http\Requests\SetUserPreferncesRequest;

class UserPreferenceController extends BaseController
{
    protected $userPreferenceService;

    public function __construct(UserPreferenceService $userPreferenceService)
    {
        $this->userPreferenceService = $userPreferenceService;
    }

    public function setPreferences(SetUserPreferncesRequest $request)
    {
        $validatedData = $request->validated();
        $userId = Auth::id();

        try {
            $preferences = $this->userPreferenceService->setUserPreferences($userId, $validatedData);

            return $this->sendResponse(
                $preferences,
                'Preferences saved successfully.'
            );
        } catch (\Exception $e) {
            return $this->sendError(
                'An error occurred while saving preferences. Please try again later.',
                [],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function getPreferences()
    {
        $userId = Auth::id();

        try {
            $preferences = $this->userPreferenceService->getUserPreferences($userId);

            return $this->sendResponse(
                $preferences,
                'User Preferences retrieved successfully.'
            );
        } catch (\Exception $e) {
            return $this->sendError(
                'An error occurred while fetching preferences. Please try again later.',
                [],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function getPersonalizedFeed()
    {
        $userId = Auth::id();

        try {
            $articles = $this->userPreferenceService->getPersonalizedArticles($userId);

            return $this->sendResponse(
                $articles,
                'User Personalized Feed retrieved successfully.'
            );
        } catch (\Exception $e) {
            return $this->sendError(
                'An error occurred while fetching personalized feed. Please try again later.',
                [],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
