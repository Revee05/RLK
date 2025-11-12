<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Setting;
use Session;
class SettingController extends Controller
{
    public function index(){
        $data = Setting::first() ?  Setting::first() : \abort(404);
        return view('admin.setting.data',compact('data'));
    }
    /**
     * update
     *
     * @param Request $request
     * @return void
     */
    public function update(Request $request){
        // dd($request->all());
        $id = $request->id;
        $general = Setting::findOrFail($id);
        $this->_rules($request);
        $data = $request->all();
        if ($request->hasFile('logo')) {
            $dir = 'uploads/logos/';
            $extension = strtolower($request->file('logo')->getClientOriginalExtension()); // get image extension
            $fileName = uniqid() . '.' . $extension; // rename image
            $request->file('logo')->move($dir, $fileName);
            $data['logo'] =  $fileName;
        }
        $general->update($data);
        Session::flash('message', 'Successfully Updated the Seeting General!');
        return redirect()->back();  
    }
    /**
     * _rules
     *
     * @param Request $request
     * @return void
     */
    public function _rules(Request $request){
        $this->validate($request, [
            'title' => 'required',
            'tagline' => 'required',
            'address' => 'required',
            'email' => 'required',
            'favicon' => 'nullable',
            'logo' => 'nullable',
        ],[
            'title.required'    => 'Judul wajib disini',
            'tagline.required'    => 'tagline wajib disini',
            'address.required'  => 'address wajib disini',
            'email.required'  => 'email wajib disini',
            'favicon.required'  => 'favicon wajib disini',
            'logo.required'  => 'logo wajib disini'
        ]);
    }
    /**
     * social
     *
     * @return void
     */
    public function social(){
        $social = Setting::first() ?  Setting::first() : \abort(404);
        return view('admin.setting.social',compact('social'));
    }
    public function updateSocial(Request $request){
        $this->validate($request, [
            'social.facebook' => 'required',
            'social.instagram' => 'required',
            'social.youtube' => 'required',
            'social.twitter' => 'required',
            'social.tiktok' => 'required',
            'social.threads' => 'required',
        ],[
            'social.facebook'    => 'Can\'t Null',
            'social.instagram'    => 'Can\'t Null',
            'social.youtube'    => 'Can\'t Null',
            'social.twitter'    => 'Can\'t Null',
            'social.tiktok'    => 'Can\'t Null',
            'social.threads'    => 'Can\'t Null',
        ]);
        try {
            
            $id = $request->id;
            $general = Setting::findOrFail($id);
            $data = $request->all();
            $general->update($data);
            return redirect()->back()->with('message', 'Successfully Updated social media!');;  
            
        } catch (Exception $e) {
            
        }
    }
}
