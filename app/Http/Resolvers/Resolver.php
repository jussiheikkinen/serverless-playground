<?php

namespace App\Http\Resolvers;

use GraphQL\Type\Definition\ResolveInfo;

interface Resolver
{
    public function resolve($rootValue, $args, $context, ResolveInfo $info);
}
