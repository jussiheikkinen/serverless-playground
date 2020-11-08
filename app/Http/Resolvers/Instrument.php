<?php

namespace App\Http\Resolvers;

use App\Http\Controllers\GraphqlController;

class Instrument implements Resolver
{
    public $name;

    public function resolve($instrument, $args, $context, $info)
    {
        foreach(GraphqlController::getSelectionSet($info, 'instruments') as $fieldName => $val) {
            $method = 'resolve' . ucfirst($fieldName);

            if (method_exists($this, $method)) {
                $this->{$fieldName} = $this->{$method}($instrument, $args, $context, $info);
            } else {
                if (property_exists($this, $fieldName)) {
                    $this->{$fieldName} = $instrument->{$fieldName};
                }
            }
        }

        return $this;
    }
}
