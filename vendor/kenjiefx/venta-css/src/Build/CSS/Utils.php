<?php

namespace Kenjiefx\VentaCss\Build\CSS;
use \Kenjiefx\VentaCss\Build\CSS\CSSModel;

class Utils {

    public static function parseRawCss(
        CSSModel $css
        )
    {
        preg_match_all(
            '/(?ims)([a-z0-9*\s\,\.\:#_\-@]+)\{([^\}]*)\}/',
            $css->getRaw(),
            $arr
        );
        foreach ($arr[0] as $i => $x) {
            # Registering a new css selector
            $selector = trim($arr[1][$i]);
            $css->createSelector($selector);
            $rules = explode(';', trim($arr[2][$i]));

            foreach ($rules as $strRule) {
                if (!empty($strRule)){
                    $rule = explode(":", $strRule);
                    $css->setAttribute(
                        selectorName: $selector,
                        property: trim($rule[0]),
                        value: trim($rule[1])
                    );
                }
            }
        }
    }

    /**
     * Creates a random 3-letter class name
     * @param array $existingClassNames to make sure that
     * the class name given do not yet exist within
     * the existing array of class names.
     */
    public static function createClassName(
        array $existingClassNames
        )
    {
        /**
         * Valid class names do not start with numeric characters
         * While we can loop over this method to arrive into
         * a valid class name, for performance consideration,
         * we delcare a predefined set of class name's
         * first character
         */
        $firsts = 'abcdefghijklmnopqrstuvwxyz';
        $chars  = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

        $isValid = true;
        $first   = substr(str_shuffle($firsts),(-1));
        $name    = $first.substr(str_shuffle($chars),(-2));

        // Invalidate a generated class name if it already exists
        if(isset($existingClassNames[$name])) $isValid = false;
        while(!$isValid) $name = createClassName();

        return $name;

    }

}
