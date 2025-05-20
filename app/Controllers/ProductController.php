<?php

namespace App\Controllers;

use App\Models\ProductModel; 
class ProductController extends BaseController
{
    protected $product;

    function __construct()
    {
        $this->product = new ProductModel();
    }

    public function index()
    {
        $product = $this->product->findAll();
        $data['product'] = $product;

        return view('v_product', $data);
    }

    public function create()
    {
        $dataFoto = $this->request->getFile('foto');

        $dataForm = [
            'nama' => $this->request->getPost('nama'),
            'harga' => $this->request->getPost('harga'),
            'jumlah' => $this->request->getPost('jumlah'),
            'created_at' => date("Y-m-d H:i:s")
        ];

        if ($dataFoto->isValid()) {
            $fileName = $dataFoto->getRandomName();
            $dataForm['foto'] = $fileName;
            $dataFoto->move('img/', $fileName);
        }

        $this->product->insert($dataForm);

        return redirect('product')->with('success', 'Data Berhasil Ditambah');
    } 

    public function edit($id)
    {
        $productData = $this->product->find($id);

        $dataForm = [
            'nama' => $this->request->getPost('nama'),
            'harga' => $this->request->getPost('harga'),
            'jumlah' => $this->request->getPost('jumlah'),
            'updated_at' => date("Y-m-d H:i:s")
        ];

        if ($this->request->getPost('check') == 1) {
            if ($productData['foto'] != '' and file_exists("img/" . $productData['foto'] . "")) {
                unlink("img/" . $productData['foto']);
            }

            $dataFoto = $this->request->getFile('foto');

            if ($dataFoto->isValid()) {
                $fileName = $dataFoto->getRandomName();
                $dataFoto->move('img/', $fileName);
                $dataForm['foto'] = $fileName;
            }
        }

        $this->product->update($id, $dataForm);

        return redirect('product')->with('success', 'Data Berhasil Diubah');
    }

    public function delete($id)
    {
        $productData = $this->product->find($id);

        if ($productData['foto'] != '' and file_exists("img/" . $productData['foto'] . "")) {
            unlink("img/" . $productData['foto']);
        }

        $this->product->delete($id);

        return redirect('product')->with('success', 'Data Berhasil Dihapus');
    }
}
