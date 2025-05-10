<?php

namespace App\Services;

use App\Models\PersonalAccessToken;
use Illuminate\Support\Collection;

class TokenService
{
    public function getAllTokens(): Collection
    {
        $connections = ['mysql_admin', 'mysql_driver', 'mysql_user'];
        $tokens = collect();
        
        foreach ($connections as $connection) {
            $tokens = $tokens->merge(
                PersonalAccessToken::on($connection)
                    ->select('id', 'tokenable_type', 'name', 'created_at')
                    ->get()
                    ->map(function ($token) use ($connection) {
                        $token->connection = $connection;
                        return $token;
                    })
            );
        }
        
        return $tokens;
    }
    
    public function findToken(string $token): ?PersonalAccessToken
    {
        $connections = ['mysql_admin', 'mysql_driver', 'mysql_user'];
        
        foreach ($connections as $connection) {
            if ($found = PersonalAccessToken::on($connection)->where('token', hash('sha256', $token))->first()) {
                return $found;
            }
        }
        
        return null;
    }
}