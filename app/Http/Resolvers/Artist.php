<?php

namespace App\Http\Resolvers;

use App\Http\Controllers\GraphqlController;

class Artist implements Resolver
{
    public $id;
    public $name;
    public $members;

    public static function getArtist($needle)
    {
        $data = GraphqlController::getData();
        if (!empty($data)) {
            foreach($data['artists'] as $k => $value) {
                if ($value['id'] === $needle) {
                    return $value;
                }
            }
        }
        return [];
    }

    public function resolve($artist, $args, $context, $info)
    {
        foreach(GraphqlController::getSelectionSet($info, 'artist') as $fieldName => $val) {
            $method = 'resolve' . ucfirst($fieldName);

            if (method_exists($this, $method)) {
                $this->{$fieldName} = $this->{$method}($artist, $args, $context, $info);
            } else {
                if (property_exists($this, $fieldName)) {
                    $this->{$fieldName} = $artist->{$fieldName};
                }
            }
        }

        return $this;
    }

    private function resolveMembers($artist, $args, $context, $info)
    {
        return array_map(function ($member) use ($args, $context, $info) {
            return (new Member())->resolve((object)$member, $args, $context, $info);
        }, $artist->members);
    }
}
