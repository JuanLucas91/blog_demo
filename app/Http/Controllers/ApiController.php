<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
/**
 * @OA\Info(title="Demo Blog Api",version="1")
 */
abstract class ApiController extends Controller{

    /**
     * Numeric status of the response.
     * Used values: 1 - Request failed, 2 - OK, 3 - Invalid Request, 4 - Resource not found
     * @var int
     */
    protected int $status = 1;

    /**
     * Text message to inform what happened.
     * @var string
     */
    protected string $message = "Something went wrong";

    /**
     * Extra information when needed.
     * @var mixed
     */
    protected mixed $payload;

    /**
     * Information of the errors if any.
     * @var array<string>
     */
    protected array $errors;

    /**
     * Parses all response fields to json and generates response.
     * @return JsonResponse
     */
    protected function generateResponse() : JsonResponse{
        $response = [
            "status" => $this->status,
            "message" => $this->message
        ];

        if(!empty($this->payload)){
            $response["payload"] = $this->payload;
        }

        if(!empty($this->errors)){
            $response["errors"] = $this->errors;
        }

        return response()->json($response);
    }

}