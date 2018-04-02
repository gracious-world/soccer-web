<?php

use Illuminate\Support\Str;

class String extends Str
{
    public static function humenlize($sString){
        return ucwords(str_replace(array('-', '_'), ' ', parent::snake($sString)));
//      return str_replace(' ', '', $value);
    }

    /**
     * 去除字符串中的脚本
     *
     * @param String $mString
     * @param boolean $bStripTag        是否一并去取HTML标签
     * @return String
     */
    public static function stripScript($mString, $bStripTag = false, $bTrim = false) {
        if (is_array($mString)) {
            foreach ($mString as $key => $val) {
                $mString[$key] = self::stripScript($val, $bStripTag, $bTrim);
            }
        } else {
            $sPattern = array("!<script.*>.*</script>!Uis", "!<\?.*\?>!Uis", "!<%.*%>!Uis");
            $mString = preg_replace($sPattern, '', $mString);
            !$bStripTag or $mString = strip_tags($mString);
            !$bTrim or $mString = trim($mString);
        }

        return $mString;
    }

    /**
     * 将字符串转换为SQL安全的
     *
     * @param String $string
     * @param boolean $force            是否强制
     * @return unknown
     */
    public static function sqlSafe($string, $force = false) {
        if (isset($GLOBALS['magic_quotes_gpc']))
            $magic_quotes_gpc = $GLOBALS['magic_quotes_gpc'];
        else
            $magic_quotes_gpc = get_magic_quotes_gpc();

        if (!$magic_quotes_gpc || $force) {
            if (is_array($string)) {
                foreach ($string as $key => $val) {
                    $string[$key] = self::sqlSafe($val, $force);
                }
            } else {
                $string = addslashes($string);
            }
        }
        return $string;
    }

}