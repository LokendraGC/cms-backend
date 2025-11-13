<?php

namespace App\Enums;

class SocialMediaType
{
    const FACEBOOK = 'fa-facebook';
    const TWITTER = 'fa-x-twitter';
    const INSTAGRAM = 'fa-instagram';
    const YOUTUBE = 'fa-youtube';
    const PINTEREST = 'fa-pinterest-p';
    const LINKEDIN = 'fa-linkedin';
    const WHATSAPP = 'fa-whatsapp';
    const TIKTOK = 'fa-tiktok';


    public static function toArray()
    {
        return [
            self::FACEBOOK,
            self::INSTAGRAM,
            self::TWITTER,
            self::YOUTUBE,
            self::PINTEREST,
            self::LINKEDIN,
            self::WHATSAPP,
            self::TIKTOK,

        ];
    }

    // for separate displaying text and value
    public static function getKeyValuePairs()
    {
        $keyValuePairs = [];
        $keyValuePairs['Facebook'] = self::FACEBOOK;
        $keyValuePairs['Instagram'] = self::INSTAGRAM;
        $keyValuePairs['Twitter'] = self::TWITTER;
        $keyValuePairs['Youtube'] = self::YOUTUBE;
        $keyValuePairs['Pinterest'] = self::PINTEREST;
        $keyValuePairs['Linkedin'] = self::LINKEDIN;
        $keyValuePairs['Whatsapp'] = self::WHATSAPP;
        $keyValuePairs['Tiktok'] = self::TIKTOK;



        return $keyValuePairs;
    }
}
