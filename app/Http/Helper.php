<?php 

function api($status = 402, $data = null, $message = null)
{
    return response()->json([
        "status" => $status,
        "data" => $data,
        "message" => $message
    ], $status);
}
