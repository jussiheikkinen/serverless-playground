<?php

use App\Http\Controllers\GraphqlController;
use App\Http\TypeResolvers\RecordResolver;
use GraphQL\GraphQL;
use GraphQL\Type\Schema;
use GraphQL\Utils\BuildSchema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('/test-json', function () use ($router) {
    try {
        //return file_get_contents('../storage/fakedata.json');
        return GraphqlController::getData();
    } catch (Exception $e) {
        return $e->getMessage();
    }
});

$router->post('/graphql', function (Request $request) {
    $typeConfigDecorator = function($typeConfig, $typeDefinitionNode) {
        $name = $typeConfig['name'];

        switch($name) {
            case 'RecordOfficial':
                $typeConfig['resolveField'] = function ($value, $args, $context, $info) {
                    return (new RecordResolver())->resolve($value, $args, $context, $info);
                };
                break;
            default:
                break;
        }

        return $typeConfig;
    };

    $contents = file_get_contents('../schema.graphql');
    $schema = BuildSchema::build($contents, $typeConfigDecorator);

    $query = $request->json('query');
    $variableValues = $request->json('variables', null);
    $operationName = $request->json('operationName', null);

    // Log::debug($request);

    try {
        $result = GraphQL::executeQuery(
            $schema,
            $query,
            GraphqlController::getRootValues(), // root values -> resolvers
            null, // context
            $variableValues,
            $operationName
        );
        $output = $result->toArray();
    } catch (\Exception $e) {
        print_r($e->getMessage());
        $output = [
            'errors' => [
                [
                    'message' => $e->getMessage()
                ]
            ]
        ];
    }

    return response()->json($output);
});
