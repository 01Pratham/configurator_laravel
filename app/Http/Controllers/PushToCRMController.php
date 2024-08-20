<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PushToCRMController extends Controller
{
    private string $Data;

    private string $URL;

    public function __construct(Request $request)
    {
        $this->Data = base64_decode($request->data, true);
        // $this->URL = "http://localhost:8000/api/test";
        $this->URL = env("CRM_API");
    }
    public function index()
    {
        $decodedData = json_decode($this->Data, true);

        // Use Laravel's HTTP client to send the request
        $response = Http::post($this->URL, $decodedData);

        // Return the response as JSON
        return response()->json($response->json());
    }
}
