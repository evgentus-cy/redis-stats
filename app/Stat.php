<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Agent\Agent;
use Cookie;
use Illuminate\Support\Facades\Redis;

class Stat extends Model {

    /**
     * Добавлям информацию о посещении
     */
    static function addStat() {

        // Получаем различную инфу о клиенте
        $agent = new Agent();

        // Получаем гео-данные по ИП адресу
        $geo = \SypexGeo::get();

        //  Получение или установка куки
        if (Cookie::has('key')) {
            $cookie = Cookie::get('key');
        } else {
            $cookie = \Illuminate\Support\Str::random(8);
            Cookie::queue(Cookie::forever('key', $cookie));
        }

        // Браузер
        $browser_family = (!empty($agent->browser())) ? $agent->browser() : 'Other';
        $browser_version = (!empty($agent->version($browser_family))) ? ' ' . $agent->version($browser_family) : '';
        $browser = $browser_family . $browser_version;

        // Ось
        $platform_family = (!empty($agent->platform())) ? $agent->platform() : 'Other';
        $platform_version = (!empty($agent->version($platform_family))) ? ' ' . $agent->version($platform_family) : '';
        $platform = $platform_family . $platform_version;

        // Код страны
        $country = $geo['country']['iso'];

        // Инфа о сеферрере
        $referer = getenv('HTTP_REFERER');
        $referer_host = (!empty($referer)) ? parse_url($referer, PHP_URL_HOST) : 'empty referer';

        // IP-адрес
        $ip = Stat::getIP();

        // Количество посещений с хоста и куки
        $host_browser = Redis::incr('host:' . $ip . ':browser:' . $browser);
        $cookie_browser = Redis::incr('cookie:' . $cookie . ':browser:' . $browser);

        $host_platform = Redis::incr('host:' . $ip . ':platform:' . $platform);
        $cookie_platform = Redis::incr('cookie:' . $cookie . ':platform:' . $platform);

        $host_country = Redis::incr('host:' . $ip . ':country:' . $country);
        $cookie_country = Redis::incr('cookie:' . $cookie . ':country:' . $country);

        $host_referer = Redis::incr('host:' . $ip . ':referer:' . $referer_host);
        $cookie_referer = Redis::incr('cookie:' . $cookie . ':referer:' . $referer_host);

        // Пишем
        Redis::pipeline(function ($pipe) use($platform, $browser, $country, $referer_host, $host_browser, $cookie_browser, $host_platform, $cookie_platform, $host_country, $cookie_country, $host_referer, $cookie_referer) {
            $pipe->zincrby('stats:platforms:visits', 1, $platform);
            $pipe->zincrby('stats:browsers:visits', 1, $browser);
            $pipe->zincrby('stats:countries:visits', 1, $country);
            $pipe->zincrby('stats:referers:visits', 1, $referer_host);

            if ($host_platform === 1) {
                $pipe->zincrby('stats:platforms:hosts', 1, $platform);
            }
            if ($host_browser === 1) {
                $pipe->zincrby('stats:browsers:hosts', 1, $browser);
            }
            if ($host_country === 1) {
                $pipe->zincrby('stats:countries:hosts', 1, $country);
            }
            if ($host_referer === 1) {
                $pipe->zincrby('stats:referers:hosts', 1, $referer_host);
            }


            if ($cookie_platform === 1) {
                $pipe->zincrby('stats:platforms:cookies', 1, $platform);
            }
            if ($cookie_browser === 1) {
                $pipe->zincrby('stats:browsers:cookies', 1, $browser);
            }
            if ($cookie_country === 1) {
                $pipe->zincrby('stats:countries:cookies', 1, $country);
            }
            if ($cookie_referer === 1) {
                $pipe->zincrby('stats:referers:cookies', 1, $referer_host);
            }
        });
    }

    /**
     * Статистика по платформам
     * @return array
     */
    static function getStatByPlatforms() {
        $platforms_visits = Redis::zrange('stats:platforms:visits', 0, -1, 'withscores');
        $platforms_hosts = Redis::zrange('stats:platforms:hosts', 0, -1, 'withscores');
        $platforms_cookies = Redis::zrange('stats:platforms:cookies', 0, -1, 'withscores');

        $platforms = [];

        foreach ($platforms_visits as $key => $val) {
            $platforms[$key] = ['visits' => $val, 'hosts' => (isset($platforms_hosts[$key])) ? $platforms_hosts[$key] : 0, 'cookies' => (isset($platforms_cookies[$key])) ? $platforms_cookies[$key] : 0];
        }

        return $platforms;
    }

    /**
     * Статистика по браузерам
     * @return array
     */
    static function getStatByBrowsers() {
        $browsers_visits = Redis::zrange('stats:browsers:visits', 0, -1, 'withscores');
        $browsers_hosts = Redis::zrange('stats:browsers:hosts', 0, -1, 'withscores');
        $browsers_cookies = Redis::zrange('stats:browsers:cookies', 0, -1, 'withscores');

        $browsers = [];

        foreach ($browsers_visits as $key => $val) {
            $browsers[$key] = ['visits' => $val, 'hosts' => (isset($browsers_hosts[$key])) ? $browsers_hosts[$key] : 0, 'cookies' => (isset($browsers_cookies[$key])) ? $browsers_cookies[$key] : 0];
        }

        return $browsers;
    }

    /**
     * Статистика по странам
     * @return array
     */
    static function getStatByCountries() {
        $countries_visits = Redis::zrange('stats:countries:visits', 0, -1, 'withscores');
        $countries_hosts = Redis::zrange('stats:countries:hosts', 0, -1, 'withscores');
        $countries_cookies = Redis::zrange('stats:countries:cookies', 0, -1, 'withscores');

        $countries = [];

        foreach ($countries_visits as $key => $val) {
            $countries[$key] = ['visits' => $val, 'hosts' => (isset($countries_hosts[$key])) ? $countries_hosts[$key] : 0, 'cookies' => (isset($countries_cookies[$key])) ? $countries_cookies[$key] : 0];
        }

        return $countries;
    }

    /**
     * Статистика по реферерам
     * @return array
     */
    static function getStatByReferers() {
        $referers_visits = Redis::zrange('stats:referers:visits', 0, -1, 'withscores');
        $referers_hosts = Redis::zrange('stats:referers:hosts', 0, -1, 'withscores');
        $referers_cookies = Redis::zrange('stats:referers:cookies', 0, -1, 'withscores');

        $referers = [];

        foreach ($referers_visits as $key => $val) {
            $referers[$key] = ['visits' => $val, 'hosts' => (isset($referers_hosts[$key])) ? $referers_hosts[$key] : 0, 'cookies' => (isset($referers_cookies[$key])) ? $referers_cookies[$key] : 0];
        }

        return $referers;
    }

    /**
     * Определяем IP
     * @return string IP
     */
    static function getIP() {
        if (getenv('HTTP_CLIENT_IP'))
            $ip = getenv('HTTP_CLIENT_IP');
        elseif (getenv('HTTP_X_FORWARDED_FOR'))
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        elseif (getenv('HTTP_X_FORWARDED'))
            $ip = getenv('HTTP_X_FORWARDED');
        elseif (getenv('HTTP_FORWARDED_FOR'))
            $ip = getenv('HTTP_FORWARDED_FOR');
        elseif (getenv('HTTP_FORWARDED'))
            $ip = getenv('HTTP_FORWARDED');
        else
            $ip = getenv('REMOTE_ADDR');

        if (strpos($ip, ',')) {
            $arr = explode(',', $ip);
            $ip = $arr[0];
        }

        return preg_replace("/[^0-9\.]/", "", $ip);
    }

}
