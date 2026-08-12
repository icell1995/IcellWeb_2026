<?php

namespace App\Http\Controllers\CMS\EventGallery;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EventGalleryController extends Controller
{
    public function index()
    {
        return view('cms.event-gallery.index');
    }

    public function show($id)
    {
        $viewData = [
            'title' => 'Event Gallery',
        ];

        return view('cms.event-gallery.show', $viewData);
    }

    public function create()
    {
        return view('cms.event-gallery.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('cms.event-gallery.index');
    }

    public function edit($id)
    {
        return view('cms.event-gallery.edit');
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('cms.event-gallery.index');
    }

    public function destroy($id)
    {
        return redirect()->route('cms.event-gallery.index');
    }
}
