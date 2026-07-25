<?php

namespace App\Modules\Strava;

use App\Models\OauthToken;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class StravaClient
{
    private const AUTHORIZE_URL = 'https://www.strava.com/oauth/authorize';

    private const TOKEN_URL = 'https://www.strava.com/oauth/token';

    private const API_URL = 'https://www.strava.com/api/v3';

    public function authorizeUrl(string $redirectUri, string $state): string
    {
        return self::AUTHORIZE_URL.'?'.http_build_query([
            'client_id' => config('services.strava.client_id'),
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'approval_prompt' => 'auto',
            'scope' => 'read,activity:read_all',
            'state' => $state,
        ]);
    }

    public function exchangeCode(User $user, string $code): OauthToken
    {
        $data = Http::asForm()->post(self::TOKEN_URL, [
            'client_id' => config('services.strava.client_id'),
            'client_secret' => config('services.strava.client_secret'),
            'code' => $code,
            'grant_type' => 'authorization_code',
        ])->throw()->json();

        return OauthToken::updateOrCreate(
            ['user_id' => $user->id, 'provider' => 'strava'],
            [
                'access_token' => $data['access_token'],
                'refresh_token' => $data['refresh_token'],
                'expires_at' => now()->setTimestamp($data['expires_at']),
                'provider_user_id' => $data['athlete']['id'] ?? null,
            ],
        );
    }

    public function freshToken(OauthToken $token): OauthToken
    {
        if (! $token->isExpired()) {
            return $token;
        }

        $data = Http::asForm()->post(self::TOKEN_URL, [
            'client_id' => config('services.strava.client_id'),
            'client_secret' => config('services.strava.client_secret'),
            'grant_type' => 'refresh_token',
            'refresh_token' => $token->refresh_token,
        ])->throw()->json();

        $token->update([
            'access_token' => $data['access_token'],
            'refresh_token' => $data['refresh_token'],
            'expires_at' => now()->setTimestamp($data['expires_at']),
        ]);

        return $token;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function activities(OauthToken $token, ?int $after = null, int $page = 1, int $perPage = 100): array
    {
        return Http::withToken($token->access_token)
            ->get(self::API_URL.'/athlete/activities', array_filter([
                'after' => $after,
                'page' => $page,
                'per_page' => $perPage,
            ]))
            ->throw()
            ->json();
    }
}
