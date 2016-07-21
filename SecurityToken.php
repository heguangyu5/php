<?php
/**
 * 生成授权码
 *
 * 在很多地方,我们都需要授权一个不明身份的人访问指定的资源.
 *
 * 常见的做法是临时生成一个随机的access key,放到数据库里,然后将这个key发送给指定的人.
 * 之后,只要拿着正确的key的人,我们就授权他访问相应的资源.
 *
 * 上述方法的优点是key是完全随机的,安全性高;缺点是实现麻烦,需要数据库存储.
 * 并且当在应用程序多处需要使用该key时,必须保存在可随时访问的变量里.
 *
 * 这里我们换个思路: 
 * 对指定的资源,我们使用签名算法做出一个签名(需要加salt),然后将签名发给需要的人.
 * 只要salt不泄露,虽然同一资源的签名始终不变,也是安全的.
 * 进一步的,我们定期更换salt,提升安全性.
 *
 * 给每家客户使用的salt应该不同,不然在A系统生成的签名拿到B系统也可以用
 *
 * 注: OST short for OurATS signature token
 */
class Ourats_SecurityToken
{
    public static $ID;

    /**
     * 始终保留两个salt,以版本号区分
     * 第1个salt是最新版本的,第2个salt是上个版本的,每个salt的有效期有两个月
     * 这样,新生成的token始终用最新的salt,同时我们也在两个月内支持之前salt生成的签名
     */
    protected static $salt = array(
        1001 => 'M6eouODi/oOy/JQ9i2L79PDtMeOiV9/O5J41O+G+IZOWXf22mZ+nh3YfuvKmur1NxNDKsZxkGH3h',
        1000 => 'EtICVFsfwUKOyrZX5Ebt47uSL9bdunrtk650Bx0iykXmtCO2lQPoCL/na/gSqXJL7vi+6hC64idV'
    );

    protected static $today;

    /**
     * resource 标明是什么资源
     * id       资源唯一标识
     * expired  过期时间
     * version  使用的salt版本
     */
    protected static function generate($resource, $id, $expired = '', $version = 0)
    {
        if (!self::$ID) {
            throw new Exception('ID should be set before generate');
        }

        $version = (int)$version;
        if ($version == 0) {
            $salt    = current(self::$salt);
            $version = key(self::$salt);
        } else {
            if (!isset(self::$salt[$version])) {
                return;
            }
            $salt = self::$salt[$version];
        }

        // 5184000 = 60 * 24 * 3600
        $expired = 'EXPIRED' . ($expired ? $expired : date('Y-m-d', time() + 5184000));

        return $version . sha1($salt . self::$ID . 'RES' . $resource . 'ID' . $id . $expired);
    }

    /**
     * ost      待验证的签名
     * resource 标明是什么资源
     * id       资源唯一标识
     * expired  过期时间
     * version  使用的salt版本
     */
    protected static function validate($ost, $resource, $id, $expired)
    {
        if (strlen($ost) != 44) {
            return false;
        }

        if (strlen($expired) != 10) {
            return false;
        }

        if (!self::$today) {
            self::$today = date('Y-m-d');
        }
        if ($expired < self::$today) {
            return false;
        }

        return $ost == self::generate($resource, $id, $expired, substr($ost, 0, 4));
    }

    public static function generateResetPasswordOST($uid, $updateDate, $expired)
    {
        return self::generate('ResetPassword', $uid . '-' . $updateDate, $expired);
    }

    public static function validateResetPasswordOST($ost, $uid, $updateDate, $expired)
    {
        return self::validate($ost, 'ResetPassword', $uid . '-' . $updateDate, $expired);
    }
}
