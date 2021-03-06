<?php

namespace App\Http\Controllers\Admin\Blood;

use App\Filters\SmsFilter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blood;
use App\Models\Sms;

class SmsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(SmsFilter $filters)
    {
        $this->authorize('send', Blood::class);

        $messages = Sms::where('category', 'Kan Bağışı')->filter($filters)->with('sender')->latest()->safePaginate();

        $senders = Sms::toSenderSelect('Hepsi', 'Kan Bağışı');

        return view('admin.blood.sms', compact('messages', 'senders'));
    }

    public function show()
    {
        $this->authorize('send', Blood::class);

        return view('admin.blood.send');
    }

    public function preview(Request $request)
    {
        $this->authorize('send', Blood::class);

        $this->validate($request, [
            'cities'      => 'required',
            'blood_types' => 'required',
            'rhs'         => 'required',
            'message'     => 'required|string|max:150|min:20',
        ]);

        $blood_types = $request->blood_types;
        $rhs = $request->rhs;
        $cities = $request->cities;
        $message = $request->message;

        $bloods = Blood::whereIn('city', $cities)->whereIn('blood_type', $blood_types)->whereIn('rh', $rhs)->get();

        return view('admin.blood.preview', compact('bloods', 'message', 'blood_types', 'rhs', 'cities'));
    }

    public function send(Request $request)
    {
        $this->authorize('send', Blood::class);

        $this->validate($request, [
            'message' => 'required|string|max:150|min:20',
        ]);

        $sms = Sms::create([
            'title'          => 'LEYLADANSNR',
            'message'        => $request->message,
            'category'       => 'Kan Bağışı',
            'receiver_count' => count($request->bloods),
            'sent_by'        => auth()->user()->id,
        ]);
        $sms->send($request->bloods);

        session_success(__('messages.blood.sms.successful', ['count' => count($request->bloods)]));

        return redirect()->route('admin.blood.sms.show');

    }

    public function test(Request $request)
    {
        $this->authorize('send', Blood::class);

        $this->validate($request, [
            'message' => 'required|string|max:150|min:20',
        ]);

        $sms = Sms::create([
            'title'          => 'LEYLADANSNR',
            'message'        => $request->message,
            'category'       => 'Kan Bağışı Test',
            'receiver_count' => 1,
            'sent_by'        => auth()->user()->id,
        ]);

        $sms->send(make_mobile($request->number));

        return api_success($sms);
    }

    public function checkBalance()
    {
        return api_success(['balance' => Sms::checkBalance()]);
    }
}