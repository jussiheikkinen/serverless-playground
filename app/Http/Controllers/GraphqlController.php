<?php

namespace App\Http\Controllers;

use App\Http\Resolvers\Record;
use Exception;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Log;

class GraphqlController extends Controller
{
    /**
     * Root query resolvers
     */
    public static function getRootValues()
    {
        return [
            'node' => function ($root, $args, $context, ResolveInfo $info) {
                return null;
            },
            // This is made using my custom resolving logic which was trying
            // to achieve same as typeConfigDecorator
            'records' => function ($root, $args, $context, ResolveInfo $info) {
                return array_map(function ($record) use ($root, $args, $context, $info) {
                    return (new Record())->resolve((object)$record, $args, $context, $info);
                }, Record::getRecords($args));
            },
            // This id made using the right way using typeConfigDecorator
            'records_official' => function ($root, $args, $context) {
                return Record::getRecords($args);
            },
        ];
    }

    /**
     * Draft to resolve types automatically againt the data
     */
    public static function dynamicFieldResolver($obj, $rootValue, $args, $context, $info, $typeName)
    {
        foreach(self::getSelectionSet($info, $typeName) as $fieldName => $val) {
            $method = 'resolve' . ucfirst($fieldName);

            if (method_exists($obj, $method)) {
                $obj->{$fieldName} = $obj->{$method}($rootValue, $args, $context, $info);
            } else {
                if (property_exists($obj, $fieldName)) {
                    $obj->{$fieldName} = $rootValue->{$fieldName};
                }
            }
        }

        return $obj;
    }

    /**
     * Get queried fields from provided type
     */
    public static function getSelectionSet(ResolveInfo $info, $needle)
    {
        $values = self::array_find_recursively($needle, $info->getFieldSelection(10));
        return $values ?: $info->getFieldSelection();
    }

    /**
     * Search value from provided muultidimensional array
     */
    public static function array_find_recursively($needle, $haystack)
    {
        foreach($haystack as $key => $value) {
            if($key === $needle) {
                return $value;
            } else if (is_array($value)) {
                $match = self::array_find_recursively($needle, $value);
                if ($match) {
                    return $match;
                }
            }
        }
        return false;
    }

    /**
     * Read data from mock json file
     */
    public static function getData()
    {
        try {
            $fileContents = file_get_contents(__DIR__.'/../../../storage/fakedata.json');
            return json_decode($fileContents, true);
        } catch (Exception $e) {
            return null;
        }
    }
}
