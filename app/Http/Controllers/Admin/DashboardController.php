<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $visitors = Visitor::select('country', 'flag', 'lat', 'lon')
        ->selectRaw('COUNT(*) as count')
        ->groupBy('country', 'flag', 'lat', 'lon')
        ->get();

    // Calculate total visitors
    $totalVisitors = $visitors->sum('count');

    // Add percentage and other necessary data for table
    $visitorStats = $visitors->map(function ($visitor) use ($totalVisitors) {
        return [
            'country' => $visitor->country,
            'flag' => $visitor->flag,
            'count' => $visitor->count,
            'percentage' => ($visitor->count / $totalVisitors) * 100,
        ];
    });

    // Prepare map markers
    $mapMarkers = $visitors->map(function ($visitor) {
        return [
            'lat' => $visitor->lat,
            'lon' => $visitor->lon,
            'country' => $visitor->country, // Add country name for tooltip (optional)
        ];
    });
        return view('admin.dashboard',compact('visitorStats','mapMarkers'));
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('admin.login')->with('success', 'Successfully logged');
    }

    public function user_list()
    {
        $users = User::whereRole(0)->latest()->paginate(10)->withQueryString();
        return view('admin.users.list', compact('users'));
    }

    // delete user
    public function user_delete($id)
    {
        User::find($id)->delete();
        return back()->with('success', 'User deleted successfully');
    }

    public function user_status($id)
    {
        $user = User::find($id);
        if ($user->status == 0) {
            $user->status = 1;
            $user->save();
            return response()->json([
                'success'=>true,
                'message'=>'Banned On successfully'
            ]);
        } else {
            $user->status = 0;
            $user->save();
            return response()->json([
                'success'=>true,
                'message'=>'Banned Off successfully'
            ]);
        }
    }
}
