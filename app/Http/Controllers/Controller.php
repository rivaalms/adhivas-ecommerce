<?php

namespace App\Http\Controllers;

abstract class Controller
{
    /**
     * @template T
     *
     * @param T $data
     * @param string $message
     * @param int $code
     */
    protected function response($data, $message = "", $code = 200)
    {
        $status = $code >= 200 && $code < 400 ? "success" : "error";

        return response()->json([
            "status" => $status,
            "message" => $message,
            "data" => $data
        ], $code);
    }
}
