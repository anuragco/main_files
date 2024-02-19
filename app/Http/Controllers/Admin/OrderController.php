<?php

namespace App\Http\Controllers\Admin;
// namespace App\Http\Controllers\Api\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\Setting;
use App\Models\OrderItem;
use App\Models\OrderAddress;
use App\Models\OrderProduct;
use Illuminate\Http\Request;
use App\Models\RefundRequest;
use App\Models\TicketMessage;
use App\Models\CompleteRequest;
use App\Models\OrderProductVariant;
use App\Http\Controllers\Controller;

use App\Models\ProviderClientReport;
use Illuminate\Pagination\Paginator;


class OrderController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request){
        Paginator::useBootstrap();

        $orders = Order::with('client','provider', 'user')->orderBy('id','desc');

        if($request->provider){
            $orders = $orders->where('provider_id', $request->provider);
        }

        if($request->client){
            $orders = $orders->where('client_id', $request->client);
        }

        if($request->booking_id){
            $orders = $orders->where('order_id', $request->booking_id);
        }

        $orders = $orders->paginate(15);
        $title = trans('admin_validation.All Order');
        $setting = Setting::first();
        $currency_icon = array(
            'icon' => $setting->currency_icon
        );
        $currency_icon = (object) $currency_icon;

        $providers = User::where(['status' => 1, 'is_provider' => 1])->orderBy('name','asc')->get();
        $clients = User::where(['status' => 1, 'is_provider' => 0])->orderBy('name','asc')->get();

        return view('admin.order', compact('orders','title','currency_icon','providers','clients'));
    }
    
    public function pendingOrder(){
        Paginator::useBootstrap();

        $orders = Order::with('user')->where('order_status', 0)->orderBy('id','desc')->paginate(15);
        $title = trans('admin_validation.Pending orders');
        $setting = Setting::first();
        $currency_icon = array(
            'icon' => $setting->currency_icon
        );
        $currency_icon = (object) $currency_icon;

        return view('admin.pending_order', compact('orders','title'));
    }

    public function completeOrder(){
        Paginator::useBootstrap();

        $orders = Order::with('user')->where('order_status', 1)->orderBy('id','desc')->paginate(15);
        $title = trans('admin_validation.Complete orders');
        $setting = Setting::first();
        $currency_icon = array(
            'icon' => $setting->currency_icon
        );
        $currency_icon = (object) $currency_icon;

        return view('admin.complete_order', compact('orders','title'));
    }

    public function awaitingBooking(Request $request){
        Paginator::useBootstrap();
        $orders = Order::with('client','provider')->orderBy('id','desc')->where('order_status','awaiting_for_provider_approval');

        if($request->provider){
            $orders = $orders->where('provider_id', $request->provider);
        }

        if($request->client){
            $orders = $orders->where('client_id', $request->client);
        }

        if($request->booking_id){
            $orders = $orders->where('order_id', $request->booking_id);
        }

        $orders = $orders->paginate(15);

        $title = trans('admin_validation.Awaiting for approval');
        $setting = Setting::first();
        $currency_icon = array(
            'icon' => $setting->currency_icon
        );
        $currency_icon = (object) $currency_icon;

        $providers = User::where(['status' => 1, 'is_provider' => 1])->orderBy('name','asc')->get();
        $clients = User::where(['status' => 1, 'is_provider' => 0])->orderBy('name','asc')->get();

        return view('admin.order', compact('orders','title','currency_icon','providers','clients'));
    }

    public function activeBooking(Request $request){
        Paginator::useBootstrap();
        $orders = Order::with('client','provider')->orderBy('id','desc')->where('order_status','approved_by_provider');

        if($request->provider){
            $orders = $orders->where('provider_id', $request->provider);
        }

        if($request->client){
            $orders = $orders->where('client_id', $request->client);
        }

        if($request->booking_id){
            $orders = $orders->where('order_id', $request->booking_id);
        }

        $orders = $orders->paginate(15);

        $title = trans('admin_validation.Active Booking');
        $setting = Setting::first();
        $currency_icon = array(
            'icon' => $setting->currency_icon
        );
        $currency_icon = (object) $currency_icon;

        $providers = User::where(['status' => 1, 'is_provider' => 1])->orderBy('name','asc')->get();
        $clients = User::where(['status' => 1, 'is_provider' => 0])->orderBy('name','asc')->get();

        return view('admin.order', compact('orders','title','currency_icon','providers','clients'));
    }

    public function completeBooking(Request $request){
        Paginator::useBootstrap();
        $orders = Order::with('client','provider')->orderBy('id','desc')->where('order_status','complete');

        if($request->provider){
            $orders = $orders->where('provider_id', $request->provider);
        }

        if($request->client){
            $orders = $orders->where('client_id', $request->client);
        }

        if($request->booking_id){
            $orders = $orders->where('order_id', $request->booking_id);
        }

        $orders = $orders->paginate(15);

        $title = trans('admin_validation.Complete Booking');
        $setting = Setting::first();
        $currency_icon = array(
            'icon' => $setting->currency_icon
        );
        $currency_icon = (object) $currency_icon;

        $providers = User::where(['status' => 1, 'is_provider' => 1])->orderBy('name','asc')->get();
        $clients = User::where(['status' => 1, 'is_provider' => 0])->orderBy('name','asc')->get();

        return view('admin.order', compact('orders','title','currency_icon','providers','clients'));
    }

    public function declineBooking(Request $request){
        Paginator::useBootstrap();
        $orders = Order::with('client','provider')->orderBy('id','desc')->where('order_status','order_decliened_by_provider')->orWhere('order_status', 'order_decliened_by_client');

        if($request->provider){
            $orders = $orders->where('provider_id', $request->provider);
        }

        if($request->client){
            $orders = $orders->where('client_id', $request->client);
        }

        if($request->booking_id){
            $orders = $orders->where('order_id', $request->booking_id);
        }

        $orders = $orders->paginate(15);

        $title = trans('admin_validation.Declined Booking');
        $setting = Setting::first();
        $currency_icon = array(
            'icon' => $setting->currency_icon
        );
        $currency_icon = (object) $currency_icon;

        $providers = User::where(['status' => 1, 'is_provider' => 1])->orderBy('name','asc')->get();
        $clients = User::where(['status' => 1, 'is_provider' => 0])->orderBy('name','asc')->get();

        return view('admin.order', compact('orders','title','currency_icon','providers','clients'));
    }

    public function show($id){
        $order = Order::with('user')->find($id);
        $setting = Setting::first();
        return view('admin.show_order',compact('order', 'setting'));
    }

    private function getProductIdForOrder(Order $order)
{
    // Assuming there is a relationship between orders and order items
    \Log::info('Order ID: ' . $order->id);

    $orderItem = $order->orderItems->first();
    
    if ($orderItem) {
        \Log::info('Order Item found. Product ID: ' . $orderItem->product_id);
        return $orderItem->product_id;
    }
    
    \Log::info('No Order Item found.');
    return null;
}

    









public function updateOrderStatus(Request $request, $id)
{
    try {
        // Start a database transaction
        DB::beginTransaction();

        // Retrieve the order with products
        $order = Order::with('products')->find($id);

        // Check if the order is null
        if (!$order) {
            // Rollback the transaction and return an error response
            DB::rollBack();
            return response()->json(['error' => 'Order not found'], 404);
        }

        // Retrieve the associated products
        $products = $order->products;

        // Update the order status first
        $order->update(['order_status' => 1, 'payment_status' => 'success']);

        // Debugging statement for order update
        Log::debug('Order updated: ' . json_encode($order));

        foreach ($products as $product) {
            Log::debug('Inside product loop for order ' . $order->id);

            // Update product payment status
            $product->update(['payment_status' => 'success']);

            // Debugging statement for product update
            Log::debug('Product updated: ' . json_encode($product));

            // Log product status before update
            Log::debug('Product status before update: ' . $product->status);

          // Check if the payment status is 'success' and update product status accordingly
            if ($order->payment_status === 'success') {
                Log::debug('Updating product status to 0...');
                $product->status = 0;
                $product->save(); // Save the model to persist the changes
                // Log product status after update
                Log::debug('Product status after update: ' . $product->status);
                Log::debug('Product status updated: ' . json_encode($product));
            } else {
                Log::debug('Payment status is not success. No update to product status.');
            }

            Log::debug('Order payment status: ' . $order->payment_status);
        }

        // Commit the transaction
        DB::commit();

        // Return a success response
        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        // Log any exception that occurs
        Log::error('Error updating order status: ' . $e->getMessage());

        // Rollback the transaction and return an error response
        DB::rollBack();
        return response()->json(['error' => 'An error occurred'], 500);
    }
}








// public function updatePaymentStatus(Request $request, $orderId)
// {
//     $order = Order::findOrFail($orderId);

//     // Update payment status
//     $order->update(['payment_status' => $request->payment_status]);

//     // Update product status based on payment status
//     $product = $order->product;
//     if ($request->payment_status == 'success') {
//         $product->update(['status' => 0]); // Set status to inactive
//     } elseif ($request->payment_status == 'pending') {
//         $product->update(['status' => 1]); // Set status to active
//     }

//     // ... remaining code
// }


    


    public function destroy($id){
        $order = Order::find($id);
        $order_item=OrderItem::where('order_id', $id)->delete();
        $order->delete();
        $notification = trans('admin_validation.Delete successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('admin.all-booking')->with($notification);
    }


    public function bookingDecilendRequest($id){
        $order = Order::find($id);
        $order->order_status = 'order_decliened_by_provider';
        $order->save();

        $notification= trans('admin_validation.Declined Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function bookingApprovedRequest($id){
        $order = Order::find($id);
        $order->order_status = 'approved_by_provider';
        $order->save();

        $notification= trans('admin_validation.Approved Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

    public function paymentApproved($id){
        $order = Order::find($id);
        $order->payment_status = 'success';
        $order->save();

        $notification= trans('admin_validation.Approved Successfully');
        $notification=array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
    }

}
