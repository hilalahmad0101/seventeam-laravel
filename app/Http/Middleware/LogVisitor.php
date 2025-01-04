<?php

namespace App\Http\Middleware;

use App\Models\Visitor;
use Closure;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogVisitor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $ip = $request->ip();

        // Use a fallback IP for testing
        if ($ip === '127.0.0.1' || $ip === '::1') {
            $ip = '8.8.8.8';
        }

        // Check if the visitor's IP already exists in the database
        $existingVisitor = Visitor::where('ip', $ip)->first();

        if (!$existingVisitor) {
            // API URL
            $url = "http://ip-api.com/json/{$ip}";

            // Fetch visitor details
            $client = new Client();
            $response = $client->get($url);
            $data = json_decode($response->getBody(), true);

            if ($data['status'] === 'success') {
                // Construct the flag URL using the country code
                $flagUrl = "https://flagcdn.com/w320/" . strtolower($data['countryCode']) . ".png";

                // Save the visitor to the database
                Visitor::create([
                    'ip' => $data['query'],
                    'country' => $data['country'],
                    'region' => $data['regionName'],
                    'city' => $data['city'],
                    'zip' => $data['zip'],
                    'lat' => $data['lat'],
                    'lon' => $data['lon'],
                    'isp' => $data['isp'],
                    'flag' => $flagUrl,
                ]);
            }
        }

        // Continue with the request
        return $next($request);
    }
}
