<?php

declare(strict_types=1);

if (! function_exists('applyScopesToQuery')) {
    function applyScopesToQuery($query, ?array $scopes = null)
    {
        if (is_null($scopes)) {
            return $query;
        }

        foreach ($scopes as $scope => $args) {
            if (is_null($args) || empty($args)) {
                $query->{$scope}();
            } else {
                $query->{$scope}(...$args);
            }
        }

        return $query;
    }
}
