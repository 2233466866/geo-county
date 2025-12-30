<?php

namespace GeojsonLookup;

/**
 * 县级行政区划编码查找器
 */
class CountyLocator
{
    private $geojsonData;
    
    /**
     * 构造函数
     * @param string $geojsonPath GeoJSON 文件路径
     */
    public function __construct(string $geojsonPath)
    {
        $geojsonPath = is_file($geojsonPath)?$geojsonPath:__DIR__.'/../county.json';
        $jsonContent = file_get_contents($geojsonPath);
        $data = json_decode($jsonContent, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('无效的JSON文件: ' . json_last_error_msg());
        }
        
        if (!isset($data['features']) || !is_array($data['features'])) {
            throw new \InvalidArgumentException('无效的GeoJSON格式');
        }
        
        $this->geojsonData = $data;
    }
    
    /**
     * 根据经纬度获取行政区划编码
     * @param float $longitude 经度
     * @param float $latitude 纬度
     * @return string|null 行政区划编码，未找到返回 null
     */
    public function getGbCode(float $longitude, float $latitude): ?string
    {
        foreach ($this->geojsonData['features'] as $feature) {
            if (!isset($feature['geometry']['type']) || $feature['geometry']['type'] !== 'MultiPolygon') {
                continue;
            }
            
            $coordinates = $feature['geometry']['coordinates'];
            
            if (PolygonHelper::pointInMultiPolygon($longitude, $latitude, $coordinates)) {
                return $feature['properties']['gb'] ?? null;
            }
        }
        
        return null;
    }
    
    /**
     * 批量查找行政区划编码
     * @param array $coordinates 坐标数组，格式：[['lng' => 经度, 'lat' => 纬度], ...]
     * @return array 结果数组，格式：[['lng' => 经度, 'lat' => 纬度, 'gb' => 编码], ...]
     */
    public function batchGetGbCodes(array $coordinates): array
    {
        $results = [];
        
        foreach ($coordinates as $coord) {
            if (!isset($coord['lng'], $coord['lat'])) {
                continue;
            }
            
            $gbCode = $this->getGbCode($coord['lng'], $coord['lat']);
            $results[] = [
                'longitude' => $coord['lng'],
                'latitude' => $coord['lat'],
                'gb_code' => $gbCode
            ];
        }
        
        return $results;
    }
    
    /**
     * 获取行政区划信息（包含编码和名称）
     * @param float $longitude 经度
     * @param float $latitude 纬度
     * @return array|null 返回包含 name 和 gb 的数组，未找到返回 null
     */
    public function getCountyInfo(float $longitude, float $latitude): ?array
    {
        foreach ($this->geojsonData['features'] as $feature) {
            if (!isset($feature['geometry']['type']) || $feature['geometry']['type'] !== 'MultiPolygon') {
                continue;
            }
            
            $coordinates = $feature['geometry']['coordinates'];
            
            if (PolygonHelper::pointInMultiPolygon($longitude, $latitude, $coordinates)) {
                return [
                    'name' => $feature['properties']['name'] ?? null,
                    'gb' => $feature['properties']['gb'] ?? null
                ];
            }
        }
        
        return null;
    }
}