<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SitemapController extends Controller
{
    /** Maximum URLs per chunked sitemap (Google standard is 50,000, 5,000 ensures sub-second rendering) */
    public const CHUNK_SIZE = 5000;

    /** Cache duration: 24 hours (auto-flushed whenever products/categories change) */
    public const CACHE_TTL = 86400;

    /**
     * Master Sitemap Index.
     * Google and other crawlers read this and discover all child sitemaps.
     */
    public function index(): Response
    {
        $xml = Cache::remember('sitemap:index', self::CACHE_TTL, function () {
            $baseUrl = rtrim(url('/'), '/');
            $nowAtom = now()->toAtomString();

            $productCount = DB::table('products')->where('is_active', 1)->count();
            $latestProductUpdate = DB::table('products')->where('is_active', 1)->max('updated_at');
            $latestCategoryUpdate = DB::table('categories')->where('is_active', 1)->max('updated_at');
            $latestPageUpdate = DB::table('pages')->where('is_active', 1)->max('updated_at');

            $productMod = $latestProductUpdate ? date('c', strtotime($latestProductUpdate)) : $nowAtom;
            $categoryMod = $latestCategoryUpdate ? date('c', strtotime($latestCategoryUpdate)) : $nowAtom;
            $pageMod = $latestPageUpdate ? date('c', strtotime($latestPageUpdate)) : $nowAtom;

            $w = new \XMLWriter();
            $w->openMemory();
            $w->setIndent(true);
            $w->startDocument('1.0', 'UTF-8');
            $w->writePI('xml-stylesheet', 'type="text/xsl" href="/sitemap.xsl"');
            $w->startElement('sitemapindex');
            $w->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

            // Static & CMS Pages
            $w->startElement('sitemap');
            $w->writeElement('loc', $baseUrl . '/sitemap-pages.xml');
            $w->writeElement('lastmod', $pageMod);
            $w->endElement();

            // Categories & Brands
            $w->startElement('sitemap');
            $w->writeElement('loc', $baseUrl . '/sitemap-categories.xml');
            $w->writeElement('lastmod', $categoryMod);
            $w->endElement();

            // Product Sitemaps
            if ($productCount <= self::CHUNK_SIZE) {
                $w->startElement('sitemap');
                $w->writeElement('loc', $baseUrl . '/sitemap-products.xml');
                $w->writeElement('lastmod', $productMod);
                $w->endElement();
            } else {
                $totalPages = (int) ceil($productCount / self::CHUNK_SIZE);
                for ($p = 1; $p <= $totalPages; $p++) {
                    $w->startElement('sitemap');
                    $w->writeElement('loc', $baseUrl . "/sitemap-products-{$p}.xml");
                    $w->writeElement('lastmod', $productMod);
                    $w->endElement();
                }
            }

            $w->endElement(); // sitemapindex
            $w->endDocument();

            return $w->outputMemory();
        });

        return $this->xmlResponse($xml);
    }

    /**
     * Primary Products Sitemap (serves chunk 1).
     */
    public function products(): Response
    {
        return $this->productsChunk(1);
    }

    /**
     * Chunked Products Sitemap (supports thousands of products with zero memory overhead).
     */
    public function productsChunk(int $page = 1): Response
    {
        if ($page < 1) {
            $page = 1;
        }

        $cacheKey = "sitemap:products:{$page}";

        $xml = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($page) {
            $baseUrl = rtrim(url('/'), '/');
            $offset = ($page - 1) * self::CHUNK_SIZE;

            $products = DB::table('products')
                ->where('is_active', 1)
                ->select(['slug', 'name', 'main_image', 'updated_at'])
                ->orderBy('id', 'desc')
                ->offset($offset)
                ->limit(self::CHUNK_SIZE)
                ->cursor();

            $w = new \XMLWriter();
            $w->openMemory();
            $w->setIndent(false);
            $w->startDocument('1.0', 'UTF-8');
            $w->writePI('xml-stylesheet', 'type="text/xsl" href="/sitemap.xsl"');
            $w->startElement('urlset');
            $w->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
            $w->writeAttribute('xmlns:image', 'http://www.google.com/schemas/sitemap-image/1.1');

            foreach ($products as $product) {
                $w->startElement('url');
                $w->writeElement('loc', $baseUrl . '/products/' . $product->slug);
                if ($product->updated_at) {
                    $w->writeElement('lastmod', date('c', strtotime($product->updated_at)));
                }
                $w->writeElement('changefreq', 'weekly');
                $w->writeElement('priority', '0.8');

                // Google Image Sitemap extension for high image rankings
                if ($product->main_image) {
                    $w->startElement('image:image');
                    $w->writeElement('image:loc', image_url($product->main_image, urlencode($product->name ?? 'Product')));
                    if (! empty($product->name)) {
                        $w->writeElement('image:title', $product->name);
                    }
                    $w->endElement();
                }

                $w->endElement();
            }

            $w->endElement();
            $w->endDocument();

            return $w->outputMemory();
        });

        return $this->xmlResponse($xml);
    }

    /**
     * Categories and Brands Sitemap.
     */
    public function categories(): Response
    {
        $xml = Cache::remember('sitemap:categories', self::CACHE_TTL, function () {
            $baseUrl = rtrim(url('/'), '/');

            $categories = DB::table('categories')
                ->where('is_active', 1)
                ->select(['slug', 'name', 'image', 'updated_at'])
                ->orderBy('sort_order', 'asc')
                ->cursor();

            $brands = DB::table('brands')
                ->where('is_active', 1)
                ->select(['slug', 'name', 'logo', 'updated_at'])
                ->orderBy('sort_order', 'asc')
                ->cursor();

            $w = new \XMLWriter();
            $w->openMemory();
            $w->setIndent(false);
            $w->startDocument('1.0', 'UTF-8');
            $w->writePI('xml-stylesheet', 'type="text/xsl" href="/sitemap.xsl"');
            $w->startElement('urlset');
            $w->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
            $w->writeAttribute('xmlns:image', 'http://www.google.com/schemas/sitemap-image/1.1');

            foreach ($categories as $cat) {
                $w->startElement('url');
                $w->writeElement('loc', $baseUrl . '/shop?category=' . $cat->slug);
                if ($cat->updated_at) {
                    $w->writeElement('lastmod', date('c', strtotime($cat->updated_at)));
                }
                $w->writeElement('changefreq', 'weekly');
                $w->writeElement('priority', '0.7');

                if ($cat->image) {
                    $w->startElement('image:image');
                    $w->writeElement('image:loc', image_url($cat->image, urlencode($cat->name ?? 'Category')));
                    if (! empty($cat->name)) {
                        $w->writeElement('image:title', $cat->name);
                    }
                    $w->endElement();
                }

                $w->endElement();
            }

            foreach ($brands as $brand) {
                $w->startElement('url');
                $w->writeElement('loc', $baseUrl . '/shop?brand=' . $brand->slug);
                if ($brand->updated_at) {
                    $w->writeElement('lastmod', date('c', strtotime($brand->updated_at)));
                }
                $w->writeElement('changefreq', 'weekly');
                $w->writeElement('priority', '0.6');

                if ($brand->logo) {
                    $w->startElement('image:image');
                    $w->writeElement('image:loc', image_url($brand->logo, urlencode($brand->name ?? 'Brand')));
                    if (! empty($brand->name)) {
                        $w->writeElement('image:title', $brand->name);
                    }
                    $w->endElement();
                }

                $w->endElement();
            }

            $w->endElement();
            $w->endDocument();

            return $w->outputMemory();
        });

        return $this->xmlResponse($xml);
    }

    /**
     * Static and CMS Pages Sitemap.
     */
    public function pages(): Response
    {
        $xml = Cache::remember('sitemap:pages', self::CACHE_TTL, function () {
            $baseUrl = rtrim(url('/'), '/');
            $now = date('c');

            $staticRoutes = [
                ['url' => $baseUrl . '/', 'priority' => '1.0', 'freq' => 'daily'],
                ['url' => $baseUrl . '/shop', 'priority' => '0.9', 'freq' => 'daily'],
                ['url' => $baseUrl . '/offers', 'priority' => '0.8', 'freq' => 'daily'],
                ['url' => $baseUrl . '/categories', 'priority' => '0.7', 'freq' => 'weekly'],
                ['url' => $baseUrl . '/brands', 'priority' => '0.6', 'freq' => 'weekly'],
                ['url' => $baseUrl . '/about', 'priority' => '0.5', 'freq' => 'monthly'],
                ['url' => $baseUrl . '/contact', 'priority' => '0.5', 'freq' => 'monthly'],
                ['url' => $baseUrl . '/track', 'priority' => '0.4', 'freq' => 'monthly'],
            ];

            $cmsPages = DB::table('pages')
                ->where('is_active', 1)
                ->select(['slug', 'updated_at'])
                ->cursor();

            $w = new \XMLWriter();
            $w->openMemory();
            $w->setIndent(false);
            $w->startDocument('1.0', 'UTF-8');
            $w->writePI('xml-stylesheet', 'type="text/xsl" href="/sitemap.xsl"');
            $w->startElement('urlset');
            $w->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

            foreach ($staticRoutes as $item) {
                $w->startElement('url');
                $w->writeElement('loc', $item['url']);
                $w->writeElement('lastmod', $now);
                $w->writeElement('changefreq', $item['freq']);
                $w->writeElement('priority', $item['priority']);
                $w->endElement();
            }

            foreach ($cmsPages as $page) {
                $w->startElement('url');
                $w->writeElement('loc', $baseUrl . '/page/' . $page->slug);
                if ($page->updated_at) {
                    $w->writeElement('lastmod', date('c', strtotime($page->updated_at)));
                }
                $w->writeElement('changefreq', 'monthly');
                $w->writeElement('priority', '0.4');
                $w->endElement();
            }

            $w->endElement();
            $w->endDocument();

            return $w->outputMemory();
        });

        return $this->xmlResponse($xml);
    }

    /**
     * Unified all-in-one Sitemap (backward-compatible fallback).
     */
    public function all(): Response
    {
        $xml = Cache::remember('sitemap:all', self::CACHE_TTL, function () {
            $baseUrl = rtrim(url('/'), '/');
            $now = date('c');

            $w = new \XMLWriter();
            $w->openMemory();
            $w->setIndent(false);
            $w->startDocument('1.0', 'UTF-8');
            $w->writePI('xml-stylesheet', 'type="text/xsl" href="/sitemap.xsl"');
            $w->startElement('urlset');
            $w->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
            $w->writeAttribute('xmlns:image', 'http://www.google.com/schemas/sitemap-image/1.1');

            $staticRoutes = [
                ['url' => $baseUrl . '/', 'priority' => '1.0', 'freq' => 'daily'],
                ['url' => $baseUrl . '/shop', 'priority' => '0.9', 'freq' => 'daily'],
                ['url' => $baseUrl . '/offers', 'priority' => '0.8', 'freq' => 'daily'],
                ['url' => $baseUrl . '/categories', 'priority' => '0.7', 'freq' => 'weekly'],
                ['url' => $baseUrl . '/brands', 'priority' => '0.6', 'freq' => 'weekly'],
                ['url' => $baseUrl . '/about', 'priority' => '0.5', 'freq' => 'monthly'],
                ['url' => $baseUrl . '/contact', 'priority' => '0.5', 'freq' => 'monthly'],
                ['url' => $baseUrl . '/track', 'priority' => '0.4', 'freq' => 'monthly'],
            ];
            foreach ($staticRoutes as $item) {
                $w->startElement('url');
                $w->writeElement('loc', $item['url']);
                $w->writeElement('lastmod', $now);
                $w->writeElement('changefreq', $item['freq']);
                $w->writeElement('priority', $item['priority']);
                $w->endElement();
            }

            foreach (DB::table('pages')->where('is_active', 1)->select(['slug', 'updated_at'])->cursor() as $page) {
                $w->startElement('url');
                $w->writeElement('loc', $baseUrl . '/page/' . $page->slug);
                if ($page->updated_at) {
                    $w->writeElement('lastmod', date('c', strtotime($page->updated_at)));
                }
                $w->writeElement('changefreq', 'monthly');
                $w->writeElement('priority', '0.4');
                $w->endElement();
            }

            foreach (DB::table('categories')->where('is_active', 1)->select(['slug', 'name', 'image', 'updated_at'])->cursor() as $cat) {
                $w->startElement('url');
                $w->writeElement('loc', $baseUrl . '/shop?category=' . $cat->slug);
                if ($cat->updated_at) {
                    $w->writeElement('lastmod', date('c', strtotime($cat->updated_at)));
                }
                $w->writeElement('changefreq', 'weekly');
                $w->writeElement('priority', '0.7');
                if ($cat->image) {
                    $w->startElement('image:image');
                    $w->writeElement('image:loc', image_url($cat->image, urlencode($cat->name ?? 'Category')));
                    if (! empty($cat->name)) {
                        $w->writeElement('image:title', $cat->name);
                    }
                    $w->endElement();
                }
                $w->endElement();
            }

            foreach (DB::table('brands')->where('is_active', 1)->select(['slug', 'name', 'logo', 'updated_at'])->cursor() as $brand) {
                $w->startElement('url');
                $w->writeElement('loc', $baseUrl . '/shop?brand=' . $brand->slug);
                if ($brand->updated_at) {
                    $w->writeElement('lastmod', date('c', strtotime($brand->updated_at)));
                }
                $w->writeElement('changefreq', 'weekly');
                $w->writeElement('priority', '0.6');
                if ($brand->logo) {
                    $w->startElement('image:image');
                    $w->writeElement('image:loc', image_url($brand->logo, urlencode($brand->name ?? 'Brand')));
                    if (! empty($brand->name)) {
                        $w->writeElement('image:title', $brand->name);
                    }
                    $w->endElement();
                }
                $w->endElement();
            }

            foreach (DB::table('products')->where('is_active', 1)->select(['slug', 'name', 'main_image', 'updated_at'])->orderBy('id', 'desc')->cursor() as $product) {
                $w->startElement('url');
                $w->writeElement('loc', $baseUrl . '/products/' . $product->slug);
                if ($product->updated_at) {
                    $w->writeElement('lastmod', date('c', strtotime($product->updated_at)));
                }
                $w->writeElement('changefreq', 'weekly');
                $w->writeElement('priority', '0.8');
                if ($product->main_image) {
                    $w->startElement('image:image');
                    $w->writeElement('image:loc', image_url($product->main_image, urlencode($product->name ?? 'Product')));
                    if (! empty($product->name)) {
                        $w->writeElement('image:title', $product->name);
                    }
                    $w->endElement();
                }
                $w->endElement();
            }

            $w->endElement();
            $w->endDocument();

            return $w->outputMemory();
        });

        return $this->xmlResponse($xml);
    }

    /**
     * Clear all sitemap cache entries.
     */
    public static function clearCache(): void
    {
        Cache::forget('sitemap:index');
        Cache::forget('sitemap:pages');
        Cache::forget('sitemap:categories');
        Cache::forget('sitemap:products');
        Cache::forget('sitemap:all');

        for ($i = 1; $i <= 50; $i++) {
            if (! Cache::has("sitemap:products:{$i}")) {
                break;
            }
            Cache::forget("sitemap:products:{$i}");
        }
    }

    /**
     * Standard HTTP XML response headers for search engine crawlers.
     */
    protected function xmlResponse(string $xml): Response
    {
        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
            'Cache-Control' => 'public, max-age=86400, stale-while-revalidate=3600',
            'X-Robots-Tag' => 'noindex, follow',
        ]);
    }

    /**
     * Dynamic robots.txt fallback.
     */
    public function robots(): Response
    {
        $lines = [
            'User-agent: *',
            'Disallow: /admin',
            'Disallow: /admin/',
            'Disallow: /checkout',
            'Disallow: /cart',
            'Disallow: /user',
            'Disallow: /user/',
            'Disallow: /login',
            'Disallow: /register',
            'Disallow: /forgot-password',
            'Disallow: /reset-password',
            'Disallow: /verify-otp',
            '',
            'Sitemap: ' . url('/sitemap.xml'),
        ];

        return response(implode("\n", $lines), 200, [
            'Content-Type' => 'text/plain; charset=utf-8',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
