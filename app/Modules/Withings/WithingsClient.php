<?php

namespace App\Modules\Withings;

use App\Models\OauthToken;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class WithingsClient
{
    private const AUTHORIZE_URL = 'https://account.withings.com/oauth2_user/authorize2';

    private const TOKEN_URL = 'https://wbsapi.withings.net/v2/oauth2';

    private const MEASURE_URL = 'https://wbsapi.withings.net/measure';

    public function authorizeUrl(string $redirectUri, string $state): string
    {
        return self::AUTHORIZE_URL.'?'.http_build_query([
            'response_type' => 'code',
            'client_id' => config('services.withings.client_id'),
            'scope' => 'user.metrics',
            'redirect_uri' => $redirectUri,
            'state' => $state,
        ]);
    }

    public function exchangeCode(User $user, string $code, string $redirectUri): OauthToken
    {
        $body = $this->request(self::TOKEN_URL, [
            'action' => 'requesttoken',
            'grant_type' => 'authorization_code',
            'client_id' => config('services.withings.client_id'),
            'client_secret' => config('services.withings.client_secret'),
            'code' => $code,
            'redirect_uri' => $redirectUri,
        ]);

        return OauthToken::updateOrCreate(
            ['user_id' => $user->id, 'provider' => 'withings'],
            [
                'access_token' => $body['access_token'],
                'refresh_token' => $body['refresh_token'],
                'expires_at' => now()->addSeconds($body['expires_in']),
                'provider_user_id' => $body['userid'] ?? null,
                'scopes' => $body['scope'] ?? null,
            ],
        );
    }

    public function freshToken(OauthToken $token): OauthToken
    {
        if (! $token->isExpired()) {
            return $token;
        }

        $body = $this->request(self::TOKEN_URL, [
            'action' => 'requesttoken',
            'grant_type' => 'refresh_token',
            'client_id' => config('services.withings.client_id'),
            'client_secret' => config('services.withings.client_secret'),
            'refresh_token' => $token->refresh_token,
        ]);

        $token->update([
            'access_token' => $body['access_token'],
            'refresh_token' => $body['refresh_token'],
            'expires_at' => now()->addSeconds($body['expires_in']),
        ]);

        return $token;
    }

    /**
     * Fetch measure groups (weight, fat, muscle, water, bone).
     *
     * @return array<int, array<string, mixed>>
     */
    public function measurements(OauthToken $token, ?int $startdate = null): array
    {
        $body = $this->request(self::MEASURE_URL, array_filter([
            'action' => 'getmeas',
            'meastypes' => '1,6,8,76,77,88',
            'category' => 1,
            'startdate' => $startdate,
        ]), $token->access_token);

        return $body['measuregrps'] ?? [];
    }

    /**
     * Withings wraps every response in {status, body} — status 0 means success.
     *
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     */
    private function request(string $url, array $params, ?string $accessToken = null): array
    {
        $pending = Http::asForm();

        if ($accessToken !== null) {
            $pending = $pending->withToken($accessToken);
        }

        $response = $pending->post($url, $params)->throw()->json();

        if (($response['status'] ?? -1) !== 0) {
            throw new RuntimeException(
                'Withings API error '.($response['status'] ?? '?').': '.($response['error'] ?? 'unknown'),
            );
        }

        return $response['body'] ?? [];
    }
}
