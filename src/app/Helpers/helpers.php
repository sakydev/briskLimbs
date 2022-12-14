<?php

function convertToBytes(string $size): float {
    $parsedSize = (float) $size;
    if (str_contains($size, 'M')) {
        return ($parsedSize * 1024) * 1024;
    }

    if (str_contains($size, 'G')) {
        return (($parsedSize * 1024) * 1024) * 1024;
    }

    if (str_contains($size, 'T')) {
        return ((($parsedSize * 1024) * 1024) * 1024) * 1024;
    }

    return (float) $size;
}

function convertToKB(string $size): float {
    $parsedSize = (float) $size;
    if (str_contains($size, 'M')) {
        return $parsedSize * 1024;
    }

    if (str_contains($size, 'G')) {
        return ($parsedSize * 1024) * 1024;
    }

    if (str_contains($size, 'T')) {
        return (($parsedSize * 1024) * 1024) * 1024;
    }

    return (float) $size;
}

function convertToMB(mixed $inputSize): float {
    if (str_contains($inputSize, 'M')) {
        return (float) $inputSize;
    }

    if (str_contains($inputSize, 'G')) {
        return round((float) $inputSize / 1024, 2);
    }

    return convertBytesToMB((float) $inputSize);
}

function convertBytesToMB(float $bytes): float {
    return round(($bytes / 1024) / 1024, 2);
}

function getMaxUploadSizeInKB(): float {
    $iniMaxFilesize = convertToKB(ini_get('upload_max_filesize'));
    $configMaxFilesize = config('settings.max_filesize_video'); // already in KB

    // always prefer ini if it is smaller to prevent unexpected errors
    return $iniMaxFilesize < $configMaxFilesize ? $iniMaxFilesize : $configMaxFilesize;
}
