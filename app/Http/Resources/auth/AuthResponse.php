<?php

namespace App\Http\Resources\auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response;

class AuthResponse extends JsonResource
{
    /**
     * 
     * @var bool
     */
    public $success;

    /**
     * 
     * @var string
     */
    public $message;

    /**
     * 
     * @var int
     */
    public $statusCode;

    /**
     * 
     * @var array
     */
    public $errors;

    /**
     * @var null
     */
    public static $wrap = null;

    /**
     * 
     *
     * @param bool $success
     * @param string $message
     * @param int $statusCode
     * @param mixed $resource
     * @param array $errors
     */
    public function __construct(bool $success, string $message, int $statusCode, $resource = [], array $errors = [])
    {
        parent::__construct($resource);
        $this->success = $success;
        $this->message = $message;
        $this->statusCode = $statusCode;
        $this->errors = $errors;
    }

    /**
     * Factory method untuk membuat respons sukses.
     *
     * @param mixed $data
     * @param string $message
     * @param int $statusCode
     * @return static
     */
    public static function success($data = [], string $message = 'Request successful.', int $statusCode = Response::HTTP_OK): self
    {
        return new self(true, $message, $statusCode, $data);
    }

    /**
     * Factory method untuk membuat respons error.
     *
     * @param string $message
     * @param int $statusCode
     * @param array $errors
     * @return static
     */
    public static function error(string $message, int $statusCode, array $errors = []): self
    {
        return new self(false, $message, $statusCode, [], $errors);
    }

    /**
     * Mengubah resource menjadi array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $response = [
            'success' => $this->success,
            'message' => $this->message,
        ];

        if (!empty($this->resource)) {
            $response['data'] = $this->resource;
        }

        if (!empty($this->errors)) {
            $response['errors'] = $this->errors;
        }

        return $response;
    }

    /**
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Http\JsonResponse $response
     * @return void
     */
    public function withResponse(Request $request, $response): void
    {
        $response->setStatusCode($this->statusCode);
    }
}
