<?php
// OpenAPI Specification
$exe = "java -jar openapi-generator-cli-6.2.1.jar";
$urlToOpenApiYaml = "spec.json";
$generatorName = "javascript";
$outputPath = "public/api";
echo shell_exec("$exe generate -g $generatorName -i $urlToOpenApiYaml -o $outputPath");

