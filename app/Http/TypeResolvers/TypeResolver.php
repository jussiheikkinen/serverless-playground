<?php

namespace App\Http\TypeResolvers;

use GraphQL\Type\Definition\ResolveInfo;

abstract class TypeResolver implements TypeResolverInterface
{
    public function resolve($value, $args, $context, ResolveInfo $info)
    {
        $method = 'resolve' . ucfirst($info->fieldName);

        // If field has custom resolver
        if (method_exists($this, $method)) {
            return $this->{$method} ($value, $args, $context, $info);
        } else {
            // if data is provided in assoc array format
            if (is_array($value)) {
                if (array_key_exists($info->fieldName, $value)) {
                    return $value[$info->fieldName];
                }
            } else {
                // if data is provided in object format
                if (property_exists($value, $info->fieldName)) {
                    return $value->{$info->fieldName};
                }
            }
        }

        // default value
        return null;
    }
}
