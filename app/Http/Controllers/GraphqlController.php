<?php

namespace App\Http\Controllers;

use App\Http\Resolvers\Record;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Log;

class GraphqlController extends Controller
{
    public static function getRootValues ()
    {
        return [
            'node' => function ($root, $args, $context, ResolveInfo $info) {
                return null;
            },
            'records' => function ($root, $args, $context, ResolveInfo $info) {
                return array_map(function ($record) use ($root, $args, $context, $info) {
                    return (new Record())->resolve((object)$record, $args, $context, $info);
                }, Record::getRecords($args));
            }
        ];
    }
}
