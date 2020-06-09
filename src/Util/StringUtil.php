<?php

declare(strict_types=1);

namespace Alpabit\ApiSkeleton\Util;

use Symfony\Component\String\Inflector\EnglishInflector;
use Symfony\Component\String\UnicodeString;

/**
 * @author Muhamad Surya Iksanudin<surya.iksanudin@alpabit.com>
 */
final class StringUtil
{
    public static function uppercase(string $string): string
    {
        return (new UnicodeString($string))->upper()->toString();
    }

    public static function lowercase(string $string): string
    {
        return (new UnicodeString($string))->lower()->toString();
    }

    public static function title(string $string, bool $allWords = true): string
    {
        return (new UnicodeString($string))->lower()->title($allWords)->toString();
    }

    public static function underscore(string $string): string
    {
        return (new UnicodeString($string))->snake()->toString();
    }

    public static function camelcase(string $string): string
    {
        return (new UnicodeString($string))->camel()->toString();
    }

    public static function dash(string $string): string
    {
        return (new UnicodeString($string))->snake()->replace('_', '-')->toString();
    }

    public static function plural(string $string): string
    {
        $plural = EnglishInflector::pluralize($string);
        if (is_array($plural)) {
            $plural = $plural[0];
        }

        return $plural;
    }
}
