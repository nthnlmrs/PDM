<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = Cart::with('product.seller')
            ->where('user_id', Auth::id())
            ->get();

        $total = $cartItems->sum('subtotal');
        $voucher = session('voucher');
        $discount = 0;

        if ($voucher) {
            $voucherModel = Voucher::find($voucher['id']);
            if ($voucherModel && $voucherModel->isValid()) {
                $discount = $voucherModel->calculateDiscount($total);
            } else {
                session()->forget('voucher');
                $voucher = null;
            }
        }

        return view('cart.index', compact('cartItems', 'total', 'voucher', 'discount'));
    }

    public function store(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        if ($product->status !== 'active') {
            return back()->with('error', 'Produk tidak tersedia.');
        }

        if ($product->stock < $request->quantity) {
            return back()->with('error', 'Stok tidak mencukupi.');
        }

        $cart = Cart::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->first();

        if ($cart) {
            $newQuantity = $cart->quantity + $request->quantity;
            if ($product->stock < $newQuantity) {
                return back()->with('error', 'Tidak dapat menambah lagi. Stok tidak mencukupi.');
            }
            $cart->update(['quantity' => $newQuantity]);
        } else {
            Cart::create([
                'user_id' => Auth::id(),
                'product_id' => $product->id,
                'quantity' => $request->quantity,
            ]);
        }

        return back()->with('success', 'Ditambahkan ke keranjang!');
    }

    public function update(Request $request, Cart $cart)
    {
        if ($cart->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        if ($cart->product->stock < $request->quantity) {
            return back()->with('error', 'Stok tidak mencukupi.');
        }

        $cart->update(['quantity' => $request->quantity]);

        return back()->with('success', 'Keranjang diupdate.');
    }

    public function destroy(Cart $cart)
    {
        if ($cart->user_id !== Auth::id()) {
            abort(403);
        }

        $cart->delete();

        return back()->with('success', 'Item dihapus dari keranjang.');
    }

    public function applyVoucher(Request $request)
    {
        $request->validate([
            'voucher_code' => 'required|string',
        ]);

        $voucher = Voucher::where('code', strtoupper($request->voucher_code))->first();

        if (!$voucher) {
            return back()->with('error', 'Kode voucher tidak ditemukan.');
        }

        if (!$voucher->isValid()) {
            return back()->with('error', 'Voucher tidak valid atau sudah kadaluarsa.');
        }

        $cartItems = Cart::where('user_id', Auth::id())->get();
        $total = $cartItems->sum('subtotal');

        if ($total < $voucher->min_purchase) {
            return back()->with('error', 'Minimum pembelian ' . formatRupiah($voucher->min_purchase) . ' untuk menggunakan voucher ini.');
        }

        session(['voucher' => [
            'id' => $voucher->id,
            'code' => $voucher->code,
            'name' => $voucher->name,
        ]]);

        return back()->with('success', 'Voucher berhasil diterapkan!');
    }

    public function success()
    {
        return view('cart.success');
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:wallet,apple_pay,paypal,credit_card',
            'shipping_name' => 'required|string|max:255',
            'shipping_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string|max:500',
        ]);

        $user = Auth::user();
        $cartItems = Cart::with('product')->where('user_id', $user->id)->get();

        if ($cartItems->isEmpty()) {
            return back()->with('error', 'Keranjang Anda kosong.');
        }

        // Validate stock
        foreach ($cartItems as $item) {
            if ($item->product->stock < $item->quantity) {
                return back()->with('error', "Stok tidak mencukupi untuk {$item->product->name}.");
            }
            if ($item->product->status !== 'active') {
                return back()->with('error', "{$item->product->name} tidak tersedia lagi.");
            }
        }

        $subtotal = $cartItems->sum('subtotal');
        $discount = 0;
        $voucherId = null;

        // Apply voucher if exists
        $voucherSession = session('voucher');
        if ($voucherSession) {
            $voucher = Voucher::find($voucherSession['id']);
            if ($voucher && $voucher->isValid()) {
                $discount = $voucher->calculateDiscount($subtotal);
                $voucherId = $voucher->id;
            }
        }

        $totalAmount = $subtotal - $discount;

        // Check balance if wallet
        if ($request->payment_method === 'wallet' && $user->balance < $totalAmount) {
            return back()->with('error', 'Saldo wallet tidak mencukupi.');
        }

        try {
            DB::transaction(function () use ($user, $cartItems, $totalAmount, $discount, $voucherId, $request) {
                $paymentStatus = $request->payment_method === 'wallet' ? 'paid' : 'unpaid';

                // Deduct balance if wallet
                if ($request->payment_method === 'wallet') {
                    $user->balance -= $totalAmount;
                    $user->save();
                }

                // Create order
                $order = Order::create([
                    'user_id' => $user->id,
                    'invoice_number' => 'INV-' . strtoupper(Str::random(10)),
                    'total_amount' => $totalAmount,
                    'status' => 'pending',
                    'payment_status' => $paymentStatus,
                    'payment_method' => $request->payment_method,
                    'shipping_name' => $request->shipping_name,
                    'shipping_phone' => $request->shipping_phone,
                    'shipping_address' => $request->shipping_address,
                    'voucher_id' => $voucherId,
                    'discount_amount' => $discount,
                ]);

                // Create order items and deduct stock
                foreach ($cartItems as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item->product_id,
                        'price' => $item->product->discount_price ?? $item->product->price,
                        'quantity' => $item->quantity,
                    ]);

                    $item->product->decrement('stock', $item->quantity);
                }

                // Increment voucher usage
                if ($voucherId) {
                    Voucher::where('id', $voucherId)->increment('used_count');
                }

                // Clear cart and voucher session
                Cart::where('user_id', $user->id)->delete();
                session()->forget('voucher');
            });

            return redirect()->route('cart.success')->with('success', 'Pesanan berhasil dibuat!');
        } catch (\Exception $e) {
            return back()->with('error', 'Checkout gagal: ' . $e->getMessage());
        }
    }
}
