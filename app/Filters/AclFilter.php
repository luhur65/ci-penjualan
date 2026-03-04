<?php

namespace App\Filters;

use App\Libraries\MyExceptionHandler;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class AclFilter implements FilterInterface
{

    protected $exceptMethods = ['gridtab', 'grid', 'operation', 'crud'];
    protected $mapToIndex = ['fieldlength', 'getallclass', 'getmenuparent'];

    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $router = Services::router();

        // Controller & method yang sedang diakses
        $controllerFQCN = $router->controllerName();   // contoh: App\Controllers\MenuController
        $method         = strtolower($router->methodName()); // contoh: index/create/update/delete/getallclass

        // Ambil nama controller pendek (tanpa namespace & tanpa "Controller")
        $short = $controllerFQCN;
        if (str_contains($short, '\\')) {
            $short = substr($short, strrpos($short, '\\') + 1);
        }
        $short = preg_replace('/Controller$/', '', $short);
        $class = strtolower($short); // menu, user, role, acos, dst

        // Pengecualian (kalau memang mau di-bypass)
        if (in_array($method, $this->exceptMethods, true)) {
            return;
        }

        /**
         * Mapping untuk method non-CRUD yang Anda punya,
         * supaya ikut permission yang sudah ada (misalnya "index").
         *
         * - fieldLength, getAllClass, getMenuParent biasanya hanya data pendukung halaman index
         * - show/new/edit dari resource CI4 sering tidak Anda pakai, tapi route-nya bisa ada
         */
        if (in_array($method, $this->mapToIndex, true)) {
            $method = 'index';
        }

        // Optional mapping: new/edit (kalau rute resource kepakai)
        if ($method === 'new')  $method = 'create';
        if ($method === 'edit') $method = 'update';

        // Ambil permissions dari session (sesuai helper Anda)
        $permissions = session()->get('permissions') ?? [];

        $accessKey = $class . '.' . $method; // contoh: menu.index, menu.create, menu.update, ...

        if (!in_array($accessKey, $permissions, true)) {
            return service('response')
                ->setStatusCode(403)
                ->setBody(view('errors/html/error_403', [
                    'required' => $accessKey,
                ]));
        }

        // if (!in_array($accessKey, $permissions, true)) {
        //     // Untuk API: balikin JSON 403
        //     return Services::response()
        //         ->setStatusCode(403)
        //         ->setJSON([
        //             // 'status'   => 403,
        //             'message'  => 'Forbidden',
        //             'required' => $accessKey,
        //         ]);
        // }
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
