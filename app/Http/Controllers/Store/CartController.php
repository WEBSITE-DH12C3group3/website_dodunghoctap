<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class CartController extends Controller
{
    protected function getCart(): array
    {
        $cart = session('cart', []);
        if (empty($cart)) {
            $cookie = Cookie::get('cart');
            if ($cookie) {
                $decoded = json_decode($cookie, true);
                if (is_array($decoded)) $cart = $decoded;
                session(['cart' => $cart]);
            }
        }
        return $cart;
    }

    protected function persistCart(array $cart): void
    {
        session(['cart' => $cart]);
        // Lưu cookie 30 ngày
        Cookie::queue('cart', json_encode($cart), 60 * 24 * 30);
    }

    public function index()
    {
        $cart = $this->getCart();
        $total = collect($cart)->sum(fn($i) => $i['price'] * $i['qty']);
        return view('store.cart.index', compact('cart', 'total'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer',
            'qty' => 'nullable|integer|min:1|max:999'
        ]);

        $product = Product::findOrFail($request->product_id);
        $qty = max((int)$request->input('qty', 1), 1);

        $cart = $this->getCart();

        if (isset($cart[$product->product_id])) {
            $cart[$product->product_id]['qty'] += $qty;
        } else {
            $cart[$product->product_id] = [
                'id'    => $product->product_id,
                'name'  => $product->product_name,
                'price' => (float)$product->price,
                'image' => $product->image_url,
                'qty'   => $qty,
            ];
        }

        $this->persistCart($cart);

        if ($request->boolean('buy_now')) {
            return redirect()->route('cart.index')->with('success', 'Đã thêm vào giỏ. Tiến hành thanh toán.');
        }
        return back()->with('success', 'Đã thêm vào giỏ hàng.');
    }

    public function update(Request $request)
    {
        $request->validate([
            'id'  => 'required|integer',
            'qty' => 'required|integer|min:1|max:999',
        ]);

        $cart = $this->getCart();
        if (isset($cart[$request->id])) {
            $cart[$request->id]['qty'] = (int)$request->qty;
            $this->persistCart($cart);
        }
        return back();
    }

    public function remove($id)
    {
        $cart = $this->getCart();
        unset($cart[$id]);
        $this->persistCart($cart);
        return back()->with('success', 'Đã xóa sản phẩm khỏi giỏ.');
    }
}
