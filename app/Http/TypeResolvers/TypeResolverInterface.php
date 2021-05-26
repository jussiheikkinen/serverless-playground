<?php

namespace App\Http\TypeResolvers;

use GraphQL\Type\Definition\ResolveInfo;

interface TypeResolverInterface
{
    public function resolve($value, $args, $context, ResolveInfo $info);
}
