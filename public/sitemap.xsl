<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0"
    xmlns:html="http://www.w3.org/TR/REC-html40"
    xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"
    xmlns:sitemap="http://www.sitemaps.org/schemas/sitemap/0.9"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="html" version="1.0" encoding="UTF-8" indent="yes"/>
    <xsl:template match="/">
        <html xmlns="http://www.w3.org/1999/xhtml" lang="en">
            <head>
                <title>XML Sitemap | Raimart</title>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <meta name="viewport" content="width=device-width, initial-scale=1.0" />
                <style type="text/css">
                    * { box-sizing: border-box; margin: 0; padding: 0; }
                    body {
                        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
                        background: #f8fafc;
                        color: #1e293b;
                        padding: 30px 20px;
                        font-size: 14px;
                        line-height: 1.5;
                    }
                    .container {
                        max-width: 1080px;
                        margin: 0 auto;
                        background: #ffffff;
                        border-radius: 12px;
                        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
                        border: 1px solid #e2e8f0;
                        overflow: hidden;
                    }
                    .header {
                        padding: 24px 28px;
                        border-bottom: 1px solid #e2e8f0;
                        background: #0B2545;
                        color: #ffffff;
                    }
                    .header h1 {
                        font-size: 22px;
                        font-weight: 700;
                        color: #ffffff;
                        margin-bottom: 6px;
                    }
                    .header p {
                        font-size: 13px;
                        color: #94a3b8;
                    }
                    .meta-bar {
                        padding: 12px 28px;
                        background: #f1f5f9;
                        border-bottom: 1px solid #e2e8f0;
                        font-size: 13px;
                        color: #475569;
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                    }
                    .badge {
                        background: #F37021;
                        color: #ffffff;
                        font-weight: 600;
                        padding: 2px 8px;
                        border-radius: 6px;
                        font-size: 12px;
                    }
                    table {
                        width: 100%;
                        border-collapse: collapse;
                    }
                    th {
                        background: #f8fafc;
                        text-align: left;
                        padding: 12px 20px;
                        font-size: 12px;
                        font-weight: 600;
                        color: #64748b;
                        text-transform: uppercase;
                        letter-spacing: 0.05em;
                        border-bottom: 1px solid #e2e8f0;
                    }
                    td {
                        padding: 12px 20px;
                        border-bottom: 1px solid #f1f5f9;
                        font-size: 13px;
                        word-break: break-all;
                    }
                    tr:hover td {
                        background: #f8fafc;
                    }
                    a {
                        color: #F37021;
                        text-decoration: none;
                        font-weight: 500;
                    }
                    a:hover {
                        text-decoration: underline;
                    }
                    .date {
                        color: #64748b;
                        font-size: 12px;
                        white-space: nowrap;
                    }
                    .footer {
                        padding: 16px 28px;
                        font-size: 12px;
                        color: #94a3b8;
                        text-align: center;
                        background: #f8fafc;
                        border-top: 1px solid #e2e8f0;
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <h1>Raimart XML Sitemap</h1>
                        <p>Dynamic search engine sitemap generated for Google, Bing, and other crawlers.</p>
                    </div>

                    <xsl:if test="sitemap:sitemapindex">
                        <div class="meta-bar">
                            <span>Sitemap Index (Master Directory)</span>
                            <span class="badge"><xsl:value-of select="count(sitemap:sitemapindex/sitemap:sitemap)"/> Sub-Sitemaps</span>
                        </div>
                        <table>
                            <thead>
                                <tr>
                                    <th width="70%">Sub-Sitemap URL</th>
                                    <th width="30%">Last Modified</th>
                                </tr>
                            </thead>
                            <tbody>
                                <xsl:for-each select="sitemap:sitemapindex/sitemap:sitemap">
                                    <tr>
                                        <td>
                                            <a href="{sitemap:loc}"><xsl:value-of select="sitemap:loc"/></a>
                                        </td>
                                        <td class="date">
                                            <xsl:value-of select="sitemap:lastmod"/>
                                        </td>
                                    </tr>
                                </xsl:for-each>
                            </tbody>
                        </table>
                    </xsl:if>

                    <xsl:if test="sitemap:urlset">
                        <div class="meta-bar">
                            <span>Total Indexed URLs: <strong style="color: #0B2545;"><xsl:value-of select="count(sitemap:urlset/sitemap:url)"/></strong></span>
                            <span class="badge"><a href="/sitemap.xml" style="color: white; text-decoration: none;">← Back to Index</a></span>
                        </div>
                        <table>
                            <thead>
                                <tr>
                                    <th width="50%">URL</th>
                                    <th width="10%">Priority</th>
                                    <th width="15%">Change Frequency</th>
                                    <th width="10%">Images</th>
                                    <th width="15%">Last Modified</th>
                                </tr>
                            </thead>
                            <tbody>
                                <xsl:for-each select="sitemap:urlset/sitemap:url">
                                    <tr>
                                        <td>
                                            <a href="{sitemap:loc}"><xsl:value-of select="sitemap:loc"/></a>
                                        </td>
                                        <td>
                                            <xsl:value-of select="sitemap:priority"/>
                                        </td>
                                        <td>
                                            <xsl:value-of select="sitemap:changefreq"/>
                                        </td>
                                        <td>
                                            <xsl:value-of select="count(image:image)"/>
                                        </td>
                                        <td class="date">
                                            <xsl:value-of select="sitemap:lastmod"/>
                                        </td>
                                    </tr>
                                </xsl:for-each>
                            </tbody>
                        </table>
                    </xsl:if>

                    <div class="footer">
                        Raimart eCommerce &#8226; Ultra-Fast Cached XML Sitemap
                    </div>
                </div>
            </body>
        </html>
    </xsl:template>
</xsl:stylesheet>
