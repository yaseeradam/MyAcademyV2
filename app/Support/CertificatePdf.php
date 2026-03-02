<?php

namespace App\Support;

use Spatie\Browsershot\Browsershot;

class CertificatePdf
{
    /**
     * Generate a PDF from a Blade view using Browsershot (headless Chrome).
     * This gives full CSS support: flexbox, gradients, shadows, transforms, etc.
     */
    public static function fromView(string $view, array $data, string $orientation = 'landscape'): string
    {
        $html = view($view, $data)->render();

        $width = $orientation === 'landscape' ? 1123 : 794;
        $height = $orientation === 'landscape' ? 794 : 1123;

        $pageSize = $orientation === 'landscape' ? 'A4 landscape' : 'A4 portrait';
        $fullPageCss = "<style>
            @page { margin: 0 !important; size: {$pageSize}; }
            html, body {
                margin: 0 !important;
                padding: 0 !important;
                width: {$width}px !important;
                height: {$height}px !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .page {
                box-sizing: border-box !important;
                width: {$width}px !important;
                height: {$height}px !important;
                margin: 0 !important;
                overflow: hidden !important;
            }
        </style>";

        if (str_contains($html, '</head>')) {
            $html = str_replace('</head>', $fullPageCss . '</head>', $html);
        }

        $browsershot = Browsershot::html($html)
            ->setNodeBinary('C:\\Program Files\\nodejs\\node.exe')
            ->setNpmBinary('C:\\Program Files\\nodejs\\npm.cmd')
            ->setChromePath(self::findChromePath())
            ->windowSize($width, $height)
            ->emulateMedia('screen')
            ->format('A4')
            ->landscape($orientation === 'landscape')
            ->showBackground()
            ->waitUntilNetworkIdle()
            ->margins(0, 0, 0, 0)
            ->setOption('args', ['--no-sandbox', '--disable-setuid-sandbox']);

        return $browsershot->pdf();
    }

    /**
     * Locate the Chrome binary installed by Puppeteer.
     */
    private static function findChromePath(): string
    {
        // First try Puppeteer's cache location
        $cacheDir = getenv('USERPROFILE') ?: (getenv('HOME') ?: 'C:\\Users\\HP');
        $puppeteerCacheDir = $cacheDir . '\\.cache\\puppeteer\\chrome';

        if (is_dir($puppeteerCacheDir)) {
            // Find the latest version directory
            $versions = @scandir($puppeteerCacheDir);
            if ($versions) {
                $versions = array_filter($versions, fn($v) => $v !== '.' && $v !== '..');
                rsort($versions); // Latest version first

                foreach ($versions as $version) {
                    $chromePath = $puppeteerCacheDir . '\\' . $version . '\\chrome-win64\\chrome.exe';
                    if (file_exists($chromePath)) {
                        return $chromePath;
                    }
                }
            }
        }

        // Fallback: let Browsershot find it
        return '';
    }
}
