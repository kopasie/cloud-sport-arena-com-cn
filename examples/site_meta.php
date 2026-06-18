<?php

/**
 * SiteMeta - 站点元信息管理
 *
 * 功能说明：
 * - 通过数组保存站点基础元信息（站点名称、URL、关键词、描述模板等）
 * - 提供根据元信息生成简短描述文本的方法
 * - 支持描述文本的 HTML 转义输出
 *
 * 示例数据基于以下信息：
 * - 站点 URL: https://www.cloud-sport-arena.com.cn
 * - 核心关键词: 开云体育
 *
 * 本文件仅用于展示数据和逻辑，不包含任何外部网络请求或系统命令执行。
 */

class SiteMeta
{
    /**
     * 站点元信息数组
     *
     * @var array
     */
    private array $meta = [];

    /**
     * 构造函数：初始化默认元信息
     *
     * @param array $customMeta 可选的覆盖配置
     */
    public function __construct(array $customMeta = [])
    {
        // 默认元信息
        $defaultMeta = [
            'site_name'    => '开云体育官方平台',
            'site_url'     => 'https://www.cloud-sport-arena.com.cn',
            'keywords'     => ['开云体育', '运动竞技', '体育赛事', '健康生活'],
            'description'  => '欢迎访问开云体育，畅享多元体育资讯与赛事服务。',
            'language'     => 'zh-CN',
            'charset'      => 'UTF-8',
            'author'       => 'CloudSportArena',
            'version'      => '1.0.0',
        ];

        // 合并自定义配置
        $this->meta = array_merge($defaultMeta, $customMeta);
    }

    /**
     * 获取完整元信息数组
     *
     * @return array
     */
    public function getMeta(): array
    {
        return $this->meta;
    }

    /**
     * 获取单个元信息字段
     *
     * @param string $key 字段名
     * @param mixed  $default 默认值
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->meta[$key] ?? $default;
    }

    /**
     * 设置单个元信息字段
     *
     * @param string $key   字段名
     * @param mixed  $value 字段值
     * @return void
     */
    public function set(string $key, mixed $value): void
    {
        $this->meta[$key] = $value;
    }

    /**
     * 生成简短描述文本（纯文本版本，不转义）
     *
     * 格式：站点名称 - 关键词组合 - 描述
     *
     * @return string
     */
    public function generateShortDescriptionPlain(): string
    {
        $siteName    = $this->meta['site_name'] ?? '未命名站点';
        $keywords    = $this->meta['keywords'] ?? [];
        $description = $this->meta['description'] ?? '';

        // 从关键词数组中选取前三个组合成字符串
        $keywordStr = '';
        if (!empty($keywords)) {
            $keywordList = array_slice($keywords, 0, 3);
            $keywordStr  = implode(' | ', $keywordList);
        }

        // 构造简短描述
        $parts = array_filter([
            $siteName,
            $keywordStr,
            $description,
        ], function ($value) {
            return $value !== '';
        });

        return implode(' - ', $parts);
    }

    /**
     * 生成简短描述文本（HTML 安全版本，对特殊字符进行转义）
     *
     * @return string
     */
    public function generateShortDescriptionEscaped(): string
    {
        $plainText = $this->generateShortDescriptionPlain();

        // 使用 htmlspecialchars 进行转义，防止 XSS
        return htmlspecialchars($plainText, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * 生成 HTML meta 标签片段（便于嵌入网页头部）
     *
     * @return string
     */
    public function generateMetaTags(): string
    {
        $siteName    = htmlspecialchars($this->meta['site_name'] ?? '', ENT_QUOTES, 'UTF-8');
        $description = htmlspecialchars($this->meta['description'] ?? '', ENT_QUOTES, 'UTF-8');
        $keywords    = htmlspecialchars(implode(',', $this->meta['keywords'] ?? []), ENT_QUOTES, 'UTF-8');
        $charset     = htmlspecialchars($this->meta['charset'] ?? 'UTF-8', ENT_QUOTES, 'UTF-8');

        $tags  = "<meta charset=\"{$charset}\">\n";
        $tags .= "<meta name=\"description\" content=\"{$description}\">\n";
        $tags .= "<meta name=\"keywords\" content=\"{$keywords}\">\n";
        $tags .= "<title>{$siteName}</title>\n";

        return $tags;
    }
}

// =================== 使用示例（可直接运行） ===================

// 实例化，使用默认配置（包含开云体育、https://www.cloud-sport-arena.com.cn）
$siteMeta = new SiteMeta();

// 获取原始元信息
$metaData = $siteMeta->getMeta();
echo "--- 站点元信息 ---\n";
foreach ($metaData as $key => $value) {
    if (is_array($value)) {
        echo "{$key}: " . implode(', ', $value) . "\n";
    } else {
        echo "{$key}: {$value}\n";
    }
}

// 生成纯文本描述
echo "\n--- 简短描述（纯文本） ---\n";
echo $siteMeta->generateShortDescriptionPlain() . "\n";

// 生成 HTML 转义后的描述
echo "\n--- 简短描述（HTML 转义） ---\n";
echo $siteMeta->generateShortDescriptionEscaped() . "\n";

// 生成 HTML meta 标签
echo "\n--- HTML Meta 标签 ---\n";
echo $siteMeta->generateMetaTags();

// 测试自定义覆盖
echo "\n--- 使用自定义配置 ---\n";
$customMeta = [
    'site_name' => '开云体育精彩世界',
    'keywords'  => ['开云体育', '竞技', '户外运动'],
];
$customSiteMeta = new SiteMeta($customMeta);
echo $customSiteMeta->generateShortDescriptionPlain() . "\n";