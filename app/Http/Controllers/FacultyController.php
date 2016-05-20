<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Auth, Datatables, File, Log;
use App\Faculty, App\Child, App\Post, App\PostImage;

class FacultyController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $faculties = Faculty::with('responsibles')->get();
        return view('admin.faculty.index', compact('faculties'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.faculty.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, Faculty::$validationRules, Faculty::$validationMessages);
        $faculty = new Faculty($request->except('next'));
        $faculty->save();

        if($request->next == "1"){ return redirect()->route('admin.faculty.create'); }
        else{ return redirect()->route('admin.faculty.index'); }


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Display a listing of faculty's children.
     *
     * @return Response
     */
    public function cities()
    {

        $cities = Faculty::lists('id','code')->toArray();

        foreach($cities as $key => $city){
            $cities[$key] = '#339a99';
        }
        return $cities;
    }

    /**
     * Display a listing of faculty's children.
     *
     * @return Response
     */
    public function children($id)
    {
        $faculty = Faculty::find($id);
        return view('admin.faculty.children', compact(['faculty']));
    }



    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function childrenData($id)
    {

        $faculty = Faculty::find($id);

        $user = Auth::user();
        return Datatables::of(
            Child::select('id', 'first_name', 'last_name', 'department', 'diagnosis', 'wish', 'birthday', 'gift_state', 'meeting_day','faculty_id','until')->where('faculty_id', $faculty->id)->with('users')->get()
        )
            ->editColumn('operations', '
                                    @if (Auth::user()->title == "Yönetici" || Auth::user()->title == "Fakülte Sorumlusu" || Auth::user()->title == "Fakülte Yönetim Kurulu")
                                        <a class="btn btn-primary btn-sm" href="{{ route("admin.child.show", $id) }}"><i class="fa fa-search"></i></a>
                                        <a class="edit btn btn-success btn-sm" href="{{ route("admin.child.edit", $id) }}"><i class="fa fa-pencil"></i></a>
                                        <a class="delete btn btn-danger btn-sm" href="javascript:;"><i class="fa fa-trash"></i> </a>
                                    @elseif(Auth::user()->title == "İletişim Sorumlusu")
                                        <a class="road btn btn-default btn-sm" href="javascript:;"> Gönüllü bulundu </a>
                                    @elseif(Auth::user()->title == "Site Sorumlusu")
                                        <a class="post btn btn-default btn-sm" href="{{ route("admin.faculty.posts", Auth::user()->faculty_id) }}"> Yazısını göster </a>
                                    @elseif(Auth::user()->title == "Hediye Sorumlusu")
                                        <a class="gift btn btn-default btn-sm" href="javascript:;"> Hediyesi geldi </a>
                                    @endif
                              ')
            ->editColumn('first_name','{{$full_name}}')
            ->editColumn('gift_state',' @if ($gift_state == "Bekleniyor")
                                        <td><span class="label label-danger"> Bekleniyor </span></td>
                                    @elseif ($gift_state == "Yolda")
                                        <td><span class="label label-warning"> Yolda </span></td>
                                    @elseif ($gift_state == "Bize Ulaştı")
                                        <td><span class="label label-primary"> Bize Ulaştı </span></td>
                                    @elseif ($gift_state == "Teslim Edildi")
                                        <td><span class="label label-success"> Teslim Edildi </span></td>
                                    @else
                                        <td><span class="label label-default"> Problem </span></td>
                                    @endif')
            ->editColumn('until','@if ($until == null )
                                        <td><span class="label label-danger"> Hata </span></td>
                                    @elseif ($until->isFuture())
                                        <td><span class="label label-success"> {{date("d.m.Y", strtotime($until))}} </span></td>
                                    @elseif ($until->isPast())
                                        <td><span class="label label-warning"> {{date("d.m.Y", strtotime($until))}} </span></td>
                                    @endif')
            ->editColumn('users','{{implode(\', \', array_map(function($user){ return $user[\'full_name\']; }, $users->toArray()))}}')
            ->editColumn('birthday','{{date("d.m.Y", strtotime($birthday))}}')
            ->editColumn('meeting_day','{{date("d.m.Y", strtotime($meeting_day))}}')
            ->make(true);
    }

    /**
     * Display a listing of faculty's posts.
     *
     * @return Response
     */
    public function posts($id)
    {
        $faculty = Faculty::find($id);
        return view('admin.faculty.posts', compact(['faculty']));
    }

    public function postsData($id)
    {
        $faculty = Faculty::find($id);
        $user = Auth::user();
        return Datatables::of($faculty->posts()->with('child','images')->get())
            ->addColumn('operations', '
                <a class="approve btn btn-success btn-sm" href="javascript:;"><i class="fa fa-check"></i></a>
                <a class="edit btn btn-primary btn-sm" href="{{ route("admin.post.edit", $id) }}"><i class="fa fa-pencil"></i> </a>
                <a class="delete btn btn-danger btn-sm" href="javascript:;"><i class="fa fa-trash"></i> </a>')
            ->editColumn('status',' @if ($approved_at != null)
                            <span class="label label-success"> Onaylandı </span>
                        @else
                            <span class="label label-danger"> Onaylanmadı </span>
                        @endif')
            ->editColumn('images','
                        @forelse ($images as $image)
                            <img src="{{ asset("resources/admin/uploads/child_photos/". $image->name) }}" class="img-responsive"/>
                        @empty
                            <img src="{{ asset("resources/admin/media/child_no_image.jpg") }}" class="img-responsive"/>
                        @endforelse
                        ')
            ->make(true);
    }


    public function postsUnapproved($id)
    {
        $faculty = Faculty::find($id);
        return view('admin.faculty.posts_unapproved', compact(['faculty']));
    }

    public function postsUnapprovedData($id)
    {
        $faculty = Faculty::find($id);
        $user = Auth::user();

        return Datatables::of($faculty->posts()->whereNull('approved_at')->with('child','images')->get())
            ->addColumn('operations', '
                <a class="approve btn btn-success btn-sm" href="javascript:;"><i class="fa fa-check"></i></a>
                <a class="edit btn btn-primary btn-sm" href="{{ route("admin.post.edit", $id) }}"><i class="fa fa-pencil"></i> </a>
                <a class="delete btn btn-danger btn-sm" href="javascript:;"><i class="fa fa-trash"></i> </a>')
            ->editColumn('status',' @if ($approved_at != null)
                            <span class="label label-success"> Onaylandı </span>
                        @else
                            <span class="label label-danger"> Onaylanmadı </span>
                        @endif')
            ->editColumn('images','
                        @forelse ($images as $image)
                            <img src="{{ asset("resources/admin/uploads/child_photos/". $image->name) }}" class="img-responsive"/>
                        @empty
                            <img src="{{ asset("resources/admin/media/child_no_image.jpg") }}" class="img-responsive"/>
                        @endforelse
                        ')
            ->make(true);
    }

    public function postsUnapprovedCount($id)
    {
        $faculty = Faculty::find($id);
        return $faculty->posts()->whereNull('approved_at')->count();
    }

    public function profiles($id){
        $faculty = Faculty::find($id);

        $users = $faculty->users()
            ->orderby('first_name')
            ->where('profile_photo','!=' ,'default')
            ->simplePaginate(16);
        return view('admin.faculty.profiles', compact(['faculty', 'users']));
    }

    public function users($id)
    {
        $faculty = Faculty::find($id);
        return view('admin.faculty.users', compact(['faculty']));
    }

    public function usersData($id)
    {
        $faculty = Faculty::find($id);
        $user = Auth::user();
        return Datatables::of($faculty->users)
            ->addColumn('operations', '
                <a class="approve btn btn-success btn-sm" href="javascript:;"><i class="fa fa-check"></i></a>
                <a class="title btn blue-steel btn-sm"  data-toggle="modal" data-target="#titleModal"><i class="fa fa-sitemap"></i></a>
                <a class="delete btn btn-danger btn-sm" href="javascript:;"><i class="fa fa-trash"></i> </a>')
            ->editColumn('activated_by','
                        @if ($activated_by != null)
						    <span class=\'label label-success\'> Onaylandı </span>
                        @else
						    <span class=\'label label-danger\'> Onaylanmadı </span>
                        @endif')
            ->editColumn('birthday','{{date("d.m.Y", strtotime($birthday))}}')
            ->make(true);
    }


    public function unapproved($id)
    {
        $faculty = Faculty::find($id);
        $users = $faculty->users()->whereNull('activated_by')->get();
        return view('admin.faculty.unapproved', compact(['faculty','users']));
    }

    public function unapprovedData($id)
    {
        $faculty = Faculty::find($id);
        return Datatables::of($faculty->users()->whereNull('activated_by')->get())
            ->addColumn('operations', '
                <a class="approve btn btn-success btn-sm" href="javascript:;"><i class="fa fa-check"></i></a>
                <a class="delete btn btn-danger btn-sm" href="javascript:;"><i class="fa fa-trash"></i> </a>')
            ->editColumn('activated_by','
                        @if ($activated_by != null)
						    <span class=\'label label-success\'> Onaylandı </span>
                        @else
						    <span class=\'label label-danger\'> Onaylanmadı </span>
                        @endif')
            ->editColumn('birthday','{{date("d.m.Y", strtotime($birthday))}}')
            ->make(true);
    }

    public function unapprovedCount($id)
    {
        $faculty = Faculty::find($id);
        return $faculty->users()->whereNull('activated_by')->count();
    }


    public function messages($id)
    {
        $faculty = Faculty::find($id);
        return view('admin.faculty.messages', compact(['faculty']));
    }

    public function messagesUnanswered($id)
    {
        $authUser = Auth::user();
        $colors = ["purple", "red", "green"];
        $faculty = Faculty::find($id);
        return view('admin.faculty.messages_unanswered', compact(['faculty','colors','authUser']));
    }


}