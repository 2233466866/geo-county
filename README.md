# GeoJSON 行政区划编码查找器

根据经纬度查找中国县级行政区划编码的PHP库。

## 技术支持

2233466866@qq.com

## 安装

使用 Composer 安装：

```bash
composer require perfect/geo-county
```

## 使用方法

### 基本用法

```php
require 'vendor/autoload.php';

use GeojsonLookup\CountyLocator;

// 初始化查找器（需要 GeoJSON 文件路径）
$locator = new CountyLocator('path/to/your/geojson.json');

// 获取行政区划编码
$gbCode = $locator->getGbCode(115.008321, 30.407552);
// 返回: "156420704"

// 获取完整行政区划信息
$info = $locator->getCountyInfo(115.008321, 30.407552);
// 返回: ['name' => '鄂城区', 'gb' => '156420704']

// 批量查找
$coordinates = [
    ['lng' => 115.008321, 'lat' => 30.407552],
    ['lng' => 107.2514, 'lat' => 26.795673],
];
$results = $locator->batchGetGbCodes($coordinates);
```

