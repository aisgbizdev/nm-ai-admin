<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Google\Auth\Credentials\ServiceAccountCredentials;

class FirestoreHistoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        $role = strtolower((string) ($user->role ?? ''));
        if (! $user || ! in_array($role, ['admin', 'superadmin'], true)) {
            abort(403);
        }

        $credentialsPath = config('services.firebase.credentials');
        if (! $credentialsPath || ! is_file($credentialsPath)) {
            return response()->json([
                'message' => 'Firebase credentials belum dikonfigurasi.',
                'items' => [],
            ], 500);
        }

        $credentials = json_decode(file_get_contents($credentialsPath), true);
        if (! is_array($credentials)) {
            return response()->json([
                'message' => 'Firebase credentials tidak valid.',
                'items' => [],
            ], 500);
        }

        $projectId = $credentials['project_id'] ?? config('services.firebase.projectId');
        if (! $projectId) {
            return response()->json([
                'message' => 'Project ID Firebase belum dikonfigurasi.',
                'items' => [],
            ], 500);
        }

        $accessToken = $this->fetchAccessToken($credentials);
        if (! $accessToken) {
            return response()->json([
                'message' => 'Gagal mendapatkan access token Firebase.',
                'items' => [],
            ], 500);
        }

        $items = [];
        $sessionIdFilter = $request->query('sessionId');
        if ($sessionIdFilter) {
            $queryResponse = $this->listSessionMessages($projectId, $accessToken, $sessionIdFilter);
        } else {
            $queryResponse = $this->runCollectionGroupQuery($projectId, $accessToken, null);
        }

        foreach ($queryResponse as $result) {
            $document = $result['document'] ?? $result ?? null;
            if (! is_array($document)) {
                continue;
            }

            $data = $this->decodeFields($document['fields'] ?? []);
            $documentName = $document['name'] ?? '';
            $documentId = $documentName ? basename($documentName) : '';
            $sessionId = $data['sessionId'] ?? $this->extractSessionId($documentName);

            $items[] = [
                'id' => $documentId,
                'sessionId' => $sessionId,
                'text' => $data['text'] ?? '',
                'role' => $data['role'] ?? '',
                'status' => $data['status'] ?? '',
                'createdAt' => $this->normalizeTimestamp($data['createdAt'] ?? null),
            ];
        }

        return response()->json([
            'items' => $items,
        ]);
    }

    private function extractSessionId(string $documentPath): ?string
    {
        $parts = explode('/', $documentPath);
        $index = array_search('sessions', $parts, true);
        if ($index === false || ! isset($parts[$index + 1])) {
            return null;
        }

        return $parts[$index + 1];
    }

    private function fetchAccessToken(array $credentials): ?string
    {
        try {
            $scopes = ['https://www.googleapis.com/auth/datastore'];
            $creds = new ServiceAccountCredentials($scopes, $credentials);
            $token = $creds->fetchAuthToken();
            return $token['access_token'] ?? null;
        } catch (\Throwable) {
            return null;
        }
    }

    private function runCollectionGroupQuery(string $projectId, string $accessToken, ?string $sessionId): array
    {
        $url = "https://firestore.googleapis.com/v1/projects/{$projectId}/databases/(default)/documents:runQuery";
        $payload = [
            'structuredQuery' => [
                'from' => [
                    [
                        'collectionId' => 'messages',
                        'allDescendants' => true,
                    ],
                ],
            ],
        ];
        if ($sessionId) {
            $payload['structuredQuery']['where'] = [
                'fieldFilter' => [
                    'field' => ['fieldPath' => 'sessionId'],
                    'op' => 'EQUAL',
                    'value' => ['stringValue' => $sessionId],
                ],
            ];
        }

        $response = Http::withToken($accessToken)->post($url, $payload);
        if (! $response->ok()) {
            return [];
        }

        $data = $response->json();
        return is_array($data) ? $data : [];
    }

    private function listSessionMessages(string $projectId, string $accessToken, string $sessionId): array
    {
        $baseUrl = "https://firestore.googleapis.com/v1/projects/{$projectId}/databases/(default)/documents/sessions/{$sessionId}/messages";
        $documents = [];
        $pageToken = null;

        do {
            $url = $baseUrl;
            if ($pageToken) {
                $url .= '?pageToken='.urlencode($pageToken);
            }

            $response = Http::withToken($accessToken)->get($url);
            if (! $response->ok()) {
                return [];
            }

            $payload = $response->json();
            $docs = $payload['documents'] ?? [];
            if (is_array($docs)) {
                foreach ($docs as $doc) {
                    $documents[] = $doc;
                }
            }
            $pageToken = $payload['nextPageToken'] ?? null;
        } while ($pageToken);

        return $documents;
    }

    private function decodeFields(array $fields): array
    {
        $result = [];
        foreach ($fields as $key => $value) {
            $result[$key] = $this->decodeValue($value);
        }
        return $result;
    }

    private function decodeValue(mixed $value): mixed
    {
        if (! is_array($value)) {
            return $value;
        }

        if (array_key_exists('stringValue', $value)) {
            return $value['stringValue'];
        }
        if (array_key_exists('integerValue', $value)) {
            return (int) $value['integerValue'];
        }
        if (array_key_exists('doubleValue', $value)) {
            return (float) $value['doubleValue'];
        }
        if (array_key_exists('booleanValue', $value)) {
            return (bool) $value['booleanValue'];
        }
        if (array_key_exists('nullValue', $value)) {
            return null;
        }
        if (array_key_exists('timestampValue', $value)) {
            return $value['timestampValue'];
        }
        if (array_key_exists('mapValue', $value)) {
            $fields = $value['mapValue']['fields'] ?? [];
            return $this->decodeFields($fields);
        }
        if (array_key_exists('arrayValue', $value)) {
            $items = $value['arrayValue']['values'] ?? [];
            return array_map(fn ($item) => $this->decodeValue($item), $items);
        }

        return $value;
    }

    private function normalizeTimestamp(mixed $value): ?string
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format(DATE_ATOM);
        }

        if (is_object($value) && method_exists($value, 'get')) {
            $date = $value->get();
            if ($date instanceof \DateTimeInterface) {
                return $date->format(DATE_ATOM);
            }
        }

        if (is_string($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return gmdate(DATE_ATOM, (int) $value);
        }

        return null;
    }
}
