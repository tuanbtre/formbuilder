<?php

namespace Tuanbtre\FormBuilder\Http\Controllers;

use Tuanbtre\FormBuilder\Models\Form;
use Tuanbtre\FormBuilder\Models\FormSubmission;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FormController extends Controller
{
    public function index()
    {
        $forms = Form::all();
        return view('form-builder::forms.index', compact('forms'));
    }

    public function create()
    {
        return view('form-builder::forms.create');
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

        return redirect()->route('form-builder.forms.index')->with('success', 'Form created successfully!');
    }

    public function showPublic()
    {
        $now = Carbon::now();
        $form = Form::where('is_active', true)
            ->where('start_time', '<=', $now)
            ->where('end_time', '>=', $now)
            ->first();

        return view('form-builder::welcome', compact('form'));
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