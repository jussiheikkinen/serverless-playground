<?php

namespace App\Http\Resolvers;

use App\Http\Controllers\GraphqlController;

class Member implements Resolver
{
    public $name;
    public $birthday;
    public $instruments;

    public function resolve($member, $args, $context, $info)
    {
        foreach(GraphqlController::getSelectionSet($info, 'members') as $fieldName => $val) {
            $method = 'resolve' . ucfirst($fieldName);

            if (method_exists($this, $method)) {
                $this->{$fieldName} = $this->{$method}($member, $args, $context, $info);
            } else {
                if (property_exists($this, $fieldName)) {
                    $this->{$fieldName} = $member->{$fieldName};
                }
            }
        }

        return $this;
    }

    private function resolveInstruments($member, $args, $context, $info)
    {
        if (!property_exists($member, 'instruments')) return null;

        return array_map(function ($instrument) use ($args, $context, $info) {
            return (new Instrument())->resolve((object)$instrument, $args, $context, $info);
        }, $member->instruments);
    }
}
