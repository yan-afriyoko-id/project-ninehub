<?php

namespace App\Http\Resources\auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response;

class AuthResponse extends JsonResource
{
    public $success;
    public $message;
    public $statusCode;
    public $errors;

    public static $wrap = null;

    public function __construct(bool $success, string $message, int $statusCode, $resource = [], array $errors = [])
    {
        parent::__construct($resource);
        $this->success = $success;
        $this->message = $message;
        $this->statusCode = $statusCode;
        $this->errors = $errors;
    }

    public static function success($data = [], string $message = 'Request successful.', int $statusCode = Response::HTTP_OK): self
    {
        return new self(true, $message, $statusCode, $data);
    }

    public static function error(string $message, int $statusCode, array $errors = []): self
    {
        return new self(false, $message, $statusCode, [], $errors);
    }

    public function toArray(Request $request): array
    {
        $response = [
            'success' => $this->success,
            'message' => $this->message,
        ];

        if (!empty($this->resource)) {
            if (is_object($this->resource)) {
                $userData = [
                    'id' => $this->resource->id,
                    'name' => $this->resource->name,
                    'email' => $this->resource->email,
                    'token' => $this->resource->token ?? null,
                ];

                // Add roles if loaded
                if ($this->resource->relationLoaded('roles')) {
                    $userData['roles'] = $this->resource->roles->pluck('name');
                }

                // Add permissions if loaded
                if ($this->resource->relationLoaded('permissions')) {
                    $userData['permissions'] = $this->resource->permissions->pluck('name');
                }

                $response['data'] = $userData;

                if (
                    method_exists($this->resource, 'tenant') &&
                    $this->resource->relationLoaded('tenant') &&
                    $this->resource->tenant
                ) {
                    $tenant = $this->resource->tenant;

                    $response['tenant'] = [
                        'id' => $tenant->id,
                        'company' => $tenant->data['company'] ?? null,
                        'domains' => $tenant->relationLoaded('domains')
                            ? $tenant->domains->pluck('domain')
                            : [],
                    ];
                }
            } else {
                $response['data'] = $this->resource;
            }
        }

        if (!empty($this->errors)) {
            $response['errors'] = $this->errors;
        }

        return $response;
    }

    public function withResponse(Request $request, $response): void
    {
        $response->setStatusCode($this->statusCode);
    }
}
