<?php

use Soda\Core\Database\MongoDBClient;
use Soda\Core\Presentation\View;

function redirect($url) {
    ob_end_clean();
    header('Location: ' . $url);
    exit;
}

function notFound()
{
    header('HTTP/1.1 404 Not Found');
    $view = new View();
    die($view->getViewEngineInstance()->render('error.404'));
}

function unauthorized()
{
    header('HTTP/1.1 401 Unauthorized');
    $view = new View();
    die($view->getViewEngineInstance()->render('error.401'));
}

function forbidden()
{
    header('HTTP/1.1 403 Forbidden');
    $view = new View();
    die($view->getViewEngineInstance()->render('error.403'));
}

function setOpEs($errorsArr)
{
    $_SESSION['opes'] = $errorsArr;
}

function getOpEs()
{
    $errors = isset($_SESSION['opes']) ? $_SESSION['opes'] : null;

    unset($_SESSION['opes']);

    return $errors;
}

function setOpR($success, $msg)
{
    $_SESSION['opr_type'] = $success ? 'success' : 'failure';
    $_SESSION['opr_text'] = $msg;

    if($success)
        $_SESSION['opr_class'] = 'success';
    else
        $_SESSION['opr_class'] = 'danger';
}

function getOpR()
{
    $type = isset($_SESSION['opr_type']) ? $_SESSION['opr_type'] : null;
    $text = isset($_SESSION['opr_text']) ? $_SESSION['opr_text'] : null;
    $class = isset($_SESSION['opr_class']) ? $_SESSION['opr_class'] : null;

    unset($_SESSION['opr_type']);
    unset($_SESSION['opr_text']);
    unset($_SESSION['opr_class']);

    if($type == null || $text == null)
        return null;

    return ['type' => $type, 'text' => $text, 'class' => $class];
}

function getJSONInput()
{
    try
    {
        return json_decode(file_get_contents('php://input'), true);
    }
    catch (\Exception $e)
    {
        return [];
    }
}

function getBase64WithoutHeader($base64)
{
    return preg_replace('#^data:image/[^;]+;base64,#', '', $base64);
}

function getDataURI($pathToImageFile, $mime = '') {
    return 'data: '.(function_exists('mime_content_type') ? mime_content_type($pathToImageFile) : $mime).';base64,'.base64_encode(file_get_contents($pathToImageFile));
}

function generateRandomToken($length = 10, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
{
    $str = '';
    $max = mb_strlen($keyspace, '8bit') - 1;
    for ($i = 0; $i < $length; ++$i) {
        $str .= $keyspace[random_int(0, $max)];
    }
    return $str;
}

function setToken()
{
    $token = generateRandomToken();
    $_SESSION['_token'] = $token;
    return $token;
}

function getToken()
{
    $token = isset($_SESSION['_token'])?$_SESSION['_token']:null;
    $_SESSION = null;
    unset($_SESSION);
    return $token;
}

function ppd($arr)
{
    echo '<pre>';
    print_r($arr);
    echo '</pre>';
    exit;
}

function removeParamFromUrl($url, $param)
{
    $url = preg_replace('/(&|\?)'.preg_quote($param).'=[^&]*$/', '', $url);
    $url = preg_replace('/(&|\?)'.preg_quote($param).'=[^&]*&/', '$1', $url);
    return $url;
}

function loggedIn()
{
    if(isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === true && isset($_SESSION['_id']))
    {
        return true;
    }

    return false;
}

function companyLoggedIn()
{
    if(isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === true && isset($_SESSION['_id']) && isset($_SESSION['type']) && $_SESSION['type'] === 'company')
    {
        return true;
    }

    return false;
}

//function adminLoggedIn()
//{
//    if(isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === true && isset($_SESSION['id']) && isset($_SESSION['admin']) && $_SESSION['admin'] === true)
//    {
//        return true;
//    }
//
//    return false;
//}

function makeRedirectLogin($router, $redirect)
{
    if(starts_with($redirect, '/') === false)
        $redirect = '/' . $redirect;
    return '/' . $router->route('login') . '?redirect=' . $redirect;
}

function convertNumbers($srting, $toPersian = true)
{
    $en_num = array('0','1','2','3','4','5','6','7','8','9');
    $fa_num = array('۰','۱','۲','۳','۴','۵','۶','۷','۸','۹');
    if( $toPersian ) return str_replace($en_num, $fa_num, $srting);
    else return str_replace($fa_num, $en_num, $srting);
}

/**
 * @param int $unixtimestamp
 * @return string
 */
function toRelativeDate($unixtimestamp)
{
    $now = time();
    $diff = $now - $unixtimestamp;

    if ($diff < 60){
        return sprintf($diff > 1 ? '%s ثانیه پیش' : 'چند لحظه پیش', $diff);
    }

    $diff = floor($diff/60);

    if ($diff < 60){
        return sprintf($diff > 1 ? '%s دقیقه پیش' : 'یک دقیقه پیش', $diff);
    }

    $diff = floor($diff/60);

    if ($diff < 24){
        return sprintf($diff > 1 ? '%s ساعت پیش' : 'یک ساعت پیش', $diff);
    }

    $diff = floor($diff/24);

    if ($diff < 7){
        return sprintf($diff > 1 ? '%s روز پیش' : 'دیروز', $diff);
    }

    if ($diff < 30)
    {
        $diff = floor($diff / 7);

        return sprintf($diff > 1 ? '%s هفته پیش' : 'یک هفته پیش', $diff);
    }

    $diff = floor($diff/30);

    if ($diff < 12){
        return sprintf($diff > 1 ? '%s ماه پیش' : 'یک ماه پیش', $diff);
    }

    $diff = date('Y', $now) - date('Y', $unixtimestamp);

    return sprintf($diff > 1 ? '%s سال پیش' : 'یک سال پیش', $diff);
}

/**
 * Returns a relative string based on the passed $datetime
 * --VERY IMPORTANT-- If you pass the datetime in a localtime rather than UTC
 * you must also pass the EasyDateTime instance as that localtime instead of UTC
 *
 * @param int $unixtimestamp
 * @param int $depth
 * @return string
 */
function toRelativeTimeWithDepth($unixtimestamp, $depth = 1)
{
    $units = [
        "سال" => 31104000,
        "ماه" => 2592000,
        "هفته" => 604800,
        "روز" => 86400,
        "ساعت" => 3600,
        "دقیقه" => 60,
        "ثانیه" => 1
    ];

    $plural = "";
    $conjugator = " و ";
    $separator = ", ";
    $suffix1 = " پیش";
    $suffix2 = " گذشته";
    $now = "حالا";
    $empty = "";


//    $time = strtotime($edt->date());
//    $timediff = $time - strtotime($datetime);
    // Original code from the function creator, with this code you won't need EasyDateTime,
    // But it will require you to pass the datetime as UTC at all times
    $timediff = time() - $unixtimestamp;
    if ($timediff == 0) return $now;
    if ($depth < 1) return $empty;

    $max_depth = count($units);
    $remainder = abs($timediff);
    $output = "";
    $count_depth = 0;
    $fix_depth = true;

    foreach ($units as $unit=>$value) {
        if ($remainder>$value && $depth-->0) {
            if ($fix_depth) {
                $max_depth -= ++$count_depth;
                if ($depth >= $max_depth) $depth = $max_depth;
                $fix_depth = false;
            }
            $u = (int)($remainder / $value);
            $remainder %= $value;
            $pluralise = $u>1?$plural:$empty;
            $separate = $remainder==0||$depth==0?$empty:
                ($depth==1?$conjugator:$separator);
            $output .= "{$u} {$unit}{$pluralise}{$separate}";
        }
        $count_depth++;
    }
    return $output.($timediff<0?$suffix2:$suffix1);
}

function getSmartPersianDate($expression, $persianNumbers = true)
{
    $unixtimestamp = strtotime($expression);

    if($unixtimestamp >= strtotime('-30 days'))
    {
        $ret = toRelativeTimeWithDepth($unixtimestamp, 2);
    }
    else
    {
        $edt = new EasyDateTime('Asia/Tehran', 'jalali');
        $ret = $edt->date('d F Y H:i', $expression);
    }

    if($persianNumbers)
        $ret = convertNumbers($ret);

    return $ret;
}

function get1to10RatingColors($rateNum)
{
    switch ($rateNum)
    {
        case $rateNum > 0 && $rateNum < 2:
            return '#cf1d0b';
        case $rateNum > 1 && $rateNum < 3:
            return '#f02e1b';
        case $rateNum > 2 && $rateNum < 4:
            return '#f06928';
        case $rateNum > 3 && $rateNum < 5:
            return '#ff870b';
        case $rateNum > 4 && $rateNum < 6:
            return '#ffd700';
        case $rateNum > 5 && $rateNum < 7:
            return '#c1d13a';
        case $rateNum > 6 && $rateNum < 8:
            return '#b9d51e';
        case $rateNum > 7 && $rateNum < 9:
            return '#bee620';
        case $rateNum > 8 && $rateNum < 10:
            return '#8cdf1d';
        case 10:
            return '#58e610';
        default:
            return '#ffec09';
    }
}

function startsWith($haystack, $needle)
{
    $length = mb_strlen($needle, 'UTF-8');
    return (mb_substr($haystack, 0, $length, 'UTF-8') === $needle);
}

function endsWith($haystack, $needle)
{
    $length = mb_strlen($needle, 'UTF-8');
    return $length === 0 ||
        (mb_substr($haystack, -$length, $length, 'UTF-8') === $needle);
}

function prepareImagesPath($name)
{
    $path = "/content/images/".$name;

    return $path;
}

function isNullOrEmpty($var)
{
    if(!isset($var) || $var == null || $var == '')
        return true;
    return false;
}

function validateRawValue($var)
{
    if(!isNullOrEmpty($var))
        return $var;
    return null;
}

function setAbsoluteUrlForTrackListWithEmbeddedAlbum(&$list)
{
    foreach ($list as &$item)
    {
        if(isset($item['album']) && isset($item['type'])) {
            if($item['type'] == 'album') {
                setAbsoluteUrlForTracks($item['album']['tracks'], $item['album']['_id']);
            } else if($item['type'] == 'playlist') {
                setAbsoluteUrlForTrackListWithEmbeddedAlbum($item['album']['tracks']);
            }
        }

        if(isset($item['fileName'])) {
//            $item['fileName'] = BASE_MUSICS_URL . '/' . $item['album']['_id'] . '/' . $item['fileName'];
            $item['fileName'] = SITE_ADDRESS . '/access/music/' . $item['album']['_id'] . '/' . $item['fileName'];
        }
        if(isset($item['album']['image'])) {
            $item['album']['image'] = BASE_IMAGES_URL . "/" . $item['album']['image'];
        }
        if(isset($item['image'])) {
            $item['image'] = BASE_IMAGES_URL . "/" . $item['image'];
        }

        unset($item);
    }
}

function setAbsoluteUrlForTracks(&$list, $albumId)
{
    foreach ($list as &$item)
    {
        if(isset($item['album'])) {
            $innerAlbumId = $item['album']['_id'];
            if(isset($item['album']['image'])) {
                $item['album']['image'] = BASE_IMAGES_URL . "/" . $item['album']['image'];
            }
        }
        if(isset($item['fileName'])) {
//            $item['fileName'] = BASE_MUSICS_URL . '/' . (isset($innerAlbumId) ? $innerAlbumId : $albumId) . '/' . $item['fileName'];
            $item['fileName'] = SITE_ADDRESS . '/access/music/' . (isset($innerAlbumId) ? $innerAlbumId : $albumId) . '/' . $item['fileName'];
        }
        if(isset($item['image'])) {
            $item['image'] = BASE_IMAGES_URL . "/" . $item['image'];
        }

        unset($innerAlbumId);
        unset($item);
    }
}

function setAbsoluteUrlForAlbumsList(&$list)
{
    foreach ($list as &$item)
    {
        if(isset($item['tracks'])) {
            setAbsoluteUrlForTracks($item['tracks'], $item['_id']);
        }
        setAbsoluteUrlForRootImages($item);

        unset($item);
    }
}

function setAbsoluteUrlForPlaylistsList(&$list)
{
    foreach ($list as &$item)
    {
        if(isset($item['tracks'])) {
            setAbsoluteUrlForTrackListWithEmbeddedAlbum($item['tracks']);
        }
        setAbsoluteUrlForRootImages($item);

        unset($item);
    }
}

function setAbsoluteUrlForRootImages(&$item)
{
    if(isset($item['image'])) {
        $item['image'] = BASE_IMAGES_URL . "/" . $item['image'];
    }
}

function extractAuthorizationToken($token)
{
    return substr($token, 7);
}

function flattenStringArray($arr): string
{
    return implode (", ", $arr);
}

function array_unique_assoc($multiArray){

    $uniqueArray = array();

    foreach($multiArray as $subArray){

        if(!in_array($subArray, $uniqueArray)){
            $uniqueArray[] = $subArray;
        }
    }
    return $uniqueArray;
}

//function setAbsoluteUrlForSingleTrack(&$item, $albumId = "")
//{
//    if(isset($item['fileName'])) {
//        $item['fileName'] = BASE_MUSICS_URL . "/$albumId/" . $item['fileName'];
//    }
//    if(isset($item['album'])) {
//        if(isset($item['album']['image'])) {
//            $item['album']['image'] = BASE_IMAGES_URL . "/" . $item['album']['image'];
//        }
//    } else if($albumId != "") {
//
//    }
//}
//
//function setAbsoluteUrlForTracks(&$tracks, $albumId = "")
//{
//    foreach ($tracks as &$item)
//    {
//        setAbsoluteUrlForSingleTrack($item, $albumId);
//    }
//}
//
//function setAbsoluteUrlForLists(&$lists)
//{
//    foreach($lists as &$list)
//    {
//        if(isset($list['tracks']))
//        {
//            setAbsoluteUrlForTracks($list['tracks'], $list['_id']);
//        }
//        if(isset($list['fileName']))
//        {
//            setAbsoluteUrlForSingleTrack($list);
//        }
//        if(isset($list['image'])) {
//            $list['image'] = BASE_IMAGES_URL . "/" . $list['image'];
//        }
//    }
//}