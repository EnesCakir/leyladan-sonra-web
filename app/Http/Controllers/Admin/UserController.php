<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\User;
use App\Models\Role;
use App\Models\Child;
use App\Models\Process;
use Auth;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $users = User::orderBy('id', 'DESC')->with(['roles', 'faculty']);

        if ($request->filled('approval')) {
            $users->approved($request->approval);
        }
        if ($request->filled('role_name')) {
            $users->role($request->role_name);
        }
        if ($request->filled('faculty_id')) {
            $users->where('faculty_id', $request->faculty_id);
        }
        if ($request->filled('search')) {
            $users->search($request->search);
        }
        if ($request->filled('download')) {
            User::download($users);
        }
        $users = $users->paginate($request->per_page ?: 25);
        if ($request->has('page') && $request->page != 1 && $request->page > $users->lastPage()) {
            return redirect($request->fullUrlWithQuery(array_merge(request()->all(), ['page' => $users->lastPage()])));
        }
        return view('admin.user.index', compact(['users']));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        $user->child_count = $user->children()->count();
        $user->visit_count = Process::where('created_by', $user->id)->where('desc', 'Ziyaret edildi.')->count();
        $user->child_delivered_count = $user->children()->where('gift_state', 'Teslim Edildi')->count();
        return view('admin.user.show', compact('user'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $user->child_count = $user->children()->count();
        $user->visit_count = Process::where('created_by', $user->id)->where('desc', 'Ziyaret edildi.')->count();
        $user->child_delivered_count = $user->children()->where('gift_state', 'Teslim Edildi')->count();
        return view('admin.user.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->fill($request->all());
        $user->save();
        if ($request->has('form')) {
            return redirect()->route('admin.user.edit', $id);
        }

        if ($request->hasFile('photo')) {
            ini_set('memory_limit', '-1');
            // $smallPhoto = Image::make($request->file('photo'))
            //     ->rotate(-$request->rotation)
            //     ->crop($request->w, $request->h, $request->x, $request->y)
            //     ->resize(100, 100)
            //     ->save('resources/admin/uploads/profile_photos/' . $user->id . '_s.jpg', 80);

            // $largePhoto = Image::make($request->file('photo'))
            //     ->rotate(-$request->rotation)
            //     ->crop($request->w, $request->h, $request->x, $request->y)
            //     ->resize(600, 600)
            //     ->save('resources/admin/uploads/profile_photos/' . $user->id . '_l.jpg', 80);

            ini_restore('memory_limit');

            $user->profile_photo = $user->id;
            $user->save();

            return redirect()->route('admin.user.edit', $id);
        }

        return http_response_code(200);
    }

    public function destroy($id)
    {
        //
    }

    public function children($id)
    {
        $user = User::findOrFail($id);
        $user->child_count = $user->children()->count();
        $user->visit_count = Process::where('created_by', $user->id)->where('desc', 'Ziyaret edildi.')->count();
        $user->child_delivered_count = $user->children()->where('gift_state', 'Teslim Edildi')->count();
        $children = $user->children()->get();
        return view('admin.user.children', compact('children', 'user'));
    }

    public function childrenData($id)
    {
    }

    public function approve(Request $request, User $user)
    {
        $user->approve($request->approval);
        // TODO:
        // Send notification
        // \Mail::send('email.admin.activation', ['user' => $user], function ($message) use ($user) {
        //     $message
        //         ->to($user->email)
        //         ->from('teknik@leyladansonra.com', 'Leyladan Sonra Sistem')
        //         ->subject('Hesabınız artık aktif!');
        // });

        return api_success(['approval' => (int) $user->isApproved(), 'user' => $user]);
    }
}
