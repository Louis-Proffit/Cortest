<?php
// OpenAPI Specification
$exe = "java -jar openapi-generator-cli-6.2.1.jar";
$urlToOpenApiYaml = "spec.json";
$generatorName = "php-symfony";
$outputPath = "openapi";
$configFile = "openapi/config.json";
$vendor = "cortest";
$package = "api";
echo shell_exec("$exe generate -g $generatorName -i $urlToOpenApiYaml -o $outputPath --git-user-id $package --git-repo-id $vendor -c $configFile");

