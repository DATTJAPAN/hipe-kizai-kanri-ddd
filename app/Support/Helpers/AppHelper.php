<?php

declare(strict_types=1);

if (! function_exists('responseJson')) {
    /**
     * Base JSON response builder.
     */
    function responseJson(
        string $origin = 'server',
        string $status = 'success',
        array $data = [],
        int $httpStatus = 200,
        array $headers = []
    ) {
        $payload = array_merge([
            'origin' => $origin,
            'status' => $status,
        ], $data);

        return response()->json($payload, $httpStatus, $headers);
    }
}

if (! function_exists('successResponseJson')) {
    /**
     * Business success response, always HTTP 200.
     */
    function successResponseJson(
        array $data = [],
        array $headers = []
    ) {
        return responseJson('server', 'success', $data, 200, $headers);
    }
}

if (! function_exists('failedResponseJson')) {
    /**
     * Business failure response, always HTTP 200.
     * Example: no data found, invalid credentials.
     */
    function failedResponseJson(
        array $data = [],
        array $headers = []
    ) {
        return responseJson('server', 'failed', $data, 200, $headers);
    }
}

if (! function_exists('errorResponseJson')) {
    /**
     * System or validation error response.
     * HTTP status code reflects actual error.
     */
    function errorResponseJson(
        array $data = [],
        int $httpStatus = 400,
        array $headers = []
    ) {
        return responseJson('server', 'error', $data, $httpStatus, $headers);
    }
}

if (! function_exists('checkClassTraits')) {
    function checkClassTraits($class, $needle): bool
    {
        if (empty($class) || empty($needle)) {
            return false;
        }

        if (is_object($class)) {
            $class = get_class($class);
        }

        $found = in_array($needle, class_uses_recursive($class), true);

        return (bool) ($found);
    }
}
