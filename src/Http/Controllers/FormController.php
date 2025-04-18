<?php

namespace App\Http\Controllers\Controller;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\FormSubmission;
use App\Models\Language;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FormController extends Controller
{
    public function __construct()
	{
      $this->middleware('auth:admin');
	}
	public function index()
    {
        $language = Language::all();
      $current_language = $request->l?? config('admin.lang', 2); //kiểm tra ngôn ngữ nếu ko có lấy default APP_LANG_ADMIN trong file env
      $strsearch = $request->search;
      if($request->isMethod('get')){
         if($strsearch)
            $list =Form::where([['language_id', $current_language], ['title', 'like', '%'.$strsearch.'%']])->orderBy('priority','desc')->paginate(15);  
         else
            $list =Form::where('language_id', $current_language)->orderBy('priority','desc')->paginate(15);  
         return view('admin.form.index', compact('list','current_language', 'language'));
      }elseif($request->deleteMode==1){//Xóa
         $record = Form::find($request->Id);
         $record->delete();
         return redirect()->back()->with(['Flass_Message'=>'Xóa dữ liệu thành công']);
      }else{
         $this->validateform($request); // validate database
         $priority = $request->priority==0? Form::where('language_id', $request->l)->max('priority')+1 : $request->priority;
         $record = Form::updateOrCreate(
            ['id'=>$request->Id],
            ['title'=>$request->title,
             'field'=>$re_name,
             'priority'=>$priority,
             'language_id'=>$request->l,
             'isactive'=>($request->isactive==1)? 1 : 0
            ]);             
         return redirect()->back()->with(['Flass_Message'=>'Cập nhật dữ liệu thành công']);
      }
    }

    public function create()
    {
        return view('admin.forms.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'fields' => 'required|array',
            'fields.*.name' => 'required|string|max:255',
            'fields.*.type' => 'required|in:text,number,email,textarea',
            'fields.*.max_length' => 'required|integer|min:1|max:1000',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ]);

        Form::create([
            'title' => $request->title,
            'fields' => $request->fields,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        return redirect()->route('admin.forms.index')->with('success', 'Form created successfully!');
    }

    public function showPublic()
    {
        $now = Carbon::now();
        $form = Form::where('is_active', true)
            ->where('start_time', '<=', $now)
            ->where('end_time', '>=', $now)
            ->first();

        return view('mainpage', compact('form'));
    }

    public function submit(Request $request, Form $form)
    {
        $rules = [];
        foreach ($form->fields as $field) {
            $rule = 'required';
            if ($field['type'] === 'email') {
                $rule .= '|email';
            } elseif ($field['type'] === 'number') {
                $rule .= '|numeric';
            }
            $rules["data.{$field['name']}"] = $rule . "|max:{$field['max_length']}";
        }

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        FormSubmission::create([
            'form_id' => $form->id,
            'data' => $request->data,
        ]);

        $form->update(['is_active' => false]);

        return response()->json(['message' => 'Form submitted successfully!']);
    }
}