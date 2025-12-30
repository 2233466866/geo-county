<?php

namespace GeojsonLookup;

/**
 * 多边形几何计算辅助类
 */
class PolygonHelper
{
    /**
     * 判断点是否在 MultiPolygon 内
     * @param float $longitude 经度
     * @param float $latitude 纬度
     * @param array $multiPolygon 多边形坐标数组
     * @return bool
     */
    public static function pointInMultiPolygon(float $longitude, float $latitude, array $multiPolygon): bool
    {
        foreach ($multiPolygon as $polygon) {
            // 检查外环
            if (self::pointInPolygon($longitude, $latitude, $polygon[0])) {
                // 检查是否在孔洞内
                $inHole = false;
                for ($i = 1; $i < count($polygon); $i++) {
                    if (self::pointInPolygon($longitude, $latitude, $polygon[$i])) {
                        $inHole = true;
                        break;
                    }
                }
                if (!$inHole) {
                    return true;
                }
            }
        }
        return false;
    }
    
    /**
     * 判断点是否在多边形内（射线法）
     * @param float $longitude 经度
     * @param float $latitude 纬度
     * @param array $polygon 多边形坐标数组
     * @return bool
     */
    public static function pointInPolygon(float $longitude, float $latitude, array $polygon): bool
    {
        $n = count($polygon);
        $inside = false;
        
        for ($i = 0, $j = $n - 1; $i < $n; $j = $i++) {
            $xi = $polygon[$i][0];
            $yi = $polygon[$i][1];
            $xj = $polygon[$j][0];
            $yj = $polygon[$j][1];
            
            // 检查点是否在边的水平线上
            $intersect = (($yi > $latitude) != ($yj > $latitude))
                && ($longitude < ($xj - $xi) * ($latitude - $yi) / ($yj - $yi) + $xi);
            
            if ($intersect) {
                $inside = !$inside;
            }
        }
        
        return $inside;
    }
}