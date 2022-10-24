<?php

function convertToBytes(string $size): float {
    $parsedSize = (float) $size;
    if (strstr($size, 'M')) {
        return ($parsedSize * 1024) * 1024;
    }

    if (strstr($size, 'G')) {
        return (($parsedSize * 1024) * 1024) * 1024;
    }

    if (strstr($size, 'T')) {
        return ((($parsedSize * 1024) * 1024) * 1024) * 1024;
    }

    return (float) $size;
}

function convertBytesToMB(float $bytes): float {
    return round(($bytes / 1024) / 1024, 2);
}

function convertToMB(mixed $inputSize): float {
    if (strstr($inputSize, 'M')) {
        return (float) $inputSize;
    }

    if (strstr($inputSize, 'G')) {
        return round((float) $inputSize / 1024, 2);
    }

    return convertBytesToMB((float) $inputSize);
}
