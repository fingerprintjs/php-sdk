<?php

/**
 * Moves the configured parameters to the end of an operation's parameter list in an OpenAPI schema,
 * so the generated method signatures stay compatible for callers passing arguments positionally.
 *
 * Usage: php scripts/reorder-schema-parameters.php <config.json> <input schema> <output schema>
 */
function moveParametersToEnd(string $schema, string $operationId, array $trailingNames): string
{
    // Matches the lines of the `parameters:` list that follows the `operationId` line.
    $parametersList = '/^( +)operationId: '.preg_quote($operationId, '/').'$.*?^\1parameters:\n\K.*?(?=^(?!\1 )[^\n]*\S)/ms';

    $schema = preg_replace_callback($parametersList, function (array $match) use ($operationId, $trailingNames) {
        $indent = strspn($match[0], ' ');
        $parameters = [];
        foreach (preg_split("/^(?= {{$indent}}- name: )/m", $match[0], -1, PREG_SPLIT_NO_EMPTY) as $parameter) {
            preg_match('/- name: (\S+)/', $parameter, $name);
            $parameters[$name[1]] = $parameter;
        }

        if ($missing = array_diff($trailingNames, array_keys($parameters))) {
            throw new RuntimeException("Parameters not found in '{$operationId}': ".implode(', ', $missing));
        }

        $position = array_flip($trailingNames);
        uksort($parameters, fn ($a, $b) => ($position[$a] ?? -1) <=> ($position[$b] ?? -1));

        return implode('', $parameters);
    }, $schema, 1, $count);

    return $count ? $schema : throw new RuntimeException("Operation '{$operationId}' not found");
}

[, $configFile, $inputFile, $outputFile] = $argv;
$schema = file_get_contents($inputFile);

foreach (json_decode(file_get_contents($configFile), true, flags: JSON_THROW_ON_ERROR) as $operationId => $config) {
    $schema = moveParametersToEnd($schema, $operationId, $config['trailingParameters']);
}

file_put_contents($outputFile, $schema);
