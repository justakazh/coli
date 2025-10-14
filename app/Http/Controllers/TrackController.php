<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Scans;
class TrackController extends Controller
{
    public function index($id)
    {
        $data['scan'] = Scans::find($id);
        $data['drawflow'] = $data['scan']->workflow->diagram_data;
        return view('track.index', compact('data'));
    }

}
