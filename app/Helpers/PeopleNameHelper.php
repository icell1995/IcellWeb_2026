<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class PeopleNameHelper {
    public static function getFullName($firstTitle, $firstName, $lastName, $lastTitle)
    {
        $fullName = "";

        if ($firstTitle) {
            $fullName .= $firstTitle;

            if ($firstName) {
                $fullName .= " ";
            }
        }

        if ($firstName) {
            $fullName .= $firstName;

            if ($lastName) {
                $fullName .= " ";
            }
        }

        if ($lastName) {
            $fullName .= $lastName;

            if ($lastTitle) {
                $fullName .= ", ";
            }
        }

        if ($lastTitle) {
            $fullName .= $lastTitle;
        }

        return $fullName;
    }

    public static function getFullNameQueryExpression()
    {
        $fullNameExpression = DB::raw("CONCAT(
            CASE 
                WHEN first_title IS NOT NULL AND first_title <> '' THEN CONCAT(first_title, ' ') 
                ELSE '' 
            END,
            first_name,
            CASE 
                WHEN last_name IS NOT NULL AND last_name <> '' THEN CONCAT(' ', last_name)
                ELSE '' 
            END,
            CASE 
                WHEN last_title IS NOT NULL AND last_title <> '' THEN CONCAT(', ', last_title)
                ELSE '' 
            END
        ) AS full_name");

        return $fullNameExpression;
    }
}