<?php

namespace App\Http\TypeResolvers;

/**
 * This class resolves only queried fields
 */
class RecordResolver extends TypeResolver
{

    /**
     * Resolver to resolve something that was not in
     * the data provided to parent classes resolve function
     */
    protected function resolveSomething()
    {
        return 'resolved something';
    }

    protected function resolveThisRecord($value)
    {
        return $value;
    }
}
