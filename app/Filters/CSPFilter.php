<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class CSPFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Tidak melakukan apa-apa
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Mengambil nonce dari service renderer jika sudah diset di controller
        $nonce = service('renderer')->getVar('csp_nonce') ?? '';

        // Hapus header CSP yang mungkin sudah ada
        $response->removeHeader('Content-Security-Policy');

        // Atur header CSP yang baru dan aman
        $policy = "script-src 'self' 'nonce-{$nonce}' https://app.sandbox.midtrans.com; ";
        $policy .= "frame-src 'self' https://app.sandbox.midtrans.com; ";
        $policy .= "connect-src 'self' https://rajaongkir.komerce.id; ";
        $policy .= "style-src 'self' 'unsafe-inline' https://*.googleapis.com; ";
        $policy .= "font-src 'self' https://*.gstatic.com; ";
        $policy .= "object-src 'none'; ";
        $policy .= "base-uri 'self';";

        $response->setHeader('Content-Security-Policy', $policy);
    }
}