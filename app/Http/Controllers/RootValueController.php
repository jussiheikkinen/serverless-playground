<?php

namespace App\Http\Controllers;

use GraphQL\Type\Definition\ResolveInfo;

class RootValueController extends Controller
{

  public static function getRootValues () {
    return [
        'helloWorld' => function ($root, $args, $context, ResolveInfo $info) {
            return 'Hello serverless graphql';
        },
        'greetings' => function ($root, $args, $context, ResolveInfo $info) {
            return [
                'firstName' => $args['firstName'],
                'lastName' => $args['lastName']
            ];
        },
        'updateGreetings' => function ($root, $args, $context, ResolveInfo $info) {
            return [
                'firstName' => $args['firstName'],
                'lastName' => $args['lastName']
            ];
        }
    ];
  }
}
