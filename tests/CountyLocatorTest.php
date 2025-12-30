<?php

namespace GeojsonLookup\Tests;

use PHPUnit\Framework\TestCase;
use GeojsonLookup\CountyLocator;

class CountyLocatorTest extends TestCase
{
    private $locator;
    
    protected function setUp(): void
    {
        // 使用示例 GeoJSON 文件
        $this->locator = new CountyLocator(__DIR__ . '/../example.json');
    }
    
    public function testGetGbCode()
    {
        $gbCode = $this->locator->getGbCode(115.008321, 30.407552);
        $this->assertEquals('156420704', $gbCode);
        
        $gbCode = $this->locator->getGbCode(107.2514, 26.795673);
        $this->assertEquals('156522730', $gbCode);
    }
    
    public function testGetCountyInfo()
    {
        $info = $this->locator->getCountyInfo(115.008321, 30.407552);
        $this->assertIsArray($info);
        $this->assertArrayHasKey('name', $info);
        $this->assertArrayHasKey('gb', $info);
        $this->assertEquals('156420704', $info['gb']);
    }
    
    public function testBatchGetGbCodes()
    {
        $coordinates = [
            ['lng' => 115.008321, 'lat' => 30.407552],
            ['lng' => 107.2514, 'lat' => 26.795673],
        ];
        
        $results = $this->locator->batchGetGbCodes($coordinates);
        $this->assertCount(2, $results);
        $this->assertEquals('156420704', $results[0]['gb_code']);
        $this->assertEquals('156522730', $results[1]['gb_code']);
    }
}