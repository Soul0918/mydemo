<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/13
 * Time: 10:20
 */

namespace Common\Lib;

class Hcpms
{
    protected static function asset_modify($source, $fileid, $orderid)
    {
        D('Asset')->where([
            'source'   => $source,
            'order_id' => $orderid
        ])->save([
            'status' => -1
        ]);
        if (is_array($fileid) && count($fileid) > 0) {
            D('Asset')->where([
                'aid' => [
                    'in',
                    join(',', $fileid)
                ]
            ])->save([
                'status'   => 1,
                'order_id' => $orderid
            ]);
        } else {
            if ($fileid) {
                D('Asset')->where([
                    'aid' => $fileid
                ])->save([
                    'status'   => 1,
                    'order_id' => $orderid
                ]);
            }
        }
    }

    /**
     * 公司
     *
     * @param array $fileid
     * @param int $orderid
     */
    public static function asset_company($fileid, $orderid)
    {
        if (empty ($fileid)) {
            return;
        }
        self::asset_modify(1, $fileid, $orderid);
    }

    /**
     *
     * @param array $fileid
     * @param int $orderid
     */
    public static function asset_avatar($fileid, $orderid)
    {
        if (empty ($fileid)) {
            return;
        }
        self::asset_modify(3, $fileid, $orderid);
    }

    /**
     *
     * @param fileid
     * @param $orderid
     */
    public static function asset_public($fileid, $orderid)
    {
        if (empty ($fileid)) {
            return;
        }
        self::asset_modify(2, $fileid, $orderid);
    }

    /**
     *
     * @param $fileid
     * @param $orderid
     */
    public static function asset_guide($fileid, $orderid)
    {
        // if (empty($fileid)) {
        // return ;
        // }
        self::asset_modify(6, $fileid, $orderid);
    }

    /**
     *
     * @param $fileid
     * @param $orderid
     */
    public static function asset_express($fileid, $orderid)
    {
        if (empty ($fileid)) {
            return;
        }
        self::asset_modify(7, $fileid, $orderid);
    }

    /**
     *
     * @param $fileid
     * @param $orderid
     */
    public static function asset_bills($fileid, $orderid)
    {
        if (empty ($fileid)) {
            return;
        }
        self::asset_modify(8, $fileid, $orderid);
    }

    /**
     * 广告图片
     *
     * @param $fileid
     * @param $orderid
     */
    public static function asset_ads($fileid, $orderid)
    {
        if (empty ($fileid)) {
            return;
        }
        self::asset_modify(9, $fileid, $orderid);
    }

    /**
     * 积分商城图片
     *
     * @param $fileid
     * @param $orderid
     */
    public static function asset_cmalls($fileid, $orderid)
    {
        if (empty ($fileid)) {
            return;
        }
        self::asset_modify(10, $fileid, $orderid);
    }

    /**
     * 富文本图片
     *
     * @param $fileid
     * @param $orderid
     */
    public static function asset_ueditor($fileid, $orderid)
    {
        if (empty ($fileid)) {
            return;
        }
        self::asset_modify(11, $fileid, $orderid);
    }

    /**
     * 报修
     *
     * @param $fileid
     * @param $orderid
     */
    public static function asset_repair($fileid, $orderid)
    {
        if (empty ($fileid)) {
            return;
        }
        self::asset_modify(12, $fileid, $orderid);
    }

    /**
     * 社区公告
     * @param $fileid
     * @param $orderid
     */
    public static function asset_notice($fileid, $orderid)
    {
        self::asset_modify(13, $fileid, $orderid);
    }
    
    /**
     * 报修
     *
     * @param $fileid
     * @param $orderid
     */
    public static function asset_autorep($fileid, $orderid)
    {
    	if (empty ($fileid)) {
    		return;
    	}
    	self::asset_modify(14, $fileid, $orderid);
    }
    
    /**
     * 邻里通发布互助
     * @param $fileid
     * @param $orderid
     */
    public static function asset_msrelease($fileid, $orderid)
    {
    	if (empty ($fileid)) {
    		return;
    	}
    	self::asset_modify(15, $fileid, $orderid);
    }
    
    
    
    /**
     * 
     * @param $fileid
     * @param $orderid
     */
    public static function asset_icon($fileid, $orderid)
    {
    	if (empty ($fileid)) {
    		return;
    	}
    	self::asset_modify(16, $fileid, $orderid);
    }
    
    /**
     * 邻里通发布买卖
     * @param $fileid
     * @param $orderid
     */
    public static function asset_mbrelease($fileid, $orderid)
    {
    	if (empty ($fileid)) {
    		return;
    	}
    	self::asset_modify(17, $fileid, $orderid);
    }
    
    /**
     * 关注后回复封面图片
     *
     * @param $fileid
     * @param $orderid
     */
    public static function asset_autocoverrep($fileid, $orderid)
    {
    	if (empty ($fileid)) {
    		return;
    	}
    	self::asset_modify(18, $fileid, $orderid);
    }
    
    /**
     * 关键字回复封面图片
     *
     * @param $fileid
     * @param $orderid
     */
    public static function asset_autorepcoverimg($fileid, $orderid)
    {
    	if (empty ($fileid)) {
    		return;
    	}
    	self::asset_modify(19, $fileid, $orderid);
    }
}