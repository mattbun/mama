<?php

/*
 * Functions that are useful for all database code, not any specific database type.
 */

// Modifies a string to make it sort better in lists. Specifically, it turns
// all characters to lower case and removes the word "the" from the beginning 
// of the string
function createSortName($name){
        $name = strtolower($name);
        $words = array("the"); //TODO Add other languages (la, le, los...), allow user to modify this list

        foreach ($words as $word){
                if (strlen($name) > strlen($word) && substr($name, 0, strlen($word) + 1) == ($word . " ")){
                        return substr($name, strlen($word) + 1);
                }
        }

        return $name;
}

?>
