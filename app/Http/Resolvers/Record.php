<?php

namespace App\Http\Resolvers;

use App\Http\Controllers\GraphqlController;

class Record implements Resolver
{
    public $id;
    public $name;
    public $artist;
    public $timestamp;
    public $coverArt;

    public static function getRecords($args)
    {
        $data = GraphqlController::getData();
        if (!empty($data)) {
            return $data['records'][$args['genre']] ?? [];
        }
        return [];
    }

    public function resolve($record, $args, $context, $info)
    {
        foreach(GraphqlController::getSelectionSet($info, 'records') as $fieldName => $val) {
            $method = 'resolve' . ucfirst($fieldName);

            if (method_exists($this, $method)) {
                $this->{$fieldName} = $this->{$method}($record, $args, $context, $info);
            } else {
                if (property_exists($this, $fieldName)) {
                    $this->{$fieldName} = $record->{$fieldName};
                }
            }
        }

        return $this;
    }

    private function resolveCoverArt($record, $args, $context, $info)
    {
        return "../assets/{$record['coverArt']}";
    }

    private function resolveArtist($record, $args, $context, $info)
    {
        $artist = Artist::getArtist($record->artist_id);
        return (new Artist())->resolve((object)$artist, $args, $context, $info);
    }
}
